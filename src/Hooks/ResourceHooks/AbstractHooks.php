<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Hooks\ResourceHooks;

use Mrself\Bigcommerce\Client\Client;
use Mrself\Bigcommerce\Hooks\HooksList;
use Mrself\Options\Annotation\Option;
use Mrself\Options\WithOptionsTrait;

abstract class AbstractHooks
{
    use WithOptionsTrait;

    /**
     * @Option()
     * @var Client
     */
    protected $client;

    /**
     * @Option()
     * @var string
     */
    protected $authHeader;

    /**
     * @var string
     */
    protected $resourceName;

    public function createAll(string $destUrl)
    {
        $this->create($destUrl, '*');
    }

    public function createBaseHooks(string $destUrl)
    {
        $this->createCreatedHook($destUrl);
        $this->createUpdatedHook($destUrl);
        $this->createDeletedHook($destUrl);
    }

    public function createCreatedHook(string $destUrl)
    {
        $this->create($destUrl, 'created');
    }

    public function createUpdatedHook(string $destUrl)
    {
        $this->create($destUrl, 'updated');
    }

    public function createDeletedHook(string $destUrl)
    {
        $this->create($destUrl, 'deleted');
    }

    protected function create(string $destUrl, string $scope)
    {
        $this->client->createWebhook([
            'scope' => 'store/'. $this->resourceName . '/' . $scope,
            'destination' => $destUrl,
            'active' => true,
            'headers' => [
                'X-Custom-Auth-Header' => $this->authHeader
            ]
        ]);
    }

    public function list(): array
    {
        $hooks = $this->client->listWebhooks();
        return HooksList::make(['hooks' => $hooks])
            ->byScope($this->resourceName);
    }

    public function delete()
    {
        $hooks = $this->client->listWebhooks();
        $hooks = HooksList::make(['hooks' => $hooks])
            ->byScope($this->resourceName);
        foreach ($hooks as $hook) {
            $this->client->deleteWebhook($hook['id']);
        }
    }

    protected function onOptionsResolve()
    {
        $this->resourceName = $this->getResourceName();
    }

    abstract public function getResourceName(): string;
}