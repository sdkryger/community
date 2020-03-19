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
      <div class="row mt-2">
        <div class="col h3">
          My resources
        </div>
      </div>
      <div class="row" v-for="resource in myResources">
        <div class="col" @click="editResource(resource.id)" style="cursor:pointer;">
          {{resource.title}}
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
        groups:[],
        myResources:[]
      }
    },
    methods:{
      editGroup(isAdmin,id){
        if(isAdmin){
          var url = '/groups/'+id;
          window.open(url,'_self');
        }
      },
      editResource(id){
        var url = '/resources/'+id;
        window.open(url,'_self');
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
      },
      updateMyResources(){
        var self = this;
        $.get(
          '/myResources',
          function(data){
            self.myResources = data;
          },
          'json'
        )
      }
    },
    mounted(){
      this.updateGroupList();
      this.updateMyResources();
    }
  }
</script>