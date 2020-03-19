<?php

namespace App\Http\Controllers;

use App\Group;
use App\User;
use App\GroupJoinRequest;
use App\GroupUser;
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
      $groups = Auth::user()->groups();
      for($i=0;$i<count($groups);$i++){
        $groups[$i]->isAdmin = GroupUser::where('group_id',$groups[$i]->id)->where('user_id',Auth::user()->id)->first()->is_admin == 1;
        if($groups[$i]->isAdmin)
          $groups[$i]->joinRequests = count(GroupJoinRequest::where('group_id',$groups[$i]->id)->get());
        $groups[$i]->numberOfMembers = count(GroupUser::where('group_id',$groups[$i]->id)->get());
      }
      return $groups;
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
        $groupUser = new GroupUser;
        $groupUser->user_id = $user->id;
        $groupUser->group_id = $group->id;
        $groupUser->is_admin = true;
        $groupUser->save();
        
        return redirect('/home');
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
      $groupUser = GroupUser::where('group_id',$group->id)->where('user_id',$user->id)->where('is_admin',1)->first();
      if($groupUser){
        $response->error = false;
        $response->message = "this is your group";
      }else{
        $response->message = "this is not your group";
        return redirect('/home');
      }
      return view('groupEdit',['group'=>$group]);
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

    public function join(){
      return view('groupJoin');
    }

    public function joinRequest(Request $request){
      
      $joinRequest = new GroupJoinRequest;
      $joinRequest->group_id = $request['id'];
      $joinRequest->requester_id = Auth::User()->id;
      $joinRequest->save();
      return redirect('/home');
    }

    public function members(Request $request, $id){
      $response = new \stdClass();
      $response->error = true;
      $response->message = 'Not group admin';
      $user_id = Auth::user()->id;
      $groupUser = GroupUser::where('group_id',$id)->where('user_id',$user_id)->where('is_admin',1)->first();
      if($groupUser){
        //user is admin of the group
        $response->error = false;
        $response->message = 'Should return list of members';
        $groupUsers = GroupUser::where('group_id',$id)->get();
        for($i = 0;$i<count($groupUsers);$i++){
          $groupUsers[$i]->is_admin = $groupUsers[$i]->is_admin == '1';
          $groupUsers[$i]->name = User::where('id',$groupUsers[$i]->user_id)->first()->name;
        }
        $response->groupMembers = $groupUsers;
      }

      return response(json_encode($response))->header('Content-Type','application/json');
    }

    public function setAdmin(Request $request){
      $response = new \stdClass();
      $response->error = true;
      $response->message = 'Not group admin or did not set groupId, memberId and isAdmin';
      $user_id = Auth::user()->id;
      if(isset($request['groupId']) and isset($request['memberId']) and isset($request['isAdmin'])){
        //$response->message = "must set groupId, memberId and isAdmin";
        $groupAdmin = GroupUser::where('group_id',$request['groupId'])->where('user_id',$user_id)->where('is_admin',1)->first();
        $groupMember = GroupUser::where('group_id',$request['groupId'])->where('user_id',$request['memberId'])->first();
        $response->groupAdmin = $groupAdmin;
        $response->groupMember = $groupMember;
        $isAdmin = $request['isAdmin'];
      }else{
        $groupAdmin = null;
        $groupMember = null;
      }
        
      
      if($groupAdmin and $groupMember){
        $groupMember->is_admin = $isAdmin=='true';
        $groupMember->save();
        return $groupMember;
      }else{
        return response(json_encode($response))->header('Content-Type','application/json');
      }
      

    }

    public function getJoinRequests($id){
      $response = new \stdClass();
      $response->error = true;
      $response->message = 'Not group admin';
      $user_id = Auth::user()->id;
      $groupUser = GroupUser::where('group_id',$id)->where('user_id',$user_id)->where('is_admin',1)->first();
      if($groupUser){
        $requests = array();
        $joinRequests = GroupJoinRequest::where('group_id',$id)->get();
        for($i = 0; $i<count($joinRequests);$i++){
          $joinRequests[$i]->name = User::where('id',$joinRequests[$i]->requester_id)->first()->name;
          $request = new \stdClass();
          $request->id = $joinRequests[$i]->requester_id;
          $request->name = User::where('id',$joinRequests[$i]->requester_id)->first()->name;
          array_push($requests,$request);
        }
        //$response->joinRequests = $joinRequests;
        $response->joinRequesters = $requests;
        $response->error = false;
        $response->message = 'success';
      }
      return response(json_encode($response))->header('Content-Type','application/json');

    }

    public function processJoinRequest(Request $request, $id){
      $response = new \stdClass();
      $response->error = true;
      $response->message = "must specify groupId, userId and action";
      if(isset($request['userId']) and isset($request['action'])){
        $response->message = 'Not group admin';
        $groupId = $id;
        $requesterId = $request['userId'];
        $action = $request['action'];
        $userId = Auth::user()->id;
        $groupUser = GroupUser::where('group_id',$groupId)->where('user_id',$userId)->where('is_admin',1)->first();
        if($groupUser){
          $request = GroupJoinRequest::where('group_id',$groupId)->where('requester_id',$requesterId)->first();
          $request->delete();
          if($action=='approve'){
            $groupUser = new GroupUser;
            $groupUser->user_id = $requesterId;
            $groupUser->group_id = $groupId;
            $groupUser->is_admin = false;
            $groupUser->save();
          }
          $response->error = false;
          $response->message = 'success';
        }
      }
      
      return response(json_encode($response))->header('Content-Type','application/json');
    }
}

