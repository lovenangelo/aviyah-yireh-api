<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomPaginatedCollection extends ResourceCollection
{
    protected $includeLinks;

    public function __construct($resource, $includeLinks = false)
    {
        parent::__construct($resource);
        $this->includeLinks = $includeLinks;
    }

    public function toArray($request)
    {
        $paginated = $this->resource->toArray();

        $paginated['items'] = $paginated['data'];
        unset($paginated['data']);

        if (!$this->includeLinks) {
            unset($paginated['links']);
        }

        return $paginated;
    }
}
