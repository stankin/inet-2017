function digitalWatch() {
    var date = new Date();
    var hours = date.getHours();//0-23
    var minutes = date.getMinutes();//0-59
    var seconds = date.getSeconds();//0-59
	var session="AM";
   if(hours==0){
	   hours=12;
   }
   if(hours>12){
	   hours=hours-12;
	   session="PM";
   }
   
   hours=(hours<10)?"0"+hours:hours;
   minutes=(minutes<10)?"0"+minutes:minutes;
   seconds=(seconds<10)?"0"+seconds:seconds;
   
    document.getElementById("digital_watch").innerHTML = hours + ":" + minutes + ":" + seconds+":"+session;
    setTimeout("digitalWatch()", 1000);
  }
