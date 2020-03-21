<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResourceDayItem extends Model
{
  public function resourceSchedule()
  {
    return $this->belongsTo('App\ResourceSchedule');
  }
}
