<?php declare(strict_types=1);

namespace Mrself\Bigcommerce;

use Mrself\BigcommerceHelper\Options\WithOptions;
use Bigcommerce\Api\Client as Base;
use Bigcommerce\Api\ClientError;
use Bigcommerce\Api\NetworkError;
use Bigcommerce\Api\ServerError;
use Symfony\Component\HttpFoundation\Response;

class Client
{
    use WithOptions;

    /**
     * How many times a client should retry request
     * after a failure before throwing an exception
     * @var int
     */
    protected $maxRetries;

    public function getOptionsSchema()
    {
        return [
            'required' => ['maxRetries'],
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
     * @throws MaxRetriesException
     * @throws ClientException
     * @throws NotFoundException
     * @throws NetworkError
     */
    public function exec(string $method, array $args, $retries = 0)
    {
        $methodArgs = func_get_args();
        $methodArgs[2] = @$methodArgs[2] ?: 0;
        if ($retries === $this->maxRetries) {
            throw new MaxRetriesException($args[0], array_slice($args, 1));
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
     * @throws ClientException
     * @throws NotFoundException
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
}