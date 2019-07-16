<?php

namespace QP\WoohoolabsYinBundle\Request;

use WoohooLabs\Yin\JsonApi\Request\JsonApiRequest;
use WoohooLabs\Yin\JsonApi\Request\Pagination\CursorBasedPagination;
use WoohooLabs\Yin\JsonApi\Request\Pagination\FixedPageBasedPagination;
use WoohooLabs\Yin\JsonApi\Request\Pagination\OffsetBasedPagination;
use WoohooLabs\Yin\JsonApi\Request\Pagination\PageBasedPagination;

class Request extends JsonApiRequest
{
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
     * @return CursorBasedPagination
     */
    public function getCursorBasedPagination($defaultCursor = null): CursorBasedPagination
    {
        return CursorBasedPagination::fromPaginationQueryParams($this->getPagination(), $defaultCursor);
    }
}
