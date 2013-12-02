<?php
session_start();
//exit(print_r($_POST));
include("../vars.php");
include("../headfoot.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
$result = mysql_query("SELECT * FROM banned_emails");
$bsites = mysql_query("SELECT * FROM banned_sites");
$bipadds = mysql_query("SELECT * FROM banned_ipadds");
if (!isset($_SESSION['sess_name']) || !isset($_SESSION['sess_passwd'])) {
	$_SESSION = array();
	session_destroy();
	header("Location: /logout.php");
	mysql_close;
	exit;
} else {
	$res = mysql_query("select * from user where email='" . $_SESSION['sess_name'] . "'");
	if (mysql_num_rows($res) != 0) {
		$usrid = mysql_result($res, 0, "id");
		$saved_passwd = mysql_result($res, 0, "passwd");
		$saved_ac = mysql_result($res, 0, "ac");
		$my_status = mysql_result($res, 0, "status");
		//exit($_SESSION['sess_passwd']);
		if (md5($saved_passwd) != $_SESSION['sess_passwd']) {
			echo("<head><title>$title</title><meta http-equiv=\"Refresh\" content=\"1;URL=$self_url\"></head><body><font face=\"$fontface\" color=\"red\"><span style=\"font-size:250%\">$title</span><br><span style=\"font-size:150%\">Invalid login details!</span></font></body></html>");
			$_SESSION = array();
			session_destroy();
			mysql_close;
			exit;
		} elseif ($my_status == 'Suspended') {
			echo("<head><title>$title</title><meta http-equiv=\"Refresh\" content=\"10;URL=$self_url\"></head><body><font face=\"$fontface\" color=\"red\"><span style=\"font-size:250%\">$title</span><br><span style=\"font-size:150%\">Account suspended, please contact $title Admin!</span></font></body></html>");
			$_SESSION = array();
			session_destroy();
			mysql_close;
			exit;
		} elseif ($saved_ac != 0) {
			$actcode = "Your account is not yet activated, please click the link below to activate your account!\n\n$self_url" . "activate.php?ac=$saved_ac&i=$usrid\n\n<a href=\"$self_url" . "activate.php?ac=$saved_ac&i=$usrid\">AOL Users</a>";
			exit($actcode);
			if (mail($_SESSION['sess_name'], "$title password reminder", "Your $title login is: " . $_SESSION['sess_name'] . "\n\nYour $title password is: $saved_passwd\n\n$actcode" . "Regards\n\n$title Admin\nhttp://$siteurl/", $email_headers)) {
				$emaileds = "<br>Check your email for the activation link that was just resent!";
			} else {
				$emaileds = "<br>Please contact us immediately our mailer is not functioning!";
			}
			echo("<head><title>$title</title><meta http-equiv=\"Refresh\" content=\"5;URL=$self_url\"></head><body><font face=\"$fontface\" color=\"red\"><span style=\"font-size:250%\">$title</span><br><span style=\"font-size:150%\">Account not yet activated!$emaileds</span></font></body></html>");
			$_SESSION = array();
			session_destroy();
			mysql_close;
			exit;
		}
	} else {
		echo("<head><title>$title</title><meta http-equiv=\"Refresh\" content=\"1;URL=$self_url\"></head><body><font face=\"$fontface\" color=\"red\"><span style=\"font-size:250%\">$title</span><br><span style=\"font-size:150%\">User not found!</span></font></body></html>");
		$_SESSION = array();
		session_destroy();
		mysql_close;
		exit;
	}
}
$name = mysql_result($res, 0, "name");
$name = stripslashes($name);
$acctype = mysql_result($res, 0, "acctype");
$credits = mysql_result($res, 0, "credits");
$credits = round($credits, 2);
$minmax = mysql_result($res, 0, "minmax");
$invested = round(mysql_result($res, 0, "invested"), 2);
$roi_cash = round(mysql_result($res, 0, "roi_cash"), 4);
$lastsurfed = mysql_result($res, 0, "lastsurfed");
$my_pages_are = mysql_result($res, 0, "lifetime_pages");
$lastroi = mysql_result($res, 0, "lastroi");
$premregdate = mysql_result($res, 0, "premregdate");
$upgrade_ends = mysql_result($res, 0, "upgrade_ends");
$la = date("Y-m-d H:i:s");
if ($my_status == 'Inactive') {
	$status_upd = ", status='Active'";
} else {
	$status_upd = "";
}
@mysql_free_result($res);
@mysql_query("update user set lastaccess='$la'" . $status_upd . " where id=$usrid");
$query = "select name, cashout, min_sites, ";
switch ($minmax) {
	case 1:
		$query = $query . "ratemin,";
		break;
	case 0:
		$query = $query . "ratemax,";
		break;
}
$query = $query . " monthly_bonus from acctype where id=$acctype";
$res = mysql_query($query);
$accname = mysql_result($res, 0, "name");
$min_cashout = mysql_result($res, 0, "cashout");
$min_sites = mysql_result($res, 0, "min_sites");
$monthly_bonus = mysql_result($res, 0, "monthly_bonus");
switch ($minmax) {
	case 1:
		$rate = mysql_result($res, 0, "ratemin");
		$allow = 'Yes';
		break;
	case 0:
		$rate = mysql_result($res, 0, "ratemax");
		$allow = 'No';
		break;
}
@mysql_free_result($res);
if ($monthly_bonus > 0) {
	$date_now = date("Y-m-d");
	$the_day = strftime("%Y-%m-%d", strtotime("$date_now + 1 month ago"));
	if ($premregdate < $the_day) {
		@mysql_query("update user set premmp=premmp+$monthly_bonus, credits=credits+$monthly_bonus, premregdate='$date_now' where id=$usrid");
		@mysql_query("update adminprops set value=value-$monthly_bonus where field='surplu'");
		echo("<head><title>$title</title><meta http-equiv=\"Refresh\" content=\"5;URL=$self_url" . "index.php?".session_name() . "=" . session_id() . "\"></head><body><font face=\"$fontface\" color=\"red\"><span style=\"font-size:250%\">$title</span><br><span style=\"font-size:150%\">Your monthly bonus of $monthly_bonus credits was just credited your account!<br>If this page does not refresh in 5 seconds <a href=../index.php?".session_name() . "=" . session_id() . " target=_top>click here</a></span></font></body></html>");
		mysql_close;
		exit;
	}
}
$last_month = date("m") - 1;
$yearis = date("Y");
if ($last_month == 0) {
	$last_month = 12;
	$yearis = $yearis - 1;
}
$this_month = date("m");
$thisyearis = date("Y");
$sharec = mysql_result(mysql_query("select value from adminprops where field='sharec'"), 0);
$sharea = mysql_result(mysql_query("select value from adminprops where field='sharea'"), 0);
$my_shares = round($invested / $sharec);
$monthly_stats_1 = mysql_query("SELECT * FROM monthly_stats WHERE usrid=$usrid && monthis=$last_month && yearis=$yearis");
$monthly_stats_2 = mysql_query("SELECT * FROM monthly_stats WHERE usrid=$usrid && monthis=$this_month && yearis=$thisyearis");
if (mysql_num_rows($monthly_stats_1) != 0) {
	$days_paid = mysql_result($monthly_stats_1, 0, "days_paid_roi");
	$past_earnings = mysql_result($monthly_stats_1, 0, "past_earnings");
	$roi_earned = mysql_result($monthly_stats_1, 0, "roi_earned");
	$coms_earned = mysql_result($monthly_stats_1, 0, "coms_earned");
	$misc_earned = mysql_result($monthly_stats_1, 0, "misc_earned");
	$sbcash_earned = mysql_result($monthly_stats_1, 0, "sbcash_earned");
	$ptc_cash_e = mysql_result($monthly_stats_1, 0, "ptc_cash_e");
	$refptc_cash = mysql_result($monthly_stats_1, 0, "refptc_cash");
	$tot_owed = mysql_result($monthly_stats_1, 0, "tot_owed");
	$paid_out = mysql_result($monthly_stats_1, 0, "paid_out");
	$tot_owed_now = $tot_owed - $paid_out;
	if ($tot_owed_now < 0) {
		$tot_owed_now = 0.00;
	}
	$monthly_stat_show_1 = "Days paid surfing: $days_paid<br>Previous month earnings: \$$past_earnings<br>$upgrade_title earnings: \$$roi_earned<br>Surf bonus cash: \$$sbcash_earned<br>PTC cash: \$$ptc_cash_e<br>PTC cash from refs: \$$refptc_cash<br>Commission earnings: \$$coms_earned<br>Other earnings: \$$misc_earned<br>Total earnings: \$$tot_owed<br>Total paid: <font color=#FF0000>\$$paid_out</font><br><b>Final Balance Owed Last Month: <font color=#0000FF>\$$tot_owed_now</font></b>";
} else {
	$monthly_stat_show_1 = "Days paid surfing: 0<br>Previous month earnings: \$0.00<br>$upgrade_title earnings: \$0.00<br>Surf bonus cash: \$0.00<br>PTC cash: \$0.00<br>PTC cash from refs: \$0.00<br>Commission earnings: \$0.00<br>Other earnings: \$0.00<br>Total earnings: \$0.00<br>Total paid: \$0.00<br><b>Final Balance Owed Last Month: \$0.00</b>";
}
if (mysql_num_rows($monthly_stats_2) != 0) {
	$days_paid2 = mysql_result($monthly_stats_2, 0, "days_paid_roi");
	$past_earnings2 = mysql_result($monthly_stats_2, 0, "past_earnings");
	$roi_earned2 = mysql_result($monthly_stats_2, 0, "roi_earned");
	$coms_earned2 = mysql_result($monthly_stats_2, 0, "coms_earned");
	$misc_earned2 = mysql_result($monthly_stats_2, 0, "misc_earned");
	$sbcash_earned2 = mysql_result($monthly_stats_2, 0, "sbcash_earned");
	$ptc_cash_e2 = mysql_result($monthly_stats_2, 0, "ptc_cash_e");
	$refptc_cash2 = mysql_result($monthly_stats_2, 0, "refptc_cash");
	$tot_owed2 = mysql_result($monthly_stats_2, 0, "tot_owed");
	$paid_out2 = mysql_result($monthly_stats_2, 0, "paid_out");
	$tot_owed_now2 = $tot_owed2 - $paid_out2;
	if ($tot_owed_now2 < 0) {
		$tot_owed_now2 = 0.00;
	}
	$monthly_stat_show_2 = "Days paid surfing: $days_paid2<br>Previous month earnings: \$$past_earnings2<br>$upgrade_title earnings: \$$roi_earned2<br>Surf bonus cash: \$$sbcash_earned2<br>PTC cash: \$$ptc_cash_e2<br>PTC cash from refs: \$$refptc_cash2<br>Commission earnings: \$$coms_earned2<br>Other earnings: \$$misc_earned2<br>Total earnings: \$$tot_owed2<br>Total paid: <font color=#FF0000>\$$paid_out2</font><br><b>Final Balance Owed This Month: <font color=#0000FF>\$$tot_owed_now2</font></b>";
} else {
	$monthly_stat_show_2 = "Days paid surfing: 0<br>Previous month earnings: \$0.00<br>$upgrade_title earnings: \$0.00<br>Surf bonus cash: \$0.00<br>PTC cash: \$0.00<br>PTC cash from refs: \$0.00<br>Commission earnings: \$0.00<br>Other earnings: \$0.00<br>Total earnings: \$0.00<br>Total paid: \$0.00<br><b>Final Balance Owed This Month: \$0.00</b>";
}
@mysql_free_result($monthly_stats_1);
@mysql_free_result($monthly_stats_2);
$res = mysql_query("select id from site where usrid=$usrid");
$site_count = mysql_num_rows($res);
@mysql_free_result($res);
$admail = mysql_result(mysql_query("select value from admin where field='email'"), 0);
if ($_POST['fform'] == 'edit') {
	if ($_POST['fac'] == 'ne') {
		if (md5(trim($_POST['passwd'])) == $_SESSION['sess_passwd'] && $_POST['emaila'] == $_SESSION['sess_name'] && trim($_POST['uname']) != "" && !ereg('%', trim($_POST['uname'])) && ($_POST['pay_out'] == "" || (ereg('.', trim($_POST['pay_out'])) && !ereg(',', trim($_POST['pay_out']))))) {
			if (trim($_POST['email1']) != "" && trim($_POST['email1']) == $_POST['email2'] && ereg('@', trim($_POST['email1'])) && ereg('.', trim($_POST['email1'])) && !ereg(',', trim($_POST['email1'])) && mysql_result(mysql_query("select count(*) from user where email='" . trim($_POST['email1']) . "'"), 0) == 0) {
				if (get_magic_quotes_gpc() == 0) {
					$name1 = addslashes(trim($_POST['uname']));
				} else {
					$name1 = trim($_POST['uname']);
				}
				for ($i = 0; $i < mysql_num_rows($result); $i++) {
					$banned = mysql_result($result, $i, "value");
					$allow = true;
					$temp = explode("@", $banned);
					if ($temp[0] == "*") {
						$temp2 = explode("@", trim($_POST['email1']));
						if (trim(strtolower($temp2[1])) == trim(strtolower($temp[1]))) {
							$allow = false;
							$zban = $temp[1];
						}
					} else {
						if (trim(strtolower($_POST['email1'])) == trim(strtolower($banned))) {
							$allow = false;
							$zban = $banned;
						}
					}
					if (!$allow) {
						header("Location: ".$self_url."members/?error&".session_name() . "=" . session_id());
						mysql_close;
						exit;
					}
				}

				$res = mysql_query("update user set name='$name1', email='" . trim($_POST['email1']) . "', pay_to=" . $_POST['pay_to'] . ", payout_address='" . trim($_POST['pay_out']) . "', allow_contact='$_POST[allow_contact]' where id=$usrid");
				$_SESSION['sess_name'] = trim($_POST['email1']);
				@mail($_SESSION['sess_name'], "$title account details change", "New details for your $title account:\n\tE-mail: $_SESSION[sess_name]\n\tName: $uname\n\n$title Admin\nhttp://$siteurl/\n$admail", $email_headers);
				header("Location: ".$self_url."members/?done&".session_name() . "=" . session_id());
				mysql_close;
				exit;
			} else {
				if (get_magic_quotes_gpc() == 0) {
					$name1 = addslashes(trim($_POST['uname']));
				} else {
					$name1 = trim($_POST['uname']);
				}
				$res = mysql_query("update user set name='$name1', pay_to=" . $_POST['pay_to'] . ", payout_address='" . trim($_POST['pay_out']) . "', allow_contact='$_POST[allow_contact]' where id=$usrid");
					//exit("Location: ".$self_url."members/?done&".session_name() . "=" . session_id());
				header("Location: ".$self_url."members/?done&".session_name() . "=" . session_id());
				mysql_close;
				exit;
			}
		}
		header("Location: ".$self_url."members/?error&".session_name() . "=" . session_id());
		mysql_close;
		exit;
	} elseif ($_POST['fac'] == 'pw') {
		if (md5($_POST['passwd']) == $_SESSION['sess_passwd'] && trim($_POST['new1']) == trim($_POST['new2']) && trim($_POST['new1']) != "" && !ereg('%', trim($_POST['new1'])) && strlen(trim($_POST['new1'])) > 5) {
			$res = mysql_query("update user set passwd='".trim($_POST['new1'])."' where id=$usrid");
			$_SESSION['sess_passwd'] = md5(trim($_POST['new1']));
			mail($_SESSION['sess_name'], "$title password change", "Your password was changed to: ".trim($_POST['new1'])."\n\n$title Admin\nhttp://$siteurl/\n$admail", $email_headers);
			header("Location: ".$self_url."members/?done&".session_name() . "=" . session_id());
			mysql_close;
			exit;
		}
		header("Location: ".$self_url."members/?error&".session_name() . "=" . session_id());
		mysql_close;
		exit;
	}
} elseif ($_POST['fform'] == 'upgrade' && $allow_mmax != 0) {
	$res = mysql_query("update user set minmax=$_POST[uminmax] where id=$usrid");
	header("Location: ".$self_url."members/index.php?done&".session_name() . "=" . session_id());
	mysql_close;
	exit;
} elseif ($_POST['fform'] == 'allocate') {
	$res = mysql_query("select id from site where usrid=$usrid");
	for ($i = 0; $i < mysql_num_rows($res); $i++) {
		$sid = mysql_result($res, $i);
		$fname = "_" . $sid;
		if (is_numeric($_POST[$fname]) && $_POST[$fname] <= $credits && $_POST[$fname] >= 0) {
			$_POST[$fname] = round($_POST[$fname], 1);
			//exit("update site set credits=credits+".$_POST[$fname]." where id=$sid");
			$res2 = mysql_query("update site set credits=credits+".$_POST[$fname]." where id=$sid");
			$credits = $credits - $_POST[$fname];
		} elseif (is_numeric($_POST[$fname]) && $_POST[$fname] >= 0) {
			$_POST[$fname] = $credits;
			$credits = $credits -$_POST[$fname];
			$res2 = mysql_query("update site set credits=credits+".$_POST[$fname]." where id=$sid");
		}
	}
	$res = mysql_query("update user set credits=$credits where id=$usrid");
	header("Location: ".$self_url."members/?done&".session_name() . "=" . session_id());
	mysql_close;
	exit;
} elseif ($_POST['fform'] == 'site') {
	if (isset($_POST['sid']) && is_numeric($_POST['sid'])) {
		if ($_POST['sid'] == 0 && trim($_POST['sname']) != "" && trim($_POST['surl']) != "" && ereg('http://', trim($_POST['surl'])) && !ereg('%', trim($_POST['sname'])) && $site_count < $min_sites) {
			if (get_magic_quotes_gpc() == 0) {
				$sname = addslashes(trim($_POST['sname']));
			} else {
				$sname = trim($_POST['sname']);
			}
			for ($i = 0; $i < mysql_num_rows($bsites); $i++) {
				$bs = mysql_result($bsites, $i, "domain");
				$site = strtolower(trim($_POST['surl']));
				$tsite = explode("/", $site);
				$test2 = explode("?", $site);
				$allowst = true;
				if ($tsite[2] == $bs) {
					$allowst = false;
					$xban = $bs;
				} elseif ($test2[0] == $bs) {
					$allowst = false;
					$xban = $bs;
				}
				if (!$allowst) {
					header("Location: ".$self_url."members/?error=banned-site&".session_name() . "=" . session_id());
					mysql_close;
					exit;
				}
			}
			$res = mysql_query("insert into site (usrid, name, url, lang, state) values ($usrid, '$sname', '".trim($_POST['surl'])."', '$_POST[slang]', 'Waiting')");
			header("Location: ".$self_url."members/?done&".session_name() . "=" . session_id());
			mysql_close;
			exit;
		} else {
			if ($_POST['fac'] != 'trash' && trim($_POST['sname']) != "" && trim($_POST['surl']) != "" && ereg('http://', trim($_POST['surl'])) && !ereg('%', trim($_POST['sname'])) && ($acctype == 1 || is_numeric($_POST['scph']))) {
				$orgurl = mysql_result(mysql_query("select url from site where id=$_POST[sid]"), 0);
				if (get_magic_quotes_gpc() == 0) {
					$sname = addslashes(trim($_POST['sname']));
				} else {
					$sname = trim($_POST['sname']);
				}
				for ($i = 0; $i < mysql_num_rows($bsites); $i++) {
					$bs = mysql_result($bsites, $i, "domain");
					$site = strtolower(trim($_POST['surl']));
					$tsite = explode("/", $site);
					$test2 = explode("?", $site);
					$allowst = true;
					if ($tsite[2] == $bs) {
						$allowst = false;
						$xban = $bs;
					} elseif ($test2[0] == $bs) {
						$allowst = false;
						$xban = $bs;
					}
					if (!$allowst) {
						header("Location: ".$self_url."members/?error=banned-site&".session_name() . "=" . session_id());
						mysql_close;
						exit;
					}
				}
				$qur = "update site set name='$sname', url='".trim($_POST['surl'])."', lang='$_POST[slang]'";
				if ($orgurl != $surl) {
					$qur = $qur . ", state='Waiting'";
				} else {
					$qur = $qur . ", state='$sstate'";
				}
				if ($acctype != 1) {$qur = $qur . ", cph=$_POST[scph]";}
				$qur = $qur . " where id=$_POST[sid] && usrid=$usrid";
				$res = mysql_query($qur);
				header("Location: ".$self_url."members/?done&".session_name() . "=" . session_id());
				mysql_close;
				exit;
			} elseif ($_POST['fac'] == 'trash') {
				$res = mysql_query("delete from site where id=$_POST[sid] && usrid=$usrid");
				if (mysql_affected_rows() != 0) {
					$res = mysql_query("delete from abuse where siteid=$_POST[sid]");
				}
				header("Location: ".$self_url."members/?done?&".session_name() . "=" . session_id());
				mysql_close;
				exit;
			}
		}
	}
	header("Location: ".$self_url."members/?".session_name() . "=" . session_id());
	mysql_close;
	exit;
} elseif ($_POST['fform'] == 'nope') {
	header("Location: ".$self_url."members/?".session_name() . "=" . session_id());
	mysql_close;
	exit;
} else {
	secheader();
                echo("<h4>Your Account Details</h4>");

echo("<div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td align=center><img src=\"images/editaccount.png\" border=\"0\"><br><a href=./edit.php?".session_name()."=".session_id().">Edit Account Info</a></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td align=center><img src=\"images/upgrades.png\" border=\"0\"><br><a href=./upgrade.php?".session_name()."=".session_id().">Upgrades & Purchases</a></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td align=center><img src=\"images/payout.png\" border=\"0\"><br><a ");
	if ($roi_cash < $min_cashout && $allow_cashout_requests != 0) {
		echo(" href=#here onClick=\"alert('You must have $$min_cashout in your account!'); return false;\"");
	} elseif ($allow_cashout_requests == 0) {
		echo(" href=#here onClick=\"alert('Sorry, not enabled on this site.\\nWe pay via mass payouts!'); return false;\"");
	} else {
		echo(" href=./cashout.php?".session_name()."=".session_id());
	}
	echo(">Request Payout</a></td></tr></table></div>");

	$passwd = strlen($saved_passwd);
	$passwd = str_repeat('*', $passwd);
	@mysql_free_result($res);
	$res = mysql_query("select * from site where usrid=$usrid");
	$site_count = mysql_num_rows($res);
	if ($my_pages_are < $activation_pages && $my_status == 'Verified') {
		$remain_pgs = $activation_pages - $my_pages_are;
		echo("<p align=left><b>ATTENTION:</b> You need to surf <b>$remain_pgs</b> to activate your account!\n<script>alert('You must surf $activation_pages credited pages ($remain_pgs remaining) before your account is Active!\\n\\nOnce Active, you may then earn from your referrals!\\nYou will see this message each time until you surf and activate your account!'); window.focus();</script></p>");
	}
	if ($my_shares > 1) {
		$ss = "s";
	}
	echo("<p><br><b>Account Details For $name:</b><br> 
<table width=98% border=0 cellpadding=2 style=\"margin-left:10px;\">
<tr><td align=left valign=middle><b>Account:</b></td>
<td align=left valign=middle> # $usrid</td></tr>
<tr><td align=left valign=middle><b>Name:</b></td>
<td align=left valign=middle>$name</td></tr>
<tr><td align=left valign=middle><b>E-mail:</b></td>
<td align=left valign=middle>$_SESSION[sess_name]</td></tr>
<tr><td align=left valign=middle><b>Password:</b></td>
<td align=left valign=middle>$passwd</td></tr>
<tr><td align=left valign=middle><b>Account Type:</b></td>
<td align=left valign=middle>$accname</td></tr>");
	if ($acctype != 1) {
		echo("<tr><td align=left valign=middle><b>Upgrade Ends:</b></td><td align=left valign=middle>$upgrade_ends</td></tr>");
	}
echo("<tr><td align=left valign=middle><b>Account Status:</b></td>
<td align=left valign=middle>$my_status</td></tr>
<tr><td align=left valign=middle><b>Surf Rate:</b></td>
<td align=left valign=middle>$rate Credits Per Site View</td></tr>
<tr><td align=left valign=middle><b>Minimized Viewing On:</b></td>
<td align=left valign=middle>$allow</td></tr>
<tr><td align=left valign=middle><b>Account credits:</b></td>
<td align=left valign=middle>$credits</td></tr>");


echo("<tr><td align=left valign=middle><b>Upgrade Level:</b></td>
<td align=left valign=middle>\$$invested ($my_shares/$sharea $upgrade_title" . "s @ \$$sharec per $upgrade_title)</td></tr>
<tr><td align=left valign=middle><b>Cash Balance:</b></td>
<td align=left valign=middle>\$$roi_cash (Last updated: $lastroi - You can cashout at \$$min_cashout)</td></tr>");

echo("</table></p>");

echo("<p><table width=80% border=0 cellpadding=2 style=\"margin-left:10px;\"><tr>");
echo("<td valign=top><b>Earnings This Month:</b><br>
$monthly_stat_show_2</td>

<td valign=top><b>Earnings Last Month:</b><br>
$monthly_stat_show_1</td>");
echo("</tr></table></p>");


echo("<h4>&nbsp;</h4><div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td align=center><img src=\"images/editaccount.png\" border=\"0\"><br><a href=./edit.php?".session_name()."=".session_id().">Edit Account Info</a></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td align=center><img src=\"images/upgrades.png\" border=\"0\"><br><a href=./upgrade.php?".session_name()."=".session_id().">Upgrades & Purchases</a></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td align=center><img src=\"images/payout.png\" border=\"0\"><br><a ");
	if ($roi_cash < $min_cashout && $allow_cashout_requests != 0) {
		echo(" href=#here onClick=\"alert('You must have $$min_cashout in your account!'); return false;\"");
	} elseif ($allow_cashout_requests == 0) {
		echo(" href=#here onClick=\"alert('Sorry, not enabled on this site.\\nWe pay via mass payouts!'); return false;\"");
	} else {
		echo(" href=./cashout.php?".session_name()."=".session_id());
	}
	echo(">Request Payout</a></td></tr></table></div>");

	secfooter();
}
mysql_close;
exit;
?>
