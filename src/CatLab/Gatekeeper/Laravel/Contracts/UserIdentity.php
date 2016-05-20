<?php

namespace CatLab\Gatekeeper\Laravel\Contracts;

use Illuminate\Foundation\Auth\User;

interface UserIdentity
{
    /**
     * @return User
     */
    public function getUser();
}