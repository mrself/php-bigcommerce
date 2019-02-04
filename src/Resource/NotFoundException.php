<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Resource;

use Mrself\Bigcommerce\Exception\BigcommereException;
use Mrself\NamespaceHelper\NamespaceHelper;

class NotFoundException extends BigcommereException
{
    /**
     * @var array
     */
    protected $resourceName;

    /**
     * @var int
     */
    protected $id;

    public function __construct(NamespaceHelper $resourceName, $id)
    {
        $this->resourceName = $resourceName->toDotted();
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