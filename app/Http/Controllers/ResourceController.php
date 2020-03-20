<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Group;
use App\Resource;
use App\GroupUser;

class ResourceController extends Controller
{
  public function listResources(){
    echo 'resources';
  }

  public function myResources(){
    return Auth::user()->resources;
  }

  public function show($id){
    if($id == -1){
      $resource = new Resource;
      $resource->new = true;
      $resource->title = '';
      $resource->id = -1;
    }
    else
      $resource = Resource::where('id',$id)->where('user_id',Auth::user()->id)->first();
    if(!$resource){
      $resource = new Resource;
      $resource->title = 'Access denied';
    }
    return view('resourceEdit',['resource'=>$resource]);
  }

  public function groupAssignment(Request $request){
    $response = new \stdClass();
    $response->error = true;
    $response->message = 'must specify groupId, resourceId and action';
    if(isset($request['groupId']) && isset($request['resourceId']) && isset($request['access']) ){
      $response->message = 'all required values are set';
      $groupId = $request['groupId'];
      $userId = Auth::user()->id;
      $resourceId = $request['resourceId'];
      $access = $request['access'] == 'true';
      $resource = Resource::where('id',$resourceId)->where('user_id',$userId)->first();
      $groupUser = GroupUser::where('user_id',$userId)->where('group_id',$groupId)->first();
      if($resource && $groupUser){
        $response->message = "you own this resource and are a member of the group";
        $group = Group::where('id',$groupId)->first();
        if($access)
          $group->resources()->attach($resource);
        else
          $group->resources()->detach($resource->id);
        $response->error = false;
        $response->message = 'success';
      }
    }
    return response(json_encode($response))->header('Content-Type','application/json');
  }

  public function listGroups($id){
    $userId = Auth::user()->id;
    $userGroups = GroupUser::where('user_id',$userId)->get();
    $resource = Resource::where('id',$id)->where('user_id',$userId)->first();
    $resourceGroups = $resource->groups;
    if($resource){
      $groups = array();
      foreach($userGroups as $userGroup){
        $group = Group::where('id',$userGroup->group_id)->first();
        $resourceGroup = new \stdClass();
        $resourceGroup->id = $group->id;
        $resourceGroup->name = $group->name;
        $resourceGroup->access = false;
        foreach($resourceGroups as $g){
          if($group->id == $g->id)
            $resourceGroup->access = true;
        }
        array_push($groups, $resourceGroup);
      }
      return $groups;
    }else{
      $response = new \stdClass();
      $response->error = true;
      $response->message = 'not authorized';
      return response(json_encode($response))->header('Content-Type','application/json');
    }
    
    //echo "should list groups for resource with id: ".$id;
  }

  public function update($id, Request $request){

    $response = new \stdClass();
    $response->error = true;
    $response->message = 'unable to update resource with id: '.$id;
    $user = Auth::user();
    if(isset($request['title'])){
      $title = $request['title'];
      if($id == -1){
        //new resource
        $response->message = 'will try to create new resource with id: '.$id.' and title: '.$title;
        $resource = new Resource;
        $resource->title = $title;
        Auth::user()->resources()->save($resource);
        return $resource;
      }else{
        $response->message = 'will try to update resource with id: '.$id.' and title: '.$title;
        $resource = Resource::where('id',$id)->where('user_id',$user->id)->first();
        if($resource){
          $resource->title = $title;
          $resource->save();
          return $resource;
        }else{
          $response->message = 'This resource does not belong to you';
        }
      }
      
    }
    return response(json_encode($response))->header('Content-Type','application/json');
  }
}
