<template>
  <div class="row">
    <div class="col-12">
      <div class="row">
        <div class="col h3">
          Edit Group - {{group.name}}
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
    </div>
  </div>
</template>

<script>
  export default{
    props:['group'],
    data(){
      return{
        members:[]
      }
    },
    mounted(){
      console.log("group edit mounted");
      this.updateMemberList();
    },
    methods:{
      updateMemberList(){
        var self = this;
        $.get(
          '/groups/members/'+this.group.id,
          function(data){
            console.log(JSON.stringify(data));
            if(data.error){
              alert("error getting member list");
            }else{
              self.members = data.groupMembers;
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