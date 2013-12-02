var test_go=true;

var delay=45;

var counter=45;

var original=45;

var def_delay;



function init(s_time, d_delay) {

 delay = s_time;

 counter = s_time;

 original = s_time;

 def_delay = d_delay;

 main_go();

}



function main_go() {

   if (test_go) {

      if (counter>=0) {

          document.f.m_text.value="Wait " + counter-- + " sec.";

          timerID=setTimeout("main_go()",1000);

      }

      else {

         if(delay==original){

              document.f.m_text.value="GO!";

         }

         else{

           document.f.m_text.value="GO!";

         }

      }

   }

}



function play(){

    if(!test_go){

      test_go=true;

      main_go();

    }

return false;

}



function pause(){

    if (test_go){

       test_go=false;

       document.f.m_text.value="Press play to start";

    }

return false;

}



function up(){

       if (delay<300){

          delay=delay+5;

          counter=counter+5;

       }

return false;

}



function down(){

   if (delay>def_delay){

      delay=delay-5;

      counter=counter-5;

   }

return false;

}



function open_w(imf){

     window.open(imf);

     return false;

}
