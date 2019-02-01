<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Resource;

use Mrself\Bigcommerce\Client;
use Mrself\Bigcommerce\ClientException;
use Mrself\Bigcommerce\MaxRetriesException;
use Mrself\Bigcommerce\NotFoundException as ClientNotFoundException;
use App\Util\ArrayUtil;
use App\Util\Options\Optionable;
use Bigcommerce\Api\Filter;
use ICanBoogie\Inflector;

class AbstractResource implements ResourceInterface
{
    use Optionable;

    /**
     * Resource name. An array of names. Some kind of namespace.
     * Should be defined in a child class
     * @var string[]
     */
    protected $name;

    /**
     * BigCommerce resource class short name. A name must be a value from
     * \Bigcommerce\Api\Resources. Can not be defined
     * @var string
     */
    protected $bigCommerceResource;

    /**
     * @var Client
     */
    protected $client;

    /**
     * Limit for #findAll
     * @var int
     */
    protected $findAllLimit = 50;

    /**
     * @var Inflector
     */
    protected $inflector;

    public function __construct(Client $client)
    {
        $this->setOptions(['client' => $client]);
        $this->inflector = Inflector::get();
        $this->defineBigCommerceResource();
    }


    public function find($id, array $urlParams = [])
    {
        $urlParams[$this->getLastName()] = $id;
        $url = $this->makeUrl($urlParams);
        try {
            return $this->client
                ->exec('getResource', [$url, $this->bigCommerceResource]);
        } catch (ClientNotFoundException $e) {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function findAll(callable $cb = null, array $query = [])
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

    /**
     * @inheritdoc
     */
    public function get($id, array $urlParams = [])
    {
        $result = $this->find($id, $urlParams);
        if ($result) {
            return $result;
        }
        throw new NotFoundException($this->name, $id);
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
            ->exec('getCollection', [$url, $this->bigCommerceResource]);
    }

    public function getOptionsConfig(): array
    {
        return [
            'required' => ['client']
        ];
    }

    /**
     * @return string
     */
    public function getBigCommerceResource(): string
    {
        return $this->bigCommerceResource;
    }

    /**
     * @inheritdoc
     */
    public function makeUrl(array $urlParams = []): string
    {
        $result = '';
        foreach ($this->name as $index => $i) {
            $result .= '/' . $this->inflector->pluralize($i);
            if (array_key_exists($i, $urlParams)) {
                $result .= '/' . $urlParams[$i];
            }
        }
        return $result;
    }

    /**
     * @param int $limit
     */
    public function setFindAllLimit(int $limit)
    {
        $this->findAllLimit = $limit;
    }

    /**
     * @return array
     */
    public function getName(): array
    {
        return $this->name;
    }

    /**
     * Defines $bigCommerceResource property
     */
    protected function defineBigCommerceResource()
    {
        if (!$this->bigCommerceResource) {
            $this->bigCommerceResource = ucfirst($this->getLastName());
        }
    }

    /**
     * Returns the last element from $name property
     * @return string
     */
    protected function getLastName(): string
    {
        return $this->name[count($this->name) - 1];
    }
}