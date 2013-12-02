<?php
session_start();
include("../vars.php");
include("../headfoot.php");
include("../auth.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
$res = checkauth();
$usrid = mysql_result($res, 0, "id");
$res = mysql_query("select * from banner order by id asc");
secheader();

echo("<h4>Promotional Banners</h4>");

echo("<div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td></tr></table></div>");

echo("<p><br><b>Referral Banners:</b><br>
You can use these banners to refer new members to <b>$title</b>.<br>
All you need to do is copy the appropriate code into your HTML page.</p>");
for ($i = 0; $i < mysql_num_rows($res); $i++) {
	$bid = mysql_result($res, $i, "id");
	$imgurl = mysql_result($res, $i, "imgurl");
	$wh = mysql_result($res, $i, "widtheight");
	$n = $i + 1;
	echo("<p><b>Banner #$n:</b><br>
<img src=$imgurl border=0 $wh><br>
<textarea cols=57 rows=3 class=webforms><a href=$self_url?ref=$usrid><img src=$imgurl border=0 $wh></a></textarea></p>");
}

echo("<h4>&nbsp;</h4><div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td></tr></table></div>");
secfooter();
mysql_close;
exit;
?>
