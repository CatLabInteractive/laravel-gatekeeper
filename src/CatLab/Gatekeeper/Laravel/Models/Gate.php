<?php

namespace CatLab\Gatekeeper\Laravel\Models;

use CatLab\Gatekeeper\Contracts\Gatekeeper;
use CatLab\Gatekeeper\Exceptions\NotImplementedException;
use CatLab\Gatekeeper\Laravel\Models\UserIdentity;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class Gate
 * @package CatLab\Gatekeeper\Laravel
 */
class Gate implements \Illuminate\Contracts\Auth\Access\Gate
{
    use HandlesAuthorization;

    /**
     * @var Gatekeeper
     */
    private $gatekeeper;
    
    /**
     * Gate constructor.
     * @param Gatekeeper $gatekeeper
     */
    public function __construct(Gatekeeper $gatekeeper)
    {
        $this->gatekeeper = $gatekeeper;
    }

    /**
     * Determine if a given ability has been defined.
     *
     * @param  string $ability
     * @return bool
     */
    public function has($ability)
    {
        return $this->gatekeeper->has($ability);
    }

    /**
     * Define a new ability.
     *
     * @param  string $ability
     * @param  callable|string $callback
     * @return \Illuminate\Contracts\Auth\Access\Gate
     * @throws NotImplementedException
     */
    public function define($ability, $callback)
    {
        throw new NotImplementedException(
            "It's not possible to define abilities through the standard Gate. Use Gatekeeper instead."
        );
    }

    /**
     * Define a policy class for a given class type.
     *
     * @param  string $class
     * @param  string $policy
     * @return \Illuminate\Contracts\Auth\Access\Gate
     * @throws NotImplementedException
     */
    public function policy($class, $policy)
    {
        throw new NotImplementedException(
            "It's not possible to set policies through the standard Gate. Use Gatekeeper instead."
        );
    }

    /**
     * Register a callback to run before all Gate checks.
     *
     * @param  callable $callback
     * @return \Illuminate\Contracts\Auth\Access\Gate
     * @throws NotImplementedException
     */
    public function before(callable $callback)
    {
        throw new NotImplementedException(
            "It's not possible to set events through the standard Gate. Use Gatekeeper instead."
        );
    }

    /**
     * Determine if the given ability should be granted for the current user.
     *
     * @param  string $ability
     * @param  array|mixed $arguments
     * @return bool
     */
    public function allows($ability, $arguments = [])
    {
        if (!is_array($arguments)) {
            $arguments = [ $arguments ];
        }

        array_unshift($arguments, $ability);
        return call_user_func_array([ $this->gatekeeper, 'allows'], $arguments);
    }

    /**
     * Determine if the given ability should be denied for the current user.
     *
     * @param  string $ability
     * @param  array|mixed $arguments
     * @return bool
     */
    public function denies($ability, $arguments = [])
    {
        if (!is_array($arguments)) {
            $arguments = [ $arguments ];
        }

        array_unshift($arguments, $ability);
        return call_user_func_array([ $this->gatekeeper, 'denies'], $arguments);
    }

    /**
     * Determine if the given ability should be granted.
     *
     * @param  string $ability
     * @param  array|mixed $arguments
     * @return bool
     */
    public function check($ability, $arguments = [])
    {
        if (!is_array($arguments)) {
            $arguments = [ $arguments ];
        }

        array_unshift($arguments, $ability);
        return call_user_func_array([ $this->gatekeeper, 'check'], $arguments);
    }

    /**
     * Get a guard instance for the given user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|mixed $user
     * @return static
     */
    public function forUser($user)
    {
        // This we can do through some magic!
        $identity = new UserIdentity($user);
        return new self($this->gatekeeper->forIdentity($identity));
    }

    /**
     * Determine if the given ability should be granted for the current user.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return \Illuminate\Auth\Access\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorize($ability, $arguments = [])
    {
        $result = $this->allows($ability, $arguments);
        return $result ? $this->allow() : $this->deny();
    }
}