<?php

namespace CatLab\Gatekeeper\Contracts;

/**
 * Interface Policy
 *
 * A policy defines who can access controller methods.
 *
 * @package CatLab\Gatekeeper\Interfaces
 */
interface Policy
{
    /**
     * @param $ability
     * @return bool
     */
    public function has($ability);

    /**
     * @param Identity $identity
     * @param string $ability
     * @param mixed[]
     * @return bool|null
     */
    public function allows(Identity $identity, $ability, array $arguments);
}