<?php

namespace CatLab\Gatekeeper\Laravel\Facades;

use CatLab\Gatekeeper\Contracts\Gatekeeper;
use Illuminate\Support\Facades\Facade;

/**
 * Class GatekeeperFacade
 * @package CatLab\Gatekeeper\Laravel
 */
class GatekeeperFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Gatekeeper::class;
    }
}