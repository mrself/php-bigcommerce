<?php declare(strict_types=1);

namespace Mrself\Bigcommerce;

class NotFoundException extends BigcommereException
{
    public function __construct(array $args)
    {
        parent::__construct('Resource not found by method "' . $args[0] . '" with arguments ' . json_encode(array_slice($args, 1)));
    }
}