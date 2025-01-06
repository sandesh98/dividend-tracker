<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function updateOrCreate(array $attributes, array $values)
    {
        return $this->model->newQuery()->updateOrCreate($attributes, $values);
    }
}
