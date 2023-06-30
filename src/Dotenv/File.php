<?php

namespace WP_CLI_Dotenv\Dotenv;

use WP_CLI_Dotenv\Dotenv\Exception\FilePermissionsException;
use WP_CLI_Dotenv\Dotenv\Exception\NonExistentFileException;

/**
 * Class File
 * @package WP_CLI_Dotenv_Command
 */
class File
{
    /**
     * Absolute path to the file
     * @var string
     */
    protected string $path;

    /**
     * Lines collection
     * @var FileLines
     */
    protected FileLines $lines;

    /**
     * File constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Get a new instance, and ensure the file is readable.
     *
     * @param $path
     *
     * @throws NonExistentFileException
     * @throws FilePermissionsException
     *
     * @return static
     */
    public static function at($path): static
    {
        $file = new static($path);

        if (! $file->exists()) {
            throw new NonExistentFileException("File does not exist at $path");
        }

        if (! $file->isReadable()) {
            throw new FilePermissionsException("File not readable at $path");
        }

        return $file;
    }

    /**
     * Get a new instance, and ensure the file is writable.
     *
     * @param $path
     *
     * @return static
     * @throws NonExistentFileException
     *
     * @throws FilePermissionsException
     */
    public static function writable($path): static
    {
        $file = static::at($path);

        if (! is_writable($path)) {
            throw new FilePermissionsException("File not writable at $path");
        }

        return $file;
    }


    /**
     * Create a new instance, including the file and parent directories.
     *
     * @param $path
     *
     * @return static
     */
    public static function create($path): static
    {
        $file = new static($path);

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        if (! $file->exists()) {
            touch($path);
        }

        return $file;
    }

    /**
     * Whether the file exists and is readable
     *
     * @return bool
     */
    public function isReadable(): bool
    {
        return is_readable($this->path);
    }

    /**
     * @return bool
     */
    public function isWritable(): bool
    {
        return is_writable($this->path);
    }

    /**
     * @return $this
     */
    public function load(): static
    {
        $this->lines = FileLines::load($this->path);

        return $this;
    }

    /**
     * Get the full path to the file.
     *
     * @return string
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * Write the lines to the file.
     *
     * @return int
     */
    public function save(): int
    {
        return file_put_contents($this->path, $this->lines->toString());
    }

    /**
     * Check if the file exists
     *
     * @return bool
     */
    public function exists(): bool
    {
        return file_exists($this->path);
    }

    /**
     * @return int
     */
    public function lineCount(): int
    {
        return $this->lines->count();
    }

    /**
     * Get the value for a key
     *
     * Ex using our format:
     * KEY='VALUE'
     *
     * @param $key
     *
     * @return null|string          string value,
     *                              null if no match was found
     */
    public function get($key): ?string
    {
        return $this->lines->getDefinition($key);
    }

    /**
     * Set a variable definition.
     *
     * @param        $key
     * @param        $value
     * @param string $quote
     *
     * @return $this
     */
    public function set($key, $value, string $quote = ''): static
    {
        $this->lines->updateOrAdd(new KeyValue($key, $value, $quote));

        return $this;
    }

    /**
     * Remove a variable definition.
     *
     * @param $key
     *
     * @return int Lines removed
     */
    public function remove($key): int
    {
        $linesBefore = $this->lineCount();

        $this->lines->removeDefinition($key);

        return $linesBefore - $this->lineCount();
    }

    /**
     * Whether the file defines the given key
     *
     * @param $key
     *
     * @return bool
     */
    public function hasKey($key): bool
    {
        return $this->lines->hasDefinition($key);
    }

    /**
     * Get the lines as key => value.
     *
     * @return Collection
     */
    public function dictionary(): Collection
    {
        return $this->lines->toDictionary();
    }

    /**
     * Get the lines as key => value pairs, where the keys match the given glob-style patterns.
     *
     * @param $patterns
     *
     * @return Collection
     */
    public function dictionaryWithKeysMatching($patterns): Collection
    {
        return $this->lines->whereKeysLike($patterns)->toDictionary();
    }
}
