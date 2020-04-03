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
      <div class="row">
        <form action="/resources/addImage" class="col-12" method="post" enctype="multipart/form-data" v-if="this.resource.owner">
          <div class="form-group">
            <label>New image</label>
            <input type="file" name="file">
          </div>
          <input type="hidden" v-model="csrf" name="_token">
          <input type="hidden" v-model="resource.id" name="resourceId">
          <input type="submit" value="Ok">
        </form>
        <div class="col-12">
          <img :src="'/'+images[activeImageIndex].path" v-if="images.length > 0" style="max-height:300px;"><br>
          <img v-for="(image, index) in images" :src="'/'+image.path" style="max-width:80px;max-height:80px;" class="m-1"
            :class="[index == activeImageIndex ? 'border border-danger' : '']" @click="setImageIndex(index)">
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
        <template v-if="requestResponseRequired>0 && resource.owner">
          <div class="col alert alert-warning">
            <span>Unapproved requests to review: {{requestResponseRequired}}</span>
            <div class="btn btn-primary" @click="reviewRequest">Review request</div>
          </div>
          <div class="w-100"></div>
        </template>
        <div class="col">
          <calendar-component class="mb-2" name="schedule" :items="scheduleItems" :requestStatus="requestStatus"
            @selected="dateSelected" :isOwner="this.resource.owner"
            @request="requestSelected" :parentMonthNumber="this.monthNumber"
            :parentYear="this.year"></calendar-component>
        </div>
      </div>
      <div class="row border border-secondary mt-1 mb-1" v-if="resource.owner">
        <div class="col-12 h4 mt-2">
          Groups
        </div>
        <ul class="list-group col-12">
            <li class="list-group-item d-flex justify-content-between" v-for="(group, index) in groups">
              <div>{{group.name}}</div> <div>Access: <input type="checkbox" v-model="group.access" @change="setAccess(index)"></div>
            </li>
          </ul>
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
              <div>
                Requested by: {{scheduleItems[scheduleItemIndex].user.name}}
              </div>
              <div>
                Starting: {{scheduleItems[scheduleItemIndex].start.substr(0,10)}}
              </div>
              <div>
                Ending: {{scheduleItems[scheduleItemIndex].end.substr(0,10)}}
              </div>
            </span>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <template v-if="scheduleItemIndex != -1">
            <button type="button" class="btn btn-primary" @click="rejectRequest"
              v-if="!scheduleItems[scheduleItemIndex].approved">Reject</button>
            <button type="button" class="btn btn-primary" @click="approveRequest"
              v-if="!scheduleItems[scheduleItemIndex].approved">Approve</button>
            </template>
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
        scheduleItemIndex:-1,
        monthNumber:-1,
        year:-1,
        groups:[],
        images:[],
        activeImageIndex: 0
      }
    },
    props:['csrf','resource'],
    mounted(){
      this.title = this.resource.title;
      this.updateSchedule();
      if(this.resource.owner){
        this.getResourceGroups();
      }
      this.getResourceImages();
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
      getResourceGroups(){
        var self = this;
        $.get(
          '/resources/groups/'+this.resource.id,
          function(data){
            self.groups = data;
          },
          'json'
        )
      },
      getResourceImages(){
        var self = this;
        $.get(
          '/resources/images/'+this.resource.id,
          function(data){
            self.images = data;
          },
          'json'
        )
      },
      requestSelected(data){
        console.log("Request selected: "+JSON.stringify(data));
        console.log("Should process click on requestIndex: "+data.index);
        //if(!this.scheduleItems[data.index].approved){
          //console.log("This request is not approved");
          this.scheduleItemIndex = data.index;
          $("#requestModal").modal('show');
        //}
      },
      updateSchedule(){
        var self = this;
        $.get(
          '/resources/scheduleList/'+this.resource.id,
          function(data){
            console.log("got schedule items");
            for(var i=0;i<data.length;i++){
              //iterate through requests
              var numDays = data[i].resource_day_items.length;
              for(var j=0;j<data[i].resource_day_items.length;j++){
                //iterate through day items in request
                if(j==0){
                  //first day type
                  if(numDays == 1)
                    data[i].resource_day_items[j].type = 'only';
                  else
                    data[i].resource_day_items[j].type = 'start';
                }else if (j==numDays - 1){
                  //last day
                  data[i].resource_day_items[j].type = 'end';
                }else{
                  data[i].resource_day_items[j].type = 'middle';
                }
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
      setAccess(index){
        var self = this;
        $.get(
          '/resources/groupAssignment',
          {
            groupId:self.groups[index].id,
            resourceId:self.resource.id,
            access:self.groups[index].access
          },
          function(data){
            self.getResourceGroups();
          },
          'json'
        )
      },
      setImageIndex(index){
        this.activeImageIndex = index;
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
        //alert("should the request at index: "+index);
        this.scheduleItemIndex = index;
        this.monthNumber = parseInt(this.scheduleItems[index].start.substr(5,2));
        this.year = parseInt(this.scheduleItems[index].start.substr(0,4));
        $("#requestModal").modal('show');
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