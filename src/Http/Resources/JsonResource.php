<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource as LaravelResource;
use Illuminate\Http\Request;
use JsonSerializable;

class JsonResource extends LaravelResource
{
    protected string $method;

    /**
     * @param string $method
     * @return self
     * 
     * @throws Exception
     */
    public function setMethod(string $method): self
    {
        $reflector = new \ReflectionClass(self::class);

        $methods = array_filter(
            $reflector->getMethods(),
            fn($m) => $m->getName() !== 'toArray'
        );

        $methodNames = array_map(fn($m) => $m->getName(), $methods);

        if (in_array($method, $methodNames)) {
            throw new \Exception("You cannot use methods of the Illuminate\Http\Resources\Json\JsonResource class");
        }

        $this->method = $method;

        return $this;
    }

    /**
     * Resolve the resource to an array.
     *
     * @param  \Illuminate\Http\Request|null  $request
     * @return array
     */
    public function resolve($request = null)
    {
        $data = $this->resolveResourceData(
            $request ?: $this->resolveRequestFromContainer()
        );

        if ($data instanceof Arrayable) {
            $method = $this->method;
            $data = method_exists($this, $method) ? $this->$method() : $data->toArray();
        } elseif ($data instanceof JsonSerializable) {
            $data = $data->jsonSerialize();
        }

        return $this->filter((array) $data);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toAttributes(Request $request)
    {
        if (property_exists($this, 'attributes')) {
            return $this->attributes;
        }

        $method = method_exists($this, $this->method) ? $this->method : 'toArray';

        return $this->$method($request);
    }
}
