<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Tests\Functional\Resource;

use Mrself\Bigcommerce\Resource\AbstractResource;
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
        $args = array_fill(0, count($resource->getName()), 1);
        $entity = call_user_func_array([$resource, 'get'], $args);
        $this->assertInstanceOf($resource->getBigcommerceResource(true), $entity);
        $this->assertEquals(1, $entity->id);
        $this->assertEquals('name', $entity->name);
    }
//
//    /**
//     * @param string $class Resource class
//     * @expectedException \App\BigCommerce\Resource\NotFoundException
//     * @dataProvider getResources
//     */
//    public function testGetThrowsNotFound(string $class)
//    {
//        $resource = static::$container->get($class);
//        $resource->resolveOptions();
//        $this->mockBc([
//            'get' => [
//                [$this->makeUrl($resource), new ClientError('', 404)]
//            ]
//        ]);
//        $args = [];
//        foreach (array_slice($resource->getName(), 0, -1) as $i) {
//            $params[$i] = 1;
//            $args[] = $params;
//        }
//        array_unshift($args, 1);
//        call_user_func_array([$resource, 'get'], $args);
//    }
//
//    /**
//     * @param string $class Resource class
//     * @dataProvider getResources
//     */
//    public function testFind(string $class)
//    {
//        $resource = static::$container->get($class);
//        $resource->resolveOptions();
//        $this->mockBc([
//            'get' => [
//                [$this->makeUrl($resource), (object) [
//                    'id' => 1,
//                    'name' => 'name'
//                ]]
//            ]
//        ]);
//        $args = [];
//        foreach (array_slice($resource->getName(), 0, -1) as $i) {
//            $params[$i] = 1;
//            $args[] = $params;
//        }
//        array_unshift($args, 1);
//        $entity = call_user_func_array([$resource, 'find'], $args);
//        $bcResourceClass = 'Bigcommerce\\Api\\Resources\\' . $resource->getBigCommerceResource();
//        $this->assertInstanceOf($bcResourceClass, $entity);
//        $this->assertEquals(1, $entity->id);
//        $this->assertEquals('name', $entity->name);
//    }
//
//    /**
//     * @param string $class Resource class
//     * @dataProvider getResources
//     */
//    public function testFindReturnsNullIfNotFound(string $class)
//    {
//        $resource = static::$container->get($class);
//        $resource->resolveOptions();
//        $this->mockBc([
//            'get' => [
//                [$this->makeUrl($resource), new ClientError('', 404)]
//            ]
//        ]);
//        $args = [];
//        foreach (array_slice($resource->getName(), 0, -1) as $i) {
//            $params[$i] = 1;
//            $args[] = $params;
//        }
//        array_unshift($args, 1);
//        $entity = call_user_func_array([$resource, 'find'], $args);
//        $this->assertNull($entity);
//    }
//
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
        $name = $this->resource->getName();
        if ($isCollection) {
            $name = array_slice($name, 0, -1);
        }
        $urlParams = [];
        foreach ($name as $i) {
            $urlParams[$i] = 1;
        }
        return $urlParams;
    }

    protected function setUp()
    {
        parent::setUp();
        $this->traitSetup();
        $this->defineResource();
    }

    abstract protected function defineResource();
}