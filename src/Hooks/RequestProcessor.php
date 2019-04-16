<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Hooks;

use Mrself\Options\Annotation\Option;
use Mrself\Options\WithOptionsTrait;
use Psr\Log\LoggerInterface;

class RequestProcessor
{
    use WithOptionsTrait;

    /**
     * @Option()
     * @var string
     */
    protected $requestContent;

    /**
     * @Option()
     * @var string
     */
    protected $bigcommerceHash;

    /**
     * @Option()
     * @var bool
     */
    protected $ifLog;

    /**
     * @Option()
     * @var LoggerInterface
     */
    protected $logger;

    protected function getOptionsSchema()
    {
        return [
            'defaults' => [
                'ifLog' => false
            ]
        ];
    }

    public function process()
    {
        if ($this->ifLog) {
            $this->logger->debug('Bigcommerce hook content', [
                'content' => $this->requestContent
            ]);
        }

        $data = json_decode($this->requestContent);
        if (null === $data) {
            return false;
        }

        if ($this->ifLog) {
            $this->logger->debug('Bigcommerce hook producer', [
                'producer' => $data->producer
            ]);
        }

        $storeHash = $this->retrieveStoreHash($data->producer);
        if ($this->ifLog) {
            $this->logger->debug('Bigcommerce hook store hash', [
                'hash' => $storeHash
            ]);
        }
        if (!$storeHash || $storeHash !== $this->bigcommerceHash) {
            return false;
        }

        if ($this->ifLog) {
            $this->logger->debug('Bigcommerce hook data', [
                'data' => $data->data
            ]);
        }

        if ($this->ifLog) {
            $this->logger->debug('Bigcommerce hook scope', [
                'data' => $data->scope
            ]);
        }

        return $data;
    }

    private function retrieveStoreHash($producer): ?string
    {
        if (!is_string($producer)) {
            return null;
        }

        $parts = explode('/', $producer);
        if (count($parts) === 2) {
            return $parts[1];
        }
        return null;
    }
}