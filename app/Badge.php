<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{

    public function getDateAttribute() {
        return Carbon::createFromDate($this->year, $this->month, 1);
    }

    public function setYearAttribute(Carbon $value) {
        $this->attributes['year'] = $value->year;
    }

    public function setMonthAttribute(Carbon $value) {
        $this->attributes['month'] = $value->month;
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
