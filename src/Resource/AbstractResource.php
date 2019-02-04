<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Resource;

use Bigcommerce\Api\Filter;
use ICanBoogie\Inflector;
use Mrself\Bigcommerce\Client\Client;
use Mrself\NamespaceHelper\NamespaceHelper;
use Mrself\Options\Annotation\Option;
use Mrself\Options\WithOptionsTrait;
use Mrself\Bigcommerce\Exception\NotFoundException as ClientNotFoundException;
use Mrself\Util\ArrayUtil;

class AbstractResource implements ResourceInterface
{
    use WithOptionsTrait;

    /**
     * Resource name. An array of names. Some kind of namespace.
     * Should be defined in a child class
     * @var string[]
     */
    protected $namespaceSource;

    /**
     * BigCommerce resource class short name. A name must be a value from
     * \Bigcommerce\Api\Resources. Can not be defined
     * @var string
     */
    protected $bigcommerceResource;

    /**
     * @Option()
     * @var Client
     */
    protected $client;

    /**
     * Limit for #findAll
     * @var int
     */
    protected $findAllLimit = 50;

    /**
     * @Option()
     * @var Inflector
     */
    protected $inflector;

    /**
     * @var NamespaceHelper
     */
    protected $namespace;

    /**
     * @inheritdoc
     */
    public function batchAll(callable $cb, array $params = [], array $urlParams = [])
    {
        $params['limit'] = @$params['limit'] ?: $this->findAllLimit;
        $params['page'] = @$params['page'] ?: 1;
        do {
            try {
                $result = $this->query($params, $urlParams);
                if (!$result) {
                    return true;
                }
                if ($cb($result) === false) {
                    break;
                }
            } catch (ClientNotFoundException $e) {
                break;
            }
            $params['page']++;
        } while ($result && count($result) === $params['limit']);
        return true;
    }

    public function all($params = []): array
    {
        $args = func_get_args();
        $argumentsCount = count($args);
        if ($argumentsCount) {
            $last = $args[count($args) - 1];
        } else {
            $last = null;
        }

        if (is_array($last)) {
            $params = $last;
            $urlParams = array_slice($args, 0, -1);
        } else {
            $urlParams = $args;
            $params = [];
        }
        $all = [];
        $this->batchAll(function (array $result) use (&$all) {
            $all = array_merge($all, $result);
        }, $params, $urlParams);
        return $all;
    }

    public function find($params)
    {
        $url = call_user_func_array([$this, 'makeUrl'], func_get_args());
        try {
            return $this->client
                ->exec('getResource', [$url, $this->bigcommerceResource]);
        } catch (ClientNotFoundException $e) {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function get($params)
    {
        $result = call_user_func_array([$this, 'find'], func_get_args());
        if ($result) {
            return $result;
        }
        if (is_numeric($params)) {
            $id = $params;
        } else {
            $id = $params[0];
        }
        throw new NotFoundException($this->getNamespace(), $id);
    }

    /**
     * @inheritdoc
     */
    public function query(array $params = [], array $urlParams = [])
    {
        $url = $this->makeCollectionUrl($urlParams, true);
        $url .= Filter::create($params)->toQuery();
        return $this->client
            ->exec('getCollection', [$url, $this->bigcommerceResource]);
    }

    /**
     * @param bool $fullNamespace
     * @return string
     */
    public function getBigcommerceResource(bool $fullNamespace = false): string
    {
        if ($fullNamespace) {
            return '\\Bigcommerce\\Api\\Resources\\' . $this->bigcommerceResource;
        }
        return $this->bigcommerceResource;
    }

    /**
     * @inheritdoc
     */
    public function makeUrlArray(array $urlParams = [], bool $isCollection = false): string
    {
        $result = '';
        if (array_key_exists('id', $urlParams)) {
            $urlParams[$this->getName()] = ArrayUtil::pull($urlParams,'id');
        }
        $names = $this->getNamespace()->get();
        foreach ($names as $index => $i) {
            $result .= '/' . $this->inflector->pluralize($i);
            if (array_key_exists($i, $urlParams)) {
                $result .= '/' . $urlParams[$i];
            }
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function makeUrl($params): string
    {
        if (is_array($params)) {
            if (ArrayUtil::isAssoc($params)) {
                return $this->makeUrlArray($params);
            }
            return $this->makeUrlFromIds($params);
        }
        return $this->makeUrlFromIds(func_get_args());
    }

    public function makeCollectionUrl($params)
    {
        if (is_array($params)) {
            if (ArrayUtil::isAssoc($params, true)) {
                return $this->makeUrlArray($params, true);
            }
            return $this->makeUrlFromIds($params, true);
        }
        return $this->makeUrlFromIds(func_get_args(), true);
    }

    public function makeUrlFromIds(array $urlParams = [], bool $isCollection = false)
    {
        $names = array_reverse($this->getNamespace()->get());
        if ($isCollection) {
            $names = array_slice($names, 1);
        }
        if (count($names) !== count($urlParams)) {
            throw new InvalidUrlParamsException($names, $urlParams);
        }
        $params = array_combine($names, $urlParams);
        return $this->makeUrlArray($params, $isCollection);
    }

    /**
     * @param int $limit
     */
    public function setFindAllLimit(int $limit)
    {
        $this->findAllLimit = $limit;
    }

    /**
     * @inheritdoc
     */
    public function getNamespace(): NamespaceHelper
    {
        if ($this->namespace) {
            return $this->namespace;
        }
        $this->namespace = NamespaceHelper::from($this->namespaceSource);
        return $this->namespace;
    }

    public function getName(): string
    {
        return $this->getNamespace()->last();
    }

    protected function defineBigcommerceResource()
    {
        if (!$this->bigcommerceResource) {
            $this->bigcommerceResource = ucfirst($this->getNamespace()->last());
        }
    }

    protected function onOptionsResolve()
    {
        $this->defineBigcommerceResource();
    }
}