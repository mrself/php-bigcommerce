<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Dev;

use Bigcommerce\Api\Client;
use Bigcommerce\Api\Connection;
use PHPUnit\Framework\TestCase;

/**
 * @mixin TestCase
 */
trait MockTrait
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Connection
     */
    protected $bcConnection;

    protected $bcMockIndex = 0;

    protected $bcGetCount = 0;

    protected $bcPostCount = 0;

    protected function mockBc(array $data = [])
    {
        if (!$this->bcConnection) {
            Client::failOnError(true);
            $this->bcConnection = $this->getMockBuilder(Connection::class)
                ->setMethods(['get', 'post', 'put'])
                ->getMock();
            Client::setConnection($this->bcConnection);
        }
        foreach ($data as $method => $requests) {
            $this->mockBcMethod($method, $requests);
        }
    }

    protected function mockBcMethod($method, $requests)
    {
        foreach ($requests as $index => $request) {
            if ($request[1] instanceof \Exception) {
                $this->mockBcMethodWithError($method, $request);
            } else {
                $innerMethod = 'mockBc' . ucfirst($method);
                $this->$innerMethod($request);
            }
        }
        $this->bcConnection->expects($this->exactly(count($requests)))
            ->method($method);
        if (!count($requests)) {
            $this->bcConnection->expects($this->never())
                ->method($method);
        }
    }

    protected function mockBcGet($request)
    {
        $index = @$request[2] ? $request[2] : $this->bcMockIndex++;
        $this->bcConnection->expects($this->at($index))
            ->method('get')
            ->with($this->stringContains($request[0]))
            ->willReturn($request[1]);
    }

    protected function mockBcPost($request)
    {
        $index = @$request[2] ? $request[2] : $this->bcMockIndex++;
        $this->bcConnection->expects($this->at($index))
            ->method('post')
            ->with($this->stringContains($request[0]), $this->equalTo($request[1]));
    }

    protected function mockBcPut($request)
    {
        $index = @$request[2] ? $request[2] : $this->bcMockIndex++;
        $this->bcConnection->expects($this->at($index))
            ->method('put')
            ->with($this->stringContains($request[0]), $this->equalTo($request[1]));
    }

    protected function mockBcMethodWithError($method, $request)
    {
        $index = @$request[2] ? $request[2] : $this->bcMockIndex++;
        $this->bcConnection->expects($this->at($index))
            ->method($method)
            ->with($this->stringContains($request[0]))
            ->willThrowException($request[1]);
    }
}