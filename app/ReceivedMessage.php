<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceivedMessage extends Model
{
  use SoftDeletes;

  public function message(){
    return $this->belongsTo('App\Message');
  }
}
