<template>
  <div class="row">
    <div class="col-12">
      <div class="row">
        <div class="col h3">
          Edit Group - Name: {{group.name}} - Number: {{group.id}}
        </div>
      </div>
      <div class="row">
        <div class="col">
          <div class="h5">
            Members
          </div>
          <ul class="list-group">
            <li class="list-group-item" v-for="(member, index) in members">
              Name: {{member.name}}, Admin: <input type="checkbox" v-model="member.is_admin" @change="setAdmin(index)">
            </li>
          </ul>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <div class="h5">
            Join requests
          </div>
          <ul class="list-group">
            <li class="list-group-item" v-for="(request, index) in joinRequesters">
              Name: {{request.name}} 
              <div class="btn btn-primary" @click="joinRequest('approve',request.id)">Approve</div>
              <div class="btn btn-primary" @click="joinRequest('deny',request.id)">Deny</div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default{
    props:['group'],
    data(){
      return{
        members:[],
        joinRequesters:[]
      }
    },
    mounted(){
      console.log("group edit mounted");
      this.updateMemberList();
      this.updateRequesterList();
    },
    methods:{
      joinRequest(action,userId){
        console.log("Should "+action+" the request from user with id: "+userId);
        var self = this;
        $.get(
          '/groups/processJoinRequest/'+this.group.id,
          {
            userId:userId,
            action:action
          },
          function(data){
            console.log(JSON.stringify(data));
            if(data.error){
              console.log("error processing join request: "+data.message);
            }else{
              self.updateMemberList();
              self.updateRequesterList();
            }
          },
          'json'
        );
      },
      updateMemberList(){
        var self = this;
        $.get(
          '/groups/members/'+this.group.id,
          function(data){
            console.log(JSON.stringify(data));
            if(data.error){
              console.log("error getting member list: "+data.message);
            }else{
              self.members = data.groupMembers;
            }
          },
          'json'
        );
      },
      updateRequesterList(){
        var self = this;
        $.get(
          '/groups/getJoinRequests/'+this.group.id,
          function(data){
            console.log(JSON.stringify(data));
            if(data.error){
              console.log("error getting requesters: "+data.error);
            }else{
              self.joinRequesters = data.joinRequesters;
            }
          },
          'json'
        );
      },
      setAdmin(index){
        console.log("should open set admin dialog for member: "+JSON.stringify(this.members[index]));
        var self = this;
        $.get(
          '/groups/setAdmin',
          {
            groupId: self.group.id,
            memberId: self.members[index].user_id,
            isAdmin: self.members[index].is_admin
          },
          function(data){
            if(data.error){
              console.log("set admin error: "+data.message);
            }else
              self.updateMemberList();
          },
          'json'
        );
      }
    }
  }
</script>