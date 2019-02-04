<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Tests\Functional;

use Mrself\Bigcommerce\BigcommerceProvider;
use Mrself\Container\Registry\ContainerRegistry;

trait TestCaseTrait
{
    protected function traitSetup()
    {
        ContainerRegistry::reset();
        (new BigcommerceProvider())->boot();
    }
}