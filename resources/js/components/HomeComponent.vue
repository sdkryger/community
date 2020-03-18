<template>
  <div class="row">
    <div class="col">
      <div class="row" v-for="group in groups">
        <div class="col" @click="editGroup(group.id)" style="cursor:pointer;">
          <span>groupId: {{group.id}}, name: {{group.name}} </span>
          <span v-if="group.joinRequests > 0" class="badge badge-pill badge-primary">Join requests: {{group.joinRequests}}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default{
    data(){
      return{
        message:'home component'
      }
    },
    props:['groups'],
    methods:{
      editGroup(id){
        var url = 'groups/'+id;
        window.open(url,'_self');
      }
    }
  }
</script>