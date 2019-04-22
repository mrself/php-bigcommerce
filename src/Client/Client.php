<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Client;

use Bigcommerce\Api\Client as Base;
use Bigcommerce\Api\Client as BigcommerceClient;
use Bigcommerce\Api\ClientError;
use Bigcommerce\Api\NetworkError;
use Bigcommerce\Api\ServerError;
use Mrself\Bigcommerce\Exception\ClientException;
use Mrself\Bigcommerce\Exception\EmptyStoreHashException;
use Mrself\Bigcommerce\Exception\RetriesExceededException;
use Mrself\Bigcommerce\Exception\NotFoundException;
use Mrself\Options\Annotation\Option;
use Mrself\Options\WithOptionsTrait;
use Symfony\Component\HttpFoundation\Response;

class Client
{
    use WithOptionsTrait;

    /**
     * How many times a client should retry request
     * after a failure before throwing an exception
	 * @Option()
     * @var int
     */
    protected $maxRetries;

    /**
     * @Option()
     * @var string
     */
    protected $storeHash = '';

    public const MAX_RESOURCES_LIMIT = 250;

    public function getOptionsSchema()
    {
        return [
            'allowedTypes' => [
                'maxRetries' => 'int'
            ]
        ];
    }

	/**
	 * @param string $method
	 * @param array $args
	 * @param int $retries
	 * @return array|bool|object
	 * @throws \Bigcommerce\Api\NetworkError
	 * @throws \Mrself\Bigcommerce\Exception\ClientException
	 * @throws \Mrself\Bigcommerce\Exception\RetriesExceededException
	 * @throws \Mrself\Bigcommerce\Exception\NotFoundException
	 */
    public function exec(string $method, array $args = [], $retries = 0)
    {
        $methodArgs = func_get_args();
        $methodArgs[2] = @$methodArgs[2] ?: 0;
        if ($retries === $this->maxRetries) {
            throw new RetriesExceededException($method, $args);
        }
        try {
            if (method_exists(Base::class, $method)) {
                return call_user_func_array([Base::class, $method], $args);
            }
            throw new \BadMethodCallException();
        } catch (ClientError $e) {
            return $this->handleClientError($e, $methodArgs);
        } catch (NetworkError $e) {
            if ($e->getCode() === CURLE_SSL_CONNECT_ERROR) {
                $methodArgs[2]++;
                return call_user_func_array([$this, 'exec'], $methodArgs);
            }
            throw $e;
        } catch (ServerError $e) {
            $methodArgs[2]++;
            return call_user_func_array([$this, 'exec'], $methodArgs);
        }
    }

	/**
	 * @param ClientError $e
	 * @param array $args
	 * @return mixed
	 * @throws \Mrself\Bigcommerce\Exception\ClientException
	 * @throws \Mrself\Bigcommerce\Exception\NotFoundException
	 */
    protected function handleClientError(ClientError $e, array $args)
    {
        if ($e->getCode() === Response::HTTP_TOO_MANY_REQUESTS) {
            $connection = Base::getConnection();
            $timeout = $connection->getHeader('X-Rate-Limit-Time-Reset-Ms');
            $timeout = (int) ceil($timeout / 60);
            sleep($timeout);
            $args[2]++;
            return call_user_func_array([$this, 'exec'], $args);
        } elseif ($e->getCode() === CURLE_OPERATION_TIMEOUTED) {
            $args[2]++;
            return call_user_func_array([$this, 'exec'], $args);
        } elseif ($e->getCode() === 404) {
            throw new NotFoundException($args);
        } else {
            throw new ClientException($args, $e);
        }
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return array|bool|object
     * @throws ClientException
     * @throws NetworkError
     * @throws NotFoundException
     * @throws RetriesExceededException
     */
    public function __call(string $method, array $arguments)
    {
        return $this->exec($method, $arguments);
    }

    /**
     * @param string|null $storeHash
     * @throws EmptyStoreHashException
     */
    public function useV3(string $storeHash = null)
    {
        $this->useVersion(3, $storeHash);
    }

    /**
     * @param string|null $storeHash
     * @throws EmptyStoreHashException
     */
    public function useV2(string $storeHash = null)
    {
        $this->useVersion(2, $storeHash);
    }

    /**
     * @param string|int $version
     * @param string|null $storeHash
     * @throws EmptyStoreHashException
     */
    protected function useVersion($version, string $storeHash = null)
    {
        if (is_null($storeHash)) {
            $storeHash = $this->storeHash;
        }
        if (!$storeHash) {
            throw new EmptyStoreHashException();
        }
        BigcommerceClient::$api_path = 'https://api.bigcommerce.com/stores/'
            . $storeHash . '/v' . $version;
    }
}