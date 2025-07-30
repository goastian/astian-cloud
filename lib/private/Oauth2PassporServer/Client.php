<?php

namespace OC\Oauth2PassporServer;

/**
 * Handle remote OAuth2 passport server integration.
 *
 * This class uses a custom client to communicate with the passport server,
 * and provides methods to retrieve user info, scopes, and permission checks.
 *
 * @package OC\Oauth2PassporServer
 * @url https://github.com/elyerr/oauth2-passport-server
 * @author Elyerr Roman <yerel9212@yahoo.es>
 * 
 */

use OCP\IConfig;
use OCP\ILogger;
use Psr\Log\LoggerInterface;
use OCP\Http\Client\IClientService;

class Client
{
    /** @var \OCP\Http\Client\IClient */
    private $client;

    /** @var IConfig */
    private $config;

    /** @var string */
    private $accessToken;

    /** @var array */
    private $oauth2_passport_server;

    public function __construct(
        IClientService $clientService,
        private LoggerInterface $logger,
        IConfig $config
    ) {
        $this->client = $clientService->newClient();
        $this->config = $config;

        // Use a configurable option from oauth2_passport_server app or default to true
        $this->oauth2_passport_server = $this->config->getSystemValue('oauth2_passport_server', []);

        $this->loadAccessToken();
    }

    /**
     * Loads the access token from the current user session.
     */
    private function loadAccessToken(): void
    {
        $session = \OC::$server->getSession();
        $tokenRaw = $session->get("user_oidc" . '-user-token');
        ;
        if ($tokenRaw) {
            $data = json_decode($tokenRaw, true);
            $this->accessToken = $data['access_token'] ?? '';
        } else {
            $this->accessToken = '';
        }
    }

    /**
     * Builds common options for all requests, including headers and SSL settings.
     *
     * @param array $headers
     * @return array
     */
    private function buildOptions(array $headers = []): array
    {
        $options = [
            'headers' => array_merge([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Connection' => 'keep-alive',
            ], $headers)
        ];

        if ($this->oauth2_passport_server['httpclient.allowselfsigned']) {
            // Allow self-signed certificates (not recommended for production)
            $options['verify'] = false;
        }

        return $options;
    }

    /**
     * Sends a GET request using the bearer token.
     * @param string $url
     * @param array $headers
     * @throws \Exception
     * @return \OCP\Http\Client\IResponse
     */
    public function get(string $url, array $headers = [])
    {
        try {
            $response = $this->client->get($url, $this->buildOptions($headers));
            return $response;
        } catch (\Throwable $e) {
            $this->logger->error('HTTP Client GET failed: ' . $e->getMessage(), ['app' => 'oauth2-passport']);
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }
    /**
     * Sends a POST request using the bearer token.
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return \OCP\Http\Client\IResponse|null
     */
    public function post(string $url, array $data = [], array $headers = [])
    {
        try {
            $options = $this->buildOptions(array_merge([
                'Content-Type' => 'application/json',
            ], $headers));

            $options['body'] = json_encode($data);

            $response = $this->client->post($url, $options);

            return $response;
        } catch (\Throwable $e) {
            $this->logger->error('HTTP Client POST failed: ' . $e->getMessage(), ['app' => 'oauth2-passport']);
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }
}
