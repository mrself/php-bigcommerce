<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Resource;

use Mrself\Bigcommerce\Resource\Product\SkuResource;
use Mrself\Options\Annotation\Option;

class ProductResource extends AbstractResource
{
    /**
     * @Option()
     * @var SkuResource
     */
    protected $skuResource;

    protected $namespaceSource = ['product'];

    public function skus()
    {
//        return $this->skuResource->all($this->)
    }
}