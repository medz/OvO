<?php

declare(strict_types=1);

namespace App\Http\Resources\Concerns;

use Carbon\Carbon;

trait ZuluDateString
{
    /**
     * When and dateTime to Zulu string.
     * @param $date
     * @return mixed
     */
    protected function whenDateToZulu($date = null)
    {
        return $this->when($date, function () use ($date) {
            $date = $this->when($date instanceof Carbon, $date, function () use ($date): Carbon {
                return new Carbon($date);
            });

            return $date->toIso8601ZuluString();
        });
    }
}
