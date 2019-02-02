<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Resource;

use Mrself\Bigcommerce\Exception\BigcommereException;

class InvalidUrlParamsException extends BigcommereException
{
    /**
     * @var array
     */
    protected $urlParams;

    /**
     * @var array
     */
    protected $resourceNamespace;

    public function __construct(array $resourceNamespace, array $urlParams)
    {
        $this->resourceNamespace = $resourceNamespace;
        $this->urlParams = $urlParams;
        $urlParams = json_encode($urlParams);
        $resourceNamespace = json_encode($resourceNamespace);

        parent::__construct("Provided url params '$urlParams' does not fit resource namespace '$resourceNamespace'");
    }

    /**
     * @return array
     */
    public function getUrlParams(): array
    {
        return $this->urlParams;
    }

    /**
     * @return array
     */
    public function getResourceNamespace(): array
    {
        return $this->resourceNamespace;
    }
}