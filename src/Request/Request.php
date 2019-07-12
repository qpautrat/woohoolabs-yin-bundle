<?php

namespace QP\WoohoolabsYinBundle\Request;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Exception\MediaTypeUnacceptable;
use WoohooLabs\Yin\JsonApi\Exception\MediaTypeUnsupported;
use WoohooLabs\Yin\JsonApi\Exception\QueryParamUnrecognized;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;
use WoohooLabs\Yin\JsonApi\Request\Pagination\CursorBasedPagination;
use WoohooLabs\Yin\JsonApi\Request\Pagination\FixedPageBasedPagination;
use WoohooLabs\Yin\JsonApi\Request\Pagination\OffsetBasedPagination;
use WoohooLabs\Yin\JsonApi\Request\Pagination\PageBasedPagination;
use WoohooLabs\Yin\JsonApi\Schema\ResourceIdentifier;

class Request implements JsonApiRequestInterface
{
    /**
     * @var \Psr\Http\Message\ServerRequestInterface
     */
    protected $serverRequest;

    /**
     * @var ExceptionFactoryInterface
     */
    protected $exceptionFactory;

    /**
     * @var array|null
     */
    protected $includedFields;

    /**
     * @var array|null
     */
    protected $includedRelationships;

    /**
     * @var array|null
     */
    protected $sorting;

    /**
     * @var array|null
     */
    protected $pagination;

    /**
     * @var array|null
     */
    protected $filtering;

    /**
     * Request constructor.
     *
     * @param ServerRequestInterface    $request
     * @param ExceptionFactoryInterface $exceptionFactory
     */
    public function __construct(ServerRequestInterface $request, ExceptionFactoryInterface $exceptionFactory)
    {
        $this->serverRequest = $request;
        $this->exceptionFactory = $exceptionFactory;
    }

    /**
     * @throws \WoohooLabs\Yin\JsonApi\Exception\MediaTypeUnsupported
     */
    public function validateContentTypeHeader(): void
    {
        if ($this->isValidMediaTypeHeader('Content-Type') === false) {
            throw new MediaTypeUnsupported($this->getHeaderLine('Content-Type'));
        }
    }

    /**
     * @throws \WoohooLabs\Yin\JsonApi\Exception\MediaTypeUnacceptable
     */
    public function validateAcceptHeader(): void
    {
        if ($this->isValidMediaTypeHeader('Accept') === false) {
            throw new MediaTypeUnacceptable($this->getHeaderLine('Accept'));
        }
    }

    /**
     * @throws \WoohooLabs\Yin\JsonApi\Exception\QueryParamUnrecognized
     */
    public function validateQueryParams(): void
    {
        foreach ($this->getQueryParams() as $queryParamName => $queryParamValue) {
            if (preg_match('/^([a-z]+)$/', $queryParamName) &&
                in_array($queryParamName, ['fields', 'include', 'sort', 'page', 'filter']) === false
            ) {
                throw new QueryParamUnrecognized($queryParamName);
            }
        }
    }

    /**
     * Returns a list of media type information, extracted from a given header in the current request.
     *
     * @param string $headerName
     *
     * @return bool
     */
    protected function isValidMediaTypeHeader(string $headerName): bool
    {
        $header = $this->getHeaderLine($headerName);

        return strpos($header, 'application/vnd.api+json') === false || $header === 'application/vnd.api+json';
    }

    protected function setIncludedFields(): void
    {
        $this->includedFields = [];
        $fields = $this->getQueryParam('fields', []);
        if (is_array($fields) === false) {
            return;
        }

        foreach ($fields as $resourceType => $resourceFields) {
            if (is_string($resourceFields)) {
                $this->includedFields[$resourceType] = array_flip(explode(',', $resourceFields));
            }
        }
    }

    /**
     * @param string $resourceType
     *
     * @return array
     */
    public function getIncludedFields(string $resourceType): array
    {
        if ($this->includedFields === null) {
            $this->setIncludedFields();
        }

        return isset($this->includedFields[$resourceType]) ? array_keys($this->includedFields[$resourceType]) : [];
    }

