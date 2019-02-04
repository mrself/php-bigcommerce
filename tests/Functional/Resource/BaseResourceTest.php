<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Tests\Functional\Resource;

use Bigcommerce\Api\ClientError;
use Mrself\Bigcommerce\Resource\InvalidUrlParamsException;
use Mrself\Bigcommerce\Resource\NotFoundException;
use Mrself\Bigcommerce\Resource\Product\SkuResource;
use Mrself\Bigcommerce\Resource\ProductResource;
use Mrself\Bigcommerce\Tests\Functional\ConnectionTrait;
use Mrself\Bigcommerce\Tests\Functional\TestCaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BaseResourceTest extends KernelTestCase
{
    use ConnectionTrait;
    use TestCaseTrait;

    public function testMakeUrlArray()
    {
        $resource = ProductResource::make();
        $actual = $resource->makeUrl(['product' => 1]);
        $this->assertEquals('/products/1', $actual);
    }

    public function testMakeUrlArrayWithId()
    {
        $resource = ProductResource::make();
        $actual = $resource->makeUrl(['id' => 1]);
        $this->assertEquals('/products/1', $actual);
    }

    public function testMakeUrlWithArrayForMultipleResources()
    {
        $resource = SkuResource::make();
        $actual = $resource->makeUrl([
            'product' => 1,
            'sku' => 2
        ]);
        $this->assertEquals('/products/1/skus/2', $actual);
    }

    public function testMakeUrlWithId()
    {
        $resource = ProductResource::make();
        $actual = $resource->makeUrl(1);
        $this->assertEquals('/products/1', $actual);
    }

    public function testMakeUrlWith2Ids()
    {
        $resource = SkuResource::make();
        $actual = $resource->makeUrl(2, 1);
        $this->assertEquals('/products/1/skus/2', $actual);
    }

    public function testMakeCollectionUrlArray()
    {
        $resource = SkuResource::make();
        $actual = $resource->makeCollectionUrl(['product' => 1]);
        $this->assertEquals('/products/1/skus', $actual);
    }

    public function testMakeCollectionUrlWithId()
    {
        $resource = SkuResource::make();
        $actual = $resource->makeCollectionUrl(1);
        $this->assertEquals('/products/1/skus', $actual);
    }

    public function testMakeCollectionUrlThrowsExceptionIfExtraUrlParamWasGiven()
    {
        $resource = ProductResource::make();
        try {
            $resource->makeCollectionUrl(1, 2);
        } catch (InvalidUrlParamsException $e) {
            $this->assertEquals([], $e->getResourceNamespace());
            $this->assertEquals([1, 2], $e->getUrlParams());
            return;
        }
        $this->assertTrue(false);
    }

    public function testGetThrowsNotFoundWithResourceIdAsInt()
    {
        $resource = ProductResource::make();
        $this->mockBc([
            'get' => [
                ['/products/100', new ClientError('', 404)]
            ]
        ]);
        try {
            $resource->get('100');
        } catch (NotFoundException $e) {
            $this->assertEquals($resource->getNamespace()->toDotted(), $e->getResourceName());
            $this->assertEquals(100, $e->getId());
            return;
        }
        $this->assertTrue(false);
    }

    public function testGetThrowsNotFoundWithProperResourceId()
    {
        $resource = SkuResource::make();
        $this->mockBc([
            'get' => [
                ['/products/1/skus/2', new ClientError('', 404)]
            ]
        ]);
        try {
            $resource->get(2, 1);
        } catch (NotFoundException $e) {
            $this->assertEquals($resource->getNamespace()->toDotted(), $e->getResourceName());
            $this->assertEquals(2, $e->getId());
            return;
        }
        $this->assertTrue(false);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->traitSetup();
    }
}