<?php
namespace WP_CLI_Dotenv\WP_CLI;

use WP_CLI;
use WP_CLI_Dotenv\Dotenv\File;
use function cli\prompt;
use function WP_CLI\Utils\get_flag_value;

class Command
{
    /**
     * @var AssocArgs
     */
    protected AssocArgs $args;

    /**
     * Initialize the arguments.
     *
     * @param $args array All arguments passed to the sub-command method
     */
    protected function init_args(array $args): void
    {
        $this->args = new AssocArgs($args[1]);
    }

    /**
     * Get flag value for command line option.
     *
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    protected function get_flag($key, $default = null): mixed
    {
        return get_flag_value($this->args->original(), $key, $default);
    }

    /**
     * Load the environment file, while ensuring read permissions or die trying!
     *
     * @return File
     */
    protected function get_env_for_read_or_fail(): File
    {
        try {
            return File::at($this->resolve_file_path())->load();
        } catch (\Exception $e) {
            WP_CLI::error($e->getMessage());
        }
    }

    /**
     * Load the environment file, while ensuring read permissions or die trying!
     *
     * @return File
     */
    protected function get_env_for_write_or_fail(): File
    {
        try {
            return File::writable($this->resolve_file_path())->load();
        } catch (\Exception $e) {
            WP_CLI::error($e->getMessage());
        }
    }

    /**
     * Get the absolute path to the file.
     *
     * @param null $file The path to resolve, defaults to argument passed value.
     *
     * @return string|null
     */
    protected function resolve_file_path($file = null): ?string
    {
        if (is_null($file)) {
            $file = $this->args->file;
        }

        if (file_exists($file)) {
            return $file;
        }

        $dirname  = dirname($file);
        $filename = basename($file);
        $relpath  = $dirname ? "/$dirname" : '';
        $path     = getcwd() . "$relpath/$filename";

        /**
         * realpath will return false if path does not exist
         */
        return realpath($path) ?: str_replace('/./', '/', $path);
    }


    /**
     * Prompt the user for input at the command line or use a default.
     *
     * @param $question
     * @param $default
     *
     * @return bool
     */
    protected function prompt($question, $default): bool
    {
        try {
            return prompt($question, $default);
        } catch (\Exception $e) {
            WP_CLI::error($e->getMessage());
            die;
        }
    }
}
