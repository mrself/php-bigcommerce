<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Exception;

class EmptyStoreHashException extends BigcommereException
{
    public function __construct()
    {
        parent::__construct('Provided store hash is empty');
    }
}