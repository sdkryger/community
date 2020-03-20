<template>
  <div class="row">
    <div class="col">
      <div class="row">
        <div class="col h4">
          {{resource.title}}
        </div>
      </div>
      <div class="row">
        <div class="col">
          <ul class="list-group">
            <li class="list-group-item" v-for="item in scheduleItems">
              Start: {{item.start_time}}, End: {{item.end_time}}
            </li>
          </ul>
        </div>
      </div>
    </div>
    
  </div>
</template>

<script>
  export default{
    data(){
      return{
        message:'view data',
        scheduleItems:[]
      }
    },
    props:['csrf','resource'],
    mounted(){
      this.updateSchedule();
    },
    methods:{
      updateSchedule(){
        var self = this;
        $.get(
          '/resources/scheduleList/'+this.resource.id,
          function(data){
            self.scheduleItems = data;
          },
          'json'
        );
      }
    }
  }
</script>