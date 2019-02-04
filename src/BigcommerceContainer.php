<?php declare(strict_types=1);

namespace Mrself\Bigcommerce;

class BigcommerceContainer extends \Mrself\Container\Container
{
    public function get(string $key, $default = false)
    {
        if (strpos($key, 'Mrself\\Bigcommerce\Resource\\') !== false) {
            return $key::make();
        }
        return parent::get($key, $default);
    }
}