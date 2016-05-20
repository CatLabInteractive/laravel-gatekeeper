<?php

namespace CatLab\Gatekeeper\Laravel;

use CatLab\Gatekeeper\Contracts\Gatekeeper as GatekeeperContract;
use CatLab\Gatekeeper\Gatekeeper;
use Illuminate\Support\ServiceProvider;

/**
 * Class GatekeeperProvider
 * @package CatLab\Gatekeeper\Laravel
 */
class GatekeeperProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerGatekeeper();
        $this->registerPolicies();
    }

    /**
     * Register the gatekeeper
     */
    protected function registerGatekeeper()
    {
        $parent = $this;

        // Our own custom gatekeeper
        $this->app->singleton(GatekeeperContract::class, function() use ($parent) {
            return $parent->createGatekeeper();
        });
    }

    /**
     *
     */
    protected function registerPolicies()
    {

    }

    protected function createGatekeeper()
    {
        return new Gatekeeper();
    }
}