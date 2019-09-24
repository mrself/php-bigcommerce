<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Connection;

use Bigcommerce\Api\Client as Bigcommerce;
use Bigcommerce\Api\Error;
use Mrself\Options\Annotation\Option;
use Mrself\Options\WithOptionsTrait;

class ConnectionProvider
{
    use WithOptionsTrait;

    /**
     * @var mixed
     */
    protected $connection;

    /**
     * Environment
     * @Option()
     * @var string
     */
    protected $env = 'dev';

    /**
     * @Option()
     * @var int
     */
    private $timeout;

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

        $this->reset();
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
        $connection = $this->connection ?: new Connection($this->timeout);
        $connection->authenticateOauth($clientId, $apiToken);
        Bigcommerce::setConnection($connection);
        Bigcommerce::failOnError(true);
    }

    protected function reset()
    {
        $this->setup('', '', '');
    }
}