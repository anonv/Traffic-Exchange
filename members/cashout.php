<?php
session_start();
include("../vars.php");
include("../headfoot.php");
include("../auth.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
if ($allow_cashout_requests == 0) {
	echo("<head><title>$title</title><meta http-equiv=\"Refresh\" content=\"2;URL=$self_url" . "members/?not-enabled\"></head><body><font face=\"$fontface\" color=\"red\"><span style=\"font-size:250%\">$title</span><br><span style=\"font-size:150%\">This page is not enabled on $tile!</span></font></body></html>");
	mysql_close;
	exit;
}
$res = checkauth();
$usrid = mysql_result($res, 0, "id");
$acctype = mysql_result($res, 0, "acctype");
$pay_to = mysql_result($res, 0, "pay_to");
$pay_address = mysql_result($res, 0, "payout_address");
$roi_cash = round(mysql_result($res, 0, "roi_cash"), 4);
$qaqa = "select cashout from acctype where id=$acctype";
$min_cashout = mysql_result(mysql_query($qaqa), 0);
if ($min_cashout > $roi_cash) {
	echo("<head><title>$title</title><meta http-equiv=\"Refresh\" content=\"2;URL=$self_url" . "members/?not-enough-funds\"></head><body><font face=\"$fontface\" color=\"red\"><span style=\"font-size:250%\">$title</span><br><span style=\"font-size:150%\">You do not have enough funds to request a cashout at this time.</span></font></body></html>");
	mysql_close;
	exit;
}
if ($_POST['req_cash'] == 'requested' && $_POST['submit'] == '  Request Cashout  ' && is_numeric($_POST['amount']) && $_POST['amount'] >= $min_cashout && $_POST['amount'] <= $roi_cash) {
	mail($private_sys_email, "Member $usrid: Requesting \$" . $_POST['amount'] . " Cashout", "Hi $title System Here...\n\n\nMember ID: $usrid is requesting \$" . $_POST['amount'] . " cashout.\n\nTheir payout details are: $payout_merchants[$pay_to] - $pay_address\n\nTheir registered $title email: " . $_SESSION['sess_name'] . "\n\nTheir Total Cash: \$$roi_cash", $email_headers);
	secheader();
echo("<h4>Cashout Request</h4>");
echo("<div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td></tr></table></div>");
	echo("<p><br><b>SUCCESS!</b><br>You just requested \$" . $_POST['amount'] . " to be sent to you.<br>Paid to: $payout_merchants[$pay_to] - $pay_address<br><br>Please allow $title Admin time to verify your account earnings and send your cashout to your processor.</font></p>");
echo("<h4>&nbsp;</h4><div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td></tr></table></div>");
	secfooter();
	mysql_close;
	exit;
} else {
	$error = "Please make sure the amount to cashout is over \$$min_cashout  and <b>not</b> more than \$$roi_cash.";
}
secheader();
echo("<h4>Cashout Request</h4>");
echo("<div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td></tr></table></div>");
echo("<p><br><b>Member Cashout Process</b><br>");

if ($error != "") {
	echo("<font color=#ff0000>$error</font><script>alert('Please make sure the amount to cashout is over \$$min_cashout  and <b>not</b> more than \$$roi_cash.');</script><br><br>");
}
if ($pay_address == "") {
	echo("<font color=#ff0000>You need to have your payment address filled out to be able to receive a payment.</font><br><a href=/members/edit.php>Do this FIRST or you will not be paid!</a><br><br>");
} else {
	echo("You have a total of \$<b>$roi_cash</b> available to use for cashout purposes. Use the simple form below to make your request. Please allow $title Admin time to verify your account earnings and send your cashout to your processor.</p>");
	echo("<p><form name=\"formc\" method=\"post\"><table  width=98% border=0 cellpadding=2 style=\"margin-left:10px;\">
<tr>
<td><b>Cashout Amount:</b></td>
<td>$<input name=\"amount\" type=\"text\" size=\"6\" maxlength=\"11\" value=$roi_cash></td>
</tr>
<tr>
<td><b>Payout Details:</b></td>
<td><a href=edit.php title=\"Click Here To Change Your Payout Details.\">$payout_merchants[$pay_to] : $pay_address</a></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type=\"hidden\" name=\"req_cash\" value=\"requested\"><input type=\"submit\" name=\"submit\" value=\"  Request Cashout  \" style=\"font-size: 11px; padding: 2px;\"></td>
</tr></table></form></p>
<p>When a cashout request is placed, the admin will verify account earnings and then pay you through the processor and email provided by you in your account. Always make sure to keep your cashout information up-to-date or you may not get paid.</p>");
}
echo("<h4>&nbsp;</h4><div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td></tr></table></div>");
secfooter();
mysql_close;
exit;
?>
