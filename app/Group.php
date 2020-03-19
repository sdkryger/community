<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
  use SoftDeletes;
  
  public function resources(){
    return $this->belongsToMany('App\Resource');
  }
}
