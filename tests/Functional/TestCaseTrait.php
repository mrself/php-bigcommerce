<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Tests\Functional;

use ICanBoogie\Inflector;
use Mrself\Bigcommerce\Client\Client;
use Mrself\Container\Container;
use Mrself\Container\Registry\ContainerRegistry;

trait TestCaseTrait
{
    protected function traitSetup()
    {
        ContainerRegistry::reset();
        $container = new Container();
        $client = Client::make(['maxRetries' => 2]);
        $container->set(Client::class, $client);
        $container->set(Inflector::class, Inflector::get());
        ContainerRegistry::add('Mrself\\Bigcommerce', $container);
    }
}