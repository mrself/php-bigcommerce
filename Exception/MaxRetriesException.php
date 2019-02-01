<?php declare(strict_types=1);

namespace Mrself\Bigcommerce;

class MaxRetriesException extends BigcommereException
{
    /**
     * @var string
     */
    protected $method;

    /**
     * @var array
     */
    protected $args;

    public function __construct(string $method, array $args)
    {
        $this->method = $method;
        $this->args = $args;
        parent::__construct('Max retries exceeded when tried to execute method "' . $method . '" with args: ' . json_encode($args));
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getArgs(): array
    {
        return $this->args;
    }
}