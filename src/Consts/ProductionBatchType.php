<?php

namespace Modules\Warehouse\Consts;

use App\Constants\ConstanstAbstraction;

class ProductionBatchType extends ConstanstAbstraction
{
    const prepared = 'prepared';

    const started = 'started';

    const finished = 'finished';
}
