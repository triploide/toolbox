<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Actions;

use Illuminate\Database\Eloquent\Model;
use Triploide\Toolbox\Helpers\Data;

abstract class Action
{
    protected ?Model $model = null;
    protected mixed $user = null;
    protected Data $data;

    /**
     * Entry point
     */
    final public function execute(array|Data $data, ?Model $model = null, $user = null): mixed
    {
        $this->data = $data instanceof Data ? $data : new Data($data);
        $this->model = $model;
        $this->user = $user;

        return $this->handle();
    }

    /**
     * Main logic
     */
    abstract protected function handle(): mixed;

    // Helpers
    protected function user()
    {
        return $this->user;
    }

    protected function model(): ?Model
    {
        return $this->model;
    }

    protected function data(?string $key = null, $default = null): mixed
    {
        return $key
            ? $this->data->get($key, $default)
            : $this->data;
    }
}