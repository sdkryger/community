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
      <div class="row border border-secondary mt-1 mb-1">
        <div class="col h3 mt-3">
          <span>Schedule</span>
        </div>
        <div class="w-100"></div>
        <div class="col">
          <calendar-component class="mb-2" name="schedule" :items="scheduleItems"></calendar-component>
        </div>
      </div>
      <div class="row border border-secondary">
        <div class="col h3 mt-3">
          <span>Schedule request</span>
          <div v-if="requestedStart != '' && requestedEnd != '' && !requestSuccess" class="btn btn-primary ml-2" @click="sendRequest()">Send request</div>
          <div v-if="requestSuccess" class="alert alert-success">Request submitted</div>
        </div>
        <template v-if="!requestSuccess">
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
            <calendar-component @selected="dateSelected" name="start" class="mb-2"></calendar-component>
          </div>
          <div class="col">
            <calendar-component @selected="dateSelected" name="end" class="mb-2"></calendar-component>
          </div>
        </template>
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
        requestedEnd:'',
        requestSuccess:false
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
            console.log("got schedule items");
            for(var i=0;i<data.length;i++){
              for(var j=0;j<data[i].resource_day_items.length;j++){
                data[i].resource_day_items[j].timestamp = data[i].resource_day_items[j].timestamp.substr(0,10);
              }
            }
            self.scheduleItems = data;
          },
          'json'
        );
      },
      sendRequest(){
        var self = this;
        $.get(
          '/resources/scheduleRequest',
          {
            _token: self.csrf,
            resourceId: self.resource.id,
            start: self.requestedStart,
            end: self.requestedEnd
          },
          function(data){
            console.log(data);
            if(data.id){
              self.requestSuccess = true;
              self.updateSchedule();
            }
          },
          'json'
        );
      }
    }
  }
</script>