<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Tests\Functional;

use ICanBoogie\Inflector;
use Mrself\Bigcommerce\Client\Client;
use Mrself\Bigcommerce\Resource\Product\SkuResource;
use Mrself\Container\Container;
use Mrself\Container\Registry\ContainerRegistry;

trait TestCaseTrait
{
    protected function traitSetup()
    {
        ContainerRegistry::reset();
        $container = new Container();
        ContainerRegistry::add('Mrself\\Bigcommerce', $container);

        $client = Client::make(['maxRetries' => 2]);
        $container->set(Client::class, $client);
        $container->set(Inflector::class, Inflector::get());
        $skuResource = SkuResource::make();
        $container->set(SkuResource::class, $skuResource);
    }
}