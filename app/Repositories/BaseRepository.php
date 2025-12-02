<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;

/**
 * Base Repository Class
 * Provides common CRUD operations and query methods
 */
abstract class BaseRepository
{
    /**
     * The model instance.
     */
    protected Model $model;

    /**
     * Create a new repository instance.
     */
    public function __construct()
    {
        $this->model = $this->getModel();
    }

    /**
     * Get the model instance.
     */
    abstract protected function getModel(): Model;

    /**
     * Get all records.
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Find a record by ID.
     */
    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * Find a record by ID or throw exception.
     */
    public function findOrFail(int $id): Model
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create a new record.
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update a record.
     */
    public function update(int $id, array $data): bool
    {
        return $this->findOrFail($id)->update($data);
    }

    /**
     * Delete a record.
     */
    public function delete(int $id): bool
    {
        return $this->findOrFail($id)->delete();
    }

    /**
     * Get paginated results.
     */
    public function paginate(int $perPage = 15): Paginator
    {
        return $this->model->paginate($perPage);
    }

    /**
     * Get records by criteria.
     */
    public function findBy(string $column, mixed $value): Collection
    {
        return $this->model->where($column, $value)->get();
    }

    /**
     * Get first record by criteria.
     */
    public function findOneBy(string $column, mixed $value): ?Model
    {
        return $this->model->where($column, $value)->first();
    }

    /**
     * Count all records.
     */
    public function count(): int
    {
        return $this->model->count();
    }

    /**
     * Check if record exists.
     */
    public function exists(int $id): bool
    {
        return $this->model->find($id) !== null;
    }
}
