<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
  use SoftDeletes;

  public function sender(){
    return $this->belongsTo('App\User');
  }

  public function receivedMessages(){
    return $this->belongsToMany('App\ReceivedMessage');
  }

}
