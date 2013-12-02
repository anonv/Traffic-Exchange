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
$sharec = mysql_result(mysql_query("select value from adminprops where field='sharec'"), 0);
$sharea = mysql_result(mysql_query("select value from adminprops where field='sharea'"), 0);
if (is_numeric($_POST['amount']) && round($_POST['amount']) >= 1 && $_POST['pay_meth'] != "") {
	if ($sharec > 0 && $sharea > 0) {
		$my_shares = round($invested / $sharec);
		if ($my_shares < $sharea) {
			$allow_buy = 'yes';
		} else {
			header("Location: $self_url" . "members/upgrade.php?maximum");
			mysql_close;
			exit;
		}
		$tot_shares = $my_shares + round($_POST['amount']);
		$req_shares = round($_POST['amount']);
		$price = $req_shares * $sharec;
		if ($tot_shares > $sharea || $req_shares > $sharea) {
			header("Location: $self_url" . "members/upgrade.php?too-many");
			mysql_close;
			exit;
		}
		if ($req_shares > 1) {
			$ss = "s";
		}
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
			$code = str_replace('[cost]', $price, $code);
			$code = str_replace('[description]', "$req_shares $upgrade_title$ss @ \$$sharec per $upgrade_title - Total: \$$price - User: $usrid", $code);
			$code = str_replace('[email]', $_SESSION['sess_name'], $code);
			secheader();
echo("<h4>Upgrade Your Account</h4>");
echo("<div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td></tr></table></div><br>");

			echo("You have requested to buy <b>$req_shares</b> $upgrade_title$ss for <font color=red>\$$sharec</font> per $upgrade_title<br><br>
Please make sure to follow through your payment processor back to here to complete your order. To avoid fradulant purchases, all orders are manually approved and you can expect to see the change in your account within 48 hours.<br><br>
<b>Your Total:</b> <font color=red>\$$price</font><br>
<b>Processor:</b> $merch_name<br><br>
$code");

echo("<h4>&nbsp;</h4><div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td></tr></table></div>");
			secfooter();
		} else {
			if ($roi_cash < $price) {
				header("Location: $self_url" . "members/upgrade.php?not-enough-funds");
				mysql_close;
				exit;
			} elseif ($allow_member_roi_upgrades != 1) {
				header("Location: $self_url" . "members/upgrade.php?not-allowed");
				mysql_close;
				exit;
			}
			$date_now = date("Y-m-d");
			$datetime_now = date("Y-m-d H:i:s");
			$getdays = mysql_result(mysql_query("SELECT upg_time FROM acctype WHERE id!=1"), 0);
			$the_day = strftime("%Y-%m-%d", strtotime("$date_now + $getdays days"));
			if ($acctype == 1 && $upgrade_member_if_buy != 0) {
				$accupd = ", acctype=2, upgrade_ends='$the_day'";
			} elseif ($acctype == 2 && $upgrade_member_if_buy != 0) {
				$accupd = ", upgrade_ends='$the_day'";
			} else {
				$accupd = "";
			}
			$month_now = date("m");
			$year_now = date("Y");
			$resm = mysql_query("SELECT * FROM monthly_stats WHERE monthis=$month_now && yearis=$year_now && usrid=$usrid");
			if (mysql_num_rows($resm) != 0) {
				$ins_upd = mysql_query("UPDATE monthly_stats SET paid_out=paid_out+$price WHERE usrid=$usrid && yearis=" . date("Y") . " && monthis=" . date("m")) or die (mysql_error());
			} else {
				header("Location: $self_url" . "members/upgrade.php?system-error_try-again");
				include("../cronjob.php");
				mysql_close;
				exit;
			}
			$upd = mysql_query("INSERT INTO investment_history (usrid, amount, descr, is_from, processor, adate) VALUES ($usrid, $price, '$req_shares $upgrade_title$ss @ \$$sharec per $upgrade_title', 'Account Earnings', '$title', '$date_now')") or die (mysql_error());
			$upd = mysql_query("UPDATE user SET invested=invested+$price, roi_cash=roi_cash-$price" . $accupd . " WHERE id=$usrid");
			$csures = mysql_query("UPDATE adminprops SET value=value+$price WHERE field='csurpl'");
			if ($email_admin_if_buy == 1) {
				$left_ammmv = $roi_cash - $price;
				$ad_msg = "System here...\n\nMember Number $usrid just purchased $req_shares $upgrade_title$ss @ \$$sharec each..\n\nThey had: \$$roi_cash and now have \$$left_ammmv remaining\n\nRegards\n\n$title System\n$self_url";
				mail($private_sys_email, "User $usrid Bought $upgrade_title$ss With Earnings", $ad_msg , $email_headers);
			}
			secheader();
echo("<h4>Upgrade Your Account</h4>");
echo("<div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td></tr></table></div><br>");
			echo("<p><b>Success!</b><br>You just purchased $req_shares $upgrade_title$ss for \$$sharec each. This upgrade has been instantly added to your account.</p>");
echo("<h4>&nbsp;</h4><div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td></tr></table></div>");
			secfooter();
			mysql_close;
			exit;
		}
	} else {
		header("Location: $self_url" . "members/?not-allowed");
		mysql_close;
		exit;
	}
} else {
	header("Location: $self_url" . "members/upgrade.php?form-error");
	mysql_close;
	exit;
}
mysql_close;
exit;
?>
