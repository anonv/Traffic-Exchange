<?php
session_start();
include("vars.php");
include("headfoot.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
$my_ip_add = $_SERVER['REMOTE_ADDR'];
if (isset($_GET['ref']) && is_numeric($_GET['ref'])) {
	if ($_SESSION['ref'] != $_GET['ref']) {
		$_SESSION['ref'] = $_GET['ref'];
	}
	$ref = $_SESSION['ref'];
}elseif(isset($_SESSION['ref']) && is_numeric($_SESSION['ref']) && $_SESSION['ref'] > 0){
	$ref = $_SESSION['ref'];	
}
if ($_GET['show'] == 'terms') {
	uheader();
	$res = mysql_query("select content from html where type='terms'");
	$terms = mysql_result($res, 0, "content");
	echo("<h4>Terms Of Use</h4>");
	echo("<p>$terms</p>");
	ufooter();
	mysql_close;
	exit;
} elseif ($_GET['show'] == 'privacy') {
	uheader();
	$res = mysql_query("select content from html where type='priva'");
	$privacy_policy = mysql_result($res, 0, "content");
	echo("<h4>Privacy Policy</h4>");
	echo("<p>$privacy_policy</p>");
	ufooter();
	mysql_close;
	exit;
} elseif ($_GET['show'] == 'testimonials') {
	uheader();
	$res = mysql_query("select content from html where type='testi'");
	$testimonials = mysql_result($res, 0, "content");
	echo("<h4>Member Testimonials</h4>");
	echo("<p>$testimonials</p>");
	ufooter();
	mysql_close;
	exit;
} else {
	$result = mysql_query ("SELECT * FROM banned_emails");
	$bsites = mysql_query ("SELECT * FROM banned_sites");
	$bipadds = mysql_query ("SELECT * FROM banned_ipadds");
	if (is_string($my_ip_add) && ereg('^([0-9]{1,3})\.([0-9]{1,3})\.' . '([0-9]{1,3})\.([0-9]{1,3})$', $my_ip_add, $sect)) {
		if ($sect[1] <= 255 && $sect[2] <= 255 && $sect[3] <= 255 && $sect[4] <= 255) {
			$reip = "$sect[1].$sect[2].$sect[3].$sect[4]";
			$reipa = "$sect[1].$sect[2].$sect[3].*";
			$reipb = "$sect[1].$sect[2].*.*";
			$reipc = "$sect[1].*.*.*";
		}
	}
	for ($i = 0; $i < mysql_num_rows($bipadds); $i++) {
		$theculpid = mysql_result($bipadds, $i, "id");
		$intval = mysql_result($bipadds, $i, "value");
		if ($reip == $intval || $reipa == $intval || $reipb == $intval || $reipc == $intval) {
			header("Location:index.php?gt=invip");
			mysql_close;
			exit;
		}
	}
	$langs = array("English" => "English", "Arabic" => "Arabic", "Chinese" => "Chinese", "Czech" => "Czech", "Danish" => "Danish", "Dutch" => "Dutch", "Estonian" => "Estonian", "Finnish" => "Finnish", "French" => "French", "German" => "German", "Greek" => "Greek", "Hebrew" => "Hebrew", "Hungarian" => "Hungarian", "Icelandic" => "Icelandic", "Italian" => "Italian", "Japanese" => "Japanese", "Korean" => "Korean", "Latvian" => "Latvian", "Lithuanian" => "Lithuanian", "Norwegian" => "Norwegian", "Polish" => "Polish", "Portuguese" => "Portuguese", "Romanian" => "Romanian", "Russian" => "Russian", "Spanish" => "Spanish", "Swedish" => "Swedish", "Turkish" => "Turkish");
	uheader();
	echo("<script language=\"javascript1.2\" type=\"text/javascript\">\nfunction TestURL()\n{\n	var URL = document.nu.url.value;\n	if(URL == \"\" || URL == 'http://')	{\n		alert(\"You must provide the URL before testing!\");\n		document.nu.url.focus();\n		return false;\n	}\n	var URL = 'urltest.php?url='+URL;\n	window.open(URL, '_blank' );\n	return false;\n}\n</script>\n");
	echo("<h4>Member Signup Form</h4>\n");
	if ($_POST['form'] == 'sent') {
		$emaila = trim($_POST['email1']);
		$pay_to = trim($_POST['pay_to']);
		$payout_address = trim($_POST['canpay']);
		$name = trim($_POST['name']);
		$passwd = trim($_POST['passwd']);
		$sitename = trim($_POST['sitename']);
		$url = trim($_POST['url']);
		if (ereg(',', $emaila)) {
			$recon = explode(',', $emaila);
			$emaila = trim($recon[0]);
		}
		if (ereg(',', $payout_address)) {
			$reconst = explode(',', $payout_address);
			$payout_address = trim($reconst[0]);
		}
		$checkpass = 'true';
		$error = '<p align=center><b>Please correct the following:<font color=red><br>';
		if (ereg('%', $name) || ereg('<', $name) || ereg('>', $name)) {
			$error = $error . 'Your name contains inadmissible characters<br>';
			$checkpass = 'false';
		} elseif ($name == "") {
			$error = $error . 'You must enter your name<br>';
			$checkpass = 'false';
		}
		$trimail = trim($emaila);
		$res = mysql_query("select id from user where email='$trimail'");
		if (mysql_num_rows($res) != 0) {
			$error = $error . 'Your e-mail address is already registered<br>';
			$checkpass = 'false';
		} elseif (!ereg('@', $emaila) || !ereg('.', $emaila)) {
			$error = $error . 'Your e-mail address is invalid<br>';
			$checkpass = 'false';
		} elseif ($emaila != $_POST['email2']) {
			$error = $error . 'Your e-mail address doesn\'t match<br>';
			$checkpass = 'false';
		}
		for ($i = 0; $i < mysql_num_rows($result); $i++) {
			$banned = mysql_result($result, $i, "value");
			$allow = true;
			$temp = explode("@", $banned);
			if ($temp[0] == "*") {
				$temp2 = explode("@", $emaila);
				if (trim(strtolower($temp2[1])) == trim(strtolower($temp[1]))) {
					$allow = false;
					$zban = $temp[1]; }
			} else {
				if (trim(strtolower($emaila)) == trim(strtolower($banned))) {
					$allow = false;
					$zban = $banned; }
			}
			if (!$allow) {
				$error = $error . "This email: <b>$zban</b> is a banned email address or email domain<br>";
				$checkpass = 'false';
			}
		}
		if ($payout_address == "") {
			$payout_address = "None";
		}
		if ($passwd == "") {
			$error = $error . 'You must enter your password<br>';
			$checkpass = 'false';
		} elseif (strlen($passwd) < 6) {
			$error = $error . 'Your password must be at least 6 characters long<br>';
			$checkpass = 'false';
		} elseif (ereg('%', $passwd) || ereg(' ', $passwd)) {
			$error = $error . 'Your password contains inadmissible characters<br>';
			$checkpass = 'false';
		} elseif ($passwd != $_POST['passb']) {
			$error = $error . 'Your passwords do not match!<br>';
			$checkpass = 'false';
		}
		if ($sitename == "") {
			$error = $error . 'You must enter your site name<br>';
			$checkpass = 'false';
		} else if (ereg('%', $sitename) || ereg('<', $sitename) || ereg('>', $sitename)) {
			$error = $error . 'Your site name contains inadmissible characters<br>';
			$checkpass = 'false';
		}
		if ($url == "") {
			$error = $error . 'You must enter your site URL<br>';
			$checkpass = 'false';
		} elseif (!ereg('http://', $url) || !ereg('.', $url)) {
			$error = $error . 'Your site URL is invalid<br>';
			$checkpass = 'false';
		}
		for ($i = 0; $i < mysql_num_rows($bsites); $i++) {
			$bs = mysql_result($bsites, $i, "domain");
			$site = strtolower($url);
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
				$error = $error . "This site: <b>$xban</b> is a banned affiliate URL or domain<br>";
				$checkpass = 'false';
			}
		}
		if ($_POST[termscheck] != 1) {
			$error = $error . 'Your must check and agree to our terms<br>';
			$checkpass = 'false';
		}
		if ($checkpass != 'true') {
			$error = $error . '</font></b><br></p>';
			echo($error);
		}
	}
	if ($checkpass != 'true') {
		echo("<p><table style=\"padding-left: 10px;\"><form action=$self_url" . "signup.php method=post name=nu><input type=hidden name=form value=sent>\n");
		if (isset($ref) && is_numeric($ref)) {
			echo("<input type=hidden name=ref value=$ref>");
		}else {
			$ref = 0;
		}
		echo("<tr><td valign=top>Your Name:</td><td valign=top><input value=\"$name\" type=text name=name size=20 maxlength=100></td></tr>\n");
		echo("<tr><td valign=top>Your E-mail Address:</td><td valign=top><input value=\"$email1\" type=text name=email1 size=20 maxlength=100></td></tr>\n");
		echo("<tr><td valign=top>Confirm Your E-mail address:</td><td valign=top><input value=\"$email2\" type=text name=email2 size=20 maxlength=100 class=webforms></td></tr>\n");
                                echo("<tr><td>&nbsp;</td><td>&nbsp;</td></tr>\n");
		echo("<tr><td valign=top>Password:</td><td valign=top><input type=password name=passwd size=20 maxlength=20></td></tr>\n");
		echo("<tr><td valign=top>Confirm Password:</td><td valign=top><input type=password name=passb size=20 maxlength=20></td></tr>\n");
                                echo("<tr><td>&nbsp;</td><td>&nbsp;</td></tr>\n");
		echo("<tr><td valign=top>Payout Details:</td><td valign=top><select name=pay_to>");
		while (list($m, $j) = each($payout_merchants)) {
			echo("<option");
			if ($m == $pay_to) {echo(" selected");}
			echo(" value=$m>$j</option>");
		}
		echo("</select><input type=text name=canpay size=20 maxlength=150></td></tr>\n");
                                echo("<tr><td>&nbsp;</td><td>&nbsp;</td></tr>\n");
		echo("<tr><td valign=top>Your Website Title:</td><td valign=top><input value=\"$sitename\" type=text name=sitename size=20 maxlength=255></td></tr>\n");
		echo("<tr><td valign=top>Your Website URL:</td><td valign=top><input value=\"");
		if ($url == "") {
			echo("http://");
		}else {
			echo("$url");
		}
		echo("\" type=text name=url size=20 maxlength=255> <A href=\"\" onclick=\"return TestURL();\"><b>Click Here To Test Your Website</font></b></a></td></tr>\n");
		echo("<tr><td valign=top>Your Website Language:</td><td valign=top><select name=lang>");
		while (list($k, $v) = each($langs)) {
			echo("<option");
			if ($k == $lang) {echo(" selected");}
			echo(" value=$k>$v</option>");
		}
		echo("</select></td></tr>\n");
                                echo("<tr><td>&nbsp;</td><td>&nbsp;</td></tr>\n");

                                echo("<tr valign=top><td><font color=#ff0000><b>NOT Allowed</b></font><br>
(Grounds For Account Termination)&nbsp;&nbsp;</td>
<td><b>NO Popups Or Popunders<br>
NO Adult Or Illegal Content<br>
NO Flying Ads (including exit Support windows)<br>
NO Rotators Of Any Type<br>
NO Frame Breakers<br>
NO Phishing Sites<br>
NO Paid-To-Promote<br>
NO Viruses Or Download Prompts<br>
NO Masked URLs (example: ihid.com, tinyurl.com, ect...)
</b></td></tr>\n");

                                echo("<tr><td>&nbsp;</td><td>&nbsp;</td></tr>\n");
		echo("<tr><td valign=top>Your Sponsor ID#:</td><td><label><b>");
		if ($ref > 0) {
			echo("$ref");
		} else {
			echo("None - You Will Be Automaticly Assigned One");
		}
		echo("</b><br><input type=\"radio\" name=\"allow_emails\" value=\"yes\"");
		if ($allow_emails == 'yes' || $allow_emails == "") {
			echo(" checked");
		}
		echo("> Allow Your Sponsor To Contact You?</label><br><label><input type=\"radio\" name=\"allow_emails\" value=\"no\"");
		if ($allow_emails == 'no') {
			echo(" checked");
		}
		echo("> Keep Your Email Private?</label><br></td></tr>");
                                echo("<tr><td>&nbsp;</td><td>&nbsp;</td></tr>\n");
		echo("<tr><td>&nbsp;</td><td valign=top><input type=checkbox");
		if ($termscheck == 1) {echo(" checked");}
		echo(" name=termscheck value=1> Do You Agree With The <a href=$self_url"."signup.php?show=terms target=_blank><b>Terms Of Use?</b></a></td></tr>\n");
		echo("<tr><td>&nbsp;</td><td valign=top><input type=submit value=\" Signup \" style=\"font-size: 11px; padding: 2px;\"></td></tr>\n");
		echo("</form></table></p>\n");
	} else {
		srand((double)microtime()*1000000);
		$ac = rand(10000, 1000000);
		$name = trim($name);
		$email = trim($_POST[email1]);
		$sitename = trim($sitename);
		$name = addslashes($name);
		$sitename = addslashes($sitename);
		$res = mysql_query("select value from adminprops where field='inibon'");
		$inibon = mysql_result($res, 0, "value");
		$res = mysql_query("select value from adminprops where field='insbon'");
		$insbon = mysql_result($res, 0, "value");
		if ($insbon >= 1) {
			$resq = mysql_query("select value from adminprops where field='sharec'");
			$sharec = mysql_result($resq, 0, "value");
			$insbonus = $insbon * $sharec;
		} else {
			$insbonus = 0;
		}
		if ((!isset($ref) || !is_numeric($ref)) && $allow_rand_refs == 'yes') {
			$get_rand_ref = mysql_query("SELECT id FROM user WHERE acctype>=2 order by rand() limit 1");
			if (mysql_num_rows($get_rand_ref) == 0) {
				$ref = 0;
			} else {
				$ref = mysql_result($get_rand_ref, 0);
			}
		} elseif (!isset($ref) || !is_numeric($ref)) {
			$ref = 0;
		}
		$date = date("Y-m-d H:i:s");
		$adate = date("Y-m-d");
		if ($allow_emails == "") {
			$allow_emails = 'yes';
		}
		if ($activation_pages == 0) {
			$ins_crds = $inibon;
		} else {
			$ins_crds = 0;
		}
		$new_ins = "insert into user (name, email, passwd, pay_to, payout_address, ref, acctype, credits, lifetime_credits, invested, joindate, minmax, lastaccess, allow_contact, status, ip_address, ac) values ('$name', '$email', '$passwd', $pay_to, '$payout_address', $ref, 1, $ins_crds, $ins_crds, $insbonus, '$date', 0, '$date', '$allow_emails', 'Un-verified', '$my_ip_add', $ac)";
		$res = mysql_query($new_ins) or die (mysql_error());
		$usrid = mysql_insert_id();
		$res = mysql_query("insert into site (usrid, name, url, lang, state, credits) values ($usrid, '$sitename', '$url', '$lang', 'Waiting', 0)");
		if ($ref >= 1) {$doias = mysql_query("insert into member_refs values ($usrid, $ref)"); }
		if ($insbonus > 0) {
			if ($insbon > 1) {
				$ss = "s";
			}
			$res = mysql_query("insert into investment_history (usrid, amount, descr, is_from, processor, adate) values ($usrid, $insbonus, 'Member $upgrade_title$ss : $$sharec per $upgrade_title$ss', 'Signup Bonus', '$title Admin', '$adate')");
		}
		$surpres = mysql_query("update adminprops set value=value-$ins_crds where field='surplu'");
		$surpres = mysql_query("update adminprops set value=value-$insbonus where field='csurpl'");
		$res = mysql_query("select value from admin where field='email'");
		$admail = mysql_result($res, 0, "value");
		echo(ucwords($name).", thank you for you registration!<br><br>Your $title login is: $email<br>Your $title password is: $passwd<br><br>To activate your account you have to open the following link:<br><a href=$self_url" . "activate.php?ac=$ac&i=$usrid>$self_url" . "activate.php?ac=$ac&i=$usrid</a><br>Click on the link or copy and paste it to your browser's query string.<br><br>Your refferal link  is:<br>http://$siteurl/index.php?ref=$usrid<br><br>You will earn $ref_earnings credit every time your referant views a site!<br><br>$title Admin<br>http://$siteurl/<br>$admail</p>");
		@mail($email, "Thank you for registering at $title!", ucwords($name).", thank you for registration!\n\nYour $title login is: $email\nYour $title password is: $passwd\n\nTo activate your account you have to open the following link:\n$self_url" . "activate.php?ac=$ac&i=$usrid\nClick it or copy-paste it to your browser's query string.\n\nYour refferal link  is:\nhttp://$siteurl/index.php?ref=$usrid\nYou will earn $ref_earnings credit every time your referant views a site!\n\n$title Admin\nhttp://$siteurl/\n$admail", $email_headers);
		echo("<p><b>Thank you for registering!</b><br>");
		echo("Please check your email for your account activation link.<br>The activation link for your account was sent to <b>$email</b>.</p>");
	}
	ufooter();
}
mysql_close;
exit;
?>
