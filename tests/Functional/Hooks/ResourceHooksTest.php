<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Tests\Functional\Hooks;

use Mrself\Bigcommerce\Client\Client;
use Mrself\Bigcommerce\Dev\BigcommerceTrait;
use Mrself\Bigcommerce\Hooks\ResourceHooks\AbstractHooks;
use PHPUnit\Framework\TestCase;

abstract class ResourceHooksTest extends TestCase
{
    use BigcommerceTrait;

    /**
     * @var AbstractHooks
     */
    protected $hooksClass;

    protected $resourceName;

    public function testCreate()
    {
        $client = $this->getMockBuilder(Client::class)
            ->setMethods(['createWebhook'])
            ->getMock();
        $client->expects($this->once())
            ->method('createWebhook')
            ->with([
                'scope' => 'store/' . $this->resourceName . '/created',
                'destination' => 'url',
                'active' => true,
                'headers' => [
                    'X-Custom-Auth-Header' => 'authHeader'
                ]
            ]);
        $this->hooksClass::make([
            'client' => $client,
            'authHeader' => 'authHeader',
        ])->createCreatedHook('url');
    }

    public function testList()
    {
        $client = $this->getMockBuilder(Client::class)
            ->setMethods(['listWebhooks'])
            ->getMock();
        $client->expects($this->once())
            ->method('listWebhooks')
            ->willReturn([
                $this->getBcHook(['id' => 1, 'scope' => 'store/' . $this->resourceName]),
                $this->getBcHook(['id' => 2])
            ]);

        $list = $this->hooksClass::make([
            'client' => $client,
            'authHeader' => 'authHeader',
        ])->list();

        $this->assertCount(1, $list);
        $this->assertEquals(1, $list[0]['id']);
    }

    public function testDelete()
    {
        $client = $this->getMockBuilder(Client::class)
            ->setMethods(['listWebhooks', 'deleteWebhook'])
            ->getMock();
        $client->expects($this->once())
            ->method('listWebhooks')
            ->willReturn([
                $this->getBcHook(['id' => 1, 'scope' => 'store/' . $this->resourceName]),
                $this->getBcHook(['id' => 2])
            ]);
        $client->expects($this->once())
            ->method('deleteWebhook')
            ->with(1);

        $this->hooksClass::make([
            'client' => $client,
            'authHeader' => 'authHeader',
        ])->delete();
    }
}