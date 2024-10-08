<?php

namespace App\JsonApi;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class JsonApiQueryBuilder
{
    public function allowedSorts(): Closure
    {
        return function (array $allowedSortFields) {
            /* @var Builder $this */
            if (request()->filled('sort')) {
                $sortFields = explode(',', request()->input('sort'));
                foreach ($sortFields as $sortField) {
                    // Determines the sort direction (ascending or descending)
                    $sortDirection = str_starts_with($sortField, '-') ? 'desc' : 'asc';
                    // Removes '-' sign from sort field if present
                    $sortField = ltrim($sortField, '-');

                    // Check if the sort field is allowed
                    abort_unless(in_array($sortField, $allowedSortFields), 400, 'Invalid sort field');

                    $this->orderBy($sortField, $sortDirection);
                }
            }
            return $this;
        };
    }

    public function allowedFilters(): Closure
    {
        return function (array $allowedFilters) {
            /* @var Builder $this */
            foreach (request('filter', []) as $filter => $value) {
                abort_unless(in_array($filter, $allowedFilters), 400, 'Filter not allowed');
                $this->where($filter, 'like', '%' . $value . '%');
            }
            return $this;
        };
    }

    public function sparseFieldSet(): Closure
    {
        return function () {
            /* @var Builder $this */
            $fields = explode(',', request()->input('fields.admin-users'));
            return $this->addSelect($fields);
        };
    }

    public function jsonPaginate(): Closure
    {
        return function () {
            /* @var Builder $this */
            return $this->paginate(
                $perPage = request('page.size', 15),
                $columns = ['*'],
                $pageName = 'page[number]',
                $page = request('page.number', 1),
            )->appends(request()->only('sort', 'filter', 'page.size'));
        };
    }
}
