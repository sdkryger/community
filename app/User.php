<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Group;
use App\GroupUser;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function groups(){
      $groupUsers = GroupUser::where('user_id',$this->id)->get();
      $groups = array();
      foreach($groupUsers as $groupUser){
        $group = Group::where('id',$groupUser->group_id)->first();
        array_push($groups, $group);
      }
      return $groups;
    }

    public function resources(){
      return $this->hasMany('App\Resource');
    }

    public function resourceImages(){
      return $this->hasMany('App\ResourceImage');
    }

    public function sentMessages(){
      return $this->hasMany('App\Message');
    }

    

    public function resourceSchedules(){
      return $this->hasMany('App\ResourceSchedule');
    }
}
