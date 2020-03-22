<template>
  <div class="row">
    <div class="col">
      <div class="row">
        <div class="form-group col" v-if="this.resource.owner">
          <label>Title</label>
          <input class="form-control" type="text" v-model="title">
        </div>
        <div class="col h4" v-else>
          {{resource.title}}
        </div>
      </div>
      <div class="row border border-secondary mt-1 mb-1">
        <div class="col h3 mt-3">
          <span>Schedule</span> 
          <div class="btn btn-primary" v-if="requestStatus == 'notStarted'" @click="startRequest()">Create request</div>
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
        <template v-if="requestResponseRequired>0">
          <div class="col alert alert-warning">
            <span>Unapproved requests to review: {{requestResponseRequired}}</span>
            <div class="btn btn-primary" @click="reviewRequest">Review request</div>
          </div>
          <div class="w-100"></div>
        </template>
        <div class="col">
          <calendar-component class="mb-2" name="schedule" :items="scheduleItems" :requestStatus="requestStatus"
            @selected="dateSelected" :isOwner="this.resource.owner"
            @request="requestSelected"></calendar-component>
        </div>
      </div>
    </div>
    <div class="modal fade" id="requestModal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Schedule request</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <span v-if="scheduleItemIndex != -1">
              Requested by: {{scheduleItems[scheduleItemIndex].user.name}}
            </span>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" @click="rejectRequest">Reject</button>
            <button type="button" class="btn btn-primary" @click="approveRequest">Approve</button>
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
        message:'view data',
        scheduleItems:[],
        requestedStart:'',
        requestedEnd:'',
        requestStatus:'notStarted',
        title:'',
        scheduleItemIndex:-1
      }
    },
    props:['csrf','resource'],
    mounted(){
      this.title = this.resource.title;
      this.updateSchedule();
    },
    methods:{
      dateSelected(data){
        //console.log(JSON.stringify(data));
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
      requestSelected(data){
        console.log("Request selected: "+JSON.stringify(data));
        console.log("Should process click on requestIndex: "+data.index);
        if(!this.scheduleItems[data.index].approved){
          console.log("This request is not approved");
          this.scheduleItemIndex = data.index;
          $("#requestModal").modal('show');
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
      },
      approveRequest(){
        var self = this;
        $.get(
          '/resources/scheduleRequestProcess',
          {
            _token: self.csrf,
            resourceId: self.resource.id,
            action: 'approve',
            requestId: self.scheduleItems[self.scheduleItemIndex].id
          },
          function(data){
            console.log(data);
            self.scheduleItemIndex = -1;
            self.updateSchedule();

            $("#requestModal").modal('hide');
          },
          'json'
        );
      },
      rejectRequest(){
        var self = this;
        $.get(
          '/resources/scheduleRequestProcess',
          {
            _token: self.csrf,
            resourceId: self.resource.id,
            action: 'reject',
            requestId: self.scheduleItems[self.scheduleItemIndex].id
          },
          function(data){
            console.log(data);
            self.scheduleItemIndex = -1;
            self.updateSchedule();
            $("#requestModal").modal('hide');
          },
          'json'
        );
      },
      reviewRequest(){
        
        var index = -1;
        for(var i=0;i<this.scheduleItems.length;i++){
          if(!this.scheduleItems[i].approved && index == -1)
            index = i;
        }
        alert("should the request at index: "+index);
      }
    },
    computed:{
      requestResponseRequired(){
        var count = 0;
        for(var i=0;i<this.scheduleItems.length;i++){
          if(!this.scheduleItems[i].approved)
            count++;
        }
        return count;
      }
    }
  }
</script>