<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Base Repository Interface
 * Defines common CRUD operations that all repositories must implement
 */
interface RepositoryInterface
{
    /**
     * Get all records
     *
     * @param array $columns
     * @return Collection
     */
    public function all(array $columns = ['*']): Collection;

    /**
     * Get paginated records
     *
     * @param int $perPage
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    /**
     * Find a record by ID
     *
     * @param int $id
     * @param array $columns
     * @return Model|null
     */
    public function find(int $id, array $columns = ['*']): ?Model;

    /**
     * Find a record by ID or fail
     *
     * @param int $id
     * @param array $columns
     * @return Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id, array $columns = ['*']): Model;

    /**
     * Find records by field
     *
     * @param string $field
     * @param mixed $value
     * @param array $columns
     * @return Collection
     */
    public function findBy(string $field, $value, array $columns = ['*']): Collection;

    /**
     * Find first record by field
     *
     * @param string $field
     * @param mixed $value
     * @param array $columns
     * @return Model|null
     */
    public function findOneBy(string $field, $value, array $columns = ['*']): ?Model;

    /**
     * Create a new record
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * Update a record
     *
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function update(int $id, array $data): Model;

    /**
     * Delete a record
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Count records
     *
     * @return int
     */
    public function count(): int;

    /**
     * Get records with relationships
     *
     * @param array|string $relations
     * @return $this
     */
    public function with($relations): self;

    /**
     * Order records
     *
     * @param string $column
     * @param string $direction
     * @return $this
     */
    public function orderBy(string $column, string $direction = 'asc'): self;

    /**
     * Filter records by where clause
     *
     * @param string $field
     * @param mixed $value
     * @param string $operator
     * @return $this
     */
    public function where(string $field, $value, string $operator = '='): self;

    /**
     * Get query results
     *
     * @return Collection
     */
    public function get(): Collection;

    /**
     * Reset query builder
     *
     * @return $this
     */
    public function resetQuery(): self;
}
