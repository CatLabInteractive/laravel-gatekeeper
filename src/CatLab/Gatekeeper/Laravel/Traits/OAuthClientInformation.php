<?php

namespace CatLab\Gatekeeper\Laravel\Traits;

/**
 * Class OAuthClientIdentity
 * @package CatLab\Gatekeeper\Laravel\Models
 */
trait OAuthClientInformation
{
    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string[]
     */
    private $scopes;

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * @return string[]
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * @param string[] $scopes
     */
    public function setScopes($scopes)
    {
        $this->scopes = $scopes;
    }

    /**
     * @param string $scope
     * @return bool
     */
    public function hasScope($scope)
    {
        return array_search($scope, $this->scopes) !== false;
    }
}