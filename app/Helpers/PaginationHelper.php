<?php

namespace App\Helpers;

use Arr;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\Builder;

/**
 * Please use the facade registered
 * @see \App\Providers\FacadeServiceProvider
 */
class PaginationHelper
{
    /**
     * Re-arrange Laravel's paginate() method results
     * to a cleaner API response
     *
     * @param array $results
     * @return array
     */
    public function formatLengthAwarePagination(array $results): array
    {
        $data = $results['data'];

        $otherQueryInputs = '&' . http_build_query(Arr::except(request()->all(), ['page']));
        $pagination = [
            'current_page' => $results['current_page'],
            'first_page_url' => $results['first_page_url'] . $otherQueryInputs,
            'next_page_url' => $results['next_page_url'] . $otherQueryInputs,
            'prev_page_url' => $results['prev_page_url'] . $otherQueryInputs,
            'from' => $results['from'],
            'to' => $results['to'],
            'last_page' => $results['last_page'],
            'last_page_url' => $results['last_page_url'] . $otherQueryInputs,
            'path' => $results['path'],
            'per_page' => $results['per_page'],
            'total' => $results['total'],
        ];

        $formattedResults = [];
        $formattedResults['data'] = $data;
        $formattedResults['pagination'] = $pagination;

        return $formattedResults;
    }
}
