<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Models;

use Triploide\Toolbox\Traits\EloquentJoin;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
{
    use EloquentJoin;
    use Traits\HasCache;
}
