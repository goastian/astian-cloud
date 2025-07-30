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
 */

use OCP\IConfig;
use Psr\Log\LoggerInterface; 
use OC\Oauth2PassporServer\Client;
use OCP\Http\Client\IClientService;
use OC\Oauth2PassporServer\Passport;

class PassportService
{
    private Passport $passport;

    public function __construct(
        IClientService $clientService,
        private LoggerInterface $logger,
        IConfig $config
    ) {
        $client = new Client($clientService, $logger, $config);
        $this->passport = new Passport($client, $config);
    }

    public function get(): Passport
    {
        return $this->passport;
    }
}
