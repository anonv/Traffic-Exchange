<?php
session_start();
include("vars.php");
include("auth.php");
include("headfoot.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
$res = checkbarauth();
$usrid = mysql_result($res, 0, "id");
$my_investment = mysql_result($res, 0, "invested");
$my_acctype = mysql_result($res, 0, "acctype");
$my_last_roi = mysql_result($res, 0, "lastroi");
$my_ref = mysql_result($res, 0, "ref");
$lastsurfed = mysql_result($res, 0, "lastsurfed");
$dummyt = explode(" ", $lastsurfed);
if ($my_acctype == 1) {$daily_max = $surf_max_free;} else {$daily_max = $surf_max_pro;}
if ($dummyt[0] != $date) {
	$lastsurfed_time = "00:00:00";
} else {
	$lastsurfed_time = $dummyt[1];
}
$my_status = mysql_result($res, 0, "status");
$mref = $my_ref;
@mysql_free_result($res);
if ($my_acctype == 1) {
	$min_credits_to_earn = $min_credits_to_earn_free;
	$roi_conversion_ratio = $roi_conversion_ratio_free;
} else {
	$min_credits_to_earn = $min_credits_to_earn_pro;
	$roi_conversion_ratio = $roi_conversion_ratio_pro;
}
if ($_SESSION['sess_data']['usrid'] != $usrid) {
	header("Location: " . $self_url . "bar_break.php?error=session-expired");
	mysql_close;
	exit;
}
$credit_me = 'yes';
$hour = date('Y-m-d:H');
$date = date("Y-m-d");
$time_now = date("H:i:s");
if (!isset($_SESSION['sess_data']['usrid'])) {
	header("Location: " . $self_url . "bar_break.php?error=session-expired");
	mysql_close;
	exit;
}
if ($_SESSION['sess_data']['from'] != md5($_SESSION['sess_data']['surfing'])) {
	header("Location: " . $self_url . "bar_break.php?error=inv-access");
	mysql_close;
	exit;
}
$vf_time_amt = strftime("%H:%M:%S", strtotime("$lastsurfed_time + " . $_SESSION['sess_data']['reftim'] . " seconds"));
if ($vf_time_amt>$time_now) {
	$wait = $_SESSION['sess_data']['reftim'] - (time() - $_SESSION['sess_data']['time']);
	if ($wait > $_SESSION['sess_data']['reftim'] || $wait == "" || $wait < 0) {
		$wait = $_SESSION['sess_data']['reftim'];
	}
	echo("<head><title>$title</title><meta http-equiv=\"Refresh\" content=\"$wait;URL=" . $self_url . "surfbar.php?vc_val=" . md5($_SESSION['sess_data']['surf_encoder_vals']) . "\"></head><body><span style=\"font-size:150%\"><font face=$fontface>Surfing too fast! Re-connecting in $wait secs..</span></body></html>");
	mysql_close;
	exit;
}
if ($_GET['vc_val'] == 'begin' && $_GET['coder'] == md5($_SESSION['sess_data']['from'])) {
	$credit_me = 'no';
	$why = "Starting/Resuming Session";
} elseif ($_GET['vc_val'] != md5($_SESSION['sess_data']['surf_encoder_vals'])) {
	header("Location: " . $self_url . "?error=inv-access");
	mysql_close;
	exit;
}
if (!isset($_SESSION['sess_data']['time']) || (time() - $_SESSION['sess_data']['time']) >= $_SESSION['sess_data']['reftim']) {
	$_SESSION['sess_data']['time'] = time();
} else {
	$wait = $_SESSION['sess_data']['reftim'] - (time() - $_SESSION['sess_data']['time']);
	echo("<head><title>$title</title><meta http-equiv=\"Refresh\" content=\"$wait;URL=" . $self_url . "surfbar.php?vc_val=" . md5($_SESSION['sess_data']['surf_encoder_vals']) . "\"></head><body><span style=\"font-size:150%\"><font face=$fontface>Surfing too fast! Re-connecting in $wait secs..</span></body></html>");
	mysql_close;
	exit;
}
@mysql_query("update site set hour='$hour', cth=0 where hour!='$hour' && cph!=0");
$query = "select id, url from site where usrid!=" .$_SESSION['sess_data']['usrid'] . " && state='Enabled'";
if ($_SESSION['sess_data']['negact'] == 0) {$query = $query . " && credits>=1";}
$query = $query . " && (cth<cph || cph=0)";
$query = $query . " order by rand() limit 1";
$res = mysql_query($query);
if (mysql_num_rows($res) == 0) {
	$url = $default_site;
	$siteid = 0;
	@mysql_free_result($res);
} else {
	$url = mysql_result($res, 0, "url");
	$siteid = mysql_result($res, 0, "id");
	@mysql_free_result($res);
	if ($siteid != 0) {
		@mysql_query("update site set credits=credits-1, totalhits=totalhits+1, hitslastmail=hitslastmail+1, cth=cth+1 where id=$siteid");
		$res = mysql_query("select num from 7statsite where siteid=$siteid && date='$date'");
		if (mysql_num_rows($res) == 0) {
			$queryas = "insert into 7statsite (siteid, date, last_hit_time, num) values ($siteid, '$date', '$time_now', 1)";
		} else {
			$queryas = "update 7statsite set last_hit_time='$time_now', num=num+1 where siteid=$siteid && date='$date'";
		}
		@mysql_free_result($res);
		@mysql_query($queryas);
	}
}
$_SESSION['sess_data']['surf_encoder_vals'] = md5(rand(10000, 100000000));
$_SESSION['sess_data']['pgv']++;
if (!isset($delay)) {$delay = $_SESSION['sess_data']['reftim'];}
if ($credit_me == 'yes') {
	$why = "Credits Per View: <b>" . $_SESSION['sess_data']['rate'] . "</b>";
	$res = mysql_query("SELECT * FROM 7stat WHERE usrid=" . $_SESSION['sess_data']['usrid'] . " && date='$date'");
	if (mysql_num_rows($res) == 0) {
		$query = "insert into 7stat (usrid, date, time, pg_views, num) values (" . $_SESSION['sess_data']['usrid'] . ", '" . $date . "', '" . $time_now . "', 1, " . $_SESSION['sess_data']['rate'] . ")";
		$my_crds_today = $_SESSION['sess_data']['rate'];
		$iam_waiting = 'yes';
		@mysql_free_result($res);
	} else {
		$c_today = mysql_result($res, 0, "num");
		$laccess_time = mysql_result($res, 0, "time");
		$was_paid_t = mysql_result($res, 0, "received_pay");
		$thevftime = strftime("%H:%M:%S", strtotime("$laccess_time + " . $_SESSION['sess_data']['reftim'] . " seconds"));
		if ($thevftime>$time_now) {
			header("Location: $self_url" . "bar_break.php?error=cheating-timer");
			@mysql_free_result($res);
			session_destroy();
			mysql_close;
			exit;
		} elseif ($c_today >= $daily_max) {
			echo("Sorry you have earned your daily maximum of $daily_max credits!<br />Please return tomorrow!<br /><br /><a href=\"$self_url\" target=\"_top\">Login to your account here</a>");
			session_destroy();
			mysql_close;
			exit;
		} else {
			$query = "update 7stat set time='$time_now', pg_views=pg_views+1, num=num+" . $_SESSION['sess_data']['rate'] . " where usrid=" . $_SESSION['sess_data']['usrid'] . " && date='$date'";
			$my_crds_today = mysql_result($res, 0, "num") + $_SESSION['sess_data']['rate'];
			if ($was_paid_t == 'no') {
				$iam_waiting = 'yes';
			} elseif ($was_paid_t == 'yes') {
				$iam_waiting = 'no';
			}
		}
		@mysql_free_result($res);
	}
	$_SESSION['sess_data']['cts'] = $_SESSION['sess_data']['cts'] + $_SESSION['sess_data']['rate'];
	$_SESSION['sess_data']['sts']++;
}
if ($_SESSION['sess_data']['mmax'] == 0) {
	$extra_js = "top.window.moveTo(0,0);\nif (document.all) {\ntop.window.resizeTo(screen.availWidth,screen.availHeight);\n}\nelse if (document.layers||document.getElementById) {\nif\n(top.window.outerHeight<screen.availHeight||top.window.outerWidth<screen.availWidth){\ntop.window.outerHeight = screen.availHeight;\ntop.window.outerWidth = screen.availWidth;\n}\n}\nwindow.focus();\n";
}
echo("<html>\n<head>\n<title>$title</title>\n<link rel=stylesheet type=text/css href=$self_url"."bar.css>\n");
echo("<script language=\"JavaScript\">\n<!--\ndefaultStatus = '$title';\nif (parent.location.href == self.location.href) {window.location.href = 'surf.php';}\n
var counter=1+parseInt($delay);
var paused=0;

function start_time()
{
do_count();
}

function do_count()
{
   if (paused==0){
	counter--;
   }
	if (counter>=0) {document.f.stimer.value=counter;
	setTimeout(\"do_count()\",1000);
	}
   if (counter<0)
   {
      document.f.submit();
   }
}

function pause_time()
{
   paused=1-paused;
   if (paused==1) {document.f.stopgo.value='« Start Surf »';} else {document.f.stopgo.value='« Pause Surf »';}
}

function open_w(imf){

     window.open(imf);
	 return false;

}
$extra_js
//-->\n</script>\n");
if ($my_crds_today == "") {$my_crds_today = 0; }
echo("</head>\n<body onLoad=\"do_count();\"  bgcolor=\"#000000\">\n");
echo("<script language=JavaScript>window.status=\"$title\";top.frames[1].location.href = \"$url\";</script>\n");
echo("<script language=JavaScript>if (document.all) document.body.onmousedown=new Function(\"if (event.button==2||event.button==3)alert('Sorry, right click is disabled here!')\")</script>\n");
echo("<form name=f method=GET><input type=hidden name=\"".session_name()."\" value=" . session_id() . "><input type=hidden name=vc_val value=" . md5($_SESSION['sess_data']['surf_encoder_vals']) . ">
<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\"><tr>
<td width=\"20%\" valign=top><div align=\"center\">
<img src=$self_url" . "images/surflogo.jpg border=0>
</div></td>
<td width=\"40%\" valign=top><div align=\"center\">");
echo("<input type=button name=\"stimer\" size=4 style=\"border: 0px;border-style: none;Color: #FFFFFF;background-color: #000000;text-align: center; top: 0; position: relative; float: center;\" onClick=\"return false;\"> <input name=\"stopgo\" value=\"« Pause Surf »\" type=\"button\" style=\"border: 0px;border-style: none;Color: #FFFFFF;background-color: #000000;width:100px; height:20px; text-align: center; top: 0; position: relative; float: center; cursor: hand;\" onClick=\"pause_time();\"><br>
        [<a href=$self_url" . "members/ target=_top>Open Account</a>] [<a href=\"#\" onClick=\"open_w('$url');\">Open Site</a>] [<a href=report.php?s=$siteid target=_top><font color=#ff0000>Report Abuse</font></a>]");
if (isset($_SESSION['sess_data']['contex']) && $credit_me == 'yes') {
	if (($_SESSION['sess_data']['sts'] % $_SESSION['sess_data']['contex']) == 0) {
		$_SESSION['sess_data']['won'] = 'really';
		echo("<br><br><b>You have won " . $_SESSION['sess_data']['contey'] . " bonus credits!</b><br><a href=$self_url" . "bonus.php?next=" . md5($_SESSION['sess_data']['surf_encoder_vals']) . " target=_top>Click here to add them to your account!</a>\n");
	}
}
if (isset($_SESSION['sess_data']['contcx']) && $credit_me == 'yes' && $_SESSION['sess_data']['won'] != 'really') {
	if (($_SESSION['sess_data']['sts'] % $_SESSION['sess_data']['contcx']) == 0) {
		$_SESSION['sess_data']['ccwon'] = 'ccreallycc';
		echo("<br><br><b>You have won \$" . $_SESSION['sess_data']['contcy'] . " bonus cash!</b><br><a href=$self_url" . "bonus_cash.php?next=" . md5($_SESSION['sess_data']['surf_encoder_vals']) . " target=_top>Click here to credit it to your account!</a>\n");
	}
}
echo("</div></td><td width=\"40%\"><center>
ID #<b>" . $_SESSION['sess_data']['usrid'] . "</b> | $why | Credits This Surf: <b>" . $_SESSION['sess_data']['cts'] . "</b> | Credits Today: <b>$my_crds_today</b><br>
$surf_ban_rotator</center></td></tr></table>\n");
$surplus = 1;
if ($credit_me == 'yes') {
	@mysql_query("update user set credits=credits+" . $_SESSION['sess_data']['rate'] . ", lifetime_pages=lifetime_pages+1, lifetime_credits=lifetime_credits+" . $_SESSION['sess_data']['rate'] . " where id=" . $_SESSION['sess_data']['usrid']);
	if ($my_status == 'Verified' && $activation_pages >= 1) {
		$my_pages_are = mysql_result(mysql_query("SELECT lifetime_pages FROM user WHERE id=" . $_SESSION['sess_data']['usrid']), 0);
		$inibon = mysql_result(mysql_query("select value from adminprops where field='inibon'"), 0);
		if ($my_pages_are >= $activation_pages) {
			@mysql_query("update user set credits=credits+$inibon, lifetime_credits=lifetime_credits+$inibon, status='Active' where id=" . $_SESSION['sess_data']['usrid']);
			$surplus = $surplus - $inibon;
			if ($mref >= 1) {
				$accs = mysql_query("SELECT * FROM acctype");
				for ($i = 0; $i < mysql_num_rows($accs); $i++) {
					$accids = mysql_result($accs, $i, "id");
					$r_bons = mysql_result($accs, $i, "rbonuses");
					$r_bons2[$accids] = explode(",", $r_bons);
					$acc_r_bon[$accids] = count($r_bons2[$accids]);
				}
				@mysql_free_result($accs);
				if ($acc_r_bon[2] > $acc_r_bon[1]) {$greatest = 2; } elseif ($acc_r_bon[1] > $acc_r_bon[2]) {$greatest = 1; } else {$greatest = 2; }
				if ($my_ref >= 1 && ($acc_r_bon[1] > 0 || $acc_r_bon[2] > 0)) {
					$cc = 0;
					$ref_id[$cc]=$my_ref;
					for ($v = 0; $v < ($acc_r_bon[$greatest] - 1); $v++) {
						$my_ref = get_referral($my_ref);
						if(!$my_ref || $my_ref == 0)
						break;
						++$cc;
						$ref_id[$cc] = $my_ref;
					}
					credit_ref_bonuses($ref_id);
				}
			}
			echo("<center><font size=1>Your account was just awarded the signup bonus of $inibon credits! You are now set to Active status and can now earn from referrals!</font></center>");
		}
	}
	if ($my_crds_today >= $min_credits_to_earn && $iam_waiting == 'yes' && $my_investment > 0) {
		if ($my_last_roi < $date) {
			$roi_rate = round($roi_conversion_ratio / 100, 3);
			$roi_credit_return = $my_investment * $roi_rate;
			@mysql_query("UPDATE user SET roi_cash=roi_cash+$roi_credit_return, lifetot_roi=lifetot_roi+$roi_credit_return, lifetime_cash=lifetime_cash+$roi_credit_return, lastroi='$date' WHERE id=$usrid") or die(mysql_error());
			@mysql_query("UPDATE 7stat SET received_pay='yes' WHERE usrid=$usrid && date='$date' && received_pay='no'") or die (mysql_error());
			@mysql_query("UPDATE adminprops SET value=value-$roi_credit_return WHERE field='csurpl'");
			$nns = explode('-', $date);
			$yearis = $nns[0];
			$monthis = $nns[1];
			$get_stats = mysql_query("SELECT * FROM monthly_stats WHERE usrid=$usrid && monthis=$monthis && yearis=$yearis");
			if (mysql_num_rows($get_stats) != 0) {
				@mysql_query("UPDATE monthly_stats SET roi_earned=roi_earned+$roi_credit_return, days_paid_roi=days_paid_roi+1, tot_owed=tot_owed+$roi_credit_return, this_month='$date' WHERE usrid=$usrid && monthis=$monthis && yearis=$yearis") or die(mysql_error());
			} else {
				@mysql_query("INSERT INTO monthly_stats (usrid, days_paid_roi, roi_earned, tot_owed, monthis, yearis, this_month) VALUES ($usrid, 1, $roi_credit_return, $roi_credit_return, $monthis, $yearis, '$date')") or die(mysql_error());
			}
			@mysql_free_result($get_stats);
			echo("<font color=#FF0000>Your account was just awarded \$$roi_credit_return for earning $min_credits_to_earn credits in one day!</font>");
		}
	}
	$surplus = $surplus - $_SESSION['sess_data']['rate'];
	if ($mref >= 1) {
		$accs = mysql_query("SELECT id, levels FROM acctype");
		for ($d = 0; $d < mysql_num_rows($accs); $d++) {
			$accida = mysql_result($accs, $d, "id");
			$r_cbons = mysql_result($accs, $d, "levels");
			$r_cbons2[$accida] = explode(",", $r_cbons);
			$acc_r_cbon[$accida] = count($r_cbons2[$accida]);
		}
		@mysql_free_result($accs);
		if ($acc_r_cbon[2] > $acc_r_cbon[1]) {$greatesta = 2; } elseif ($acc_r_cbon[1] > $acc_r_cbon[2]) {$greatesta = 1; } else {$greatesta = 2; }
		if ($mref >= 1 && ($acc_r_cbon[1] > 0 || $acc_r_cbon[2] > 0)) {
			$ccx = 0;
			$refs_id[$ccx]=$mref;
			for ($z = 0; $z < ($acc_r_cbon[$greatesta] - 1); $z++) {
				$mref = get_referral($mref);
				if(!$mref || $mref == 0)
				break;
				++$ccx;
				$refs_id[$ccx] = $mref;
			}
			$givento_ref = credit_r_bonuses($refs_id,"credits",$_SESSION['sess_data']['rate']);
		}
		if ($givento_ref > 0) {
			@mysql_query("update user set toref=toref+$givento_ref where id=" . $_SESSION['sess_data']['usrid']);
		}
	}
}
@mysql_query("update adminprops set value=value+$surplus where field='surplu'");
$resins = @mysql_query($query);
$la = date("Y-m-d H:i:s");
@mysql_query("update user set lastaccess='$la', lastsurfed='$la' where id=" . $_SESSION['sess_data']['usrid']);
echo("</form>\n</body>\n</html>");
mysql_close;
exit;
?>
