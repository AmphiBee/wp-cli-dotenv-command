<?php

namespace WP_CLI_Dotenv\Dotenv;

use ReturnTypeWillChange;
use Traversable;

class Collection implements \ArrayAccess, \IteratorAggregate, \Countable
{
    protected array|Collection $items;

    /**
     * Collection constructor.
     *
     * @param array|Collection $items
     */
    public function __construct(Collection|array $items = [])
    {
        $this->items = ($items instanceof Collection) ? $items->all() : $items;
    }

    public static function make($items): static
    {
        return new static($items);
    }

    public function all()
    {
        return $this->items;
    }

    public function keys(): static
    {
        return new static(array_keys($this->items));
    }

    public function values(): static
    {
        return new static(array_values($this->items));
    }

    public function each($callback): static
    {
        foreach ($this->items as $key => $value) {
            if (false === $callback($value, $key)) {
                break;
            }
        }

        return $this;
    }

    public function map($callback): static
    {
        $keys   = array_keys($this->items);
        $values = array_map($callback, $this->items, $keys);

        return new static(array_combine($keys, $values));
    }

    public function contains($callback): bool
    {
        return null !== $this->first($callback);
    }

    public function first($callback = null, $default = null)
    {
        if ($callback instanceof \Closure) {
            foreach ($this->items as $key => $value) {
                if ($callback($key, $value)) {
                    return $value;
                }
            }

            return $default;
        }

        return reset($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function implode($glue): string
    {
        return implode($glue, $this->items);
    }

    public function filter($callback = null): static
    {
        if ($callback instanceof \Closure) {
            return new static(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
        }

        return new static(array_filter($this->items));
    }

    public function reject($callback): static
    {
        return $this->filter(function ($value, $key) use ($callback) {
            return ! $callback($value, $key);
        });
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function reduce($callback, $initial): static
    {
        return new static(array_reduce($this->items, $callback, $initial));
    }

    public function get($key, $default = null)
    {
        return $this->offsetExists($key) ? $this->offsetGet($key) : $default;
    }

    public function pluck($prop): static
    {
        return $this->map(function ($item) use ($prop) {
            if (is_array($item) && array_key_exists($prop, $item)) {
                return $item[$prop];
            }
            if (is_object($item) && property_exists($item, $prop)) {
                return $item->$prop;
            }
            return null;
        });
    }

    public function put($key, $value): void
    {
        $this->offsetSet($key, $value);
    }

    public function search($needle): bool|int|string
    {
        if ($needle instanceof \Closure) {
            foreach ($this->items as $key => $value) {
                if ($needle($value, $key)) {
                    return $key;
                }
            }
            return false;
        }

        return array_search($needle, $this->items);
    }

    public function push($value): static
    {
        $this->items[] = $value;

        return $this;
    }

    public function only($keys): static
    {
        return $this->filter(function ($value, $key) use ($keys) {
            return in_array($key, $keys);
        });
    }

    public function unique(): static
    {
        return new static(array_unique($this->items));
    }

    /**
     * Whether a offset exists
     * @link  http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->items);
    }

    /**
     * Offset to retrieve
     * @link  http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     *
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    #[ReturnTypeWillChange] public function offsetGet(mixed $offset): mixed
    {
        return $this->offsetExists($offset) ? $this->items[ $offset ] : null;
    }

    /**
     * Offset to set
     * @link  http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->items[ $offset ] = $value;
    }

    /**
     * Offset to unset
     * @link  http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[ $offset ]);
    }

    /**
     * Retrieve an external iterator
     * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->items);
    }
}

