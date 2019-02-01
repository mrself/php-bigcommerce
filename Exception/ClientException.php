<?php declare(strict_types=1);

namespace Mrself\Bigcommerce;

class ClientException extends BigcommereException
{
    /**
     * @var string
     */
    protected $method;

    /**
     * @var array
     */
    protected $args;

    public function __construct(array $args, \Throwable $prev)
    {
        $this->method = $args[0];
        $this->args = array_slice($args, 1);
        parent::__construct('An error occured when executing method "' . $args[0] . '" with arguments ' . json_encode($this->args), 0, $prev);
    }
}