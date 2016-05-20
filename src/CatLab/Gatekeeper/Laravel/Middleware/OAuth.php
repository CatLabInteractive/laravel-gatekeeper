<?php

namespace CatLab\Gatekeeper\Laravel\Middleware;

use CatLab\Gatekeeper\Laravel\Models\OAuthClientIdentity;
use CatLab\Gatekeeper\Laravel\Models\OAuthUserIdentity;
use Closure;
use Gatekeeper;

use App\Models\User;
use CatLab\Gatekeeper\Laravel\Models\UserIdentity;
use LucaDegasperi\OAuth2Server\Middleware\OAuthMiddleware;

/**
 * Class OAuth
 * @package App\Http\Middleware
 */
class OAuth extends OAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $scopesString
     *
     * @throws \League\OAuth2\Server\Exception\InvalidScopeException
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $scopesString = null)
    {
        $parent = $this;

        return parent::handle(
            $request,
            function($request) use ($parent, $next) {

                $scopes = [];
                foreach ($this->authorizer->getScopes() as $scope) {
                    $scopes[] = $scope->getId();
                }

                switch ($this->authorizer->getResourceOwnerType()) {
                    case 'user':
                        $user = User::find($this->authorizer->getResourceOwnerId());
                        Gatekeeper::setIdentity(
                            new OAuthUserIdentity(
                                $user,
                                $this->authorizer->getClientId(),
                                $scopes
                            )
                        );
                        break;

                    case 'client':
                        Gatekeeper::setIdentity(
                            new OAuthClientIdentity(
                                $this->authorizer->getClientId(),
                                $scopes
                            )
                        );
                        break;
                }

                return $next($request);

            },
            $scopesString
        );
    }
}