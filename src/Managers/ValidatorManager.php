<?php

declare(strict_types=1);

namespace Toolbox\Managers;

use Toolbox\Validators\ValidatorContract;

/**
 *
 * @property bool $useValidator
 * @method bool useValidator()
 * @property string $validator
 * @method string validator()
 * 
 */

trait ValidatorManager
{
    /**
     * @return self
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validate(): self
    {
        if ($this->mustApply('validator')) {
            if ($validator = $this->resolveValidator()) {
                $action = $this->action;

                request()->validate(
                    rules: $validator->getRules($action),
                    messages: $validator->getMessages($action),
                    attributes: $validator->getAttributes($action)
                );
            }
        }

        return $this;
    }

    /**
     * @return ValidatorContract|null
     */
    private function resolveValidator(): ?ValidatorContract
    {
        return $this->resolveClass('validator', function ($class) {
            return class_exists($class) ? new $class : null;
        });
    }
}