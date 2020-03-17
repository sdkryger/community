<?php

namespace App\Http\Controllers;

use App\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }  
  
  /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      //$groups = Group::all();
      //return $groups;
      return Auth::user()->groupsAdministrated;
      //return DB::table('group_user')->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      return view('groupCreate');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $response = new \stdClass();
      $response->error = true;
      $response->message = 'message not set';
      if($request['name']){
        $user = Auth::user();
        $response->user = $user;
        $response->error = false;
        $response->message = "will try to add group";
        $groupName = $request['name'];
        $response->groupName = $groupName;
        $group = new Group;
        $group->name = $groupName;
        $group->created_by_id = intval($user->id);
        $group->save();
        $group->users()->save($user);
        $group->admins()->save($user);
        
        return redirect('/groups');
      }else{
        $response->message = 'must specify new group name';
      }
      return response(json_encode($response))->header('Content-Type','application/json');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group)
    {
      $response = new \stdClass();
      $response->error = true;
      $response->message = 'message not set';
      $response->message = 'request to show groupId: '.$group->id;
      $response->group = $group;
      $user = Auth::user();
      $response->user = $user;
      if(intval($user->id) == intval($group->created_by_id)){
        $response->error = false;
        $response->message = "this is your group";
      }else{
        $response->message = "this is not your group";
        return redirect('/groups');
      }
      return response(json_encode($response))->header('Content-Type','application/json');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function edit(Group $group)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Group $group)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        //
    }

}
