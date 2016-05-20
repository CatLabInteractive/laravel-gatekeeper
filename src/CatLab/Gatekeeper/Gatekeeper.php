<?php

namespace CatLab\Gatekeeper;

use CatLab\Gatekeeper\Contracts\Identity;
use CatLab\Gatekeeper\Exceptions\IdentityNotDefined;
use CatLab\Gatekeeper\Contracts\Policy;
use CatLab\Gatekeeper\Models\GlobalPolicy;
use CatLab\Gatekeeper\Models\ObjectPolicyResolver;
use InvalidArgumentException;
use stdClass;

/**
 * Class Gatekeeper
 * @package CatLab\Gatekeeper
 */
class Gatekeeper implements Contracts\Gatekeeper
{
    /**
     * @var stdClass|string
     */
    private $policies;

    /**
     * @var Contracts\Policy
     */
    private $loadedPolicies;

    /**
     * @var GlobalPolicy
     */
    private $globalPolicy;

    /**
     * @var Contracts\Identity
     */
    private $identity;

    /**
     * @var callable
     */
    private $identityCallback;

    /**
     * Gatekeeper constructor.
     */
    public function __construct()
    {
        $this->policies = [];
        $this->loadedPolicies = [];
        $this->globalPolicy = new GlobalPolicy();
    }


    /**
     * @param \CatLab\Gatekeeper\Contracts\Identity|callable $identity
     * @return $this
     */
    public function setIdentity($identity)
    {
        if ($identity instanceof Identity) {
            $this->identity = $identity;
        } elseif (is_callable($identity)) {
            $this->identityCallback = $identity;
        } else {
            throw new InvalidArgumentException(
                '$identity must be either ' . Identity::class . ' or a callback that returns ' . Identity::class . '.'
            );
        }
    }

    /**
     * @param string $className
     * @param Policy|string $policy
     * @return \CatLab\Gatekeeper\Contracts\Gatekeeper
     */
    public function addPolicy($className, $policy)
    {
        $this->policies[$className] = $policy;
    }

    /**
     * @param string $ability
     * @return bool
     */
    public function allows($ability)
    {
        return call_user_func_array([ $this, 'check' ], func_get_args());
    }

    /**
     * @param string $ability
     * @return bool
     */
    public function denies($ability)
    {
        return ! call_user_func_array([ $this, 'check' ], func_get_args());
    }

    /**
     * @param string $ability
     * @param callable $callback
     * @return \CatLab\Gatekeeper\Contracts\Gatekeeper
     */
    public function define($ability, callable $callback)
    {
        $this->globalPolicy->define($ability, $callback);
        return $this;
    }

    /**
     * @param string
     * @return bool
     * @throws IdentityNotDefined
     */
    public function check($ability)
    {
        $identity = $this->getIdentity();

        $arguments = func_get_args();

        $ability = array_shift($arguments);

        /** @var Contracts\Policy[] $policies */
        $policies = array_merge(
            [ $this->globalPolicy ],
            $this->resolvePolicies($ability)
        );

        $result = false;
        foreach ($policies as $policy) {
            $policyReply = $policy->allows($identity, $ability, $arguments);

            if ($policyReply === null) {
                // This policy doesn't care about this action
                continue;
            } elseif ($policyReply == true) {
                $result = true;
            } else {
                return false;
            }
        }

        return $result;
    }

    /**
     * @param $ability
     * @return bool
     */
    public function has($ability)
    {
        $this->globalPolicy->has($ability);
    }

    /**
     * @param Identity $identity
     * @return Gatekeeper
     */
    public function forIdentity(Identity $identity)
    {
        $gatekeeper = clone $this;
        $gatekeeper->setIdentity($identity);
        return $gatekeeper;
    }

    /**
     * @throws IdentityNotDefined
     * @return Identity
     */
    public function getIdentity()
    {
        if (!isset($this->identity) && isset($this->identityCallback)) {
            $identity = call_user_func($this->identityCallback);
            if ($identity instanceof $identity) {
                $this->identity = $identity;
            } else {
                throw new InvalidArgumentException('Identity callback must return ' . Identity::class . '.');
            }
        }

        if (!isset($this->identity)) {
            throw new IdentityNotDefined('You must define an identity before using Gatekeeper');
        }

        return $this->identity;
    }

    /**
     * @param string $ability
     * @return Contracts\Policy
     */
    private function resolvePolicies($ability)
    {
        $parts = explode('@', $ability);

        if (count($parts) < 2) {
            return [];
        }

        $className = array_shift($parts);
        $method = array_shift($parts);

        $applying = [];
        foreach ($this->policies as $policyName => $policy) {
            if ($className === $policyName) {
                $applying[] = $this->loadPolicy($policyName);
            }
        }

        return $applying;
    }

    /**
     * @param $policyKey
     * @return Contracts\Policy
     */
    private function loadPolicy($policyKey)
    {
        if (!isset($this->loadedPolicies[$policyKey])) {
            $policy = $this->policies[$policyKey];

            if ($policy instanceof Contracts\Policy) {
                $this->loadedPolicies[$policyKey] = $policy;
            } elseif ($policy instanceof stdClass) {
                $this->loadedPolicies[$policyKey] = new ObjectPolicyResolver($policyKey, $policy);
            } else {
                // First try to create the object
                $policy = new $policy();
                $this->loadedPolicies[$policyKey] = new ObjectPolicyResolver($policyKey, $policy);
            }
        }

        return $this->loadedPolicies[$policyKey];
    }
}