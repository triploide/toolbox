<?php

namespace Triploide\Toolbox\Relations;

use Triploide\Toolbox\Traits\JoinRelationTrait;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HasOneJoin extends HasOne
{
    use JoinRelationTrait;
}
