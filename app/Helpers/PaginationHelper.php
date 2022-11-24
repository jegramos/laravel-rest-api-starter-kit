<?php

namespace App\Helpers;

use Arr;

/**
 * Please use the facade registered
 * @see \App\Providers\FacadeServiceProvider
 */
class PaginationHelper
{
    /**
     * Append the other URL query inputs
     *
     * @param string|null $url
     * @param array $except
     * @return string|null
     */
    private function appendUrlQueryInputs(?string $url, array $except = ['page']): ?string
    {
        if (is_null($url)) {
            return null;
        }

        return $url . '&' . http_build_query(Arr::except(request()->all(), $except));
    }

    /**
     * Re-arrange Laravel's paginate() method results
     * Re-arrange Laravel's paginate() method results
     * for a cleaner API response
     *
     * @param array $results
     * @return array
     */
    public function formatLengthAwarePagination(array $results): array
    {
        $data = $results['data'];
        $pagination = [
            'current_page' => $results['current_page'],
            'last_page' => $results['last_page'],
            'first_page_url' => $this->appendUrlQueryInputs($results['first_page_url']),
            'next_page_url' => $this->appendUrlQueryInputs($results['next_page_url']),
            'prev_page_url' => $this->appendUrlQueryInputs($results['prev_page_url']),
            'last_page_url' => $this->appendUrlQueryInputs($results['last_page_url']),
            'from' => $results['from'],
            'to' => $results['to'],
            'per_page' => $results['per_page'],
            'total' => $results['total'],
            'path' => $results['path'],
        ];

        $formattedResults = [];
        $formattedResults['data'] = $data;
        $formattedResults['pagination'] = $pagination;

        return $formattedResults;
    }

    /**
     * Re-arrange Laravel's simplePaginate() method results
     * for a cleaner API response
     *
     * @param array $results
     * @return array
     */
    public function formatSimplePagination(array $results): array
    {
        $data = $results['data'];
        $pagination = [
            'first_page_url' => $this->appendUrlQueryInputs($results['first_page_url']),
            'prev_page_url' => $this->appendUrlQueryInputs($results['prev_page_url']),
            'next_page_url' => $this->appendUrlQueryInputs($results['next_page_url']),
            'from' => $results['from'],
            'to' => $results['to'],
            'per_page' => $results['per_page'],
            'path' => $results['path'],
        ];

        $formattedResults = [];
        $formattedResults['data'] = $data;
        $formattedResults['pagination'] = $pagination;

        return $formattedResults;
    }

    /**
     * Re-arrange Laravel's simplePaginate() method results
     * for a cleaner API response
     *
     * @param array $results
     * @return array
     */
    public function formatCursorPagination(array $results): array
    {
        $data = $results['data'];
        $pagination = [
            'next_cursor' => $results['next_cursor'],
            'prev_cursor' => $results['prev_cursor'],
            'prev_page_url' => $this->appendUrlQueryInputs($results['prev_page_url'], ['cursor']),
            'next_page_url' => $this->appendUrlQueryInputs($results['next_page_url'], ['cursor']),
            'per_page' => $results['per_page'],
            'path' => $results['path'],
        ];

        $formattedResults = [];
        $formattedResults['data'] = $data;
        $formattedResults['pagination'] = $pagination;

        return $formattedResults;
    }
}
