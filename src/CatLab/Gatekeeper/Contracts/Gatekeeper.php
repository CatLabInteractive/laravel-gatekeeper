<?php

namespace CatLab\Gatekeeper\Contracts;

/**
 * Interface Gatekeeper
 * @package CatLab\Gatekeeper\Interfaces
 */
interface Gatekeeper
{
    /**
     * @param $ability
     * @return bool
     */
    public function has($ability);

    /**
     * @param string $regex
     * @param Policy|string $policy
     * @return $this
     */
    public function addPolicy($regex, $policy);

    /**
     * @param string $ability
     * @param callable $callback
     * @return $this
     */
    public function define($ability, callable $callback);

    /**
     * @param \CatLab\Gatekeeper\Contracts\Identity|callable $identity
     * @return $this
     */
    public function setIdentity($identity);

    /**
     * @return Identity
     */
    public function getIdentity();

    /**
     * @param string $ability
     * @return bool
     */
    public function allows($ability);

    /**
     * @param string $ability
     * @return bool
     */
    public function denies($ability);

    /**
     * @param string $ability
     * @return bool
     */
    public function check($ability);

    /**
     * Return a new Gatekeeper with identity set to a specified Identity.
     * @param Identity $identity
     * @return Gatekeeper
     */
    public function forIdentity(Identity $identity);
}