<?php
session_start();
include("../vars.php");
include("../headfoot.php");
include("../auth.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
$res = checkauth();
$usrid = mysql_result($res, 0, "id");
$name = mysql_result($res, 0, "name");
$email = mysql_result($res, 0, "email");
$pay_toa = mysql_result($res, 0, "pay_to");
$payout_address = mysql_result($res, 0, "payout_address");
$allow_contact = mysql_result($res, 0, "allow_contact");
secheader();
echo("<h4>Edit Your Account Information</h4>");
echo("<div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td align=center><img src=\"images/editaccount.png\" border=\"0\"><br><a href=./accountdetails.php?".session_name()."=".session_id().">Account Details</a></td></tr></table></div>");

$allow_cs = array("no" => "No", "yes" => "Yes");

echo("<p><table border=0 style=\"margin-left:10px;\"><form action=$self_url" . "members/ method=post>
<input type=hidden name=fform value=edit>
<input type=hidden name=fac value=ne>
<tr><td align=left colspan=2><b>Your Account Details:</b></td></tr>
<tr><td colspan=2>&nbsp;</td></tr>
<tr><td valign=middle><b>Current E-mail Address:</b> </td>
<td><input type=text name=emaila value=\"$email\"></td></tr>
<tr><td valign=middle><b>New E-mail Address:</b> </td>
<td><input type=text name=email1></td></tr>
<tr><td valign=middle><b>Confirm New E-mail:</b> </td>
<td><input type=text name=email2></td></tr>
<tr><td valign=middle><b>Allow Sponsor Contact?</b> </td>
<td><select name=allow_contact class=webforms>");
while (list($k, $v) = each($allow_cs)) {
	echo("\n<option value=$k");
	if ($allow_contact == $k) {echo(" selected");}
	echo(">$v</option>");
}
echo("</select></td></tr>
<tr><td colspan=2>&nbsp;</td></tr>
<tr><td valign=middle><b>Payout Address:</b> </td>
<td align=left><select name=pay_to>");
while (list($m, $j) = each($payout_merchants)) {
	echo("\n<option");
	if ($m == $pay_toa) {echo(" selected");}
	echo(" value=$m>$j</option>");
}
echo("</select>
<input type=text name=pay_out value=\"$payout_address\"></td></tr>
<tr><td>&nbsp;</td>
<td align=left>$mem_edit_special_note</td></tr>
<tr><td colspan=2>&nbsp;</td></tr>
<tr><td valign=middle><b>Your Name:</b> </td>
<td><input type=text name=uname value=\"$name\"></td></tr>
<tr><td valign=middle><b>Your Password:</b> </td>
<td><input type=password name=passwd></td></tr>
<tr><td>&nbsp;</td>
<td><input type=submit value=\" Save \" style=\"font-size: 11px; padding: 2px;\"></form></td></tr>
</table></p>

<p><table border=0 style=\"margin-left:10px;\">
<form action=$self_url" . "members/ method=post>
<input type=hidden name=fform value=edit>
<input type=hidden name=fac value=pw>
<tr><td align=left colspan=2><b>Change Your Password: </b></td></tr>
<tr><td colspan=2>&nbsp;</td></tr>
<tr><td valign=middle><b>Current Password:</b> </td>
<td><input type=password name=passwd></td></tr>
<tr><td valign=middle><b>New Password:</b> </td>
<td><input type=password name=new1></td></tr>
<tr><td valign=middle><b>Confirm New Password:</b> </td>
<td align=left><input type=password name=new2></td></tr>
<tr><td>&nbsp;</td>
<td><input type=submit value=\" Save \" style=\"font-size: 11px; padding: 2px;\"></td></form></tr>
</table></p>");

echo("<h4>&nbsp;</h4><div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td align=center><img src=\"images/editaccount.png\" border=\"0\"><br><a href=./accountdetails.php?".session_name()."=".session_id().">Account Details</a></td></tr></table></div>");
secfooter();
mysql_close;
exit;
?>
