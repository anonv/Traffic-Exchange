<?php
session_start();
include("../vars.php");
include("../headfoot.php");
include("../auth.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
$res = checkauth();
$usrid = mysql_result($res, 0, "id");
$acctype = mysql_result($res, 0, "acctype");
$invested = round(mysql_result($res, 0, "invested"), 2);
$roi_cash = round(mysql_result($res, 0, "roi_cash"), 3);
$lastsurfed = mysql_result($res, 0, "lastsurfed");
$lastroi = mysql_result($res, 0, "lastroi");
if ($_POST['submit'] == '  Preview  ' && $_POST['pay_meth'] != "" && $_POST['purchase'] >= 1) {
	$get_purch = mysql_query("select * from sellcredit where id=" . $_POST['purchase']);
	if (mysql_num_rows($get_purch) == 0) {
		header("Location: $self_url" . "members/upgrade.php?sell-error");
		mysql_close;
		exit;
	}
	$cost = mysql_result($get_purch, 0, "cost");
	$purch_name = mysql_result($get_purch, 0, "name");
	if (is_numeric($_POST['pay_meth']) && $_POST['pay_meth'] >= 1) {
		$m_codes = mysql_query("select * from merchant_codes WHERE id=" . $_POST['pay_meth']);
		if (mysql_num_rows($m_codes) == 0) {
			header("Location: $self_url" . "members/upgrade.php?merchant-error");
			mysql_close;
			exit;
		}
		$code = mysql_result($m_codes, 0, "code");
		$merch_name = mysql_result($m_codes, 0, "name");
		$code = str_replace('[user]', $usrid, $code);
		$code = str_replace('[cost]', $cost, $code);
		$code = str_replace('[description]', "$purch_name - Total: \$$cost - User: $usrid", $code);
		$code = str_replace('[email]', $_SESSION['sess_name'], $code);
		secheader();
echo("<h4>Account Purchases</h4>");
echo("<div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td></tr></table></div><br>");

		echo("<p>You have requested to buy <b>$purch_name</b> for <font color=red>\$$cost</font><br><br>
Please make sure to follow through your payment processor back to here to complete your order. To avoid fradulant purchases, all orders are manually approved and you can expect to see the change in your account within 48 hours.<br><br>
<b>Your Total:</b> <font color=red>\$$cost</font><br>
<b>Processor:</b> $merch_name<br><br>
$code</p>");

echo("<h4>&nbsp;</h4><div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td></tr></table></div>");
		secfooter();
	} else {
		header("Location: $self_url" . "members/upgrade.php?form-error");
		mysql_close;
		exit;
	}
} else {
	header("Location: $self_url" . "members/upgrade.php?form-error");
	mysql_close;
	exit;
}
?>
