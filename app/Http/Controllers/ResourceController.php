<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Group;

class ResourceController extends Controller
{
  public function listResources(){
    echo 'resources';
  }

  public function myResources(){
    return Auth::user()->resources;
  }

}
