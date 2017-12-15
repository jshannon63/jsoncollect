<?php

namespace Jshannon63\JsonCollect;

use stdClass;
use Illuminate\Support\Collection;

class JsonCollect extends Collection
{
    public function __construct($items = null)
    {
        parent::__construct($this->parseItems($items));

        $this->resolve($this);
    }

    private function parseItems($items)
    {
        // if ($items === null) {
        //     $items = json_encode(new stdClass);
        // } elseif (is_object($items)) {
        //     if (get_class($items) != stdClass::class) {
        //         throw new \InvalidArgumentException('Invalid object type (' . get_class($items) . ') provided.');
        //     }
        // } elseif (is_string($items)) {
        //     $items = json_decode($items);
        //     if ($err = $this->getLastJsonError()) {
        //         throw new \InvalidArgumentException('Provided string cannot be evaluated as JSON: ' . $err);
        //     }
        // }

        if (is_object($items)) {
            if (get_class($items) != stdClass::class) {
                throw new \InvalidArgumentException('Invalid object type ('.get_class($items).') provided.');
            }
        } elseif (is_string($items)) {
            $items = json_decode($items);
            if ($err = $this->getLastJsonError()) {
                throw new \InvalidArgumentException('Provided string cannot be evaluated as JSON: '.$err);
            }
        }

        return $items;
    }

    private function resolve(Collection $collection)
    {
        $this->items = $collection->each(function ($item, $key) {
            if (is_array($item)) {
                $this->put($key, new JsonCollect($item));
            } elseif (is_object($item)) {
                if (get_class($item) == stdClass::class) {
                    $this->put($key, new JsonCollect($item));
                }
            }
        })->all();
    }

    public function export($options = 0)
    {
        return $this->toJson($options);
    }

    private function getLastJsonError()
    {
        $errors = [
            JSON_ERROR_NONE => null,
            JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
            JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded',
        ];

        return $errors[json_last_error()];
    }

    public function __call($method, $parameters)
    {
        if (substr($method, 0, 3) == 'get' && strlen($method) > 3) {
            $key = substr($method, 3);

            return $this->get($key);
        } elseif (substr($method, 0, 3) == 'set' && strlen($method) > 3) {
            $key = substr($method, 3);
            $this->put($key, $parameters[0]);
        } else {
            throw new \BadMethodCallException(get_class($this).' is not aware of the method: '.$method.'.');
        }
    }
}
