<?php

namespace Modules\Warehouse;

use Illuminate\Support\Facades\Event;
use Lakasir\LakasirModule\Events\TransactionSucceed;
use Lakasir\LakasirModule\Providers\ExtendModuleServiceProvider;

class WarehouseServiceProvider extends ExtendModuleServiceProvider
{
    public function register()
    {
        parent::register();

        Event::listen(TransactionSucceed::class, function (TransactionSucceed $event) {
            // dd($event->selling);
        });
    }
}
