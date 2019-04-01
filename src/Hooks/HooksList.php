<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Hooks;

use Mrself\Options\Annotation\Option;
use Mrself\Options\WithOptionsTrait;

class HooksList
{
    use WithOptionsTrait;

    /**
     * @Option
     * @var array
     */
    protected $hooks;

    public function toArray(array $hooks = null): array
    {
        if (null === $hooks) {
            $hooks = $this->hooks;
        }
        return array_map(function ($hook) {
            return [
                'id' => $hook->id,
                'scope' => $hook->scope,
                'storeHash' => $hook->store_hash,
                'destination' => $hook->destination
            ];
        }, $hooks);
    }

    public function byScope(string $scope): array
    {
        $hooks = array_filter($this->hooks, function ($hook) use ($scope) {
            return strpos($hook->scope, 'store/' . $scope) === 0;
        });
        return $this->toArray($hooks);
    }
}