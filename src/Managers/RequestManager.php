<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Managers;

use Triploide\Toolbox\Resolvers\RequestResolver;

/**
 *
 * @property bool $useRequest
 * @method bool useRequest()
 * @property string $request
 * @method string request()
 * 
 */

trait RequestManager
{
    /**
     * @return self
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validate(): self
    {
        if ($this->mustApply('request')) {
            if ($requestClass = $this->resolveClass('request')) {
                if (!class_exists($requestClass)) {
                    return $this;
                }

                $formRequest = $requestClass::createFrom(request());

                $formRequest->setContainer(app())->setRedirector(app('redirect'));

                $formRequest->validateResolved();
            }
        }

        return $this;
    }

   /**
     * Get the fully qualified name of the resource class.
     * 
     * @return string
     */
    private function getRequestFullyQualifiedName(): string
    {
        $resolver = new RequestResolver($this->getPathFinder()); // TODO: Reduce coupling

        return $resolver->getFullyQualifiedName('resource');
    }
}
