<?php

namespace WP_CLI_Dotenv\Dotenv;

class Line implements LineInterface
{
    /**
     * @var string
     */
    protected ?string $text;

    /**
     * Line constructor.
     *
     * @param string|null $text
     */
    public function __construct(string $text = null)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function key(): string
    {
        $pieces = explode('=', $this->text, 2);
        $pieces = array_map('trim', $pieces);

        return (string) reset($pieces);
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return static::clean_quotes($this->value_raw());
    }

    /**
     * Get the raw, uncleaned value.
     *
     * @return mixed|null|string
     */
    public function value_raw(): mixed
    {
        if (! strlen($this->text)) {
            return $this->text;
        }

        $pieces = explode('=', $this->text, 2);
        $pieces = array_map('trim', $pieces);

        return end($pieces);
    }

    /**
     * Get the quote that wraps the value.
     *
     * @return string
     */
    public function quote(): string
    {
        return static::wrapping_quote_for($this->value_raw());
    }

    /**
     * Whether the text is a key=value pair.
     *
     * @return bool
     */
    public function isPair(): bool
    {
        if (str_starts_with(trim($this->text), '#')) {
            return false;
        }

        $pieces = explode('=', $this->text, 2);
        $pieces = array_map('trim', $pieces);

        return 2 === count($pieces);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return (string) $this->text;
    }

    /**
     * @param string $text
     *
     * @return KeyValue|LineInterface
     */
    public static function parse_raw(string $text): KeyValue|LineInterface
    {
        $line = new static($text);

        if (! $line->isPair()) {
            return $line;
        }

        return new KeyValue($line->key(), $line->value(), $line->quote());
    }

    /**
     * Check if the given character wraps the value.
     *
     * @param $char
     * @param $value
     *
     * @return bool
     */
    public static function wraps_value($char, $value): bool
    {
        return substr((string) $value,  0, 1) == $char
            && substr((string) $value, -1, 1) == $char;
    }

    /**
     * @param $string
     *
     * @return string
     */
    public static function wrapping_quote_for($string): string
    {
        return Collection::make(["'", '"'])
            ->first(function ($key, $quote) use ($string) {
                return static::wraps_value($quote, $string);
            }, '');
    }

    /**
     * Trim surrounding quotes from a string
     *
     * @param $string
     *
     * @return string
     */
    public static function clean_quotes($string): string
    {
        if (static::wrapping_quote_for($string)) {
            $string = substr($string, 1); // remove first character
            $string = substr($string, 0, -1); // remove last
        }

        return $string;
    }
}
