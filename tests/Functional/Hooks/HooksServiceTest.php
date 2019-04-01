<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Tests\Functional\Hooks;

use Mrself\Bigcommerce\Client\Client;
use Mrself\Bigcommerce\Dev\BigcommerceTrait;
use Mrself\Bigcommerce\Hooks\HooksService;
use PHPUnit\Framework\TestCase;

class HooksServiceTest extends TestCase
{
    use BigcommerceTrait;

    public function testList()
    {
        $client = $this->getMockBuilder(Client::class)
            ->setMethods(['listWebhooks'])
            ->getMock();
        $service = HooksService::make(['client' => $client]);

        $hook = $this->getBcHook(['id' => 1]);
        $client->expects($this->once())
            ->method('listWebhooks')
            ->willReturn([
                $hook
            ]);
        $list = $service->list();
        $this->assertEquals(1, $list[0]['id']);
    }

    public function testDelete()
    {
        $client = $this->getMockBuilder(Client::class)
            ->setMethods(['listWebhooks', 'deleteWebhook'])
            ->getMock();
        $service = HooksService::make(['client' => $client]);
        $client->expects($this->once())
            ->method('listWebhooks')
            ->willReturn([
                $this->getBcHook(['id' => 1]),
                $this->getBcHook(['id' => 2])
            ]);
        $client->expects($this->exactly(2))
            ->method('deleteWebhook')
            ->withConsecutive([1], [2]);
        $service->delete();
    }
}