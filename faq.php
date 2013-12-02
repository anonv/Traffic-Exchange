<?php
session_start();
//session_register("sess_name");
//session_register("sess_passwd");
//session_register("sess_data");
include("vars.php");
include("headfoot.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
$res = mysql_query("select * from faq order by id asc");
uheader();
echo("<h4>Frequently Asked Questions (FAQs)</h4>");
for ($i = 0; $i < mysql_num_rows($res); $i++) {
	$quest = mysql_result($res, $i, "quest");
	$answ = mysql_result($res, $i, "answ");
	echo("<p><b>$quest</b><br>$answ</p>");
}
ufooter();
mysql_close;
exit;
?>
