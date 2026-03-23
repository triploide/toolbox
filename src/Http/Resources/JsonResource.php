<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource as LaravelResource;

class JsonResource extends LaravelResource
{
    protected string $method;

    /**
     * @param string $method
     * @return self
     * 
     * @throws Exception
     */
    public function setMethod(string $method) : self
    {
        $reflector = new \ReflectionClass(self::class);
        $methods = array_filter($reflector->getMethods(), fn($method) => $method != 'toArray');

        if (in_array($this->method, $methods)) {
            throw new \Exception("You cannot use methods of the Illuminate\Http\Resources\Json\JsonResource class");
        }

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
}
