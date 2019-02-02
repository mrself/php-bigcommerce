<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Resource\Order;

use Mrself\Bigcommerce\Resource\AbstractResource;

class ShippingAddressResource extends AbstractResource
{
    protected $name = ['order', 'shipping_address'];

    protected $bigcommerceResource = 'Address';
}