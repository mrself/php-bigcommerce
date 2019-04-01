<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Tests\Functional\Hooks;

use Mrself\Bigcommerce\Dev\ResourceTrait;
use Mrself\Bigcommerce\Hooks\HooksList;
use PHPUnit\Framework\TestCase;

class HooksListTest extends TestCase
{
    use ResourceTrait;

    public function testToArray()
    {
        $hook = $this->getBcHook();
        $array = HooksList::make(['hooks' => [$hook]])
            ->toArray();
        $this->assertCount(1, $array);
        $this->assertEquals($hook->id, $array[0]['id']);
        $this->assertEquals($hook->scope, $array[0]['scope']);
        $this->assertEquals($hook->store_hash, $array[0]['storeHash']);
        $this->assertEquals($hook->destination, $array[0]['destination']);
    }

    public function testByScope()
    {
        $hook = $this->getBcHook(['scope' => 'store/product', 'id' => 1]);
        $hook1 = $this->getBcHook(['scope' => 'store/category']);
        $array = HooksList::make(['hooks' => [$hook, $hook1]])
            ->byScope('product');
        $this->assertCount(1, $array);
        $this->assertEquals(1, $array[0]['id']);
    }
}