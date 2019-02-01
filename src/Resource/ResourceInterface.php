<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Resource;

use Mrself\Bigcommerce\ClientException;
use Mrself\Bigcommerce\MaxRetriesException;
use Mrself\Bigcommerce\NotFoundException as ClientNotFoundException;

interface ResourceInterface
{
    /**
     * ```
     * // Find all with a callback
     * $resource->findAll(function(array $result) {
     *  echo $result[0]->id;
     * }, ['page' => 2]);
     *
     * // Find all without a callback
     * $result = $resource->findAll(null, ['page' => 2]);
     * echo $result[0]->id;
     * ```
     *
     * @param callable|null $cb
     * @param array $query
     * @see query() for detailed $query param
     * @return array|bool True if callback is provided, otherwise result array
     * @throws ClientException
     * @throws MaxRetriesException
     */
    public function findAll(callable $cb = null, array $query = []);

    /**
     * ```php
     * // GET request for /products/1
     * $productResource->find(1);
     *
     * // GET request for /products/11/skus/1
     * $skuResource->find(1, ['product' => 11]);
     * ```
     *
     * @param int $id Resource id
     * @param array $urlParams Params for url. That can be id of resources.
     * @return mixed Resource
     * @throws ClientException
     * @throws MaxRetriesException
     */
    public function find($id, array $urlParams = []);

    /**
     * @see find()
     * @param int $id
     * @param array $urlParams Params for url
     * @return mixed
     * @throws NotFoundException
     * @throws ClientException
     * @throws MaxRetriesException
     */
    public function get($id, array $urlParams = []);

    /**
     * @param array $filter {
     *      @type int 'limit' Limit how much a page will contain resourced
     *      @type int 'page' Page number
     * }
     * @return array|bool|object
     * @throws ClientException
     * @throws MaxRetriesException
     * @throws ClientNotFoundException
     */
    public function query(array $filter = []);

    /**
     * ```
     * // Suppose $name prop is ['product', 'sku']
     * $inst->makeUrl(['product' => 1, 'sku' => 2]); // Outputs '/products/1/skus/2'
     * ```
     *
     * Makes an url for an api call
     * @param array $urlParams Associative array where keys are values from $name prop
     * @return string
     */
    public function makeUrl(array $urlParams = []): string;

    /**
     * @return array
     */
    public function getName(): array;
}