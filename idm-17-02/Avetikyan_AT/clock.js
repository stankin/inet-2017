function zero(i) {
    if (i < 10) {
        i = "0" + i;
    }
    return i;
}

function setDate(){
  var now = new Date();
  var seconds = zero(now.getSeconds());
  
document.querySelector('.sec').innerHTML = seconds;
  
  var minutes = zero(now.getMinutes());

  document.querySelector('.min').innerHTML = minutes;
  
  var hours = zero(now.getHours() % 12 || 12);
  document.querySelector('.hour').innerHTML = hours;
}
setInterval(setDate,1000);
