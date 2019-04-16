<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Tests\Functional\Hooks;

use Monolog\Logger;
use Mrself\Bigcommerce\Hooks\RequestProcessor;
use PHPUnit\Framework\TestCase;

class RequestProcessorTest extends TestCase
{
    public function testItParses()
    {
        $content = json_encode([
            'producer' => 'store/hash',
            'data' => [
                'scope' => 'store/product',
                'id' => 1
            ]
        ]);

        $data = RequestProcessor::make([
            'requestContent' => $content,
            'bigcommerceHash' => 'hash',
            'logger' => new Logger('name')
        ])->process();

        $this->assertEquals(1, $data->data->id);
    }

    public function testItReturnsFalseIfItCannotDecodeJsonContent()
    {
        $content = 'aa';

        $data = RequestProcessor::make([
            'requestContent' => $content,
            'bigcommerceHash' => 'hash',
            'logger' => new Logger('name')
        ])->process();
        $this->assertFalse($data);
    }

    public function testItReturnsFalseIfHashesDoNotMatch()
    {
        $content = json_encode([
            'producer' => 'store/hash1',
            'data' => [
                'scope' => 'store/product',
                'id' => 1
            ]
        ]);

        $data = RequestProcessor::make([
            'requestContent' => $content,
            'bigcommerceHash' => 'hash',
            'logger' => new Logger('name')
        ])->process();

        $this->assertFalse($data);
    }
}