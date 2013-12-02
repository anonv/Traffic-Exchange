<?php
session_start();
include("../vars.php");
include("../headfoot.php");
include("../auth.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
$res = checkauth();
$usrid = mysql_result($res, 0, "id");
$my_cash = mysql_result($res, 0, "roi_cash");
$sharec = mysql_result(mysql_query("select value from adminprops where field='sharec'"), 0);
$sharea = mysql_result(mysql_query("select value from adminprops where field='sharea'"), 0);
if ($my_cash < $sharec || $allow_referral_upgrades == 0) {
	header("Location: $self_url" . "members/referral.php?error1");
	mysql_close;
	exit;
}
if ($sharec == 0 || $sharea == 0 || $allow_referral_upgrades == 0) {
	header("Location: $self_url" . "members/referral.php?not-enabled");
	mysql_close;
	exit;
}
if ($_POST['transfer'] == 'transfer_submit' && is_numeric($_POST['refs_id']) && !ereg("<", $_POST['comments']) && !ereg(">", $_POST['comments']) && is_numeric(round($_POST['buy_amt'])) && round($_POST['buy_amt']) < $sharea && round($_POST['buy_amt']) >= 1) {
	$res = mysql_query("SELECT * FROM user where id=". $_POST['refs_id'] ." && ref=$usrid");
	if (mysql_num_rows($res) == 0) {
		header("Location: $self_url" . "members/referral.php?not-your-ref");
		mysql_close;
		exit;
	}
	$refs_name = mysql_result($res, 0, "name");
	$ref_e = mysql_result($res, 0, "email");
	$ref_acc = mysql_result($res, 0, "acctype");
	$invested = round(mysql_result($res, 0, "invested"), 2);
	$roi_cash = round(mysql_result($res, 0, "roi_cash"), 3);
	$refs_shares = round($invested / $sharec);
	if ($refs_shares < $sharea) {
		$allow_buy = 'yes';
	} else {
		header("Location: $self_url" . "members/referral.php?ref-has-maximum");
		mysql_close;
		exit;
	}

	$tot_shares = $refs_shares + round($_POST['buy_amt']);
	$req_shares = round($_POST['buy_amt']);
	$price = $req_shares * $sharec;
	if ($tot_shares > $sharea || $req_shares > $sharea) {
		header("Location: $self_url" . "members/referral.php?too-many");
		mysql_close;
		exit;
	}
	if ($req_shares > 1) {
		$ss = "s";
	}
	if ($my_cash < $price) {
		header("Location: $self_url" . "members/referral.php?not-enough-funds");
		mysql_close;
		exit;
	}
	$date_now = date("Y-m-d");
	$datetime_now = date("Y-m-d H:i:s");
	$getdays = mysql_result(mysql_query("SELECT upg_time FROM acctype WHERE id!=1"), 0);
	$the_day = strftime("%Y-%m-%d", strtotime("$date_now + $getdays days"));
	if ($ref_acc == 1) {
		$accupd = ", acctype=2, upgrade_ends='$the_day'";
	} else {
		$accupd = ", upgrade_ends='$the_day'";
	}
	$month_now = date("m");
	$year_now = date("Y");
	$resm = mysql_query("SELECT * FROM monthly_stats WHERE monthis=$month_now && yearis=$year_now && usrid=$usrid");
	if (mysql_num_rows($resm) != 0) {
		$ins_upd = mysql_query("UPDATE monthly_stats SET paid_out=paid_out+$price WHERE usrid=$usrid && yearis=" . date("Y") . " && monthis=" . date("m")) or die (mysql_error());
	} else {
		header("Location: $self_url" . "members/referral.php?system-error_try-again");
		include("../cronjob.php");
		mysql_close;
		exit;
	}
	$upd = mysql_query("INSERT INTO investment_history (usrid, amount, descr, is_from, processor, adate) VALUES (". $_POST['refs_id'] .", $price, '$req_shares $upgrade_title$ss @ \$$sharec per $upgrade_title', 'Upline Earnings', 'Upline: Member $usrid', '$date_now')") or die (mysql_error());
	$upd = mysql_query("UPDATE user SET invested=invested+$price" . $accupd . " WHERE id=" . $_POST['refs_id']);
	$upd = mysql_query("UPDATE user SET roi_cash=roi_cash-$price WHERE id=$usrid");
	$csures = mysql_query("UPDATE adminprops SET value=value+$price WHERE field='csurpl'");
	if ($email_admin_if_buy == 1) {
		$left_ammmv = $my_cash - $price;
		$ad_msg = "System here...\n\nMember Number $usrid just purchased for their downline member (User " . $_POST['refs_id'] . ") $req_shares $upgrade_title$ss @ \$$sharec each..\n\nThey had: \$$roi_cash and now have \$$left_ammmv remaining\n\nRegards\n\n$title System\n$self_url";
		mail($private_sys_email, "User $usrid Bought User " . $_POST['refs_id'] . " $upgrade_title$ss With Earnings", $ad_msg , $email_headers);
	}
	if ($_POST['comments'] == "") {
		$_POST['comments'] = "No comments were given";
	}
	mail($ref_e, "$title $upgrade_title$ss Added!", "Hi there $refs_name,\n\nYour upline has just bought $req_shares $upgrade_title$ss @ \$$sharec each.. for you!\n\nThis was their reason:\n\n" . $_POST['comments'] . "\n\nRegards\n\n$title Admin\n$self_url", $email_headers);
	secheader();
	members_main_menu($members_menu);
	echo("<p><font face=\"$fontface\" size=2>Success! You just purchased $req_shares $upgrade_title$ss @ \$$sharec each.. for your downline member ($refs_name). An email regarding the transfer was just sent them, if you added comments, they also will be included.</font></p>");
	secfooter();
	mysql_close;
	exit;
} elseif (isset($_POST['submit']) && is_numeric($_POST['refs_id'])) {
	$res = mysql_query("SELECT * FROM user where id=". $_POST['refs_id'] ." && ref=$usrid");
	if (mysql_num_rows($res) == 0) {
		header("Location: $self_url" . "members/referral.php?not-your-ref");
		mysql_close;
		exit;
	} else {
		if ($_POST['buy_amt'] < 1 || !is_numeric($_POST['buy_amt'])) {
			$_POST['buy_amt'] = 1;
		}
		$refs_name = mysql_result($res, 0, "name");
		$ref_e = mysql_result($res, 0, "email");
		$ref_acc = mysql_result($res, 0, "acctype");
		secheader();
		members_main_menu($members_menu);
		echo("<p class=big><a href=/members/ title=\"Back to main page\"><font face=$fontface size=2><b>User account #$usrid</a></b></font><font face=$fontface size=2> : : Upgrade Referral ($refs_name)</p>");
		print <<< REFUPDATER
<form name="form_upg" method="post" action="">
<table width="80%" border=0 align="center" cellpadding=2 cellspacing="1">
  <tr style="background-color: $cellbg1">
    <td align=left colspan=2><b><font face=$fontface size=4>Upgrade Referral
          ($refs_name) :</font></b></td>
  </tr><tr style="background-color: $cellbg2"><td width="44%" align=left><font face=$fontface size=2>$upgrade_title(s) to buy (for your referral) :</font></td><td width="56%" align=left><input name="buy_amt" type="text" size="6" maxlength="11" value="$_POST[buy_amt]" class=webforms></td></tr>
  <tr style="background-color: $cellbg2">
    <td align=left><font face=$fontface size=2>Additional Comments (No HTML!):</font></td>
    <td align=left><textarea name="comments" cols="40" rows="5" wrap="VIRTUAL" class=webforms>$_POST[comments]</textarea></td>
  </tr>
  <tr style="background-color: $cellbg2">
    <td align=left><font face=$fontface size=2>Upgrade :</font></td>
    <td align=left><input type="submit" name="submit_form" value="Upgrade $refs_name" class="formbutton"><input type="hidden" name="transfer" value="transfer_submit"><input type="hidden" name="refs_id" value="$_POST[refs_id]"></td>
  </tr>
</table>
</form>
REFUPDATER;
}
secfooter();
mysql_close;
exit;
} else {
	header("Location: $self_url" . "members/referral.php?error2");
	mysql_close;
	exit;
}
?>
