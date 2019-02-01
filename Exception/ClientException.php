<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Exception;

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
        $this->args = $args[1];
        parent::__construct('An error occurred when executing method "' . $args[0] . '" with arguments ' . json_encode($this->args), 0, $prev);
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }
}