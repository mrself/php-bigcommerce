<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Connection;

use Bigcommerce\Api\Client as Bigcommerce;
use Bigcommerce\Api\Error;

class ConnectionProvider
{
    protected $connection;

    /**
     * Kernel environment
     * @var string
     */
    protected $env;

    public function __construct(string $env)
    {
        $this->env = $env;
    }

    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    public function validate(string $clientId, string $apiToken, string $storeHash)
    {
        $this->setup($clientId, $apiToken, $storeHash);
        try {
            Bigcommerce::getTime();
            $result = true;
        } catch (Error $error) {
            $result = false;
        }
        // Reset connection configuration
        $this->setup('', '', '');
        return $result;
    }

    public function setup(string $clientId, string $apiToken, string $storeHash)
    {
        if ($this->env === 'test') {
            return;
        }
        Bigcommerce::configure([
            'client_id' => $clientId,
            'auth_token' => $apiToken,
            'store_hash' => $storeHash
        ]);
        $connection = $this->connection ?: new Connection();
        $connection->authenticateOauth($clientId, $apiToken);
        Bigcommerce::setConnection($connection);
        Bigcommerce::failOnError(true);
    }
}