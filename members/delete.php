<?php
session_start();
include("../vars.php");
include("../headfoot.php");
include("../auth.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
$res = checkauth();
$usrid = mysql_result($res, 0, "id");
$myref = mysql_result($res, 0, "ref");
if ($_POST['fform'] == 'sent') {
 if (md5($_POST['passwd']) == $_SESSION['sess_passwd']) {
  $res = mysql_query("SELECT id FROM site WHERE usrid=$usrid");
  for ($i = 0; $i < mysql_num_rows($res); $i++) {
   $sid = mysql_result($res, $i);
   @mysql_query("DELETE FROM abuse WHERE siteid=$sid");
   @mysql_query("DELETE FROM 7statsite WHERE siteid=$sid");
  }
  @mysql_query("DELETE FROM investment_history WHERE usrid=$usrid");
  @mysql_query("DELETE FROM cashout_history WHERE usrid=$usrid");
  @mysql_query("DELETE FROM comission_history WHERE paid_to=$usrid");
  @mysql_query("DELETE FROM other_history WHERE usrid=$usrid");
  @mysql_query("DELETE FROM 7stat WHERE usrid=$usrid");
  @mysql_query("DELETE FROM monthly_stats WHERE id=$usrid");
  @mysql_query("DELETE FROM ptc_tracking WHERE userid=$usrid");
  mysql_query("DELETE FROM site WHERE usrid=$usrid");
  mysql_query("DELETE FROM user WHERE id=$usrid");
  ref_shunt($usrid);
  $chkit = mysql_query("SELECT * FROM user WHERE ref=$usrid");
  if (mysql_num_rows($chkit) != 0) {
   for ($p = 0; $p < mysql_num_rows($chkit); $p++) {
    $ridd = mysql_result($chkit, $p, "id");
    $updref = mysql_query("UPDATE user SET ref=$myref WHERE id=$ridd");
   }
  }
  session_destroy();
  header("Location: $self_url?account-deleted-successfully");
  mysql_close;
  exit;
 } else {
  $error = '<p><b>Wrong password!</b></p>';
 }
}
secheader();

echo("<h4>Account Deletion</h4>");

echo("<div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td></tr></table></div>");

echo("<p><br><b>Delete Your Account:</b><br>
Please Note: This action cannot be reversed!</p>");

echo($error);

echo("<p><table width=98% border=0 cellpadding=2 style=\"margin-left:10px;\"><form action=$self_url" . "members/delete.php method=post><input type=hidden name=fform value=sent><tr>
<td align=left valign=top width=20%><b>Your Password:</b></td>
<td align=left><input type=password name=passwd class=webforms></td>
</tr><tr>
<td>&nbsp;</td>
<td align=left><input type=submit value=\" Delete Account \" style=\"font-size: 11px; padding: 2px;\"></td></tr></form></table></p>");

echo("<h4>&nbsp;</h4><div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td></tr></table></div>");
secfooter();
mysql_close;
exit;
?>
