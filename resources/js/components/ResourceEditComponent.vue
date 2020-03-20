<template>
  <div class="row">
    <div class="col">
      <div class="alert alert-danger" v-if="resource.title=='Access denied'">
        This is not your resource so you can not edit it
      </div>
      <div class="card">
        <div class="card-header h4">
          Resource info
        </div>
        <div class="card-body">
          <div class="form-group">
            <label>Title</label>
          
            <input class="form-control" type="text" v-model="title">
          </div>
        </div>
        <div class="card-footer">
          <div class="btn btn-primary" @click="saveResourceInfo()">Save</div>
        </div>
      </div>
      <template v-if="resource.new">
        This is a new resource
      </template>
      <template v-else>
        <div class="row">
          <label>Group Access</label>
        </div>
        <div class="row mt-1" v-for="(group, index) in groups">
          <input class="mr-2" type="checkbox" v-model="group.access" @change="setGroupAccess(index)">
          <span>{{group.name}}</span>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
  export default{
    props:['resource','csrf'],
    mounted(){
      this.title = this.resource.title;
      this.updateGroups();
    },
    data(){
      return{
        groups:[],
        title:''
      }
    },
    methods:{
      setGroupAccess(index){
        var resourceId = this.resource.id;
        var groupId = this.groups[index].id;
        var access = this.groups[index].access;
        console.log("Group: "+groupId+" access to resource : "+resourceId+" should be set to "+access);
        var self = this;
        $.get(
          '/resources/groupAssignment',
          {
            groupId:groupId,
            resourceId:resourceId,
            access:access
          },
          function(data){
            if(data.error)
              alert(data.message);
            else
              self.updateGroups();
          },
          'json'
        )
      },
      updateGroups(){
        var self = this;
        $.get(
          '/resources/groups/'+this.resource.id,
          function(data){
            self.groups = data;
          },
          'json'
        );
      },
      saveResourceInfo(){
        var self = this;
        $.ajax({
          method: 'PUT',
          url:'/resources/'+self.resource.id,
          data:{
            title:self.title,
            _token:self.csrf
          },
          dataType:'json'
        })
          .done(function(data){
            console.log(JSON.stringify(data));
            if(data.id)
              window.open('/resources/'+data.id,'_self');
          });
      }
    }
  }
</script>