    /**
     * @param string $resourceType
     * @param string $field
     *
     * @return bool
     */
    public function isIncludedField(string $resourceType, string $field): bool
    {
        if ($this->includedFields === null) {
            $this->setIncludedFields();
        }

        if (array_key_exists($resourceType, $this->includedFields) === false) {
            return true;
        }

        if (empty($this->includedFields[$resourceType]) === true) {
            return false;
        }

        return isset($this->includedFields[$resourceType][$field]);
    }

    protected function setIncludedRelationships(): void
    {
        $this->includedRelationships = [];

        $includeQueryParam = $this->getQueryParam('include', '');
        if ($includeQueryParam === '') {
            return;
        }

        $relationshipNames = explode(',', $includeQueryParam);
        foreach ($relationshipNames as $relationship) {
            $relationship = ".$relationship.";
            $length = strlen($relationship);
            $dot1 = 0;

            while ($dot1 < $length - 1) {
                $dot2 = strpos($relationship, '.', $dot1 + 1);
                $path = substr($relationship, 1, $dot1 > 0 ? $dot1 - 1 : 0);
                $name = substr($relationship, $dot1 + 1, $dot2 - $dot1 - 1);

                if (isset($this->includedRelationships[$path]) === false) {
                    $this->includedRelationships[$path] = [];
                }
                $this->includedRelationships[$path][$name] = $name;

                $dot1 = $dot2;
            }
        }
    }

    /**
     * @return bool
     */
    public function hasIncludedRelationships(): bool
    {
        if ($this->includedRelationships === null) {
            $this->setIncludedRelationships();
        }

        return empty($this->includedRelationships) === false;
    }

    /**
     * @param string $baseRelationshipPath
     *
     * @return array
     */
    public function getIncludedRelationships(string $baseRelationshipPath): array
    {
        if ($this->includedRelationships === null) {
            $this->setIncludedRelationships();
        }

        if (isset($this->includedRelationships[$baseRelationshipPath])) {
            return array_values($this->includedRelationships[$baseRelationshipPath]);
        } else {
            return [];
        }
    }

    /**
     * @param string $baseRelationshipPath
     * @param string $relationshipName
     * @param array  $defaultRelationships
     *
     * @return bool
     */
    public function isIncludedRelationship(
        string $baseRelationshipPath,
        string $relationshipName,
        array $defaultRelationships
    ): bool {
        if ($this->includedRelationships === null) {
            $this->setIncludedRelationships();
        }

        if ($this->getQueryParam('include') === '') {
            return false;
        }

        if (empty($this->includedRelationships) && array_key_exists($relationshipName, $defaultRelationships)) {
            return true;
        }

        return isset($this->includedRelationships[$baseRelationshipPath][$relationshipName]);
    }

    protected function setSorting(): void
    {
        $sortingQueryParam = $this->getQueryParam('sort', '');
        if ($sortingQueryParam === '') {
            $this->sorting = [];

            return;
        }

        $sorting = explode(',', $sortingQueryParam);
        $this->sorting = is_array($sorting) ? $sorting : [];
    }

    /**
     * @return array
     */
    public function getSorting(): array
    {
        if ($this->sorting === null) {
            $this->setSorting();
        }

        return $this->sorting;
    }

    protected function setPagination(): void
    {
        $pagination = $this->getQueryParam('page', null);
        $this->pagination = is_array($pagination) ? $pagination : [];
    }

    /**
     * @return array
     */
    public function getPagination(): array
    {
        if ($this->pagination === null) {
            $this->setPagination();
        }

        return $this->pagination;
    }

    /**
     * @param int|null $defaultPage
     *
     * @return FixedPageBasedPagination
     */
    public function getFixedPageBasedPagination(?int $defaultPage = null): FixedPageBasedPagination
    {
        return FixedPageBasedPagination::fromPaginationQueryParams($this->getPagination(), $defaultPage);
    }

