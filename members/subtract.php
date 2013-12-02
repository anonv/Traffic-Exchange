<?php
session_start();
include("../vars.php");
include("../headfoot.php");
include("../auth.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
$res = checkauth();
$usrid = mysql_result($res, 0, "id");
$credits = mysql_result($res, 0, "credits");
$credits = round($credits, 2);
secheader();

echo("<h4>Subtract Credits From Your Websites</h4>");

echo("<div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td align=center><img src=\"images/websites.png\" border=\"0\"><br><a href=./sitelist.php?".session_name()."=".session_id().">Your Websites</a></td></tr></table></div>");


echo("<p><b>Subtract Credits From Your Webites:</b><br>
You have <b><font color=#ff0000>$credits</font></b> total account credits.<br>");

echo("<table width=98% border=0 cellpadding=2 style=\"margin-left:10px;\">
<form action=$self_url" . "members/ method=post><input type=hidden name=fform value='deallocate'>
<tr style=\"background-color: #4DA0C6\">
<td align=center><b><font color=#FFFFFF>Webite ID</font></td>
<td align=center><b><font color=#FFFFFF>Test URL</font></td>
<td align=center><b><font color=#FFFFFF>Current Credits</font></td>
<td align=center><b><font color=#FFFFFF>Subtract Credits</td>
</tr>");
$res = mysql_query("select id, url, credits, `name` from site where usrid='$usrid' order by id asc");
for ($i = 0; $i < mysql_num_rows($res); $i++) {
    $site_name = mysql_result($res, $i, "name");
    $id = mysql_result($res, $i, "id");
    $url = mysql_result($res, $i, "url");
    $scred = mysql_result($res, $i, "credits");
    $scred = round($scred, 2);
    $name = "_" . $id;
    echo("<tr style=\"background-color: #F0F8FF\">
<td align=center><b><u>$site_name</b></u></td>
<td align=center><a href='$url' target=_blank title='$url'><b>Click To Test</b></a></td>
<td align=center><b>$scred</b></td>
<td align=center><input type='hidden' name='c$name' value='$scred'><input type=text size=5 name='$name' value=0 class=webforms></td></tr>");
}
echo("<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td align=right><b>Subtract Credits:</b></td>
<td align=center><input type=image src=images/subtract.gif border=0 alt=\"Subtract Credits\"></td></tr></form></table></p>");


echo("<h4>&nbsp;</h4><div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td align=center><img src=\"images/websites.png\" border=\"0\"><br><a href=./sitelist.php?".session_name()."=".session_id().">Your Websites</a></td></tr></table></div>");
secfooter();
mysql_close;
exit;
?>
