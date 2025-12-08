<?php

declare(strict_types=1);

namespace App\Shared\Traits;

trait ContextLoggableTrait
{
    /**
     * @return array<string, mixed>
     */
    public function getLogContext(): array
    {
        $data = [];

        foreach (get_object_vars($this) as $key => $value) {
            if (is_scalar($value) || null === $value) {
                $data[$key] = $value;
            }
            if (is_object($value)) {
                $data[$key] = get_class($value);
            }
            if (!is_object($value)) {
                $data[$key] = json_encode($value, JSON_THROW_ON_ERROR);
            }
        }

        return $data;
    }
}
