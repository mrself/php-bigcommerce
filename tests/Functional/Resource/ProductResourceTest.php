<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Tests\Functional\Resource;

use Mrself\Bigcommerce\Resource\ProductResource;

class ProductResourceTest extends AbstractResourceTest
{

    protected function defineResource()
    {
        $this->resource = ProductResource::make();
    }
}