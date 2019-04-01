<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Hooks;

use Mrself\Bigcommerce\Client\Client;
use Mrself\Options\Annotation\Option;
use Mrself\Options\WithOptionsTrait;

class HooksService
{
    use WithOptionsTrait;

    /**
     * @Option()
     * @var Client
     */
    protected $client;

    public function list()
    {
        $hooks = $this->client->listWebhooks();
        return HooksList::make(['hooks' => $hooks])
            ->toArray();
    }

    public function delete()
    {
        foreach ($this->client->listWebhooks() as $hook) {
            $this->client->deleteWebhook($hook->id);
        }
    }
}