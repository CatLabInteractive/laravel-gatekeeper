<?php

namespace CatLab\Gatekeeper\Laravel\Models;

use CatLab\Gatekeeper\Contracts\Identity;
use CatLab\Gatekeeper\Laravel\Traits\OAuthClientInformation;

/**
 * Class OAuthClientIdentity
 * @package CatLab\Gatekeeper\Laravel\Models
 */
class OAuthClientIdentity implements Identity
{
    use OAuthClientInformation;

    /**
     * OAuthClientIdentity constructor.
     * @param string $clientId
     * @param string[] $scopes
     */
    public function __construct
    (
        $clientId,
        $scopes
    ) {
        $this->setClientId($clientId);
        $this->setScopes($scopes);
    }

}