<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Tests\Functional\Hooks;

use Mrself\Bigcommerce\Hooks\ResourceHooks\ProductHooks;

class ProductHooksTest extends ResourceHooksTest
{
    protected $hooksClass = ProductHooks::class;

    protected $resourceName = 'product';
}