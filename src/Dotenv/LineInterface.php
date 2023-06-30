<?php

namespace WP_CLI_Dotenv\Dotenv;

interface LineInterface
{
    /**
     * @return string
     */
    public function key(): string;

    /**
     * @return string
     */
    public function value(): string;

    /**
     * @return string
     */
    public function toString(): string;
}
