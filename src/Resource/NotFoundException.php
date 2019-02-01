<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Resource;

use App\Exception\AbstractException;

class NotFoundException extends AbstractException
{
    /**
     * @var array
     */
    protected $resourceName;

    /**
     * @var int
     */
    protected $id;

    public function __construct(array $resourceName, int $id)
    {
        $this->resourceName = implode('.', $resourceName);
        $this->id = $id;
        parent::__construct('The resource "' . $this->resourceName . '" is not found by id: ' . $id);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getResourceName(): string
    {
        return $this->resourceName;
    }
}