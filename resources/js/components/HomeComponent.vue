<template>
  <div class="row">
    <div class="col">
      <div class="row">
        <div class="col h3">
          Groups
        </div>
      </div>
      <div class="row" v-for="group in groups">
        <div class="col" @click="editGroup(group.isAdmin,group.id)" style="cursor:pointer;">
          <span>{{group.name}} </span>
          <span v-if="group.isAdmin" class="badge badge-pill badge-primary">Admin</span>
          <span v-if="group.joinRequests > 0" class="badge badge-pill badge-primary">Join requests: {{group.joinRequests}}</span>
          <span class="badge badge-pill badge-secondary">Members: {{group.numberOfMembers}}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default{
    data(){
      return{
        message:'home component',
        groups:[]
      }
    },
    methods:{
      editGroup(isAdmin,id){
        if(isAdmin){
          var url = 'groups/'+id;
          window.open(url,'_self');
        }
      },
      updateGroupList(){
        var self = this;
        $.get(
          '/groups',
          function(data){
            self.groups = data;
          },
          'json'
        )
      }
    },
    mounted(){
      this.updateGroupList();
    }
  }
</script>