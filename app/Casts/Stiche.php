<?php

namespace App\Casts;

use App\Live\Stich;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class Stiche implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return mixed
     */
    public function get($model, $key, $value, $attributes)
    {
        $collection = collect($value);
        if ($collection->count() > 0)
        {
            $stiche = collect($value)->map(function ($item, $key)
            {
                return Stich::create((object) $item);
            });
        } else
        {
            $stiche = collect();
        }

        return $stiche;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param array $value
     * @param array $attributes
     * @return mixed
     */
    public function set($model, $key, $value, $attributes)
    {
        if (!$value instanceof Collection)
        {
            throw new InvalidArgumentException('The given value is not an Collection instance.');
        }

        return $value->toJson();
    }
}
