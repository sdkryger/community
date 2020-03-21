<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceSchedule extends Model
{
  use SoftDeletes;

  public function user(){
    return $this->belongsTo('App\User');
  }

  public function resource(){
    return $this->belongsTo('App\Resource');
  }

  public function resourceDayItems()
  {
    return $this->hasMany('App\ResourceDayItem');
  }

}
