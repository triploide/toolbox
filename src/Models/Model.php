<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Models;

use Fico7489\Laravel\EloquentJoin\Traits\EloquentJoin;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
{
    use EloquentJoin;
    use Traits\HasCache;
}
