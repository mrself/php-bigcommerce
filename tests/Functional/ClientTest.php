<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Tests\Functional;

use Bigcommerce\Api\ClientError;
use Bigcommerce\Api\Resources\Product;
use Bigcommerce\Api\ServerError;
use Mrself\Bigcommerce\Client\Client;
use Mrself\Bigcommerce\Exception\ClientException;
use Mrself\Bigcommerce\Exception\NotFoundException;
use Mrself\Bigcommerce\Exception\RetriesExceededException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class ClientTest extends TestCase
{
    use ConnectionTrait;

    /**
     * @var Client
     */
    protected $client;

    public function testItCanGetResource()
    {
        $data = [
            'get' => [
                ['/products/1', (object) [
                    'id' => 1,
                    'name' => 'name'
                ]]
            ]
        ];
        $this->mockBc($data);
        $product = $this->client->getProduct('/products/1');
        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals(1, $product->id);
        $this->assertEquals('name', $product->name);
    }

    /**
     * @dataProvider getClientErrors
     */
    public function testItRetriesOnErrors($error)
    {
        $data = [
            'get' => [
                ['/products/1', $error],
                ['/products/1', (object) [
                    'id' => 1,
                    'name' => 'name'
                ]]
            ]
        ];
        $this->mockBc($data);
        $product = $this->client->getProduct('/products/1');
        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals(1, $product->id);
        $this->assertEquals('name', $product->name);
    }

	public function getClientErrors() {
		return [
		    [new ClientError('', Response::HTTP_TOO_MANY_REQUESTS)],
            [new ClientError('', CURLE_OPERATION_TIMEOUTED)],
            [new ServerError('', Response::HTTP_INTERNAL_SERVER_ERROR)],
            [new ServerError('', CURLE_SSL_CONNECT_ERROR)],
		];
    }

    public function testRetriesExceededExceptionHasNecessaryProperties()
    {
        $this->makeClient(['maxRetries' => 1]);
        $data = [
            'get' => [
                ['/products/1', new ClientError('', Response::HTTP_TOO_MANY_REQUESTS)],
            ]
        ];
        $this->mockBc($data);
        try {
            $this->client->getProduct('/products/1');
        } catch (RetriesExceededException $e) {
            $this->_assertException($e, 'getProduct', ['/products/1']);
            return;
        }
        $this->assertTrue(false);
    }

    public function testItThrowsNotFoundIfResourceIsNotFound()
    {
        $data = [
            'get' => [
                ['/products/1', new ClientError('', 404)]
            ]
        ];
        $this->mockBc($data);
        try {
            $this->client->getProduct('/products/1');
        } catch (NotFoundException $e) {
            $this->_assertException($e, 'getProduct', ['/products/1']);
            return;
        }
        $this->assertTrue(false);
    }

    public function testItThrowsClientErrorOnUnknownError()
    {
        $data = [
            'get' => [
                ['/products/1', new ClientError('', 0)]
            ]
        ];
        $this->mockBc($data);
        try {
            $this->client->getProduct('/products/1');
        } catch (ClientException $e) {
            $this->_assertException($e, 'getProduct', ['/products/1']);
            return;
        }
        $this->assertTrue(false);
    }

    public function testItCanGetCollection()
    {
        $data = [
            'get' => [
                ['/products', [
                    (object) [
                        'id' => 1,
                        'name' => 'name'
                    ],
                    (object) [
                        'id' => 2,
                        'name' => 'name2'
                    ]
                ]]
            ]
        ];
        $this->mockBc($data);
        $products = $this->client->getProducts();
        $this->assertInstanceOf(Product::class, $products[0]);
        $this->assertEquals(1, $products[0]->id);
        $this->assertEquals('name', $products[0]->name);
    }

    public function testUseV3GetsStoreHashFromPropertyByDefault()
    {
        $this->makeClient(['storeHash' => 'hash']);
        $this->client->useV3();
        $this->assertTrue(true);
    }

    /**
     * @expectedException  \Mrself\Bigcommerce\Exception\EmptyStoreHashException
     */
    public function testUseV3ThrowsExceptionIfStoreHashOptionAndArgumentAreEmpty()
    {
        $this->client->useV3();
        $this->assertTrue(true);
    }

    public function setUp()
    {
        $this->makeClient(['maxRetries' => 2]);
    }

    protected function makeClient(array $options = [])
    {
        $defaults = ['maxRetries' => 2];
        $options = array_merge($defaults, $options);
        $this->client = Client::make($options);
    }

    protected function _assertException(\Exception $e, string $method, array $args)
    {
        $this->assertEquals($method, $e->getMethod());
        $this->assertEquals($args, $e->getArgs());
    }
}