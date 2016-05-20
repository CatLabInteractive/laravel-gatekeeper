<?php

namespace CatLab\Gatekeeper\Laravel\Models;

use CatLab\Gatekeeper\Laravel\Traits\OAuthClientInformation;
use Illuminate\Foundation\Auth\User;

/**
 * Class OAuthUserIdentity
 * @package CatLab\Gatekeeper\Laravel\Models
 */
class OAuthUserIdentity extends UserIdentity
{
    use OAuthClientInformation;
    
    public function __construct
    (
        User $user,
        $clientId,
        $scopes
    ) {
        parent::__construct($user);

        $this->setClientId($clientId);
        $this->setScopes($scopes);
    }
}