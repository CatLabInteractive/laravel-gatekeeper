<?php

namespace CatLab\Gatekeeper\Models;

use CatLab\Gatekeeper\Contracts\Identity;
use CatLab\Gatekeeper\Contracts\Policy;

/**
 * Class GlobalPolicy
 * @package CatLab\Gatekeeper\Models
 */
class GlobalPolicy implements Policy
{
    /**
     * @var callable[]
     */
    private $abilities;

    /**
     * GlobalPolicy constructor.
     */
    public function __construct()
    {
        $this->abilities = [];
    }

    /**
     * @param $ability
     * @param callable $callback
     * @return $this
     */
    public function define($ability, callable $callback)
    {
        $this->abilities[$ability] = $callback;
        return $this;
    }

    /**
     * @param Identity $identity
     * @param string $ability
     * @param mixed[] $arguments
     * @return bool|null
     */
    public function allows(Identity $identity, $ability, array $arguments)
    {
        if (isset($this->abilities[$ability])) {
            array_unshift($arguments, $identity);
            return call_user_func_array($this->abilities[$ability], $arguments);
        }
        return null;
    }

    /**
     * @param $ability
     * @return bool
     */
    public function has($ability)
    {
        return isset($this->abilities[$ability]);
    }
}