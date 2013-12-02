function clock() {
if (!document.layers && !document.all) return;
var hours = theTime.getHours();
var minutes = theTime.getMinutes();
var seconds = theTime.getSeconds();
var m;
theTime.setSeconds( seconds+1 );
var day = theTime.getDate();
var pre = "th";
var ampm = "am";
if (day == 1) pre = "st";
if (day == 21) pre = "st";
if (day == 31) pre = "st";
if (day == 2) pre = "nd";
if (day == 22) pre = "nd";
if (day == 3) pre = "rd";
if (day == 23) pre = "rd";
if (hours == 0) hours = "12";
if (hours >= 13) ampm = "pm";
if (hours >= 13) hours = hours - "12";
if (minutes <= 9) minutes = "0" + minutes;
if (seconds <= 9) seconds = "0" + seconds;
m = theTime.getMonth();

showTime = day+pre+" "+month[ m ]+" "+theTime.getFullYear()+" "+hours + ":" + minutes + ":" + seconds + ampm;
if (document.layers) {
document.layers.disp.document.write(showTime);
document.layers.disp.document.close();
}
else
if (document.all)
disp.innerHTML = showTime;
setTimeout("clock()", 1000);
} 
