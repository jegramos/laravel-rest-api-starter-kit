<?php

namespace App\Helpers;

use Arr;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Pagination\AbstractPaginator;
use InvalidArgumentException;

/**
 * Please use the facade registered
 *
 * @see \App\Providers\FacadeServiceProvider
 */
class PaginationHelper
{
    /**
     * Append the other URL query inputs
     */
    private function appendUrlQueryInputs(?string $url, array $except = ['page']): ?string
    {
        if (is_null($url)) {
            return null;
        }

        return $url.'&'.http_build_query(Arr::except(request()->all(), $except));
    }

    /**
     * Re-arrange Laravel's paginate(), simplePaginate(), and cursorPaginate() methods for a cleaner API response
     */
    public function formatPagination(CursorPaginator|AbstractPaginator $paginator): array
    {
        $data = $paginator->toArray();

        switch ($paginator) {
            case $paginator instanceof LengthAwarePaginator:
                $result = $this->formatLengthAwarePagination($data);
                break;
            case $paginator instanceof Paginator:
                $result = $this->formatSimplePagination($data);
                break;
            case $paginator instanceof CursorPaginator:
                $result = $this->formatCursorPagination($data);
                break;
            default:
                throw new InvalidArgumentException('Unsupported paginator class passed');
        }

        return $result;
    }

    /**
     * Re-arrange Laravel's paginate() method results for a cleaner API response
     * Convert an Illuminate\Contracts\Pagination\LengthAwarePaginator instance to an array and pass as the argument
     * ex. Model::select('name')->paginate()->toArray()
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
     * Re-arrange Laravel's simplePaginate() method results for a cleaner API response
     * Convert an Illuminate\Contracts\Pagination\Paginator instance to an array and pass as the argument
     * ex. Model::select('name')->simplePaginate()->toArray()
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
     * Re-arrange Laravel's simplePaginate() method results for a cleaner API response.
     * Convert an Illuminate\Contracts\Pagination\CursorPaginator instance to an array and pass as the argument
     * ex. Model::select('name')->cursorPaginate()->toArray()
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