    /**
     * @param int|null $defaultPage
     * @param int|null $defaultSize
     *
     * @return PageBasedPagination
     */
    public function getPageBasedPagination(?int $defaultPage = null, ?int $defaultSize = null): PageBasedPagination
    {
        return PageBasedPagination::fromPaginationQueryParams($this->getPagination(), $defaultPage, $defaultSize);
    }

    /**
     * @param int|null $defaultOffset
     * @param int|null $defaultLimit
     *
     * @return OffsetBasedPagination
     */
    public function getOffsetBasedPagination(
        ?int $defaultOffset = null,
        ?int $defaultLimit = null
    ): OffsetBasedPagination {
        return OffsetBasedPagination::fromPaginationQueryParams($this->getPagination(), $defaultOffset, $defaultLimit);
    }

    /**
     * @param mixed $defaultCursor
     *
     * @return \WoohooLabs\Yin\JsonApi\Request\Pagination\CursorBasedPagination
     */
    public function getCursorBasedPagination($defaultCursor = null): CursorBasedPagination
    {
        return CursorBasedPagination::fromPaginationQueryParams($this->getPagination(), $defaultCursor);
    }

    protected function setFiltering(): void
    {
        $filtering = $this->getQueryParam('filter', []);
        $this->filtering = is_array($filtering) ? $filtering : [];
    }

