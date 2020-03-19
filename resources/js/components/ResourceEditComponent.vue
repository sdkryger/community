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
        <label>Groups</label>
      </div>
      <div class="row mt-1" v-for="group in groups">
        <span>{{group.name}}</span>
        <div class="btn btn-primary btn-sm" v-if="group.access">Remove from group</div>
        <div class="btn btn-primary btn-sm" v-else>Let group access this</div>
      </div>
    </div>
  </div>
</template>

<script>
  export default{
    props:['resource'],
    mounted(){
      var self = this;
      var self = this;
      $.get(
        '/resources/groups/'+this.resource.id,
        function(data){
          self.groups = data;
        },
        'json'
      );
    },
    data(){
      return{
        groups:[]
      }
    }
  }
</script>