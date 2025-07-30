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
class Passport
{
    /** @var Client */
    private $client;

    /** @var string */
    private $baseUrl;

    /** @var array */
    private $passport_config;

    public function __construct(Client $client, IConfig $config)
    {
        $this->client = $client;

        // Get the master domain from config: oauth2_passport_server -> master
        $this->passport_config = $config->getSystemValue('oauth2_passport_server', []);

        $this->baseUrl = $this->passport_config['master'] ?? null;

        if (empty($this->baseUrl)) {
            throw new \RuntimeException('Missing oauth2_passport_server.master config value');
        }
    }

    /**
     * Admin scope on oauth2 passport server
     * @return string
     */
    public function adminScope()
    {
        return "administrator:admin:full";
    }

    /**
     * Returns the scopes granted to the current user.
     * @return array
     */
    public function scopes(): array
    {
        $endpoint = "/api/gateway/access";
        $url = $this->baseUrl . $endpoint;

        try {
            $response = $this->client->get($url);

            if (!$response) {
                return [];
            }

            $body = (string) $response->getBody();
            $data = json_decode($body, true);

            return is_array($data) ? $data : [];
        } catch (\Throwable $e) {
           
            return [];
        }
    }


    /**
     * Checks if the current user has a specific scope by making a remote call.
     *
     * @param string $scope The permission scope to check.
     * @return bool True if the user has the permission; false otherwise.
     */
    public function userCan(string $scope)
    {
        $endpoint = "/api/gateway/token-can";
        $url = $this->baseUrl . $endpoint;

        try {
            $response = $this->client->get($url, [
                'requestheaders' => [
                    'X-SCOPE' => $scope
                ]
            ]);

            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * Retrieves user info from the passport server.
     * @return array
     */
    public function user(): array
    {
        $endpoint = "/api/gateway/user";
        $url = $this->baseUrl . $endpoint;

        $response = $this->client->get($url);

        return $response ? json_decode($response->getBody(), true) : [];
    }
}
