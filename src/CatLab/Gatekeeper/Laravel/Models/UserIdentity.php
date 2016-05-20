<?php

namespace CatLab\Gatekeeper\Laravel\Models;

use CatLab\Gatekeeper\Contracts\Identity;
use Illuminate\Foundation\Auth\User;

/**
 * Class UserIdentity
 *
 * An identity for the default laravel user.
 *
 * @package CatLab\Gatekeeper\Laravel\Models
 */
class UserIdentity implements Identity, \CatLab\Gatekeeper\Laravel\Contracts\UserIdentity
{
    /**
     * @var User
     */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}