<template>
  <div class="row">
    <div class="col">
      <div class="alert alert-danger" v-if="resource.title=='Access denied'">
        This is not your resource so you can not edit it
      </div>
      <div class="row">
        <label>Title</label>
      </div>
      <div class="row">
        <input class="form-control" type="text" :value="resource.title">
      </div>
      <div class="row">
        <label>Group Access</label>
      </div>
      <div class="row mt-1" v-for="(group, index) in groups">
        <input class="mr-2" type="checkbox" v-model="group.access" @change="setGroupAccess(index)">
        <span>{{group.name}}</span>
      </div>
    </div>
  </div>
</template>

<script>
  export default{
    props:['resource'],
    mounted(){
      this.updateGroups();
    },
    data(){
      return{
        groups:[]
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
      }
    }
  }
</script>