    /**
     * {@inheritdoc}
     */
    public function getFiltering(): array
    {
        if ($this->filtering === null) {
            $this->setFiltering();
        }

        return $this->filtering;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilteringParam(string $param, $default = null)
    {
        $filtering = $this->getFiltering();

        return isset($filtering[$param]) ? $filtering[$param] : $default;
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return array|string|mixed
     */
    public function getQueryParam(string $name, $default = null)
    {
        $queryParams = $this->serverRequest->getQueryParams();

        return isset($queryParams[$name]) ? $queryParams[$name] : $default;
    }

    /**
     * Returns a query parameter with a name of $name if it is present in the request, or the $default value otherwise.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this|Request
     */
    public function withQueryParam(string $name, $value)
    {
        $self = clone $this;
        $queryParams = $this->serverRequest->getQueryParams();
        $queryParams[$name] = $value;
        $self->serverRequest = $this->serverRequest->withQueryParams($queryParams);
        $self->initializeParsedQueryParams();

        return $self;
    }

    protected function initializeParsedQueryParams(): void
    {
        $this->includedFields = null;
        $this->includedRelationships = null;
        $this->sorting = null;
        $this->pagination = null;
        $this->filtering = null;
    }

    /**
     * {@inheritdoc}
     */
    public function getResource($default = null)
    {
        $body = $this->getParsedBody();

        return isset($body['data']) ? $body['data'] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceType($default = null)
    {
        $data = $this->getResource();

        return isset($data['type']) ? $data['type'] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceId($default = null)
    {
        $data = $this->getResource();

        return isset($data['id']) ? $data['id'] : null;
    }

    /**
     * @return array
     */
    public function getResourceAttributes(): array
    {
        $data = $this->getResource();

        return isset($data['attributes']) ? $data['attributes'] : [];
    }

    /**
     * @param string $attribute
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getResourceAttribute(string $attribute, $default = null)
    {
        $attributes = $this->getResourceAttributes();

        return isset($attributes[$attribute]) ? $attributes[$attribute] : $default;
    }

    /**
     * @param string $relationship
     *
     * @return ToOneRelationship|null
     *
     * @throws \WoohooLabs\Yin\JsonApi\Exception\JsonApiExceptionInterface
     */
    public function getToOneRelationship(string $relationship): ?ToOneRelationship
    {
        $data = $this->getResource();

        //The relationship has to exist in the request and have a data attribute to be valid
        if (isset($data['relationships'][$relationship]) &&
            array_key_exists('data', $data['relationships'][$relationship])
        ) {
            //If the data is null, this request is to clear the relationship, we return an empty relationship
            if ($data['relationships'][$relationship]['data'] === null) {
                return new ToOneRelationship();
            }
            //If the data is set and is not null, we create the relationship with a resource identifier from the request
            return new ToOneRelationship(
                ResourceIdentifier::fromArray($data['relationships'][$relationship]['data'], $this->exceptionFactory)
            );
        }

        return null;
    }

    /**
     * @param string $relationship
     *
     * @return ToManyRelationship|null
     *
     * @throws \WoohooLabs\Yin\JsonApi\Exception\JsonApiExceptionInterface
     */
    public function getToManyRelationship(string $relationship): ?ToManyRelationship
    {
        $data = $this->getResource();

        if (isset($data['relationships'][$relationship]['data']) === false) {
            return null;
        }

        $resourceIdentifiers = [];
        foreach ($data['relationships'][$relationship]['data'] as $item) {
            $resourceIdentifiers[] = ResourceIdentifier::fromArray($item, $this->exceptionFactory);
        }

        return new ToManyRelationship($resourceIdentifiers);
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion()
    {
        return $this->serverRequest->getProtocolVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function withProtocolVersion($version)
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withProtocolVersion($version);

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return $this->serverRequest->getHeaders();
    }

    /**
     * {@inheritdoc}
     */
    public function hasHeader($name)
    {
        return $this->serverRequest->hasHeader($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($name)
    {
        return $this->serverRequest->getHeader($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderLine($name)
    {
        return $this->serverRequest->getHeaderLine($name);
    }

    /**
     * {@inheritdoc}
     */
    public function withHeader($name, $value)
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withHeader($name, $value);

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedHeader($name, $value)
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withAddedHeader($name, $value);

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutHeader($name)
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withoutHeader($name);

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->serverRequest->getBody();
    }

    /**
     * {@inheritdoc}
     */
    public function withBody(StreamInterface $body)
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withBody($body);

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestTarget()
    {
        return $this->serverRequest->getRequestTarget();
    }

    /**
     * {@inheritdoc}
     */
    public function withRequestTarget($requestTarget)
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withRequestTarget($requestTarget);

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return $this->serverRequest->getMethod();
    }

    /**
     * {@inheritdoc}
     */
    public function withMethod($method)
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withMethod($method);

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function getUri()
    {
        return $this->serverRequest->getUri();
    }

    /**
     * {@inheritdoc}
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withUri($uri, $preserveHost);

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function getServerParams()
    {
        return $this->serverRequest->getServerParams();
    }

    /**
     * {@inheritdoc}
     */
    public function getCookieParams()
    {
        return $this->serverRequest->getCookieParams();
    }

    /**
     * {@inheritdoc}
     */
    public function withCookieParams(array $cookies)
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withCookieParams($cookies);

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryParams()
    {
        return $this->serverRequest->getQueryParams();
    }

    /**
     * {@inheritdoc}
     */
    public function withQueryParams(array $query)
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withQueryParams($query);
        $self->initializeParsedQueryParams();

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function getUploadedFiles()
    {
        return $this->serverRequest->getUploadedFiles();
    }

    /**
     * {@inheritdoc}
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withUploadedFiles($uploadedFiles);

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function getParsedBody()
    {
        if (empty($this->serverRequest->getParsedBody()) === false) {
            return $this->serverRequest->getParsedBody();
        }

        $content = $this->serverRequest->getBody()->getContents();
        if ($content) {
            $this->serverRequest = $this->serverRequest->withParsedBody(json_decode($content, true));
        }

        return $this->serverRequest->getParsedBody();
    }

    /**
     * {@inheritdoc}
     */
    public function withParsedBody($data)
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withParsedBody($data);

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->serverRequest->getAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($name, $default = null)
    {
        return $this->serverRequest->getAttribute($name, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function withAttribute($name, $value)
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withAttribute($name, $value);

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutAttribute($name)
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withoutAttribute($name);

        return $self;
    }
}
