<?php

declare(strict_types=1);

namespace Kreait\Firebase\RemoteConfig;

use GuzzleHttp\ClientInterface;
use Kreait\Firebase\Exception\RemoteConfigApiExceptionConverter;
use Kreait\Firebase\Util\JSON;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * @internal
 */
class ApiClient
{
    /** @var ClientInterface */
    private $client;

    /** @var RemoteConfigApiExceptionConverter */
    private $errorHandler;

    /**
     * @internal
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
        $this->errorHandler = new RemoteConfigApiExceptionConverter();
    }

    public function getTemplate(): ResponseInterface
    {
        return $this->request('GET', 'remoteConfig');
    }

    public function validateTemplate(Template $template): ResponseInterface
    {
        return $this->request('PUT', 'remoteConfig', [
            'headers' => [
                'Content-Type' => 'application/json; UTF-8',
                'If-Match' => $template->etag(),
            ],
            'query' => [
                'validate_only' => 'true',
            ],
            'body' => JSON::encode($template),
        ]);
    }

    public function publishTemplate(Template $template): ResponseInterface
    {
        return $this->request('PUT', 'remoteConfig', [
            'headers' => [
                'Content-Type' => 'application/json; UTF-8',
                'If-Match' => $template->etag(),
            ],
            'body' => JSON::encode($template),
        ]);
    }

    /**
     * @see https://firebase.google.com/docs/reference/remote-config/rest/v1/projects.remoteConfig/listVersions
     */
    public function listVersions(FindVersions $query, string $nextPageToken = null): ResponseInterface
    {
        $uri = \rtrim((string) $this->client->getConfig('base_uri'), '/').':listVersions';

        $since = $query->since();
        $until = $query->until();
        $lastVersionNumber = $query->lastVersionNumber();
        $pageSize = $query->pageSize();

        $since = $since ? $since->format('Y-m-d\TH:i:s.v\Z') : null;
        $until = $until ? $until->format('Y-m-d\TH:i:s.v\Z') : null;
        $lastVersionNumber = $lastVersionNumber ? (string) $lastVersionNumber : null;
        $pageSize = $pageSize ? (string) $pageSize : null;

        return $this->request('GET', $uri, [
            'query' => \array_filter([
                'startTime' => $since,
                'endTime' => $until,
                'endVersionNumber' => $lastVersionNumber,
                'pageSize' => $pageSize,
                'pageToken' => $nextPageToken,
            ]),
        ]);
    }

    public function rollbackToVersion(VersionNumber $versionNumber): ResponseInterface
    {
        $uri = \rtrim((string) $this->client->getConfig('base_uri'), '/').':rollback';

        return $this->request('POST', $uri, [
            'json' => [
                'version_number' => (string) $versionNumber,
            ],
        ]);
    }

    private function request($method, $uri, array $options = null)
    {
        $options = $options ?? [];

        $options = \array_merge($options, [
            'decode_content' => 'gzip', // sets content-type and deflates response body
        ]);

        try {
            return $this->client->request($method, $uri, $options);
        } catch (Throwable $e) {
            throw $this->errorHandler->convertException($e);
        }
    }
}
