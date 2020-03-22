<template>
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <div class="btn btn-primary" @click="decrementMonth()"> < </div>
      <div>{{monthName}} {{year}}</div>
      <div class="btn btn-primary" @click="incrementMonth()"> > </div>
    </div>
    <div class="card-body">
      <div class="row no-gutters">
        <div class="col text-center">Su</div>
        <div class="col text-center">Mo</div>
        <div class="col text-center">Tu</div>
        <div class="col text-center">We</div>
        <div class="col text-center">Th</div>
        <div class="col text-center">Fr</div>
        <div class="col text-center">Sa</div>
        <template v-for="week in daysDisplayed">
          <div class="w-100"></div>
          <template v-for="day in week">
            <div class="col text-center" :class="dayClass(day)"
              style="cursor:pointer;" @click="daySelected(day.date)">
              <span :class="[day.date == selectedDay ? 'text-primary border border-primary' : 'text-secondary']">{{day.display}}</span>
            </div>
          </template>
        </template>
      </div>
    </div>
    <div class="card-footer" v-if="timePicker">
      <div class="row no-gutters">
        <div class="col-5">
          <select v-model="hour" class="form-control">
            <option value="00">12 am</option>
            <option value="01">1 am</option>
            <option value="02">2 am</option>
            <option value="03">3 am</option>
            <option value="04">4 am</option>
            <option value="05">5 am</option>
            <option value="06">6 am</option>
            <option value="07">7 am</option>
            <option value="08">8 am</option>
            <option value="09">9 am</option>
            <option value="10">10 am</option>
            <option value="11">11 am</option>
            <option value="12">12 pm</option>
            <option value="13">1 pm</option>
            <option value="14">2 pm</option>
            <option value="15">3 pm</option>
            <option value="16">4 pm</option>
            <option value="17">5 pm</option>
            <option value="18">6 pm</option>
            <option value="19">7 pm</option>
            <option value="20">8 pm</option>
            <option value="21">9 pm</option>
            <option value="22">10 pm</option>
            <option value="23">11 pm</option>
          </select>
        </div>
        <div class="col">
          :
        </div>
        <div class="col-5">
          <select v-model="minute" class="form-control">
            <option value="00">00</option>
            <option value="15">15</option>
            <option value="30">30</option>
            <option value="45">45</option>
          </select>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default{
    props:['date','name','items','requestStatus','isOwner'],
    data(){
      return{
        monthNumber:1,
        year:2020,
        selectedDay:new Date(),
        timePicker:false,
        hour:'12',
        minute:'00',
        dateSelected:false
      }
    },
    computed:{
      monthName(){
        var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        return months[this.monthNumber-1];
      },
      firstOfMonth(){
        return new Date(Date.UTC(this.year,this.monthNumber-1,1));
      },
      dayOfWeekForStart(){
        return this.firstOfMonth.getDay();
      },
      daysInMonth(){
        return new Date(this.year,this.monthNumber,0).getDate();
      },
      firstDayDisplayed(){
        var t = new Date();
        t.setTime(this.firstOfMonth.getTime());
        return t.setTime(this.firstOfMonth.getTime() - this.dayOfWeekForStart*86400000);
      },
      daysDisplayed(){
        var days = [];
        var t = new Date();
        var s = new Date(this.firstDayDisplayed);
        for(var i=0;i<6;i++){
          var d = [];
          for (var j=0;j<7;j++){
            t.setTime(s.getTime()+(i*7+j)*86400000);
            var temp = {};
            temp.display = t.getDate();
            temp.date = new Date(t.getTime() - 86400000);
            var temp2 = new Date(t.getTime() - 1 * 86400000);
            temp.dateString = temp2.toISOString().substr(0,10);
            temp.currentMonth = t.getMonth() == this.monthNumber - 1;
            d.push(temp);
          }
          days.push(d);
        }
        return days;
      },
      selectedDateTime(){
        var s = this.selectedDay.toISOString().substr(0,10);
        if(this.timePicker)
          s = s + " "+this.hour+":"+this.minute;
        
        return s;
      }
    },
    methods:{
      incrementMonth(){
        var monthNumber = this.monthNumber + 1;
        if(monthNumber > 12){
          this.monthNumber = 1;
          this.year++;
        }else
          this.monthNumber = monthNumber;
      },
      decrementMonth(){
        var monthNumber = this.monthNumber - 1;
        if(monthNumber < 1){
          this.monthNumber = 12;
          this.year--;
        }else
          this.monthNumber = monthNumber;
      },
      daySelected(date){
        this.dateSelected = true;
        this.selectedDay = date;
        this.sendDate();
        this.checkForOwnerAction();
      },
      checkForOwnerAction(){
        if(this.isOwner){
          //console.log("should check for owner action on date: "+this.selectedDateTime);
          var requestIndex = -1;
          var date = this.selectedDateTime;
          for(var i=0;i<this.items.length;i++){
            //iterate through requests
            //console.log("looking at request at index: "+i);
            for(var j=0;j<this.items[i].resource_day_items.length;j++){
              if(this.items[i].resource_day_items[j].timestamp == date){
                requestIndex = i;
              }
            }
          }
          //console.log('requestIndex: '+requestIndex);
          if(requestIndex != -1)
            this.$emit('request',{index:requestIndex});
        }
      },
      sendDate(){
        this.$emit('selected',{date:this.selectedDateTime});
      },
      dayClass(day){
        var temp = '';
        if(day.currentMonth)
          temp += 'font-weight-bold ';
        else
          temp += 'font-weight-light ';
        if(this.items){
          for(var i=0;i<this.items.length;i++){
            for(var j=0;j<this.items[i].resource_day_items.length;j++){
              if(this.items[i].resource_day_items[j].timestamp == day.dateString){
                if(this.isOwner){
                  if(this.items[i].approved)
                    temp += 'bg-success';
                  else
                    temp += 'bg-warning';
                }else
                  temp += 'bg-dark text-light';
              }
                
            }
          }
        }
        return temp;
      }
    },
    mounted(){
      console.log("calendar mounted");
      var temp = new Date();
      this.year = temp.getFullYear();
      this.monthNumber = temp.getMonth()+1;
    }
  }
</script>