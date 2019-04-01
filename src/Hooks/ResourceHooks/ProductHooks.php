<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Hooks\ResourceHooks;

class ProductHooks extends AbstractHooks
{
    public function getResourceName(): string
    {
        return 'product';
    }
}