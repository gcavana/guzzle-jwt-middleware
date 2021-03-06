<?php

namespace Eljam\GuzzleJwt;

use Eljam\GuzzleJwt\Manager\JwtManager;
use Psr\Http\Message\RequestInterface;

/**
 * @author Guillaume Cavana <guillaume.cavana@gmail.com>
 */
class JwtMiddleware
{
    const AUTH_BEARER = 'Bearer %s';

    /**
     * $JwtManager.
     *
     * @var JwtManager
     */
    protected $jwtManager;

    /**
     * Constructor.
     *
     * @param JwtManager $jwtManager
     */
    public function __construct(JwtManager $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    /**
     * Called when the middleware is handled by the client.
     *
     * @param callable $handler
     *
     * @return callable
     */
    public function __invoke(callable $handler)
    {
        $token = $this->jwtManager->getJwtToken()->getToken();

        return function (
            RequestInterface $request,
            array $options
        ) use (
            $handler,
            $token
        ) {
            return $handler($request->withHeader(
                'Authorization',
                sprintf(self::AUTH_BEARER, $token)
            ), $options);
        };
    }
}
