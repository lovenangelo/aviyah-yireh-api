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
        if (isset($paginated['data'])) {
            $paginated['items'] = $paginated['data'];
            unset($paginated['data']);
            if (! $this->includeLinks) {
                unset($paginated['links']);
                unset($paginated['last_page']);
                unset($paginated['last_page_url']);
                unset($paginated['first_page_url']);
                unset($paginated['prev_page_url']);
                unset($paginated['from']);
                unset($paginated['to']);
                unset($paginated['path']);
                unset($paginated['per_page']);
            }
        }

        return $paginated;
    }
}
