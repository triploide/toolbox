<?php

declare(strict_types=1);

namespace Toolbox\Validators;

interface ValidatorContract
{
    /**
     * Get the validation rules for a given action.
     * 
     * @param string $action The action for which to get the validation rules.
     * @return array The validation rules for the given action.
     */
    public function getRules(string $action): array;

    /**
     * Get the custom error messages for a given action.
     * 
     * @param string $action The action for which to get the custom error messages.
     * @return array The custom error messages for the given action.
     */
    public function getMessages(string $action): array;

    /**
     * Get the custom attribute names for a given action.
     * 
     * @param string $action The action for which to get the custom attribute names.
     * @return array The custom attribute names for the given action.
     */
    public function getAttributes(string $action): array;
}