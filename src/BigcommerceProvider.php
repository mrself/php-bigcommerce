<?php declare(strict_types=1);

namespace Mrself\Bigcommerce;

use ICanBoogie\Inflector;
use Mrself\Bigcommerce\Client\Client;
use Mrself\Container\Registry\ContainerRegistry;

class BigcommerceProvider
{
    public function boot()
    {
        $container = BigcommerceContainer::make();
        ContainerRegistry::add('Mrself\\Bigcommerce', $container);
        $client = Client::make(['maxRetries' => 2]);
        $container->set(Client::class, $client);
        $container->set(Inflector::class, Inflector::get());
    }
}