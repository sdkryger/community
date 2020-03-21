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
      <div class="row border border-secondary">
        <div class="col h3 mt-3">
          <span>Schedule request</span><div v-if="requestedStart != '' && requestedEnd != ''" class="btn btn-primary ml-2">Send request</div>
        </div>
        <div class="w-100"></div>
        <div class="col">
          <span>Starting: </span>
          <span v-if="requestedStart ==''" class="text-warning badge badge-dark">Not selected</span>
          <span v-else class="badge badge-success text-white">{{requestedStart}}</span>
        </div>
        <div class="col">
          <span>Ending: </span>
          <span v-if="requestedEnd ==''" class="text-warning badge badge-dark">Not selected</span>
          <span v-else class="badge badge-success text-white">{{requestedEnd}}</span>
        </div>
        <div class="w-100"></div>
        <div class="col">
          <calendar-component @selected="dateSelected" name="start"></calendar-component>
        </div>
        <div class="col">
          <calendar-component @selected="dateSelected" name="end"></calendar-component>
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
        scheduleItems:[],
        requestedStart:'',
        requestedEnd:''
      }
    },
    props:['csrf','resource'],
    mounted(){
      this.updateSchedule();
    },
    methods:{
      dateSelected(data){
        console.log(JSON.stringify(data));
        if(data.name == 'start')
          this.requestedStart = data.date;
        else
          this.requestedEnd = data.date;
      },
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