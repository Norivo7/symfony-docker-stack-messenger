<?php
declare(strict_types=1);

namespace App\Message\Traits;

trait ContextLoggableTrait
{
    public function getLogContext(): array
    {
        $data = [];

        foreach (get_object_vars($this) as $key => $value) {
            $data[$key] = is_scalar($value) || $value === null
                ? $value
                : (is_object($value) ? get_class($value) : json_encode($value));
        }

        return $data;
    }

}
