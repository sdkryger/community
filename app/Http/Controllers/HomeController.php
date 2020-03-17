<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\GroupJoinRequest;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
      $groups = Auth::user()->groups();
      for($i = 0;$i<count($groups);$i++){
        $groups[$i]['somethingElse'] = '$i:'.$i;
        $groups[$i]['joinRequests'] = GroupJoinRequest::where('group_id',$groups[$i]->id)->count();
      }
      return view('home',['groups'=>$groups]);
    }
}
