<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resource extends Model
{
  use SoftDeletes;

  public function user(){
    return $this->belongsTo('App\User');
  }

  public function groups(){
    return $this->belongsToMany('App\Group');
  }

  public function resourceSchedules(){
    return $this->hasMany('App\ResourceSchedule');
  }

  public function images(){
    return $this->hasMany('App\ResourceImage');
  }
}
