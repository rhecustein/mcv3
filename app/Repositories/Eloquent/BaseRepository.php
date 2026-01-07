<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Base Repository
 * Implements common CRUD operations for all repositories
 */
abstract class BaseRepository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * @var Builder
     */
    protected Builder $query;

    /**
     * BaseRepository constructor.
     */
    public function __construct()
    {
        $this->model = $this->makeModel();
        $this->resetQuery();
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    abstract protected function model(): string;

    /**
     * Make Model instance
     *
     * @return Model
     * @throws \Exception
     */
    protected function makeModel(): Model
    {
        $model = app($this->model());

        if (!$model instanceof Model) {
            throw new \Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $model;
    }

    /**
     * Get all records
     *
     * @param array $columns
     * @return Collection
     */
    public function all(array $columns = ['*']): Collection
    {
        return $this->model->all($columns);
    }

    /**
     * Get paginated records
     *
     * @param int $perPage
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        $result = $this->query->paginate($perPage, $columns);
        $this->resetQuery();
        return $result;
    }

    /**
     * Find a record by ID
     *
     * @param int $id
     * @param array $columns
     * @return Model|null
     */
    public function find(int $id, array $columns = ['*']): ?Model
    {
        return $this->model->find($id, $columns);
    }

    /**
     * Find a record by ID or fail
     *
     * @param int $id
     * @param array $columns
     * @return Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id, array $columns = ['*']): Model
    {
        return $this->model->findOrFail($id, $columns);
    }

    /**
     * Find records by field
     *
     * @param string $field
     * @param mixed $value
     * @param array $columns
     * @return Collection
     */
    public function findBy(string $field, $value, array $columns = ['*']): Collection
    {
        return $this->model->where($field, $value)->get($columns);
    }

    /**
     * Find first record by field
     *
     * @param string $field
     * @param mixed $value
     * @param array $columns
     * @return Model|null
     */
    public function findOneBy(string $field, $value, array $columns = ['*']): ?Model
    {
        return $this->model->where($field, $value)->first($columns);
    }

    /**
     * Create a new record
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update a record
     *
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function update(int $id, array $data): Model
    {
        $model = $this->findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    /**
     * Delete a record
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $model = $this->findOrFail($id);
        return $model->delete();
    }

    /**
     * Count records
     *
     * @return int
     */
    public function count(): int
    {
        $count = $this->query->count();
        $this->resetQuery();
        return $count;
    }

    /**
     * Get records with relationships
     *
     * @param array|string $relations
     * @return $this
     */
    public function with($relations): self
    {
        $this->query = $this->query->with($relations);
        return $this;
    }

    /**
     * Order records
     *
     * @param string $column
     * @param string $direction
     * @return $this
     */
    public function orderBy(string $column, string $direction = 'asc'): self
    {
        $this->query = $this->query->orderBy($column, $direction);
        return $this;
    }

    /**
     * Filter records by where clause
     *
     * @param string $field
     * @param mixed $value
     * @param string $operator
     * @return $this
     */
    public function where(string $field, $value, string $operator = '='): self
    {
        $this->query = $this->query->where($field, $operator, $value);
        return $this;
    }

    /**
     * Get query results
     *
     * @return Collection
     */
    public function get(): Collection
    {
        $result = $this->query->get();
        $this->resetQuery();
        return $result;
    }

    /**
     * Reset query builder
     *
     * @return $this
     */
    public function resetQuery(): self
    {
        $this->query = $this->model->newQuery();
        return $this;
    }

    /**
     * Get the query builder instance
     *
     * @return Builder
     */
    protected function getQuery(): Builder
    {
        return $this->query;
    }

    /**
     * Apply filters to query
     *
     * @param array $filters
     * @return $this
     */
    protected function applyFilters(array $filters): self
    {
        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                if (is_array($value)) {
                    $this->query->whereIn($field, $value);
                } else {
                    $this->query->where($field, $value);
                }
            }
        }

        return $this;
    }
}
