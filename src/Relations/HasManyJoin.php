<?php

namespace Triploide\Toolbox\Relations;

use Triploide\Toolbox\Traits\JoinRelationTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HasManyJoin extends HasMany
{
    use JoinRelationTrait;
}
