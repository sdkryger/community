<template>
  <div class="row">
    <div class="col">
      <div class="row">
        <div class="col h4">
          {{resource.title}}
        </div>
      </div>
      <div class="row border border-secondary mt-1 mb-1">
        <div class="col h3 mt-3">
          <span>Schedule</span> 
          <div class="btn btn-primary" v-if="requestStatus == 'notStarted'" @click="startRequest()">Request</div>
          <div class="alert alert-info" v-if="requestStatus == 'selectStart'" style="display:inline;">
            1. Select start date
          </div>
          <div class="alert alert-info" v-if="requestStatus == 'selectEnd'" style="display:inline;">
            2. Select end date (starting :{{requestedStart}})
          </div>
          <div v-if="requestStatus == 'awaitingSubmit'" class="btn btn-primary ml-2" @click="sendRequest()">Send request</div>
          <div class="alert alert-success" v-if="requestStatus == 'requestSuccess'" style="display:inline;">
            Request submitted
          </div>
        </div>
        <div class="w-100"></div>
        <div class="col">
          <calendar-component class="mb-2" name="schedule" :items="scheduleItems" :requestStatus="requestStatus"
            @selected="dateSelected" :isOwner="this.resource.owner"></calendar-component>
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
        requestedEnd:'',
        requestStatus:'notStarted'
      }
    },
    props:['csrf','resource'],
    mounted(){
      this.updateSchedule();
    },
    methods:{
      dateSelected(data){
        console.log(JSON.stringify(data));
        switch(this.requestStatus){
          case 'selectStart':
            this.requestedStart = data.date;
            this.requestStatus = 'selectEnd';
            break;
          case 'selectEnd':
            this.requestedEnd = data.date;
            this.requestStatus = 'awaitingSubmit';
            break;

        }
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
      startRequest(){
        this.requestStatus='selectStart';
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
              self.requestStatus = 'requestSuccess';
              self.updateSchedule();
            }
          },
          'json'
        );
      }
    }
  }
</script>