<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Hooks\ResourceHooks;

class CategoryHooks extends AbstractHooks
{
    public function getResourceName(): string
    {
        return 'category';
    }
}