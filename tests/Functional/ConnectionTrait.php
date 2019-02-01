<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Tests\Functional;

use Bigcommerce\Api\Client;
use Bigcommerce\Api\Connection;

trait ConnectionTrait
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Connection
     */
    protected $bc;

    protected $mockIndex = 0;

    protected $getCount = 0;

    protected $postCount = 0;

    protected function mockBc(array $data = [])
    {
        if (!$this->bc) {
            Client::failOnError(true);
            $this->bc = $this->getMockBuilder(Connection::class)
                ->setMethods(['get', 'post'])
                ->getMock();
            Client::setConnection($this->bc);
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
                $innerMethod = 'mockBcMethod_' . $method;
                $this->$innerMethod($request);
            }
        }
        $this->bc->expects($this->exactly(count($requests)))
            ->method($method);
        if (!count($requests)) {
            $this->bc->expects($this->never())
                ->method($method);
        }
    }

    protected function mockBcMethod_get($request)
    {
        $index = @$request[2] ? $request[2] : $this->mockIndex++;
        $this->bc->expects($this->at($index))
            ->method('get')
            ->with($this->stringContains($request[0]))
            ->willReturn($request[1]);
    }

    protected function mockBcMethod_post($request)
    {
        $index = @$request[2] ? $request[2] : $this->mockIndex++;
        $this->bc->expects($this->at($index))
            ->method('post')
            ->with($this->stringContains($request[0]), $this->equalTo($request[1]));
    }

    protected function mockBcMethodWithError($method, $request)
    {
        $index = @$request[2] ? $request[2] : $this->mockIndex++;
        $this->bc->expects($this->at($index))
            ->method($method)
            ->with($this->stringContains($request[0]))
            ->willThrowException($request[1]);
    }

    protected function compareBcResources($expected, $actual)
    {
        foreach ($expected as $key => $item) {
            $this->assertEquals($item, $actual->$key);
        }
    }
}