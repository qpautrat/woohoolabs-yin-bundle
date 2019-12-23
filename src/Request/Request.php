<?php

namespace QP\WoohoolabsYinBundle\Request;

use WoohooLabs\Yin\JsonApi\Request\JsonApiRequest;
use WoohooLabs\Yin\JsonApi\Request\Pagination\CursorBasedPagination;
use WoohooLabs\Yin\JsonApi\Request\Pagination\FixedPageBasedPagination;
use WoohooLabs\Yin\JsonApi\Request\Pagination\OffsetBasedPagination;
use WoohooLabs\Yin\JsonApi\Request\Pagination\PageBasedPagination;

class Request extends JsonApiRequest
{
    public function getFixedPageBasedPagination(?int $defaultPage = null): FixedPageBasedPagination
    {
        return FixedPageBasedPagination::fromPaginationQueryParams($this->getPagination(), $defaultPage);
    }

    public function getPageBasedPagination(?int $defaultPage = null, ?int $defaultSize = null): PageBasedPagination
    {
        return PageBasedPagination::fromPaginationQueryParams($this->getPagination(), $defaultPage, $defaultSize);
    }

    public function getOffsetBasedPagination(
        ?int $defaultOffset = null,
        ?int $defaultLimit = null
    ): OffsetBasedPagination {
        return OffsetBasedPagination::fromPaginationQueryParams($this->getPagination(), $defaultOffset, $defaultLimit);
    }

    /**
     * @param mixed $defaultCursor
     */
    public function getCursorBasedPagination($defaultCursor = null): CursorBasedPagination
    {
        return CursorBasedPagination::fromPaginationQueryParams($this->getPagination(), $defaultCursor);
    }
}
