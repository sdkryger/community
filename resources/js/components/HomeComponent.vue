<template>
  <div class="row">
    <div class="col">
      <div class="row">
        <div class="col card pr-0 pl-0">
          <div class="card-header h4">
            Groups
          </div>
          <div class="card-body">
            <div class="list-group list-group-flush" v-for="group in groups">
              <a :href="'/groups/'+group.id" class="list-group-item list-group-item-action">
                <span>{{group.name}} </span>
                <span v-if="group.isAdmin" class="badge badge-pill badge-primary">Admin</span>
                <span v-if="group.joinRequests > 0" class="badge badge-pill badge-primary">Join requests: {{group.joinRequests}}</span>
                <span class="badge badge-pill badge-secondary">Members: {{group.numberOfMembers}}</span>
              </a>
            </div>
          </div>
          <div class="card-footer">
            <a class="btn btn-primary" role="button" href="/groups/create">New</a>
            <a class="btn btn-primary" role="button" href="/groups/join">Join</a>
          </div>
        </div>
      </div>
      <div class="row mt-1">
        <div class="col card pr-0 pl-0">
          <div class="card-header h4">
            Resources
          </div>
          <div class="card-body">
            <div class="list-group list-group-flush">
              <a :href="'/resources/view/'+resource.id" v-for="resource in allResources" 
                class="list-group-item list-group-item-action">
                <span>{{resource.title}}</span>
                <span v-if="resource.isOwner" class="badge badge-pill badge-primary">Mine</span>
                <span v-if="resource.notInGroup" class="badge badge-pill badge-warning">Not available to groups</span>
                <span v-if="resource.scheduleRequests>0" class="badge badge-pill badge-primary">Schedule requests: {{resource.scheduleRequests}}</span>
              </a>
            </div>
          </div>
          <div class="card-footer">
            <a class="btn btn-primary" role="button" href="/resources/-1">New</a>
          </div>
        </div>
      </div>
      <div class="row mt-1">
        <div class="col card pr-0 pl-0">
          <div class="card-header h4">
            My requests
          </div>
          <div class="card-body">
            <div class="list-group list-group-flush">
              <div v-for="request in myRequests" 
                class="list-group-item list-group-item-action">
                <span>{{request.resource.title}} - {{request.start_time.substr(0,10)}} to {{request.end_time.substr(0,10)}}</span>
                <span v-if="request.approved" class="badge badge-pill badge-success">Approved</span>
                <span v-if="request.deleted_at" class="badge badge-pill badge-danger">Rejected</span>
                <span v-if="!request.approved && !request.deleted_at" class="badge badge-pill badge-primary">Pending</span>
              </div>
            </div>
          </div>
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
        allResources:[],
        myRequests:[]
      }
    },
    methods:{
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
      updateAllResources(){
        var self = this;
        $.get(
          '/resources',
          function(data){
            self.allResources = data;
          },
          'json'
        )
      },
      updateMyRequests(){
        var self = this;
        $.get(
          '/resources/myRequests',
          function(data){
            self.myRequests = data;
          },
          'json'
        )
      }
    },
    mounted(){
      this.updateGroupList();
      this.updateAllResources();
      this.updateMyRequests();
    }
  }
</script>