<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Enums;

use Illuminate\Support\Str;

enum Tool: string
{
    case ACTION = 'action';
    case CONTROLLER = 'controller';
    case DATAPROVIDER = 'dataprovider';
    case POLICY = 'policy';
    case REPOSITORY = 'repository';
    case RESOURCE = 'resource';
    case VALIDATOR = 'validator';

    public function name(): string
    {
        return Str::studly($this->value);
    }

    public function folder(): string
    {
        return match ($this) {
            self::CONTROLLER => 'Http/Controllers',
            self::RESOURCE => 'Http/Resources',
            default => Str::of($this->value)->plural()->studly()->__toString()
        };
    }
}