<?php

namespace WP_CLI_Dotenv\Dotenv;

/**
 * Class KeyValue
 * @package WP_CLI_Dotenv\Dotenv
 */
class KeyValue implements LineInterface
{
    /**
     * Var key
     * @var string
     */
    protected string $key;

    /**
     * Var value
     * @var string
     */
    protected string $value;

    /**
     * Quote character to wrap the value with
     * @var string
     */
    protected string $quote;

    /**
     * Single line format
     */
    const FORMAT = '{key}={quote}{value}{quote}';

    /**
     * KeyValue constructor.
     *
     * @param $key
     * @param $value
     * @param $quote
     */
    public function __construct($key, $value, $quote)
    {
        $this->key = $key;
        $this->value = $value;
        $this->quote = $quote;
    }

    /**
     * Get the key.
     *
     * @return string
     */
    public function key(): string
    {
        return $this->key;
    }

    /**
     * Get the value, unwrapped.
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Assemble the instance into its single-line string format.
     *
     * @return string
     */
    public function toString(): string
    {
        return str_replace([
                '{key}',
                '{value}',
                '{quote}'
            ],
            [
                $this->key,
                $this->value,
                $this->quote
            ],
            static::FORMAT
        );
    }
}
