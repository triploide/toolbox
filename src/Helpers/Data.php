<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Data
{
    /**
     * The data values.
     *
     * @var array
     */
    protected $values;

    /**
     * Create a new data instance.
     *
     * @param  array  $values
     * @return void
     */
    public function __construct($values = [])
    {
        $this->values = $values;
    }

    /**
     * Get all of the data values.
     *
     * @return array
     */
    public function all()
    {
        return $this->values;
    }

    /**
     * Get a subset of the data values.
     *
     * @param  array  $keys
     * @return array
     */
    public function only(array $keys)
    {
        return Arr::only($this->values, $keys);
    }

    /**
     * Get all the data values except for a specified array of items.
     *
     * @param  array  $keys
     * @return array
     */
    public function except(array $keys)
    {
        return Arr::except($this->values, $keys);
    }

    /**
     * Checks if a key exists.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function exists($key)
    {
        $placeholder = new \stdClass;

        return ! collect(is_array($key) ? $key : func_get_args())->contains(function ($key) use ($placeholder) {
            return $this->get($key, $placeholder) === $placeholder;
        });
    }

    /**
     * Determine if the given key is missing from the data values.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function missing($key)
    {
        return ! $this->exists($key);
    }

    /**
     * Checks if a key is present and not null.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function has($key)
    {
        return ! collect(is_array($key) ? $key : func_get_args())->contains(function ($key) {
            return is_null($this->get($key));
        });
    }

    /**
     * Get an item from the session.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->values, $key, $default);
    }

    /**
     * Get the value of a given key and then forget it.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function pull($key, $default = null)
    {
        return Arr::pull($this->values, $key, $default);
    }

    /**
     * Merge new data values with the existing data values.
     * 
     * @param  array|Collection  $values
     * @return static
     */
    public function merge(array|Collection $values): static
    {
        $values = ($values instanceof Collection) ? $values->all() : $values;

        return new static(array_merge($this->values, $values));
    }

    /**
     * Replace the given data values entirely.
     *
     * @param  array|Collection  $values
     * @return void
     */
    public function replace(array|Collection $values)
    {
        $values = ($values instanceof Collection) ? $values->all() : $values;

        $this->put($values);
    }

    /**
     * Put a key / value pair or array of key / value pairs in the data.
     *
     * @param  string|array  $key
     * @param  mixed  $value
     * @return void
     */
    public function put($key, $value = null)
    {
        if (! is_array($key)) {
            $key = [$key => $value];
        }

        foreach ($key as $arrayKey => $arrayValue) {
            Arr::set($this->values, $arrayKey, $arrayValue);
        }
    }

    /**
     * Push a value onto a data values.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function push($key, $value)
    {
        $array = $this->get($key, []);

        $array[] = $value;

        $this->put($key, $array);
    }

    /**
     * Increment the value of an item in the data.
     *
     * @param  string  $key
     * @param  int  $amount
     * @return mixed
     */
    public function increment($key, $amount = 1)
    {
        $this->put($key, $value = $this->get($key, 0) + $amount);

        return $value;
    }

    /**
     * Decrement the value of an item in the data.
     *
     * @param  string  $key
     * @param  int  $amount
     * @return int
     */
    public function decrement($key, $amount = 1)
    {
        return $this->increment($key, $amount * -1);
    }

    /**
     * Remove an item from the data, returning its value.
     *
     * @param  string  $key
     * @return mixed
     */
    public function remove($key)
    {
        return Arr::pull($this->values, $key);
    }

    /**
     * Remove one or many items from the data.
     *
     * @param  string|array  $keys
     * @return void
     */
    public function forget($keys)
    {
        Arr::forget($this->values, $keys);
    }

    /**
     * Remove all of the items from the data.
     *
     * @return void
     */
    public function flush()
    {
        $this->values = [];
    }
}
