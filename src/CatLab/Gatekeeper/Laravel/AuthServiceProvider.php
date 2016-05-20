<?php

namespace CatLab\Gatekeeper\Laravel;

use CatLab\Gatekeeper\Contracts\Gatekeeper;
use CatLab\Gatekeeper\Laravel\Models\Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;

/**
 * Class AuthServiceProvider
 * @package CatLab\Gatekeeper
 */
class AuthServiceProvider extends \Illuminate\Auth\AuthServiceProvider
{
    protected function registerAccessGate()
    {
        // Interface through a standard laravel gate
        $this->app->singleton(GateContract::class, function($app) {
            return new Gate($app[Gatekeeper::class]);
        });
    }
}