<?php

namespace CatLab\Gatekeeper\Models;

use CatLab\Gatekeeper\Contracts\Identity;
use CatLab\Gatekeeper\Contracts\Policy;
use stdClass;

/**
 * Class PolicyResolver
 * @package CatLab\Gatekeeper\Models
 */
class ObjectPolicyResolver implements Policy
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var stdClass
     */
    private $object;

    /**
     * ObjectPolicyResolver constructor.
     * @param $name
     * @param stdClass $object
     */
    public function __construct($name, $object)
    {
        $this->name = $name;
        $this->object = $object;
    }

    /**
     * @param Identity $identity
     * @param string $ability
     * @param mixed []
     * @return bool|null
     */
    public function allows(Identity $identity, $ability, array $arguments)
    {
        $parts = explode('@', $ability);

        if (count($parts) < 2) {
            return [];
        }

        $className = array_shift($parts);
        $method = array_shift($parts);

        if (method_exists($this->object, $method)) {
            array_unshift($arguments, $identity);
            return call_user_func_array([ $this->object, $method ], $arguments);
        }

        return null;
    }

    /**
     * @param $ability
     * @return bool
     */
    public function has($ability)
    {
        $parts = explode('@', $ability);

        if (count($parts) < 2) {
            return [];
        }

        $className = array_shift($parts);
        $method = array_shift($parts);

        return method_exists($this->object, $method);
    }
}