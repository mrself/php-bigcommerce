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
    public function all(callable $cb = null, array $query = [])
    {
        $query['limit'] = @$query['limit'] ?: $this->findAllLimit;
        $query['page'] = @$query['page'] ?: 1;
        $all = [];
        do {
            try {
                $result = $this->query($query);
                if ($cb) {
                    if (!$result) {
                        return true;
                    }
                    if ($cb($result) === false) {
                        break;
                    }
                } else {
                    if ($result) {
                        $all = array_merge($all, $result);
                    }
                }
            } catch (ClientNotFoundException $e) {
                break;
            }
            $query['page']++;
        } while ($result && count($result) === $query['limit']);
        if ($cb) {
            return true;
        } else {
            return $all;
        }
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
        if (is_int($params)) {
            $id = $params;
        } else {
            $id = $params[0];
        }
        throw new NotFoundException($this->getNamespace(), $id);
    }

    /**
     * @inheritdoc
     */
    public function query(array $filter = [])
    {
        $filter['page'] = @$filter['page'] ?: 1;
        $url = $this->makeUrl(ArrayUtil::pull($filter, 'urlParams', []));
        $url .= Filter::create($filter)->toQuery();
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
    public function makeUrlArray(array $urlParams = []): string
    {
        $result = '';
        if (array_key_exists('id', $urlParams)) {
            $urlParams[$this->getName()] = ArrayUtil::pull($urlParams,'id');
        }
        foreach ($this->getNamespace()->get() as $index => $i) {
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
            return $this->makeUrlArray($params);
        }
        $name = array_reverse($this->getNamespace()->get());
        $params = array_combine($name, func_get_args());
        return $this->makeUrlArray($params);
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