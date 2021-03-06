<?php

declare(strict_types=1);

namespace Kreait\Firebase\Http\Auth;

use function GuzzleHttp\Psr7\build_query;
use function GuzzleHttp\Psr7\parse_query;
use Kreait\Firebase\Http\Auth;
use Kreait\Firebase\Util\JSON;
use Psr\Http\Message\RequestInterface;

final class CustomToken implements Auth
{
    /**
     * @var string
     */
    private $token;

    public function __construct(string $uid, array $claims = null)
    {
        $claims = \array_filter($claims ?? [], static function ($value) {
            return $value !== null;
        });

        $claims = ['uid' => $uid] + $claims;

        $this->token = JSON::encode($claims);
    }

    public function authenticateRequest(RequestInterface $request): RequestInterface
    {
        $uri = $request->getUri();

        $queryParams = ['auth_variable_override' => $this->token] + parse_query($uri->getQuery());
        $queryString = build_query($queryParams);

        $newUri = $uri->withQuery($queryString);

        return $request->withUri($newUri);
    }
}
