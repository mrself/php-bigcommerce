<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Exception;

class NotFoundException extends BigcommereException
{
    /**
     * @var string
     */
    protected $method;

    /**
     * @var array
     */
    protected $args;

    public function __construct(array $args)
    {
        $this->method = $args[0];
        $this->args = $args[1];
        parent::__construct('Resource not found by method "' . $args[0] . '" with arguments ' . json_encode($args[1]));
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