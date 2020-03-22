<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Group;
use App\Resource;
use App\GroupUser;
use App\ResourceSchedule;
use App\ResourceDayItem;
use Carbon\Carbon;

class ResourceController extends Controller
{
  public function listResources(){
    //echo 'resources';
    $user = Auth::user();
    $groups = $user->groups();
    $groupIds = array();
    $resourceIds = array();
    foreach($groups as $group){
      array_push($groupIds,$group->id);
      $resources = $group->resources;
      foreach($resources as $resource){
        if(array_search($resource->id,$resourceIds) != -1)
          array_push($resourceIds,$resource->id);
      }
    }

    $resources = Resource::whereIn('id',$resourceIds)->get();
    return $resources;
  }

  public function myResources(){
    $resources = Auth::user()->resources;
    foreach($resources as $resource){
      //$resource->requests = $resource->resourceSchedules;
      $resource->resourceSchedules;
      $i = 0;
      $schedules = array();
      foreach($resource->resourceSchedules as $sched){
        if(is_null($sched['approved']))
          array_push($schedules, $sched);
        $i++;
      }
      unset($resource->resourceSchedules);
      $resource->scheduleRequests = count($schedules);
    }
    return $resources;
  }

  public function edit($id){
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

  public function destroy($id){
    $user = Auth::user();
    $resource = Resource::where('id',$id)->where('user_id',$user->id)->first();
    if($resource){
      $resource->groups()->detach();
      $resource->delete();
      return $resource;
    }else{
      abort(403, 'Unauthorized action.');
    }
  }

  public function show($id){
    $user = Auth::user();
    $groups = $user->groups();
    $access = false;
    foreach($groups as $group){
      $resources = $group->resources;
      foreach($resources as $resource){
        if($resource->id == $id)
          $access = true;
      }
    }
    $resource = Resource::where('id',$id)->first();
    if($resource && $access){
      $resource->owner = $resource->user_id == $user->id;
      return view('resourceView',['resource'=>$resource]);
    }else{
        abort(403, 'Unauthorized action.');
    }
  }

  public function scheduleList($id, Request $request){
    $user = Auth::user();
    $groups = $user->groups();
    $access = false;
    foreach($groups as $group){
      $resources = $group->resources;
      foreach($resources as $resource){
        if($resource->id == $id)
          $access = true;
      }
    }
    if($access){
      $scheduleItems = ResourceSchedule::where('resource_id',$id)->get();
            
      foreach($scheduleItems as $item){
        $item->start = Carbon::createFromTimeString($item->start_time);
        $item->end = Carbon::createFromTimeString($item->end_time);
        $item->numberOfDays = $item->end->diffInDays($item->start) + 1;
        $item->resourceDayItems;
      }

      return $scheduleItems;
    }
    else
      abort(403, 'Unauthorized action.');
  }

  public function scheduleRequest(Request $request){
    $access = false;
    $user = Auth::user();
    $groups = $user->groups();
    $resourceToBeScheduled = null;
    if(isset($request['resourceId']) and isset($request['start']) and isset($request['end']) ){
      $id = $request['resourceId'];
      $start = $request['start'];
      $end = $request['end'];
      foreach($groups as $group){
        $resources = $group->resources;
        foreach($resources as $resource){
          if($resource->id == $id){
            $access = true;
            $resourceToBeScheduled = $resource;
          }
            
        }
      }
    }
    
    
    
    if($access){
      $resource = Resource::where('id',$id)->first();
      //return ResourceSchedule::where('resource_id',$id)->get();
      //echo "should process request";
      $resourceSchedule = new ResourceSchedule;
      $resourceSchedule->start_time = $start;
      $resourceSchedule->end_time = $end;
      //$user->resourceSchedules()->save($resourceSchedule);
      $resourceSchedule->user()->associate($user);
      $resourceSchedule->resource()->associate($resource);
      //$resourceToBeScheduled->resourceSchedules()->save($resourceSchedule);
      $resourceSchedule->save();
      $start .= ' 00:00:00.000';
      $end .= ' 00:00:00.000';
      $start = Carbon::createFromTimeString($start);
      $end = Carbon::createFromTimeString($end);
      $numberOfDays = $end->diffInDays($start) + 1;
      for($i = 0;$i<$numberOfDays;$i++){
        $resourceDayItem = new ResourceDayItem;
        $resourceDayItem->timestamp = $start;
        $resourceSchedule->resourceDayItems()->save($resourceDayItem);
        $start->addDays(1);
      }
      return $resourceSchedule;
    }
    else
      abort(403, 'Unauthorized action.');
  }
}
