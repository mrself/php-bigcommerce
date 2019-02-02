<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Tests\Functional\Resource\Product;

use Mrself\Bigcommerce\Resource\Product\SkuResource;
use Mrself\Bigcommerce\Tests\Functional\Resource\AbstractResourceTest;

class SkuResourceTest extends AbstractResourceTest
{
    protected function defineResource()
    {
        $this->resource = SkuResource::make();
    }
}