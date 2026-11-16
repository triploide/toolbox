<?php

namespace Triploide\Toolbox\Relations;

use Triploide\Toolbox\Traits\JoinRelationTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BelongsToJoin extends BelongsTo
{
    use JoinRelationTrait;
}
