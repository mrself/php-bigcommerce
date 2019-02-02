<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Tests\Functional\Resource;

use Bigcommerce\Api\ClientError;
use Mrself\Bigcommerce\Resource\AbstractResource;
use Mrself\Bigcommerce\Resource\NotFoundException;
use Mrself\Bigcommerce\Tests\Functional\ConnectionTrait;
use Mrself\Bigcommerce\Tests\Functional\TestCaseTrait;
use PHPUnit\Framework\TestCase;

abstract class AbstractResourceTest extends TestCase
{
    use TestCaseTrait;
    use ConnectionTrait;

    /**
     * @var AbstractResource
     */
    protected $resource;

    public function testGet()
    {
        $resource = $this->resource;
        $this->mockBc([
            'get' => [
                [$this->makeUrl(), (object) [
                    'id' => 1,
                    'name' => 'name'
                ]]
            ]
        ]);
        $entity = call_user_func_array([$resource, 'get'], $this->getResourceArgs());
        $this->assertInstanceOf($resource->getBigcommerceResource(true), $entity);
        $this->assertEquals(1, $entity->id);
        $this->assertEquals('name', $entity->name);
    }

    public function testGetThrowsNotFound()
    {
        $resource = $this->resource;
        $this->mockBc([
            'get' => [
                [$this->makeUrl(), new ClientError('', 404)]
            ]
        ]);
        try {
            call_user_func_array([$resource, 'get'], $this->getResourceArgs());
        } catch (NotFoundException $e) {
            $this->assertEquals($resource->getNamespace()->toDotted(), $e->getResourceName());
            return;
        }
        $this->assertTrue(false);
    }

    public function testFind()
    {
        $resource = $this->resource;
        $this->mockBc([
            'get' => [
                [$this->makeUrl(), (object) [
                    'id' => 1,
                    'name' => 'name'
                ]]
            ]
        ]);
        $entity = call_user_func_array([$resource, 'find'], $this->getResourceArgs());
        $this->assertInstanceOf($resource->getBigcommerceResource(true), $entity);
        $this->assertEquals(1, $entity->id);
        $this->assertEquals('name', $entity->name);
    }

    public function testFindReturnsNullIfNotFound()
    {
        $resource = $this->resource;
        $this->mockBc([
            'get' => [
                [$this->makeUrl(), new ClientError('', 404)]
            ]
        ]);
        $entity = call_user_func_array([$resource, 'find'], $this->getResourceArgs());
        $this->assertNull($entity);
    }

//    /**
//     * @param string $class Resource class
//     * @dataProvider getResources
//     */
//    public function testFindAllCallsCallbackWithResultIfItIsProvided(string $class)
//    {
//        $resource = static::$container->get($class);
//        $resource->resolveOptions();
//        $this->mockBc([
//            'get' => [
//                [$this->makeUrl($resource, true) . '?limit=1&page=1', [
//                    (object) [
//                        'id' => 1,
//                        'name' => 'name'
//                    ]
//                ]],
//                [$this->makeUrl($resource, true) . '?limit=1&page=2', [
//                    (object) [
//                        'id' => 2,
//                        'name' => 'name2'
//                    ]
//                ]],
//                [$this->makeUrl($resource, true) . '?limit=1&page=3', null]
//            ]
//        ]);
//        $this->counter = 0;
//        $params = [
//            'limit' => 1,
//            'urlParams' => $this->makeUrlParams($resource, true)
//        ];
//        $resource->findAll(function ($result) {
//            $this->counter++;
//            $this->assertEquals(1, count($result));
//        }, $params);
//        $this->assertEquals(2, $this->counter);
//    }
//
//    /**
//     * @param string $class Resource class
//     * @dataProvider getResources
//     */
//    public function testFindAllReturnsAllIfNoCallbackIsProvided(string $class)
//    {
//        $resource = static::$container->get($class);
//        $resource->resolveOptions();
//        $resource->setFindAllLimit(10);
//        $this->mockBc([
//            'get' => [
//                [$this->makeUrl($resource, true) . '?limit=10&page=1', [
//                    (object) [
//                        'id' => 1,
//                        'name' => 'name'
//                    ],
//                    (object) [
//                        'id' => 2,
//                        'name' => 'name2'
//                    ]
//                ]]
//            ]
//        ]);
//        $params = [
//            'urlParams' => $this->makeUrlParams($resource, true)
//        ];
//        $entities = $resource->findAll(null, $params);
//        $this->assertEquals(2, count($entities));
//        $this->assertEquals(1, $entities[0]->id);
//        $this->assertEquals(2, $entities[1]->id);
//    }
//
//    /**
//     * @param string $class Resource class
//     * @dataProvider getResources
//     */
//    public function testFindAllReturnsEmptyArrayIfBCReturnsNullAndWithoutCallback(string $class)
//    {
//        $resource = static::$container->get($class);
//        $resource->resolveOptions();
//        $resource->setFindAllLimit(10);
//        $this->mockBc([
//            'get' => [
//                [$this->makeUrl($resource, true) . '?limit=10&page=1', null]
//            ]
//        ]);
//        $params = [
//            'urlParams' => $this->makeUrlParams($resource, true)
//        ];
//        $entities = $resource->findAll(null, $params);
//        $this->assertEquals(0, count($entities));
//    }
//
//    /**
//     * @param string $class Resource class
//     * @dataProvider getResources
//     */
//    public function testFindAllReturnsEmptyArrayIfBCReturnsNullAndWithCallback(string $class)
//    {
//        $resource = static::$container->get($class);
//        $resource->resolveOptions();
//        $resource->setFindAllLimit(10);
//        $this->mockBc([
//            'get' => [
//                [$this->makeUrl($resource, true) . '?limit=10&page=1', null]
//            ]
//        ]);
//        $params = [
//            'urlParams' => $this->makeUrlParams($resource, true)
//        ];
//        $entities = $resource->findAll(function () {
//        }, $params);
//        $this->assertTrue($entities);
//    }

    protected function makeUrl(bool $isCollection = false): string
    {
        return $this->resource->makeUrl($this->makeUrlParams($isCollection));
    }

    protected function makeUrlParams(bool $isCollection = false)
    {
        $name = $this->resource->getNamespace()->get();
        if ($isCollection) {
            $name = array_slice($name, 0, -1);
        }
        $urlParams = [];
        foreach ($name as $i) {
            $urlParams[$i] = 1;
        }
        return $urlParams;
    }

    /**
     * Returns array of arguments for resource#getUrl.
     * For example, for product resource it returns 1 (default resource id)
     * to be mocked for bigcommerce
     * @return array
     */
    protected function getResourceArgs()
    {
        $defaultResourceId = 1;
        $count = count($this->resource->getNamespace()->get());
        return array_fill(0, $count, $defaultResourceId);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->traitSetup();
        $this->defineResource();
    }

    abstract protected function defineResource();
}