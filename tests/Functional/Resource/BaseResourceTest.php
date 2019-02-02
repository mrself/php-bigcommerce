<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Tests\Functional\Resource;

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

    public function testMakeUrlWithArray()
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


    protected function setUp()
    {
        parent::setUp();
        $this->traitSetup();
    }
}