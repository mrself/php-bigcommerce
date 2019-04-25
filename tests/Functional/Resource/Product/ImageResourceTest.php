<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Tests\Functional\Resource\Product;

use Mrself\Bigcommerce\Resource\Product\ImageResource;
use Mrself\Bigcommerce\Tests\Functional\Resource\AbstractResourceTest;

class ImageResourceTest extends AbstractResourceTest
{
    protected function defineResource()
    {
        $this->resource = ImageResource::make();
    }
}