<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Resource;

use Mrself\Bigcommerce\Exception\ClientException;
use Mrself\Bigcommerce\Exception\RetriesExceededException;
use Mrself\Bigcommerce\Exception\NotFoundException as ClientNotFoundException;
use Mrself\NamespaceHelper\NamespaceHelper;

interface ResourceInterface
{
    /**
     * ```
     * // Find all with a callback
     * $resource->findAll(function(array $result) {
     *  echo $result[0]->id;
     * }, ['page' => 2]);
     *
     * ```
     *
     * @param callable|null $cb
     * @param array $params
     * @param array $urlParams
     * @return array|bool True if callback is provided, otherwise result array
     * @see query() for detailed $query param
     */
    public function batchAll(callable $cb, array $params = [], array $urlParams = []);

    /**
     * @see makeUrl()
     * @param array $params Params for url.
     * @return mixed Resource
     * @throws ClientException
     * @throws RetriesExceededException
     */
    public function find($params);

    /**
     * @see find()
     * @param array $params Params for url
     * @return mixed
     * @throws NotFoundException
     * @throws ClientException
     * @throws RetriesExceededException
     */
    public function get($params);

    /**
     * @param array $params {
     *      @type int 'limit' Limit how much a page will contain resourced
     *      @type int 'page' Page number
     * }
     * @return array|bool|object
     * @throws ClientException
     * @throws RetriesExceededException
     * @throws ClientNotFoundException
     */
    public function query(array $params = [], array $urlParams = []);

    /**
     * ```
     * // Suppose $name prop is ['product', 'sku']
     * // Outputting '/products/1/skus/2':
     * $inst->makeUrl(['product' => 1, 'sku' => 2]);
     * // or
     * $inst->makeUrl(2, 1); // 2 - sku is, 1 - product id
     * ```
     *
     * Makes an url for an api call
     * @param mixed $params Associative array where keys are values from $name prop.
     *  Can be id followed by ids of parent resources
     * @return string
     */
    public function makeUrl($params): string;

    /**
     * @return NamespaceHelper
     */
    public function getNamespace(): NamespaceHelper;
}