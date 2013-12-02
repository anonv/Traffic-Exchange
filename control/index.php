<?php
session_start();
include("../vars.php");
include("../headfoot.php");
if(isset($_GET[a])) $a= $_GET['a'];
if(isset($_POST[a])) $a= $_POST['a'];
if(isset($_GET[y])) $y= $_GET['y'];
if(isset($_POST[y])) $y= $_POST['y'];
if(isset($_GET[s])) $s= $_GET['s'];
if(isset($_POST[s])) $y= $_POST['s'];
if(isset($_GET[u])) $u= $_GET['u'];
if(isset($_POST[u])) $u= $_POST['u'];
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
$day_now_is = date("Y-m-d");
$login_screen = '<html><head>
<title>'.$title.'</title>
<link href=style.css rel=stylesheet type=text/css></head>
<body>
<p>&nbsp;</p>
<p><center><table bgcolor=#FFFFFF width=300 cellpadding=20px border=1px bordercolor=#0000FF><tr><td>
<h1>Admin Control Login:</h1>
<p><font color=#FF0000><b>You are not logged in!</b></font><br>
Please fill out the below form to login...</p>
<p><table border="0" cellpadding="0" cellspacing="3">
<form method=post action="index.php">
<input type=hidden name=form value=sent>
<tr>
<td><b>Login Name:</b></td>
<td><input type="text" name="login" size="15"></td>
</tr>
<tr>
<td><b>Password:</b></td>
<td><input type="password" name="passwd" size="15"></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="submit" value="Login"></td>
</tr>
</form></table></p>
</td></tr></table></center></p></body></html>';

//if($_GET['y'] != 24 && !isset($_POST['login'])){$login_screen .= '<script>alert(\'Your session data was not found, please login.\');</script>';}

if ($_COOKIE['ref']) {
	setcookie("ref", $_COOKIE['ref'], time()-964000);
}
if (!isset($_SESSION["asess_name"]) || !isset($_SESSION["asess_passwd"])) {
	if ($_POST['form'] == 'sent') {
		if ($_POST['login'] == "" || $_POST['passwd'] == "") {
			echo($login_screen);
			echo("<script>alert('The admin Username or Password cannot be blank!');</script>");
			session_destroy();
			mysql_close;
			exit;
		} else {
			$_SESSION['asess_name'] = $_POST['login'];
			$_SESSION['asess_passwd'] = md5($_POST['passwd']);
			header("Location: $self_url" . "control/?".session_name()."=".session_id());
			mysql_close;
			exit;
		}
	} else {
		echo($login_screen);
		if(isset($_GET['y']) && $_GET['y'] == 0) echo("<script>alert('Your session data was not found, please login.');</script>");
		session_destroy();
		mysql_close;
		exit;
	}
} else {
	$res = mysql_query("select value from admin where field='login'");
	$db_login = mysql_result($res, 0);
	$res = mysql_query("select value from admin where field='passwd'");
	$db_passwd = mysql_result($res, 0);
	if ($_SESSION['asess_name'] != $db_login || $_SESSION['asess_passwd'] != md5($db_passwd)) {
		echo($login_screen);
		echo("<script>alert('Invalid login, please check your admin username and password.');</script>");
		session_destroy();
		mysql_close;
		exit;
	}
	$last_log = mysql_result(mysql_query("select value from admin where field='lastaccess'"), 0);
	$last_ip = mysql_result(mysql_query("select value from admin where field='lastacip'"), 0);
	$resas = mysql_query("UPDATE admin SET value='" . time() . "' where field='lastaccess'");
	$resas = mysql_query("UPDATE admin SET value='$last_log' where field='lastac'");
	$resas = mysql_query("UPDATE admin SET value='" . $_SERVER['REMOTE_ADDR'] . "' where field='lastacip'");
	$resas = mysql_query("UPDATE admin SET value='$last_ip' where field='lastip'");
}

$menu = array('Administration Homepage', 'Administration Properties', 'Webite Properties', 'Full Member List', 'Full Website List', 'Abuse Reports', 'Website Content', 'Sell Credits & More', 'Account Sales Options', 'Email Members', 'Referral Banners', 'Frequently Asked Questions', 'Featured Text Ads', 'Website Surf Stats', 'Banner & Text Ad Rotator', ' Financial Transaction Manager', 'Payment Processor Codes', 'Mass Pay Members', 'Website Utilities', 'Paid to Click Area', 'Banned Websites', 'Banned Emails', 'Banned IP Addresses', 'Log Out Of Admin');

$full_menu = array('Administration Homepage', 'Administration Properties', 'Webite Properties', 'Full Member List', 'Full Website List', 'Abuse Reports', 'Website Content', 'Sell Credits & More', 'Account Sales Options', 'Email Members', 'Referral Banners', 'Frequently Asked Questions', 'Featured Text Ads', 'Website Surf Stats', 'Banner & Text Ad Rotator', ' Financial Transaction Manager', 'Payment Processor Codes', 'Mass Pay Members', 'Website Utilities', 'Paid to Click Area', 'Banned Websites', 'Banned Emails', 'Banned IP Addresses', 'Log Out Of Admin');
if (!isset($lim) || $lim < 1 || !is_numeric($lim)) {$lim = 20;}
if (!isset($y) || $y > 23 || $y < 0) {$y = 0;}

$header = "<head>\n<title>$title</title>\n<link href=\"style.css\" rel=\"stylesheet\" type=\"text/css\"/>\n<script language=\"JavaScript\" type=\"text/javascript\">\n<!--\ndefaultStatus = \"$title Admin Area\";\n\nfunction deleteThis()\n{\nvar agree=confirm(\"Are you sure you want to delete?\\n\\nTHIS CANNOT BE UNDONE!\");\nif (agree)\nreturn true ;\nelse\nreturn false ;\n}\n\nvar month = new Array(\"January\",\"February\",\"March\",\"April\",\"May\",\"June\",\"July\",\"August\",\"September\",\"October\",\"November\",\"December\");\nvar theTime = new Date( \"".date('M, d Y H:i:s')."\");\n//-->\n</script>\n</head>\n<body>\n<center><table width=900px border=0 bgcolor=#FFFFFF align=center><tr valign=top>\n";
echo("$header");
echo("<td width=200px style=\"padding:10px;\">
<h1>Admin Navagation:</h1><p>");
while (list($key, $val) = each($menu)) {
	if ($key == $y) {echo("<b>&raquo; ");}
	echo("<a href=$self_url" . "control/index.php?y=$key&".session_name()."=".session_id().">");
	echo($val);
	if ($key == $y) {echo("</b>");}
	echo("</a>");
	echo("<br>");
}
echo("</p>");
echo("</td><td width=700px>");
$sures = mysql_query("select value from adminprops where field='surplu'");
$csures = mysql_query("select value from adminprops where field='csurpl'");
function surplus() {
	global $sures, $admincolor, $fontface, $csures;
	$surp = mysql_result($sures, 0);
	$surp = round($surp, 2);
	$csurp = mysql_result($csures, 0);
	$csurp = round($csurp, 2);
	if ($surp < 0) {$uig = 'Credits Deficiency';} else {$uig = 'Surplus Credits';}
	if ($csurp < 0) {$cuig = 'Cash Deficiency';} else {$cuig = 'Surplus Cash';}
	$last_cronjob = mysql_result(mysql_query("select value from admin where field='lstcrn'"), 0);
	echo("<p><table class=info width=\"100%\" align=center><tr><td width=50% valign=middle>
<b>$uig:</b> $surp<br>
<b>$cuig:</b> \$$csurp <br>
<b>Last CronJob:</b> $last_cronjob - <a href=\"./view.php?run=cronjob&".session_name()."=".session_id()."\" target=\"_blank\">Run CronJob</a></td>");
}

function title() {
	global $full_menu, $y;
	echo("<td width=50% valign=middle>
<b>Member Search</b><br>
<table border=0><tr><td><form action=index.php method=\"GET\"><input type=hidden name=".session_name." value=".session_id()."><input type=hidden name=y value=3><input type=text name=u> <input type=submit value=\"By ID #\"></td></form></tr>");
	echo("<tr><td>
	<form action=view.php method=\"POST\">
	<input type=hidden name=action value=finduser>
	<input type=hidden name=".session_name." value=".session_id().">
	<input type=hidden name=find_em value='ok_go'>
	<input type=hidden name=findz value='eaddr'>
	<input type=text name=valza>
	<input type=submit value=\"By Email\">
	</td></form></tr></table>");
	echo("</td></tr></table></p>");

	echo("<h1>$full_menu[$y]</h1>");
}
if ($y == 0) {
	surplus();
	title();
	$res = mysql_query("select value from admin where field='lastac'");
	$lastac = date("Y-m-d H:i:s", mysql_result($res, 0));
	$lastac2 = mysql_result($res, 0);
	$last_ip = mysql_result(mysql_query("select value from admin where field='lastip'"), 0);

	echo("<p><b>Last Administration Access:</b> $lastac<br>
<b>Accessed With IP Address:</b> $last_ip</p>");
	$res = mysql_query("select id, email from user where joindate>'$lastac'");
	//exit($lastac);
	if (mysql_num_rows($res) == 0) {
		echo("<p><b>New Users:</b><br>");
		echo("There are no new users since your last login.</p>");
	} else {
		echo("<p><b>New Users:</b><br>");
		for ($i = 0; $i < mysql_num_rows($res); $i++) {
			$id = mysql_result($res, $i, "id");
			$em = mysql_result($res, $i, "email");
			echo("<a href=$self_url" . "control/index.php?y=3&u=$id&".session_name()."=".session_id().">$em</a><br>");
		}
		echo("</p>");
	}
	$res = mysql_query("select id, url from site where state='Waiting'");
	if (mysql_num_rows($res) == 0) {
		echo("<p><b>Websites Waiting For Approval:</b><br>");
		echo("There are no websites waiting for approval since your last login.</p>");
	} else {
		$kuku = mysql_num_rows($res);
		switch ($kuku) {
			case 1:
				$there = "There is";
				$sitez = "site";
				break;
			default:
				$there = "There are";
				$sitez = "sites";
		}
		echo("<p><b>Websites Waiting For Approval:</b><br>");
		echo("$there $kuku <b>$sitez</b> waiting for approval. <a href=$self_url" . "control/?y=4&a=3&".session_name()."=".session_id().">Click Here To Approve</a></p>");
	}
	$res = mysql_query("select id, siteid, usrid from abuse where unix_timestamp(date)>'$lastac2'");
	if (mysql_num_rows($res) == 0) {
		echo("<p><b>New Abuse Reports:</b><br>");
		echo("There are no new abuse reports since your last login.</p>");
	} else {
		echo("<p><b>New abuse reports:</b><br>");
		for ($i = 0; $i < mysql_num_rows($res); $i++) {
			$id = mysql_result($res, $i, "id");
			$em = mysql_result($res, $i, "siteid");
			$et = mysql_result($res, $i, "usrid");
			echo("<a href=$self_url" . "control/?y=5&u=$id&".session_name()."=".session_id().">Site #$em by user #$et</a><br>");
		}
		echo("</p>");
	}
	echo("<p class=info><b>RECOMMENDATION:</b><br>
BEFORE you open your website to the public, it is highly recommended to go through every page of the admin area and make sure you have everything setup and ready to go.<br><br>
It is also highly recommended to make a test member account that you can also go through before you open to the public so you can get a complete grasp on all the functions of this website.<br><br>
This website is very big and there is a lot of functions to it. It is in your best interest to make sure you know what you are doing before you open to the public.</p>");


} elseif ($y == 1) {
	surplus();
	title();
	if ($_POST[a_form1] == 'sent') {
		$error = "";
		if ($_POST[a_login] == "") {
			$error = $error . "You must not leave the 'login' field blank.<br>";
		}
		if ($_POST[a_email] == "") {
			$error = $error . "You must not leave the 'e-mail' field blank.<br>";
		}
		if (md5($_POST[a_passwd]) != $_SESSION['asess_passwd']) {
			$error = $error . "Your password is wrong.<br>";
		}
		if ($error != "") {
			$error = $error . "<br>Use your browser's BACK button.";
			echo($error);
		} else {
			$res = mysql_query("update admin set value='$_POST[a_login]' where field='login'");
			$res = mysql_query("update admin set value='$_POST[a_email]' where field='email'");
			$_SESSION['asess_name'] = $_POST[a_login];
			echo("New values were successfully saved in the database.");
		}
	} elseif ($_POST[a_form2] == 'sent') {
		$error = "";
		if (md5($_POST[a_old_passwd]) != $_SESSION['asess_passwd']) {
			$error = $error . "Your old password is wrong.<br>";
		}
		if ($_POST[a_new_passwd1] == "") {
			$error = $error . "You must not leave 'new password' field blank.<br>";
		}
		if ($_POST[a_new_passwd1] != $_POST[a_new_passwd2]) {
			$error = $error . "Your new password doesn't match.<br>";
		}
		if ($error != "") {
			$error = $error . "<br>Use your browser's BACK button.";
			echo($error);
		} else {
			$res = mysql_query("update admin set value='$_POST[a_new_passwd1]' where field='passwd'");
			$_SESSION['asess_passwd'] = $_POST[a_new_passwd1];
			echo("Your password was successfully changed.");
		}
	} else {
		$res = mysql_query("select value from admin where field='email'");
		$a_email = mysql_result($res, 0);
		echo("<p><table border=0>
<form method=post action='index.php?y=1'>
<input type=hidden name=".session_name." value=".session_id().">
<input type=hidden name=a_form1 value=sent>
<tr><td valign=top>Login:</td>
<td valign=top><input type=text name=a_login value='$_SESSION[asess_name]'></td>
</tr>
<tr>
<td valign=top>E-mail:</td>
<td valign=top><input type=text name=a_email value='$a_email'></td>
</tr>
<tr>
<td valign=top>Password:</td>
<td valign=top><input type=password name=a_passwd></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type=submit value=\"Save\"> <input type=reset value=\"Reset\"></td>
</tr>
</form></table></p>

<p>&nbsp;</p>

<h1>Change Administration Password</h1>
<p><table border=0>
<form method=post action='index.php?y=1'>
<input type=hidden name=".session_name." value=".session_id().">
<input type=hidden name=a_form2 value=sent>
<tr>
<td valign=top>Old Password:</td>
<td valign=top><input type=password name=a_old_passwd></td>
</tr>
<tr>
<td valign=top>New Password:</td>
<td valign=top><input type=password name=a_new_passwd1></td>
</tr>
<tr>
<td align=right valign=top>Confirm New Password:</td>
<td align=left valign=top><input type=password name=a_new_passwd2></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type=submit value=\"Save\"></td>
</tr>
</form></table></p>");
	}



} elseif ($y == 2) {
	surplus();
	title();

echo("<p>Make sure to fill in all the properties below and click on \"Save\" at the bottom of this page. Each one of these properties are very important to running your website. Make sur you take your time and fill them all out.</p>");

	$fields = array("inibon", "insbon", "reftim", "negact", "contex", "contey", "contcx", "contcy", "sharec", "sharea", "inact");
	$newfields = array('private_sys_email','self_url','default_site','title','slogan','upgrade_title','fontface','admincolor','adminbutton','activation_pages','allow_rand_refs','allow_mmax','allow_site_validation','allow_cashout_requests','allow_member_roi_upgrades','allow_referral_upgrades','max_invest_days','min_credits_to_earn_free','roi_conversion_ratio_free','min_credits_to_earn_pro','roi_conversion_ratio_pro','surf_max_free','surf_max_pro','keep_stats','keep_site_stats','keep_refpage_stats','upgrade_member_if_buy','email_admin_if_buy','email_admin_when_roi','mem_edit_special_note','default_banner','default_link','sendweeklymail');
	if ($_POST[pform] == 'sent') {
		$error = "";
		if (!is_numeric($_POST[inact]) || !is_numeric($_POST[inibon]) || !is_numeric($_POST[insbon]) || !is_numeric($_POST[reftim]) || !is_numeric($_POST[contex]) || !is_numeric($_POST[contey]) || !is_numeric($_POST[contcx]) || !is_numeric($_POST[contcy]) || !is_numeric($_POST[sharec]) || !is_numeric($_POST[sharea])) {
			$error = $error . "All the values you enter must be numeric.<br>";
		}
		if ($_POST[inact] == "" || $_POST[inibon] == "" || $_POST[insbon] == "" || $_POST[reftim] == "" || $_POST[contex] == "" || $_POST[contey] == "" || $_POST[contcx] == "" || $_POST[contcy] == "" || $_POST[sharec] == "" || $_POST[sharea] == "") {
			$error = $error . "You must not leave any fields blank.<br>";
		}
		if ($error != "") {
			$error = $error . "<br>Use your browser's BACK button.";
			echo($error);
		} else {
			while (list($k, $v) = each($fields)) {
				$res = mysql_query("update adminprops set value='".$_POST[$v]."' where field='$v'");
			}
			echo("New values were successfully saved in the database.");
		}
		$newfieldsupdate = false;
		while (list($k, $v) = each($newfields)) {
			$res = mysql_query("update adminprops set value='".addslashes($_POST[$v])."' where field='$v'");
			if(mysql_affected_rows()>0){
				$newfieldsupdate = true;
			}
		}
		if($newfieldsupdate){
			echo "<br>Extended website properties are updated.";
		}
	} else {
		while (list($k, $v) = each($fields)) {
			$res = mysql_query("select value from adminprops where field='$v'");
			$props[$v] = mysql_result($res, 0);
		}
		reset($props);
		echo("<p><table border=0>
<form action='$self_url" . "control/?y=2' method=post>
<input type=hidden name=".session_name." value=".session_id().">
<input type=hidden name=pform value=sent>");

		while (list($k, $v) = each($props)) {
			switch ($k) {
				case 'inibon':
echo("<tr><td><b>Initial Credit Bonus:</b><br>
This value is added to every new user's account.<br>
<input type=text name=$k value=$v><br><br></td></tr>");
					break;
				case 'insbon':
echo("<tr><td><b>Initial Share Bonus:</b><br>
The amount of shares added to every new user's account (1 = 1 x \$" . $props['sharec'] . " share).<br>
<input type=text name=$k value=$v><br><br></td></tr>");
					break;
				case 'reftim':
echo("<tr><td><b>Surf Refresh Time:</b><br>
Time in seconds the viewbar is refreshed.<br>
<input type=text name=$k value=$v><br><br></td></tr>");
					break;
				case 'negact':
echo("<tr><td><b>Show Sites In Advance:</b><br>
When set to 'NO' the default site is shown if there are no active sites in surf.<br>
<select name=$k>");
					switch ($v) {
						case 0:
							echo("<option value=1>Yes</option><option value=0 selected>No</option>");
							break;
						case 1:
							echo("<option value=1 selected>Yes</option><option value=0>No</option>");
							break;
					}
echo("</select><br><br></td></tr>");
					break;
				case 'contex':
echo("<tr><td><b>Contest Credit Views:</b><br>
Number of site views user gets a bonus credits link. If set to '0', the feature is disabled.<br>
<input type=text name=$k value=$v><br><br></td></tr>");
					break;
				case 'contey':
echo("<tr><td><b>Contest Credit Bonus:</b><br>
Amount of credits user wins in viewing contest.<br>
<input type=text name=$k value=$v><br><br></td></tr>");
					break;
				case 'contcx':
echo("<tr><td><b>Contest Cash Views:</b><br>
Number of site views user gets a bonus cash link. If set to '0', the feature is disabled.<br>
<input type=text name=$k value=$v><br><br></td></tr>");
					break;
				case 'contcy':
echo("<tr><td><b>Contest Cash Bonus:</b><br>
Amount of cash user wins in viewing contest (1 = $1).<br>
<input type=text name=$k value=$v><br><br></td></tr>");
					break;
				case 'sharec':
echo("<tr><td><b>Share Amount:</b><br>
The amount of cash each share is worth (1 = $1).<br>
<input type=text name=$k value=$v><br><br></td></tr>");
					break;
				case 'sharea':
					echo("<tr><td><b>Maximum Shares:</b><br>
Maximum amount of shares members are able to purchase (Default = 1000).<br>
<input type=text name=$k value=$v><br><br></td></tr>");
					break;
				case 'inact':
echo("<tr><td><b>Inactivity Time Limit:</b><br>
Number of days before user is considered inactive.<br>
<input type=text name=$k value=$v><br><br></td></tr>");
					break;
			}
		}
		while (list($k, $v) = each($newfields)) {
			$res = mysql_query("select value from adminprops where field='$v'");
			$newprops[$v] = mysql_result($res, 0);
		}

		reset($newprops);
echo("<tr><td><h1>Extended Website Properties</h1>
<p>Make sure you fill out all of these properties too.</p></td></tr>");
		while (list($k, $v) = each($newprops)) {
			switch ($k) {
				case 'private_sys_email':
echo("<tr><td><b>Contact Email:</b><br>
Purchase confirmation and site contact email address (this is never shown to members!).<br>
<input type=text name=$k value='".stripslashes($v)."'><br><br></td></tr>");
					break;
				case 'self_url':
echo("<tr><td><b>Website URL:</b><br>
Your website URL (example: http://www.example.com/).<br>
<input type=text name=$k value='".stripslashes($v)."'><br><br></td></tr>");
					break;
				case 'title':
echo("<tr><td><b>Webite Title:</b><br>
Your website title (example: YourSiteTitle).<br>
<input type=text name=$k value='".stripslashes($v)."'><br><br></td></tr>");
					break;
				case 'slogan':
echo("<tr><td><b>Webite Slogan:</b><br>
Your website slogan (example: Get Paid For Visiting Websites).<br>
<input type=text name=$k value='".stripslashes($v)."'><br><br></td></tr>");
					break;
				case 'default_site':
echo("<tr><td><b>Default Surf Website:</b><br>
Website to show in surf when no valid user sites are available.<br>
<input type=text name=$k value='".stripslashes($v)."'><br><br></td></tr>");
					break;
				case 'upgrade_title':
echo("<tr><td><b>Name Upgrade Credits:</b><br>
Do not include the \" s \" - Program will do this for you - i.e. Upgrade Credit or Upgrade Unit etc...<br>
<input type=text name=$k value='".stripslashes($v)."'><br><br></td></tr>");
					break;
				case 'activation_pages':
echo("<tr><td><b>Pages To Surf:</b><br>
Number of pages a member must surf before signup bonus is given.<br>
<input type=text name=$k value='".stripslashes($v)."'><br><br></td></tr>");
					break;
				case 'allow_rand_refs':
echo("<tr><td><b>Allow Random Referrals:</b><br>
Allow premium members to receive random referrals.<br>
<select name=$k>");
					switch ($v) {
						case 'no':
							echo("<option value='yes'>Yes</option><option value='no' selected>No</option>");
							break;
						case 'yes':
							echo("<option value='yes' selected>Yes</option><option value='no'>No</option>");
							break;
					}
					echo("</select><br><br></td></tr>");
					break;
				case 'allow_mmax':
echo("<tr><td><b>Allow Minimized Viewing:</b><br>
Allow members to minimize the surf while surfing.<br>
<select name=$k>");
					switch ($v) {
						case 0:
							echo("<option value=1>Yes</option><option value=0 selected>No</option>");
							break;
						case 1:
							echo("<option value=1 selected>Yes</option><option value=0>No</option>");
							break;
					}
					echo("</select><br><br></td></tr>");
					break;
				case 'allow_site_validation':
echo("<tr><td><b>Allow User Validation:</b><br>
Allow member to verifiy websites waiting for approval.<br>
<select name=$k>");
					switch ($v) {
						case 0:
							echo("<option value=1>Yes</option><option value=0 selected>No</option>");
							break;
						case 1:
							echo("<option value=1 selected>Yes</option><option value=0>No</option>");
							break;
					}
					echo("</select><br><br></td></tr>");
					break;
				case 'allow_cashout_requests':
echo("<tr><td><b>Allow Cashout Requests:</b><br>
Allow members to request cashout.<br>
<select name=$k>");
					switch ($v) {
						case 0:
							echo("<option value=1>Yes</option><option value=0 selected>No</option>");
							break;
						case 1:
							echo("<option value=1 selected>Yes</option><option value=0>No</option>");
							break;
					}
					echo("</select><br><br></td></tr>");
					break;
				case 'allow_member_roi_upgrades':
echo("<tr><td><b>Allow Members To Upgrade Themself:</b><br>
Using funds from their account (if they have enugh).<br>
<select name=$k>");
					switch ($v) {
						case 0:
							echo("<option value=1>Yes</option><option value=0 selected>No</option>");
							break;
						case 1:
							echo("<option value=1 selected>Yes</option><option value=0>No</option>");
							break;
					}
					echo("</select><br><br></td></tr>");
					break;
				case 'allow_referral_upgrades':
echo("<tr><td><b>Allow Members To Upgrade Their Referrals:</b><br>
Using funds from their account (if they have enugh).<br>
<select name=$k>");
					switch ($v) {
						case 0:
							echo("<option value=1>Yes</option><option value=0 selected>No</option>");
							break;
						case 1:
							echo("<option value=1 selected>Yes</option><option value=0>No</option>");
							break;
					}
					echo("</select><br><br></td></tr>");
					break;
				case 'max_invest_days':
echo("<tr><td><b>An Upgrade Unit/Credit Is Valid For:</b><br>
The amount of days (365 = 1 year, 0 = NEVER EXPIRES) (from date of purchase).<br>
<input type=text name=$k value='".stripslashes($v)."'><br><br></td></tr>");
					break;
				case 'min_credits_to_earn_free':
echo("<tr><td><b>Credits To Earn (FREE MEMBERS):</b><br>
Suring in one day before ROI awarded for FREE MEMBERS<br>
<input type=text name=$k value=$v><br><br></td></tr>");
					break;
				case 'roi_conversion_ratio_free':
echo("<tr><td><b>FREE MEMBERS Daily ROI:</b><br>
If amount of credits above --- (i.e. if the above is 100 and below is 1, the ROI is 1% per 100 credits earned surfing in 1 day.)<br>
<input type=text name=$k value=$v><br><br></td></tr>");
					break;
				case 'min_credits_to_earn_pro':
echo("<tr><td><b>Credits To Earn UPGRADED MEMBERS:</b><br>
Suring in one day before ROI awarded for UPGRADED MEMBERS<br>
<input type=text name=$k value=$v><br><br></td></tr>");
					break;
				case 'roi_conversion_ratio_pro':
echo("<tr><td><b>UPGRADED MEMBERS Daily ROI:</b><br>
If amount of credits above --- (i.e. if the above is 100 and below is 2, the ROI is 2% per 100 credits earned surfing in 1 day).<br>
<input type=text name=$k value=$v><br><br></td></tr>");
					break;
				case 'surf_max_free':
echo("<tr><td><b>Maximum Amount Of Credits FREE MEMBER:</b><br>
Maximum amount of credits member can earn in one day.<br>
<input type=text name=$k value=$v><br><br></td></tr>");
					break;
				case 'surf_max_pro':
echo("<tr><td><b>Maximum Amount Of Credits UPGRADED Member:</b><br>
Maximum amount of credits member can earn in one day.<br>
<input type=text name=$k value=$v><br><br></td></tr>");
					break;
				case 'keep_stats':
echo("<tr><td><b>Keep Surf Stats:</b><br>
Number of days to keep surf stats (0 = FOREVER).<br>
<input type=text name=$k value=$v><br><br></td></tr>");
					break;
				case 'keep_site_stats':
echo("<tr><td><b>Keep Site Hit Stats:</b><br>
Number of days to keep surf site hit stats (0 = FOREVER).<br>
<input type=text name=$k value=$v><br><br></td></tr>");
					break;
				case 'keep_refpage_stats':
echo("<tr><td><b>Referral Page Stats:</b><br>
Number of days to keep referral page stats (0 = FOREVER).<br>
<input type=text name=$k value=$v><br><br></td></tr>");
					break;
				case 'upgrade_member_if_buy':
echo("<tr><td><b>Upgrade Member:</b><br>
Upgrade member to Upgraded Status if they use their earnings to buy at least 1 share.<br>
<select name=$k>");
					switch ($v) {
						case 0:
							echo("<option value=1>Yes</option><option value=0 selected>No</option>");
							break;
						case 1:
							echo("<option value=1 selected>Yes</option><option value=0>No</option>");
							break;
					}
					echo("</select><br><br></td></tr>");
					break;
				case 'email_admin_if_buy':
echo("<tr><td><b>Email Yourself Information:</b><br>
Email sent to the email you specified, if a member purchases shares with personal earnings.<br>
<select name=$k>");
					switch ($v) {
						case 0:
							echo("<option value=1>Yes</option><option value=0 selected>No</option>");
							break;
						case 1:
							echo("<option value=1 selected>Yes</option><option value=0>No</option>");
							break;
					}
					echo("</select><br><br></td></tr>");
					break;
				case 'email_admin_when_roi':
echo("<tr><td><b>Email Yourself Daily ROI:</b><br>
Email sent to the email you specified, when daily ROI is paid.<br>
<select name=$k>");
					switch ($v) {
						case 0:
							echo("<option value=1>Yes</option><option value=0 selected>No</option>");
							break;
						case 1:
							echo("<option value=1 selected>Yes</option><option value=0>No</option>");
							break;
					}
					echo("</select><br><br></td></tr>");
					break;
				case 'mem_edit_special_note':
echo("<tr><td><b>Member Edit Page Special Note:</b><br>
A reminder note for your members.<br>
<input type=text name=$k value='".stripslashes($v)."'><br><br></td></tr>");
					break;
				case 'default_banner':
echo("<tr><td><b>Site Default Banner Image:</b><br>
Default banner for use when no banners are in the banner rotator.<br>
<input type=text name=$k value='".stripslashes($v)."'><br><br></td></tr>");
					break;
				case 'default_link':
echo("<tr><td><b>Site Default Banner URL:</b><br>
Default banner for use when no banners are in the banner rotator.<br>
<input type=text name=$k value='".stripslashes($v)."'><br><br></td></tr>");
					break;
				case 'sendweeklymail':
echo("<tr><td><b>Send Weekly Mail:</b><br>
Send weekly mail to members with detail of account statistics.<br>
<select name=$k>");
					switch ($v) {
						case 0:
							echo("<option value=1>Yes</option><option value=0 selected>No</option>");
							break;
						default:
							echo("<option value=1 selected>Yes</option><option value=0>No</option>");
							break;
					}
					echo("</select><br><br></td></tr>");
					break;
			}
		}

		echo("<tr><td><input type=submit value=\"Save\"> <input type=reset value=\"Reset\"></td></tr>");

		echo("</table></p><p>");
	}



} elseif ($y == 3) {
	surplus();
	title();
	if (!isset($u) || !is_numeric($u)) {
		$actypes[0] = "All Members";
		$res = mysql_query("select id, name from acctype order by id asc");
		for ($i = 0; $i < mysql_num_rows($res); $i++) {
			$actypes[mysql_result($res, $i, "id")] = mysql_result($res, $i, "name");
		}
		$inact = count($actypes);
		while(!in_array("Inactive", $actypes)) {
			if (!isset($actypes[$inact])) {
				$actypes[$inact] = "Inactive";
			} else {
				$inact++;
			}
		}
		if (!isset($a) || !is_numeric($a)) {
			$a = 0;
			echo("<p>");
		}
		echo(" | ");
		reset($actypes);
		while (list($k, $v) = each($actypes)) {
			if ($k == $a) {echo("<b>");}
			else {echo("<a href=./index.php?y=3&a=$k>");}
			echo($v);
			if ($k == $a) {echo("</b>");}
			else {echo("</a>");}
			echo(" | ");
		}
		echo("</p>");
		if (!isset($s) || $s < 1 || !is_numeric($s)) {
			$s = 1;
		}
		$start = ($s - 1) * $lim;
		$fquery = "select id, name, email, acctype from user";
		if ($a == $inact) {
			$inactset = mysql_result(mysql_query("select `value` from adminprops where `field`='inact'"), 0);
			$inline = date("Y-m-d H:i:s", time() - 86400 * $inactset);
			$fquery = $fquery . " where lastaccess<'$inline'";
		} elseif ($a != 0) {
			$fquery = $fquery . " where acctype=$a";
		}
		$countpages = $fquery;
		$fquery = $fquery . " order by id asc limit $start, $lim";
		$res = mysql_query($fquery);
		$pages = ceil(mysql_num_rows(mysql_query($countpages)) / $lim);
		if ($pages > 1) {
			echo("<p>");
			for ($i = 1; $i <= $pages; $i++) {
				echo("[");
				if ($i == $s) {echo("<b>");}
				else {echo("<a href='$self_url" . "control/?y=3&a=$a&s=$i'>");}
				echo("Page $i");
				if ($i == $s) {echo("</b>");}
				else {echo("</a>");}
				echo("]  ");
			}
			echo("</p>");
		}
		echo("<p><table width=100% border=0 cellpadding=2><tr style=\"background-color: #4DA0C6\">
<td align=center><b><font color=#FFFFFF>ID #</b></font></td>
<td align=center><b><font color=#FFFFFF>E-mail</b></font></td>
<td align=center><b><font color=#FFFFFF>Name</b></font></td>
<td align=center><b><font color=#FFFFFF>Account Type</b></font></td>
</tr>");
		for ($i = 0; $i < mysql_num_rows($res); $i++) {
			$id = mysql_result($res, $i, "id");
			$name = mysql_result($res, $i, "name");
			$email = mysql_result($res, $i, "email");
			$acc = mysql_result($res, $i, "acctype");
			echo("<tr style=\"background-color: #F0F8FF\"><td align=center>");
			if ($acc != 1) {
				echo("<b>");
			}
			echo("$id");
			if ($acc != 1) {
				echo("</b>");
			}
			echo("</td><td align=center>");
			if ($acc != 1) {
				echo("<b>");
			}
			echo("<a href='$self_url" . "control/?y=3&u=$id&a=$a&s=$s'>$email</a>");
			if ($acc != 1) {
				echo("</b>");
			}
			echo("</td><td align=center>$name</td><td align=center>");
			if ($acc != 1) {
				echo("<b>");
			}
			echo("$actypes[$acc]");
			if ($acc != 1) {
				echo("</b>");
			}
			echo("</td></tr>");
		}
		echo("</table></p>");
		if ($pages > 1) {
			echo("<p>");
			for ($i = 1; $i <= $pages; $i++) {
				echo("[");
				if ($i == $s) {echo("<b>");}
				else {echo("<a href='$self_url" . "control/?y=3&a=$a&s=$i'>");}
				echo("Page $i");
				if ($i == $s) {echo("</b>");}
				else {echo("</a>");}
				echo("]  ");
			}
			echo("</p>");
		}
	} else {
		if ($_POST[fform] == 'sent') {
			if ($_POST[fac] == 'trash') {
				$fquery = "delete from user where id=$u";
				$res = mysql_query("select id from site where usrid=$u");
				for ($i = 0; $i < mysql_num_rows($res); $i++) {
					$sss = mysql_result($res, $i);
					$newres = mysql_query("delete from abuse where siteid=$sss");
					$newres = mysql_query("delete from 7statsite where siteid=$sss");
				}
				$del_history = mysql_query("DELETE FROM investment_history WHERE usrid=$u");
				$del_cashouts = mysql_query("DELETE FROM cashout_history WHERE usrid=$u");
				$del_refcomms = mysql_query("DELETE FROM comission_history WHERE paid_to=$u");
				$del_other = mysql_query("DELETE FROM other_history WHERE usrid=$u");
				$res = mysql_query("delete from site where usrid=$u");
				$res = mysql_query("delete from 7stat where usrid=$u");
				$rep = "User #$u was deleted from the database.";
				$back = "<a href=$self_url" . "control/?y=3&a=$a>GO To Member List</a>";
			} else {
				$rep = "Changes to user #$u were saved in the database.";
				$back = "<a href=$self_url" . "control/?y=3&u=$u&a=$a&s=$s>GO To Member $u</a><br><a href=$self_url" . "control/?y=3&a=$a&s=$s>GO To Member List</a>";
				$opactyp = mysql_result(mysql_query("select name from acctype where id=$_POST[acctype]"), 0);
				$oldcred = mysql_result(mysql_query("select credits from user where id=$u"), 0);
				$surp = $oldcred - $_POST[credits];
				$surpres = mysql_query("update adminprops set value=value+$surp where field='surplu'");
				$fquery = "update user set name='$_POST[name]', email='$_POST[email]', passwd='$_POST[passwd]', ref='$_POST[ref]', acctype='$_POST[acctype]', credits='$_POST[credits]', minmax='$_POST[minmax]', allow_contact='$_POST[allow_contact]', upgrade_ends='$_POST[upg_ends]', status='$_POST[astatus]', ac='$_POST[ac]' where id=$u";
			}
			$res = mysql_query($fquery);
			echo("<b>$rep<br>$back");
		} else {
			$res = mysql_query("select * from user where id=$u");
			if (mysql_num_rows($res) == 0) {
				echo("<b>Member #$u was not found in the database.</b>");
			} else {
				if ($_POST[mail] == 'send') {
					$email = mysql_result(mysql_query("select email from user where id=$u"), 0);
					$subject = stripslashes($_POST[subject]);
					$message = stripslashes($_POST[message]);
					@mail($email, $subject, $message, $email_headers);
					echo("<b>Your e-mail message was sent.<br>&laquo;</b> <a href=$self_url" . "control/?y=3&u=$u&a=$a&s=$s>BACK To Member #$u</a><br><b>&laquo;</b> <a href=$self_url" . "control/?y=3&a=$a&s=$s>GO to user list</a>");
				} else {
					$get_history = mysql_query("SELECT * FROM investment_history WHERE usrid=$u ORDER BY adate");
					$get_cashouts = mysql_query("SELECT * FROM cashout_history WHERE usrid=$u ORDER BY cdate");
					$get_refcomms = mysql_query("SELECT * FROM comission_history WHERE paid_to=$u ORDER BY vdate");
					$get_other = mysql_query("SELECT * FROM other_history WHERE usrid=$u ORDER BY adate");
					$ref_count = mysql_num_rows(mysql_query("select id from user where ref=$u"));
					$name = mysql_result($res, 0, "name");
					$email = mysql_result($res, 0, "email");
					$passwd = mysql_result($res, 0, "passwd");
					$ref = mysql_result($res, 0, "ref");
					$acctype = mysql_result($res, 0, "acctype");
					$credits = mysql_result($res, 0, "credits");
					$credits = round($credits, 2);
					$invested = mysql_result($res, 0, "invested");
					$user_cash = mysql_result($res, 0, "roi_cash");
					$lifetime_cash = mysql_result($res, 0, "lifetime_cash");
					$lifetime_paid = mysql_result($res, 0, "lifetime_paid");
					$lifetot_roi = mysql_result($res, 0, "lifetot_roi");
					$lifetime_credits = mysql_result($res, 0, "lifetime_credits");
					$lifetime_pages = mysql_result($res, 0, "lifetime_pages");
					$sb_credits = mysql_result($res, 0, "sb_credits");
					$sb_cash = mysql_result($res, 0, "sb_cash");
					$lastroi = mysql_result($res, 0, "lastroi");
					$lastsurfed = mysql_result($res, 0, "lastsurfed");
					$commstoref = mysql_result($res, 0, "commstoref");
					$toref = mysql_result($res, 0, "toref");
					$allow_contact = mysql_result($res, 0, "allow_contact");
					$joindate = mysql_result($res, 0, "joindate");
					$minmax = mysql_result($res, 0, "minmax");
					$upg_ends = mysql_result($res, 0, "upgrade_ends");
					$mystatus = mysql_result($res, 0, "status");
					$ac = mysql_result($res, 0, "ac");
					$res = mysql_query("select id, name from acctype");
					for ($i = 0; $i < mysql_num_rows($res); $i++) {
						$key = mysql_result($res, $i, "id");
						$val = mysql_result($res, $i, "name");
						$actypes[$key] = $val;
					}
					$almin = array("No", "Yes");
					$allow_cs = array("no" => "No", "yes" => "Yes");
					$ast = array("Active" => "Active", "Un-verified" => "Not yet Verified", "Re-verifying" => "Re-verifying Email", "Inactive" => "Inactive", "Suspended" => "Suspended");
					$res = mysql_query("select id, url, state, credits from site where usrid=$u");

					if ($_GET['result'] == 'done') {
						echo(" <p><b>Successfully updated member $u!</b></p>");
					}
					echo("<p><table border=0 cellspacing=0 cellpadding=0>
<form action=$self_url" . "control/?y=3&u=$u&a=$a method=post>
<input type=hidden name=".session_name." value=".session_id().">
<input type=hidden name=fform value=sent>
<input type=hidden name=fac value=trash>
<tr><td><input type=submit value=\"Delete\" onclick=\"return deleteThis();\">&nbsp;&nbsp;</td></form>
<form action=$self_url" . "control/?y=3&u=$u&a=$a&s=$s method=post>
<td><input type=submit value=\"Save\">&nbsp;&nbsp;<input type=reset value=\"Reset\"></td></tr></table></p>

<p><table border=0>
<input type=hidden name=fform value=sent>
<tr>
<td>Member ID #:</td>
<td>$u</td>
</tr>
<tr>
<td>Join Date:</td>
<td>$joindate</td>
</tr>
<tr>
<td>E-mail:</td>
<td><input type=text name=email value=$email></td>
</tr>
<tr>
<td>Password:</td>
<td><input type=text name=passwd value=$passwd></td>
</tr>
<tr>
<td>Name:</td><td>
<input type=text name=name value=\"$name\"></td>
</tr>
<tr>
<td>Credits:</td>
<td><input type=text name=credits value=$credits></td>
</tr>
<tr>
<td>Account:</td>
<td><select name=acctype>
					");
					while (list($k, $v) = each($actypes)) {
						echo("<option value=$k");
						if ($acctype == $k) {echo(" selected");}
						echo(">$v</option>");
					}
					echo("</select></td>
</tr>
<tr>
<td>Upgrade Ends:</td>
<td><input type=text name=upg_ends value=$upg_ends></td>
</tr>
<tr>
<td>Allow Minimized:</td>
<td><select name=minmax>");
					while (list($k, $v) = each($almin)) {
						echo("\n\t\t\t\t\t<option value=$k");
						if ($minmax == $k) {echo(" selected");}
						echo(">$v</option>");
					}
					echo("</select></td>
</tr>
<tr>
<td>Allow Referral Contact:</td>
<td><select name=allow_contact>");
					while (list($k, $v) = each($allow_cs)) {
						echo("\n\t\t\t\t\t<option value=$k");
						if ($allow_contact == $k) {echo(" selected");}
						echo(">$v</option>");
					}
					echo("</select></td>
</tr>
<tr><td>Account Status:</td>
<td><select name=astatus>");
					while (list($k, $v) = each($ast)) {
						echo("\n\t\t\t\t\t<option value=$k");
						if ($mystatus == $k) {echo(" selected");}
						echo(">$v</option>");
					}
					echo("</select></td>
</tr>
<tr><td>Referrer ID #:</td>
<td><input type=text name=ref value=$ref></td>
</tr>
<tr>
<td>Activation Code:</td>
<td><input type=text name=ac value=$ac></td>
</tr>
</table></form></p>");

					echo("<hr color=#018BC1>");

					echo("<h1>Member History:</h1>
<p>Invested: \$$invested - <a href=transaction_manager.php>Add Investment</a><br>
Current Cash: \$$user_cash - <a href=transaction_manager.php>Credit/Debit User</a><br>
Lifetime Cash: \$$lifetime_cash<br>
Lifetime Cash Paid: \$$lifetime_paid<br>
Lifetime ROI Earned: \$$lifetot_roi<br>
Last ROI Credited: $lastroi<br>
Lifetime Credits: $lifetime_credits<br>
Lifetime Pages Surfed: $lifetime_pages<br>
Last Surfed: $lastsurfed<br>
Surf Bonus Credits: $sb_credits<br>
Surf Bonus Cash: \$$sb_cash<br>
Referral Count: $ref_count</p>");

					echo("<hr color=#018BC1>");

					echo("\n<h1>Upgrade History</h1>");
					if (mysql_num_rows($get_history) != 0) {

						echo("<p><table width=100% border=0 cellpadding=2><tr style=\"background-color: #4DA0C6\">
<td align=center><b><font color=#FFFFFF>ID</font></b></td>
<td align=center><b><font color=#FFFFFF>Date</font></b></td>
<td align=center><b><font color=#FFFFFF>Amount</font></b></td>
<td align=center><b><font color=#FFFFFF>Description</font></b></td>
<td align=center><b><font color=#FFFFFF>Processor</font></b></td>
<td align=center><b><font color=#FFFFFF>From</font></b></td>
<td align=center><b><font color=#FFFFFF>Delete</font></b></td></tr>");
						for ($i = 0; $i < mysql_num_rows($get_history); $i++) {
							$history_id = mysql_result($get_history, $i, "id");
							$amount = mysql_result($get_history, $i, "amount");
							$descr = mysql_result($get_history, $i, "descr");
							$is_from = mysql_result($get_history, $i, "is_from");
							$processor = mysql_result($get_history, $i, "processor");
							$adate = mysql_result($get_history, $i, "adate");
							echo("<tr style=\"background-color: #F0F8FF\">
<td align=\"center\">$history_id</td>
<td align=\"center\">$adate</td>
<td align=\"center\">\$$amount</td>
<td align=\"center\">$descr</td>
<td align=\"center\">$processor</td>
<td align=\"center\">$is_from</td>
<form method=\"post\" name=\"deltact\" action=\"remover.php\" onSubmit=\"return deleteThis();\">
<input type=hidden name=".session_name." value=".session_id().">
<input type=\"hidden\" name=\"tid\" value=\"$history_id\">
<td align=\"center\"><input type=\"submit\" name=\"submit\" value=\"Delete\"></td>");
						}
						echo("</form></tr></table></p>");
					} else {
						echo("\n<p>No transactions found at this time.</p>");
					}
					echo("\n<h1>Cashout History</h1>");
					if (mysql_num_rows($get_cashouts) != 0) {
						echo("<p><table width=100% border=0 cellpadding=2><tr style=\"background-color: #4DA0C6\">
<td align=center><b><font color=#FFFFFF>ID</font></b></td>
<td align=center><b><font color=#FFFFFF>Date</font></b></td>
<td align=center><b><font color=#FFFFFF>Amount</font></b></td>
<td align=center><b><font color=#FFFFFF>Description</font></b></td>
<td align=center><b><font color=#FFFFFF>Processor/Paid to</font></b></td>
<td align=center><b><font color=#FFFFFF>Delete</font></b></td></tr>");
						for ($ii = 0; $ii < mysql_num_rows($get_cashouts); $ii++) {
							$cashout_id = mysql_result($get_cashouts, $ii, "id");
							$camount = mysql_result($get_cashouts, $ii, "amount");
							$cdescr = mysql_result($get_cashouts, $ii, "descr");
							$pay_merch = mysql_result($get_cashouts, $ii, "pay_merch");
							$psid_to = mysql_result($get_cashouts, $ii, "paid_to");
							$cdate = mysql_result($get_cashouts, $ii, "cdate");
							echo("<tr style=\"background-color: #F0F8FF\">
<td align=\"center\">$cashout_id</td>
<td align=\"center\">$cdate</td>
<td align=\"center\">\$$camount</td>
<td align=\"center\">$cdescr</td>
<td align=\"center\">$pay_merch<br>$psid_to</td>
<form method=\"post\" name=\"deltact\" action=\"remover.php\" onSubmit=\"return deleteThis();\">
<input type=hidden name=".session_name." value=".session_id().">
<input type=\"hidden\" name=\"cid\" value=\"$cashout_id\">
<td align=\"center\"><input type=\"submit\" name=\"submit\" value=\"Delete\"></td></form></tr>");
						}
						echo("\n</table><br>Deleting the Cashout History will credit the members account with the amount of cash they were paid (reversal of cashout).</p>");
					} else {
						echo("\n<p>No cashout history found at this time.</p>");
					}
					echo("\n<h1>Referral Upgrade Commissions</h1>");
					if (mysql_num_rows($get_refcomms) != 0) {

						echo("<p><table width=\"100%\" border=\"0\" cellpadding=\"2\">
<tr style=\"background-color: #4DA0C6\">
<td align=center><b><font color=#FFFFFF>ID</font></b></td>
<td align=center><b><font color=#FFFFFF>Date</font></b></td>
<td align=center><b><font color=#FFFFFF>Amount</font></b></td>
<td align=center><b><font color=#FFFFFF>Description</font></b></td>
<td align=center><b><font color=#FFFFFF>Referral</font></b></td>
<td align=center><b><font color=#FFFFFF>Delete</font></b></td></tr>");
						for ($z = 0; $z < mysql_num_rows($get_refcomms); $z++) {
							$comm_id = mysql_result($get_refcomms, $z, "id");
							$comamount = mysql_result($get_refcomms, $z, "amount");
							$wasfor = mysql_result($get_refcomms, $z, "wasfor");
							$vdate = mysql_result($get_refcomms, $z, "vdate");
							$cupline = mysql_result($get_refcomms, $z, "usrid");
							echo("<tr style=\"background-color: #F0F8FF\">
<td align=\"center\">$comm_id</td>
<td align=\"center\">$vdate</td>
<td align=\"center\">\$$comamount</td>
<td align=\"center\">$wasfor</td>
<td align=\"center\">User $cupline</td>
<form method=\"post\" name=\"deltact\" action=\"remover.php\" onSubmit=\"return deleteThis();\">
<input type=hidden name=".session_name." value=".session_id().">
<input type=\"hidden\" name=\"commid\" value=\"$comm_id\">
<td align=\"center\"><input type=\"submit\" name=\"submit\" value=\"Delete\"></td></form></tr>");
						}
						echo("\n</table><br>Deleting an upgrade commission will debit the members account (reversal of upgrade commission).</p>");
					} else {
						echo("\n<p>No referral commissions were found at this time.</p>");
					}
					echo("\n<h1>Other Cash Credits</h1>");
					if (mysql_num_rows($get_other) != 0) {
						echo("<p><table width=\"100%\" border=\"0\" cellpadding=\"2\">
<tr style=\"background-color: #4DA0C6\">
<td align=center><b><font color=#FFFFFF>ID</font></b></td>
<td align=center><b><font color=#FFFFFF>Date</font></b></td>
<td align=center><b><font color=#FFFFFF>Amount</font></b></td>
<td align=center><b><font color=#FFFFFF>Description</font></b></td>
<td align=center><b><font color=#FFFFFF>From</font></b></td>
<td align=center><b><font color=#FFFFFF>Delete</font></b></td></tr>");
						for ($v = 0; $v < mysql_num_rows($get_other); $v++) {
							$oth_id = mysql_result($get_other, $v, "id");
							$othamount = mysql_result($get_other, $v, "amount");
							$other_descr = mysql_result($get_other, $v, "descr");
							$oadate = mysql_result($get_other, $v, "adate");
							$isfrm = mysql_result($get_other, $v, "is_from");
							echo("<tr style=\"background-color: #F0F8FF\">
<td align=center>$oth_id</td>
<td align=center>$oadate</td>
<td align=center>\$$othamount</td>
<td align=center>$other_descr</td>
<td align=center>$isfrm</td>
<form method=\"post\" name=\"deltact\" action=\"remover.php\" onSubmit=\"return deleteThis();\">
<input type=hidden name=".session_name." value=".session_id().">
<input type=\"hidden\" name=\"oid\" value=\"$oth_id\">
<td align=center><input type=\"submit\" name=\"submit\" value=\"Delete\"></td></form></tr>");
						}
						echo("</table><br>Deleting an other cash credit will debit the members account (reversal of cash credit).</p>");
					} else {
						echo("<p>No other cash credits were found at this time.</p>");
					}

					echo("<hr color=#018BC1>");

					echo("\n<h1>Members Submitted Websites:</h1>
<p><table width=\"100%\" border=\"0\" cellpadding=\"2\">
<tr style=\"background-color: #4DA0C6\">
<td align=center><b><font color=#FFFFFF>ID</font></b></td>
<td align=center><b><font color=#FFFFFF>URL</font></b></td>
<td align=center><b><font color=#FFFFFF>State</font></b></td>
<td align=center><b><font color=#FFFFFF>Credits</font></b></td></tr>");
					for ($i = 0; $i < mysql_num_rows($res); $i++) {
						$id = mysql_result($res, $i, "id");
						$url = mysql_result($res, $i, "url");
						$state = mysql_result($res, $i, "state");
						$credits = mysql_result($res, $i, "credits");
						$credits = round($credits, 2);

						echo("<tr style=\"background-color: #F0F8FF\">
<td align=center>$id</td>
<td align=center><a href=$self_url" . "control/?y=4&u=$id>$url</a></td>
<td align=center>$state</td>
<td align=center>$credits</td></tr>");
					}
					echo("</table></p>");

					echo("<hr color=#018BC1>");

					echo("\n<h1>Send An Email To <u>$name</u>:</h1>
<p><table border=0>
<form action=$self_url" . "control/?y=3&u=$u&a=$a&s=$s method=post>
<input type=hidden name=".session_name." value=".session_id().">
<input type=hidden name=mail value=send>
<tr>
<td valign=top>Subject:</td>
<td><input type=text name=subject></td>
</tr>
<td valign=top>Message:</td>
<td><textarea name=message cols=60 rows=10></textarea></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type=submit value=\"Send message\"></td>
</tr>
</form></table></p>");
				}}
		}
	}



} elseif ($y == 4) {
	surplus();
	title();
	if (!isset($u) || !is_numeric($u)) {
		$actypes = array("All sites", "Enabled", "On hold", "Waiting", "Suspended", "PopUps", "Adult", "Illegal", "FlyingAds", "Rotator", "StopsSurf", "BadURL", "BreaksFrames", "Phishing", "PaidToPromote", "NoCredits", "VirusFound", "MaskedURL");
		if (!isset($a) || !is_numeric($a)) {
			$a = 0;
			echo("\n<p>");
		}
		echo("\n| ");
		while (list($k, $v) = each($actypes)) {
			if ($k == $a) {echo("<b>");}
			else {echo("<a href=$self_url" . "control/?y=4&a=$k>");}
			echo($v);
			if ($k == $a) {echo("</b>");}
			else {echo("</a>");}
			echo("\n | ");
		}
		echo("\n</p>");
		if (!isset($s) || $s < 1 || !is_numeric($s)) {
			$s = 1;
		}
		$start = ($s - 1) * $lim;
		$fquery = "select id, usrid, url, credits, state from site";
		if ($a != 0) {$fquery = $fquery . " where state='$actypes[$a]'";}
		$countpages = $fquery;
		$fquery = $fquery . " order by id asc limit $start, $lim";
		$res = mysql_query($fquery);
		$pages = ceil(mysql_num_rows(mysql_query($countpages)) / $lim);
		if ($pages > 1) {
			echo("\n<p>");
			for ($i = 1; $i <= $pages; $i++) {
				echo("[");
				if ($i == $s) {echo("<b>");}
				else {echo("<a href=$self_url" . "control/?y=4&a=$a&s=$i>");}
				echo("Page $i");
				if ($i == $s) {echo("</b>");}
				else {echo("</a>");}
				echo("]  ");
			}
			echo("</p>");
		}
		echo("\n<p>
<table width=\"100%\" border=\"0\" cellpadding=\"2\">
<tr style=\"background-color: #4DA0C6\">
<td align=center><b><font color=#FFFFFF>ID</font></b></td>
<td align=center><b><font color=#FFFFFF>URL</font></b></td>
<td align=center><b><font color=#FFFFFF>User ID</font></b></td>
<td align=center><b><font color=#FFFFFF>State</font></b></td>
<td align=center><b><font color=#FFFFFF>Credits</font></b></td>
<td align=center><b><font color=#FFFFFF>Check Site</font></b></td>
		</tr>");
		for ($i = 0; $i < mysql_num_rows($res); $i++) {
			$id = mysql_result($res, $i, "id");
			$url = mysql_result($res, $i, "url");
			$usrid = mysql_result($res, $i, "usrid");
			$state = mysql_result($res, $i, "state");
			$credits = mysql_result($res, $i, "credits");
			echo("<tr style=\"background-color: #F0F8FF\">
<td align=center>$id</td>
<td align=center><a href=./index.php?y=4&u=$id&a=$a&s=$s>$url</a></td>
<td align=center>$usrid</td>
<td align=center>$state</td>
<td align=center>$credits</td>
<td align=center><a href=$url target=_blank>Open Site</a></td>
			</tr>");
		}
		echo("</table></p>");
		if ($pages > 1) {
			echo("<p>");
			for ($i = 1; $i <= $pages; $i++) {
				echo("[");
				if ($i == $s) {echo("<b>");}
				else {echo("<a href=$self_url" . "control/?y=4&a=$a&s=$i>");}
				echo("Page $i");
				if ($i == $s) {echo("</b>");}
				else {echo("</a>");}
				echo("]  ");
			}
			echo("</p>");
		}
	} else {
		if ($_POST[fform] == 'sent') {
			if ($_POST[fac] == 'trash') {
				$fquery = "delete from site where id=$u";
				$kukures = mysql_query("delete from abuse where siteid=$u");
				$rep = "Site #$u was deleted from the database.";
				$back = "a=$a";
			} else {
				$fquery = "update site set name='$_POST[name]', url='$_POST[url]', lang='$_POST[lang]', state='$_POST[state]', credits='$_POST[credits]' where id=$u";
				$rep = "Changes to site #$u were saved in the database.";
				$back = "a=$a&s=$s";
			}
			$oldcred = mysql_result(mysql_query("select credits from site where id=$u"), 0);
			$surp = $oldcred - $_POST[credits];
			$surpres = mysql_query("update adminprops set value=value+$surp where field='surplu'");
			$res = mysql_query($fquery);
			echo("<b>$rep<br>&laquo;</b> <a href=$self_url" . "control/?y=4&$back>GO back to site list.</a>");
			echo("<br>&laquo;</b> <a href=$self_url" . "control/?y=4&$back&u=$u>GO back to Site #$u.</a>");
		} else {
			$res = mysql_query("select usrid, name, url, lang, state, credits, totalhits from site where id=$u");
			if (mysql_num_rows($res) == 0) {
				echo("<b>Site #$u was not found in the database.</b>");
			} else {
				$name = mysql_result($res, 0, "name");
				$usrid = mysql_result($res, 0, "usrid");
				$url = mysql_result($res, 0, "url");
				$lang = mysql_result($res, 0, "lang");
				$state = mysql_result($res, 0, "state");
				$credits = mysql_result($res, 0, "credits");
				$credits = round($credits, 2);
				$totalhits = mysql_result($res, 0, "totalhits");
				$states = array("Enabled" => "Enabled", "On hold" => "On hold", "Waiting" => "Waiting For Approval", "Suspended" => "Suspended", "PopUps" => "PopUps", "Adult" => "Adult", "Illegal" => "Illegal", "FlyingAds" => "FlyingAds", "Rotator" => "Rotator", "StopsSurf" => "StopsSurf", "BadURL" => "BadURL", "BreaksFrames" => "BreaksFrames", "Phishing" => "Phishing", "PaidToPromote" => "PaidToPromote", "NoCredits" => "NoCredits", "VirusFound" => "VirusFound", "MaskedURL" => "MaskedURL");
				$langs = array("English" => "English", "Arabic" => "Arabic", "Chinese" => "Chinese", "Czech" => "Czech", "Danish" => "Danish", "Dutch" => "Dutch", "Estonian" => "Estonian", "Finnish" => "Finnish", "French" => "French", "German" => "German", "Greek" => "Greek", "Hebrew" => "Hebrew", "Hungarian" => "Hungarian", "Icelandic" => "Icelandic", "Italian" => "Italian", "Japanese" => "Japanese", "Korean" => "Korean", "Latvian" => "Latvian", "Lithuanian" => "Lithuanian", "Norwegian" => "Norwegian", "Polish" => "Polish", "Portuguese" => "Portuguese", "Romanian" => "Romanian", "Russian" => "Russian", "Spanish" => "Spanish", "Swedish" => "Swedish", "Turkish" => "Turkish");
				$res = mysql_query("select id, usrid, DATE_FORMAT(date, '%Y-%m-%d') as date from abuse where siteid=$u order by date desc");
				echo("<p>
<table border=0 cellspacing=0 cellpadding=0>
<form action=$self_url" . "control/?y=4&u=$u&a=$a method=post>
<input type=hidden name=fform value=sent>
<input type=hidden name=fac value=trash><tr>
<td><input type=submit value=\"Delete\"  onclick=\"return deleteThis();\">&nbsp;&nbsp;</td></form>
<form action=./index.php?y=4&u=$u&a=$a&s=$s method=post>
<td><input type=submit value=\"Save\">&nbsp;&nbsp;<input type=reset value=\"Reset\"></td>
</tr></table></p>

<p>
<table border=0>
<input type=hidden name=fform value=sent>
<tr>
<td>Site ID #:</td>
<td>$u</td>
</tr>
<tr>
<td>Site Owner:</td>
<td><a href=$self_url" . "control/?y=3&u=$usrid>user #$usrid</a></td>
</tr>
<tr>
<td>Total Hits:</td>
<td>$totalhits</td>
</tr>
<tr>
<td>URL:</td>
<td><input type=text name=url value=$url> <a href=$url target=_blank>Open Site</a></td>
</tr>
<tr>
<td>Name:</td>
<td><input type=text name=name value=\"$name\"></td>
</tr>
<tr>
<td>State:</td>
<td><select name=state>");
				while (list($k, $v) = each($states)) {
					echo("<option value=$k");
					if ($state == $k) {echo(" selected");}
					echo(">$v</option>");
				}
				echo("</select></td>
</tr>
<tr>
<td>Credits:</td>
<td><input type=text name=credits value=$credits></td>
</tr>
<tr>
<td>Language:</td>
<td><select name=lang>");
				while (list($k, $v) = each($langs)) {
					echo("<option value=$k");
					if ($lang == $k) {echo(" selected");}
					echo(">$v</option>");
				}
				echo("</select></td>
</tr>
</table></form></p>");
				echo("<h1>Abuse Reports:</h1>
<p>
<table width=100% border=0 cellpadding=2><tr style=\"background-color: #4DA0C6\">
<td align=center><b><font color=#FFFFFF>Report ID</font></b></td>
<td align=center><b><font color=#FFFFFF>Reported By</font></b></td>
<td align=center><b><font color=#FFFFFF>Date Reported</font></b></td>
<td align=center><b><font color=#FFFFFF>View Full Report</font></b></td></tr>");
				for ($i = 0; $i < mysql_num_rows($res); $i++) {
					$id = mysql_result($res, $i, "id");
					$usrid = mysql_result($res, $i, "usrid");
					$date = mysql_result($res, $i, "date");
					echo("<tr style=\"background-color: #F0F8FF\">
<td align=center>$id</td>
<td align=center>User ID # $usrid</td>
<td align=center>$date</td>
<td align=center><a href=$self_url" . "control/?y=5&u=$id><b>Open Report</b></a></td>
</tr>");
				}
				echo("</table></p>");
			}
		}
	}



} elseif ($y == 5) {
	surplus();
	title();
	if (!isset($u) || !is_numeric($u)) {
		if (!isset($s) || $s < 1 || !is_numeric($s)) {
			$s = 1;
		}
		$start = ($s - 1) * $lim;
		$fquery = "select id, siteid, usrid, DATE_FORMAT(date, '%Y-%m-%d') as date from abuse";
		$countpages = $fquery;
		$fquery = $fquery . " order by id desc limit $start, $lim";
		$res = mysql_query($fquery);
		$pages = ceil(mysql_num_rows(mysql_query($countpages)) / $lim);
		if ($pages > 1) {
			echo("<p>");
			for ($i = 1; $i <= $pages; $i++) {
				echo("[");
				if ($i == $s) {echo("<b>");}
				else {echo("<a href=$self_url" . "control/?y=5&s=$i>");}
				echo("Page $i");
				if ($i == $s) {echo("</b>");}
				else {echo("</a>");}
				echo("]  ");
			}
			echo("</p>");
		}
		echo("<p>
<table width=100% border=0 cellpadding=2><tr style=\"background-color: #4DA0C6\">
<td align=center><b><font color=#FFFFFF>Report ID</font></b></td>
<td align=center><b><font color=#FFFFFF>Subject</font></b></td>
<td align=center><b><font color=#FFFFFF>Reported By</font></b></td>
<td align=center><b><font color=#FFFFFF>Date Reported</font></b></td>
</tr>");
		for ($i = 0; $i < mysql_num_rows($res); $i++) {
			$id = mysql_result($res, $i, "id");
			$siteid = mysql_result($res, $i, "siteid");
			$usrid = mysql_result($res, $i, "usrid");
			$date = mysql_result($res, $i, "date");
			$siteurl = mysql_result(mysql_query("select url from site where id=$siteid"), 0);
			echo("<tr style=\"background-color: #F0F8FF\">
<td align=center>$id</td>
<td align=center><a href=$self_url" . "control/?y=5&u=$id&s=$s>$siteurl</a></td>
<td align=center>User ID # $usrid</td>
<td align=center>$date</td>
</tr>");
		}
		echo("</table></p>");
		if ($pages > 1) {
			echo("<p>");
			for ($i = 1; $i <= $pages; $i++) {
				echo("[");
				if ($i == $s) {echo("<b>");}
				else {echo("<a href=$self_url" . "control/?y=5&s=$i>");}
				echo("Page $i");
				if ($i == $s) {echo("</b>");}
				else {echo("</a>");}
				echo("]  ");
			}
			echo("</p>");
		}
	} else {
		if ($_POST[fform] == 'sent') {
			$res = mysql_query("delete from abuse where id=$u");
			echo("<b>Abuse report #$u was deleted from the database.<br>&laquo;</b> <a href=./index.php?y=5&s=$s>GO TO to abuse report list.</a>");
		} else {
			$res = mysql_query("select siteid, usrid, text, DATE_FORMAT(date, '%Y-%m-%d') as date from abuse where id=$u");
			if (mysql_num_rows($res) == 0) {
				echo("<b>Abuse report #$u was not found in the database.</b>");
			} else {
				if ($_POST[mail] == 'send') {
					$email = mysql_result(mysql_query("select email from user where id=$_POST[usrid]"), 0);
					$admail = mysql_result(mysql_query("select value from admin where field='email'"), 0);
					$subject = stripslashes($_POST[subject]);
					$message = stripslashes($_POST[message]);
					mail($email, $subject, $message, $email_headers);
					echo("<b>Your email message was sent.<br>&laquo;</b> <a href=$self_url" . "control/?y=5&u=$u&s=$s>BACK to abuse report #$u.</a><br><b>&laquo;</b> <a href=$self_url" . "control/?y=5&s=$s>GO TO to abuse reports list.</a>");
				} else {
					$siteid = mysql_result($res, 0, "siteid");
					$usrid = mysql_result($res, 0, "usrid");
					$text = mysql_result($res, 0, "text");
					$date = mysql_result($res, 0, "date");
					$res = mysql_query("select email from user where id=$usrid");
					$author = mysql_result($res, 0, "email");
					$res = mysql_query("select usrid, url from site where id=$siteid");
					$url = mysql_result($res, 0, "url");
					$ownid = mysql_result($res, 0, "usrid");
					$res = mysql_query("select email from user where id=$ownid");
					$ownmail = mysql_result($res, 0);
					$text = nl2br($text);
					echo("<p><table border=0>
<form action=$self_url" . "control/?y=5&u=$u&s=$s method=post>
<input type=hidden name=fform value=sent>
<tr>
<td>Abuse Report:</td>
<td># $u</td>
</tr>
<tr>
<td>Date Reported:</td>
<td>$date</td>
</tr>
<tr>
<td>Reported By:</td>
<td><a href=$self_url" . "control/?y=3&u=$usrid>$author</a></td>
</tr>
<tr>
<td>Website Reported:</td>
<td><a href=$self_url" . "control/?y=4&u=$siteid>$url</a>&nbsp;&nbsp;<span style=\"font-size: 85%\">[<a href=$url target=_blank>open</a>]</span></td>
</tr>
<tr>
<td>Website Owner:</td>
<td><a href=$self_url" . "control/?y=3&u=$ownid>$ownmail</a></td>
</tr>
<tr>
<td>Report Comments:</td>
<td>$text</td>
</tr>
<tr>
<td>&nbsp</td>
<td><input type=submit value=\"Delete\" onclick=\"return deleteThis();\"></td>
</tr>
</form></table></p>");
					echo("<p>
<h1>Email Author Of Abuse Report:</h1>
<p><table border=0>
<form action=$self_url" . "control/?y=5&u=$u&s=$s method=post>
<input type=hidden name=mail value=send>
<input type=hidden name=usrid value=$usrid>
<tr>
<td>Subject:</td>
<td><input type=text name=subject></td>
</tr>
<td>Message:</td>
<td><textarea name=message cols=60 rows=10></textarea></td>
</tr>
<tr>
<td>&nbsp</td>
<td><input type=submit value=\"Send message\"></td>
</tr>
</form></table></p>");
				}
			}
		}
	}



} elseif ($y == 6) {
	surplus();
	title();
	$css_file_name = $_SERVER['DOCUMENT_ROOT'] . "/style.css";
	$fields = array("1page", "priva", "terms", "testi");
	$fnames = array("Site Main Page", "Privacy-Policy", "Terms & Conditions", "Testimonials");
	if ($_POST[cform] == 1) {
		$fp = fopen($css_file_name, "w");
		fwrite($fp, $_POST[css]);
		fclose($fp);
	} elseif ($_POST[cform] > 1 && $_POST[cform] < 6) {
		$key = $_POST[cform] - 2;
		//exit("update html set content='".$_POST[$fields[$key]]."' where type='$fields[$key]'");
		$res = mysql_query("update html set content='".$_POST[$fields[$key]]."' where type='$fields[$key]'");
	}

	echo("<p>Fill out the below boxes to change certian pages of your website. Use the examples in the boxes to help you get started.</p>");

	while (list($k, $v) = each($fields)) {
		$cfn = $k + 2;
		$content = mysql_result(mysql_query("select content from html where type='$v'"), 0);
		echo("\n<h1>$fnames[$k] (HTML):</h1>
<p><table border=0>
<form action=$self_url" . "control/?y=6 method=post>
<input type=hidden name=cform value=$cfn>
<tr>
<td align=left><textarea cols=70 rows=10 name=$v>$content</textarea></td>
</tr>
<tr>
<td><input type=submit value=\"Save\">&nbsp;&nbsp;<input type=reset value=\"Reset\"></td>
</tr>
</form></table></p>");

		echo("<hr color=#018BC1>");
	}



} elseif ($y == 7) {
	surplus();
	title();
	if (is_numeric($_POST[fform])) {
		if ($_POST[fform] == 0) {
			$fquery = "insert into sellcredit (name, descr, cost) values ('$_POST[name]', '$_POST[descr]', '$_POST[cost]')";
		} elseif ($_POST[fac] == 'trash') {
			$fquery = "delete from sellcredit where id=$_POST[fform]";
		} else {
			$fquery = "update sellcredit set name='$_POST[name]', descr='$_POST[descr]', cost='$_POST[cost]' where id=$_POST[fform]";
		}
		$res = mysql_query($fquery);
	}

	echo("<p>Fill out the below form to add new advertising packages for members to purchase. Some suggestions would be... credit packages, paid to click packages, banner ad packages, text ad packages, and more.</p>
<p>All package purchases need to be manually added to your members account, the ptc area, or the banner & text ad rotator. Make sure you always follow through with your sales and get your members account and purchase information so you can add it in correctly.</p>");
	echo("<h1>Add A New Package:</h1>
<p><table border=0>
<form action=$self_url" . "control/?y=7 method=post>
<input type=hidden name=fform value=0>
<tr>
<td>Title:</td>
<td><input type=text name=name></td>
</tr>
<tr>
<td>Cost:</td>
<td align=left><input type=text name=cost></td>
</tr>
<tr>
<td>Description:</td>
<td><textarea name=descr cols=45 rows=5></textarea></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type=submit value=\"Save\"></td>
</tr>
</form></table></p>
<hr color=#018BC1>");
	$res = mysql_query("select * from sellcredit order by id asc");
	for ($i = 0; $i < mysql_num_rows($res); $i++) {
		$id = mysql_result($res, $i, "id");
		$name = mysql_result($res, $i, "name");
		$descr = mysql_result($res, $i, "descr");
		$cost = mysql_result($res, $i, "cost");


		echo("<h1>Package ID # $id:</h1>

<p><table border=0>
<form action=$self_url" . "control/?y=7 method=post>
<input type=hidden name=fform value=$id>
<input type=hidden name=fac value=trash>
<tr><td><input type=submit value=\"Delete\" onclick=\"return deleteThis();\">&nbsp;&nbsp;</td></form>
<form action=$self_url" . "control/?y=7 method=post><td><input type=submit value=\"Save\">&nbsp;&nbsp;<input type=reset value=\"Reset\"></td></tr></table></p>
<p><table border=0>
<input type=hidden name=fform value=$id>
<tr>
<td>Title:</td>
<td><input type=text name=name value=\"$name\"></td>
</tr>
<tr>
<td>Cost:</td>
<td><input type=text name=cost value=\"$cost\"></td>
</tr>
<tr>
<td>Description:</td>
<td><textarea name=descr cols=45 rows=5>$descr</textarea></td>
</tr>
</form></table></p><br>");
	}



} elseif ($y == 8) {
	surplus();
	title();
	if (is_numeric($_POST[fform])) {
		$commissions = round(trim($_POST['commissions']) / 100, 2);
		$rbonuses = trim($_POST['rbonuses']);
		$levels = trim($_POST['levels']);
		$ptc_levels = trim($_POST['ptc_levels']);
		$rbonusesa = explode(",", $rbonuses);
		$levelsa = explode(",", $levels);
		$ptc_levelsa = explode(",", $ptc_levels);
		for ($v = 0; $v < count($rbonusesa); $v++) {if (!is_numeric($rbonusesa[$v])) {$errorz = 'yes'; }}
		for ($v = 0; $v < count($levelsa); $v++) {if (!is_numeric($levelsa[$v])) {$errorz = 'yes'; }}
		for ($v = 0; $v < count($ptc_levelsa); $v++) {if (!is_numeric($ptc_levelsa[$v])) {$errorz = 'yes'; }}
		if ($_POST['fform'] != 1) {
			$ins_sql = "upg_time='$_POST[premmx]'";
		} else {
			$ins_sql = "upg_time=''";
		}
		if ($errorz != 'yes') {
			@mysql_query("UPDATE acctype SET name='$_POST[name]', descr='$_POST[descr]', ratemin='$_POST[ratemin]', ratemax='$_POST[ratemax]', cost='$_POST[cost]', cashout='$_POST[cashout]', commissions='$commissions', min_sites='$_POST[min_sites]', monthly_bonus='$_POST[prembn]', $ins_sql, rpgebonus='$_POST[rpgebonus]',
		 rbonuses='$rbonuses', levels='$levels', ptc_levels='$ptc_levels' WHERE id=$_POST[fform]") or die ("Oops.. there was a MySQL error, this was:<br>" . mysql_error());
			echo("<p>SUCCESSFULLY CHANGED ACCOUNT: <b>$_POST[name]</b></p>");
		} else {
			echo("<p>ERROR WITH THE REFERRING LEVELS! ACCOUNT WAS NOT UPDATED</p>");
		}
	}
	echo("Fill out the below forms to construct your members account options. You will find a couple of tips at the bottom of this page.</p>");
	$res = mysql_query("select * from acctype order by id asc");
	for ($i = 0; $i < mysql_num_rows($res); $i++) {
		$id = mysql_result($res, $i, "id");
		$name = mysql_result($res, $i, "name");
		$descr = mysql_result($res, $i, "descr");
		$ratemin = mysql_result($res, $i, "ratemin");
		$ratemax = mysql_result($res, $i, "ratemax");
		$cost = mysql_result($res, $i, "cost");
		$cashout = mysql_result($res, $i, "cashout");
		$commissions = mysql_result($res, $i, "commissions");
		$min_sites = mysql_result($res, $i, "min_sites");
		$monthly_bonus = mysql_result($res, $i, "monthly_bonus");
		$upgrade_time = mysql_result($res, $i, "upg_time");
		$rpgebonus = mysql_result($res, $i, "rpgebonus");
		$rbonuses = mysql_result($res, $i, "rbonuses");
		$levels = mysql_result($res, $i, "levels");
		$ptc_levels = mysql_result($res, $i, "ptc_levels");
		$commissions = $commissions * 100;
		echo("<h1>$name (#$id):</h1>");
		echo("<p>
<table border=0>
<form action=./index.php?y=8 method=post>
<input type=hidden name=fform value=$id>
<tr>
<td>Title:</td>
<td><input type=text name=name value=\"$name\"></td>
</tr>
<tr>
<td>Cost:</td>
<td><input type=text name=cost value=\"$cost\"></td>
</tr>
<tr>
<td>Minimized Surf Rate:</td>
<td><input type=text name=ratemin value=\"$ratemin\"></td>
</tr>
<tr>
<td>Maximized Surf Rate:</td>
<td><input type=text name=ratemax value=\"$ratemax\"></td>
</tr>");
		echo("<tr>
<td>Cashout:</td>
<td>$<input type=text name=cashout value=$cashout></td>
</tr>
<tr>
<td>Commissions Ratio:</td>
<td><input type=text name=commissions value=$commissions>%</td>
</tr>
<tr>
<td>Maximum Sites Allowed:</td>
<td><input type=text name=min_sites value=$min_sites></td></tr>");

		echo("<tr><td>Monthly Credit Bonus:</td><td><input type=text name=prembn value=$monthly_bonus></td></tr>");
		echo("<tr><td>Refer Page Credit Bonus:</td><td><input type=text name=rpgebonus value=$rpgebonus> <font size=1>1 = 1 credit</font></td></tr>");
		echo("<tr><td>Referring Bonuses*:</td><td><input type=text name=rbonuses value=$rbonuses></td></tr>");
		echo("<tr><td>Referral Levels**:</td><td><input type=text name=levels value=$levels></td></tr>");
		echo("<tr><td>Referral PTC Cash Levels**:</td><td><input type=text name=ptc_levels value=$ptc_levels></td></tr>");
		if ($id != 1) {
			echo("<tr><td>Days Upgrade Lasts:</td><td><input type=text name=premmx value=$upgrade_time></td></tr>");
		}
		echo("<tr><td>Description:</td><td><textarea name=descr cols=45 rows=5>$descr</textarea></td>
</tr>
<tr>
<td>&nbsp</td>
<td><input type=submit value=\"Save\">&nbsp;&nbsp;<input type=reset value=\"Reset\"></td>
</tr>
</form></table></p><br>");
	}
	echo("<p class=info>* Enter the <b>number of credits</b> for each level new referral bonus and separate with a comma ( , ).
<br><br>
EXAMPLE: 100,90,80,70,60,50,50,30,10,5
<br><br>
This will give new referral credit bonuses on <b>10 levels</b>. 100 credits per new referral on level 1, 90 credits per new referral on level 2, 80 credits per referral on level 3 etc... all the way down to 5 credits per new referral on level 10<br><br><b>1 = 1 credit</b><br>");
	echo("<br>** Enter the <b>percentage</b> for each level referral earnings and separate with a comma ( , ).
<br><br>
EXAMPLE: 10,9,8,7,6,5,4,3,2,1
<br><br>
This will give referral earnings on <b>10 levels</b>. 10% of surf and/or ptc credits on level 1, 9% of surf and/or ptc credits on level 2, 8% of surf and/or ptc credits on level 3 etc... all the way down to 1% of surf and/or ptc credits on level 10<br><br><b>1 = 1%</b></p>");



} elseif ($y == 9) {
	surplus();
	title();
	if ($_POST['fform'] == 'sent' && $_POST['subject'] != "" && $_POST['message'] != "") {
		$admail = mysql_result(mysql_query("select value from admin where field='email'"), 0);
		if ($_POST['send_to'] == 'all') {
			$res = mysql_query("select id, name, email, credits from user");
		} elseif ($_POST['send_to'] == 'inactiv') {
			$inactiv_set = mysql_result(mysql_query("select value from adminprops where field='inact'"), 0);
			$is_inactive = date("Y-m-d H:i:s", time() - 86400 * $inactiv_set);
			$res = mysql_query("select id, name, email, credits from user where lastaccess<'$is_inactive'");
		} elseif (is_numeric($_POST['send_to'])) {
			$res = mysql_query("select id, name, email, credits from user where acctype=$_POST[send_to]");
		} elseif ($_POST['send_to'] == 'unverif') {
			$res = mysql_query("select id, name, email, credits from user where ac!=0");
		}
		$zcount = mysql_num_rows($res);
		if ($zcount > 0) {
			for ($i = 0; $i < mysql_num_rows($res); $i++) {
				$usid = mysql_result($res, $i, "id");
				$name = mysql_result($res, $i, "name");
				$email = mysql_result($res, $i, "email");
				$crdsd = mysql_result($res, $i, "credits");
				$messa = $_POST['message'];
				$subj = $_POST['subject'];
				$messa = str_replace('[userid]', $usid, $messa);
				$messa = str_replace('[name]', $name, $messa);
				$messa = str_replace('[email]', $email, $messa);
				$messa = str_replace('[credits]', $crdsd, $messa);
				$subj = str_replace('[userid]', $usid, $subj);
				$subj = str_replace('[name]', $name, $subj);
				$subj = str_replace('[email]', $email, $subj);
				$subj = str_replace('[credits]', $crdsd, $subj);
				$subj = stripslashes($subj);
				$messa = stripslashes($messa);
				@mail($email, $subj, $messa, $email_headers);
			}
			$done = 'yes';
		} else {
			$done = 'no';
		}
	}
	$res = mysql_query("select * from user");
	$allcount = mysql_num_rows($res);
	$inactiv_set = mysql_result(mysql_query("select value from adminprops where field='inact'"), 0);
	$is_inactive = date("Y-m-d H:i:s", time() - 86400 * $inactiv_set);
	$res = mysql_query("select * from user where lastaccess<'$is_inactive'");
	$inactcount = mysql_num_rows($res);
	$res = mysql_query("select * from user where ac!=0");
	$unverifcount = mysql_num_rows($res);
	$res = mysql_query("select id, name from acctype");
	for ($i = 0; $i < mysql_num_rows($res); $i++) {
		$key = mysql_result($res, $i, "id");
		$val = mysql_result($res, $i, "name");
		$actypes[$key] = $val;
	}
	while (list($k, $v) = each($actypes)) {
		$resu = mysql_query("select * from user where acctype=$k");
		$zcountdsr = mysql_num_rows($resu);
		$accs_send = $accs_send . "\n<option value=$k";
		if ($zcountdsr == 0) {$accs_send = $accs_send . " DISABLED"; }
		$accs_send = $accs_send . ">All ".$v."s (Count: $zcountdsr)</option>\n";
	}
	if ($done == 'yes') {echo("<p>Your email was sent to $zcount members!</p>"); } elseif ($done == 'no') {echo("<p>Sorry no members found to send the email to!</p>");}
	echo("<p><table border=0>
<form action=$self_url" . "control/?y=9 method=post>
<input type=hidden name=fform value=sent>
<tr>
<td>Send Email To:</td>
<td><select name=\"send_to\"");
	if ($allcount == 0) {
		echo(" DISABLED");
	}
	echo(">\n<option value=\"all\">All Members (Count: $allcount)</option>\n<option value=\"unverif\"");
	if ($unverifcount == 0) {
		echo(" DISABLED");
	}
	echo(">Unverified Members (Count: $unverifcount)</option>\n<option value=\"inactiv\"");
	if ($inactcount == 0) {
		echo(" DISABLED");
	}
	echo(">Inactive Members (Count: $inactcount)</option>\n$accs_send</select></td>
</tr>
<tr>
<td>Subject:</td>
<td><input type=text name=subject></td>
</tr>
<tr><td>Message:</td>
<td><textarea name=message cols=60 rows=10></textarea></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type=submit value=\"Send Email\"></td>
</tr>
</form></table></p>
<p class=info>Substitutions to use in the message body and subject field):<br>
<b>[name]</b> - Members Name<br>
<b>[userid]</b> - Members ID #<br>
<b>[email]</b> - Members Email Address<br>
<b>[credits]</b> - Members Available Credits</p>");



} elseif ($y == 10) {
	surplus();
	title();
	if ($_POST[bform] == 'file') {
		if (($_FILES['upfile']['type'] == "image/gif" || $_FILES['upfile']['type'] == "image/jpeg" || $_FILES['upfile']['type'] == "image/pjpeg") && $_FILES['upfile']['size'] <= 30720) {
			switch ($_FILES['upfile']['type']) {
				case "image/gif":
					$ext = ".gif";
					break;
				case "image/pjpeg":
				case "image/jpeg":
					$ext = ".jpg";
					break;
			}
			$simgurl = '/banners/' . $_FILES['upfile']['name'];
			$wh = getimagesize($_FILES['upfile']['tmp_name']);
			$res = mysql_query("insert into banner (imgurl, widtheight) values ('$simgurl', '$wh[3]')");
			$lastid = mysql_insert_id();
			copy($_FILES['upfile']['tmp_name'], $_SERVER[DOCUMENT_ROOT] . "/banners/ban$lastid" . $ext);
			$simgurl = $self_url . "banners/ban$lastid" . $ext;
			$res = mysql_query("update banner set imgurl='$simgurl' where id=$lastid");
		}
	} elseif (is_numeric($_POST[bform])) {
		$res = mysql_query("select imgurl from banner where id=$_POST[bform]");
		$dext = mysql_result($res, 0);
		$dext = substr($dext, -4);
		unlink($_SERVER[DOCUMENT_ROOT] . "/banners/ban" . $_POST[bform] . $dext);
		$res = mysql_query("delete from banner where id=$_POST[bform]");
	}

	echo("<p>The below is used for you to provide your members banners that they can use to refer other new members to your website. Your banner can only be <b>.gif or .jpg format</b> and must <b>not be any bigger than 30kb</b>.</p>");

	echo("<h1>Upload A New Banner:</h1>
<p><table border=0>
<form enctype=\"multipart/form-data\" action=$self_url" . "control/?y=10 method=post>
<input type=hidden name=bform value=file>
<input type=hidden name=MAX_FILE_SIZE value=30720>
<tr>
<td><input type=file name=upfile></td>
</tr>
<tr>
<td><input type=submit value=\"Upload\"></td>
</tr>
</form></table></p><hr color=#018BC1>");
	$res = mysql_query("select id, imgurl, widtheight from banner order by id asc");
	for ($i = 0; $i < mysql_num_rows($res); $i++) {
		$id = mysql_result($res, $i, "id");
		$imgurl = mysql_result($res, $i, "imgurl");
		$widht = mysql_result($res, $i, "widtheight");
		echo("<h1>Banner # $id:</h1>
<p><table border=0>
<form action=$self_url" . "control/?y=10 method=post>
<input type=hidden name=bform value=$id>
<tr>
<td><img src=$imgurl $widht border=0></td>
</tr>
<tr>
<td><b>$imgurl</b></td>
</tr>
<tr>
<td><input type=submit value=\"Delete\" onclick=\"return deleteThis();\"></td>
</tr>
</form></table></p><br>");
	}



} elseif ($y == 11) {
	surplus();
	title();
	if (is_numeric($_POST[fform])) {
		if ($_POST[fform] == 0) {
			$fquery = "insert into faq (quest, answ) values ('$_POST[quest]', '$_POST[answ]')";
		} elseif ($_POST[fac] == 'trash') {
			$fquery = "delete from faq where id=$_POST[fform]";
		} else {
			$fquery = "update faq set quest='$_POST[quest]', answ='$_POST[answ]' where id=$_POST[fform]";
		}
		$res = mysql_query($fquery);
	}

	echo("<p>Use the below form to add in your most frequently asked questions and answers. This will fill in your websites FAQ Page.</p>");

	echo("\n<h1>Add New FAQ:</h1>
<p><table border=0>
<form action=$self_url" . "control/?y=11 method=post>
<input type=hidden name=fform value=0>
<tr>
<td>Question:</td>
<td><input type=text name=quest></td>
</tr>
<tr>
<td>Answer:</td>
<td><textarea name=answ cols=45 rows=5></textarea></td>
</tr>
<td>&nbsp;</td>
<td><input type=submit value=\"Save\"></td>
</tr>
</form></table></p><hr color=#018BC1>");
	$res = mysql_query("select * from faq order by id asc");
	for ($i = 0; $i < mysql_num_rows($res); $i++) {
		$id = mysql_result($res, $i, "id");
		$quest = mysql_result($res, $i, "quest");
		$answ = mysql_result($res, $i, "answ");
		echo("<h1>FAQ # $id:</h1>

<p><table border=0 cellspacing=0 cellpadding=0>
<form action=./index.php?y=11 method=post>
<input type=hidden name=fform value=$id>
<input type=hidden name=fac value=trash>
<tr>
<td><input type=submit value=\"Delete\" onclick=\"return deleteThis();\">&nbsp;&nbsp;</td></form>
<form action=./index.php?y=11 method=post><td>
<input type=submit value=\"Save\">&nbsp;&nbsp;<input type=reset value=\"Reset\"></td></tr>
</table></p>
<p><table border=0>
<input type=hidden name=fform value=$id>
<tr>
<td>Question:</td>
<td><input type=text name=quest value=\"$quest\"></td>
</tr>
<tr>
<td>Answer:</td>
<td><textarea name=answ cols=45 rows=5>$answ</textarea></td>
</tr>
</form></table></p><br>");
	}



} elseif ($y == 12) {
	surplus();
	title();
	if (is_numeric($_POST[fform])) {
		if ($_POST[fform] == 0) {
			$fquery = "insert into tads (text) values ('$_POST[text]')";
		} elseif ($_POST[fac] == 'trash') {
			$fquery = "delete from tads where id=$_POST[fform]";
		} else {
			$fquery = "update tads set text='$_POST[text]' where id=$_POST[fform]";
		}
		$res = mysql_query($fquery);
	}

	echo("</p>Use the below form to add new featured ads. These ads show along your websites sidebar. These ads do not show stats and are different from the banner and text ad rotator. A couple of examples have been provided below so you can see the proper coding to use when adding these featured text ads.</p>");

	echo("<h1>Add A Featured Text Ad:</h1>
<p><table border=0>
<form action=$self_url" . "control/?y=12 method=post>
<input type=hidden name=fform value=0>
<tr>
<td>Text Ad HTML:</td>
<td><textarea name=text cols=45 rows=5></textarea></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type=submit value=\"Save\"></td>
</tr>
</form></table></p><hr color=#018BC1>");
	$res = mysql_query("select * from tads order by id asc");
	for ($i = 0; $i < mysql_num_rows($res); $i++) {
		$id = mysql_result($res, $i, "id");
		$text = mysql_result($res, $i, "text");
		echo("<h1>Featured Text Ad # $id:</h1>

<p><table border=0 cellspacing=0 cellpadding=0>
<form action=$self_url" . "control/?y=12 method=post>
<input type=hidden name=fform value=$id>
<input type=hidden name=fac value=trash>
<tr><td><input type=submit value=\"Delete\" onclick=\"return deleteThis();\">&nbsp;&nbsp;</td>
</form>
<form action=$self_url" . "control/?y=12 method=post>
<td><input type=submit value=\"Save\">&nbsp;&nbsp;<input type=reset value=\"Reset\"></td></tr>
</table><p>

<p><table border=0>
<input type=hidden name=fform value=$id>
<tr>
<td>Text Ad HTML:</td>
<td><textarea name=text cols=45 rows=5>$text</textarea></td>
</tr>
</form></table></p><br>");
	}



} elseif ($y == 13) {
	surplus();
	title();
	$res = mysql_query("select date from 7stat order by date desc");
	for ($i = 0; $i < mysql_num_rows($res); $i++) {
		$arr1[] = mysql_result($res, $i);
	}
	$arr1u = array();
	for ($i = 0; $i < count($arr1); $i++) {
		if (!in_array($arr1[$i], $arr1u)) { $arr1u[] = $arr1[$i];}
	}
	$arr1u = array_slice($arr1u, 0, $keep_stats);
	$arr2 = array();
	while (list($k, $v) = each($arr1u)) {
		$res = mysql_query("select sum(num) from 7stat where date='$v'");
		$arr2[$v] = mysql_result($res, 0);
	}
	reset($arr2);
	while (list($k, $v) = each($arr2)) {
		$maxnum = $maxnum + $v;
	}
	$maxnum = round($maxnum);
	echo("\n<p>Below you will see your members surf stats for both credits earned and websites shown. These stats are up-to-date everytime the page is accessed or refreshed. You can target stats by date using the small form at the bottom of this page.</p>");
	echo("\n<h1>Member Credits Earned Surfing:</h1>
<p><table border=0>");
	reset($arr2);
	while (list($k, $v) = each($arr2)) {
		$v = round($v);
		$px = 500 * ((($v * 100) / $maxnum) / 100);
		$px = round($px);
		echo("\n<tr><td>$k</td><td>&nbsp;&nbsp; <b>$v Credits</b></td></tr>");
	}
	echo("\n</table><b>Total Credits Earned: $maxnum Credits</b></p><br>");
	$res = mysql_query("select date from 7statsite order by date desc");
	$sarr1 = array();
	for ($i = 0; $i < mysql_num_rows($res); $i++) {
		$sarr1[] = mysql_result($res, $i);
	}
	$sarr1u = array();
	for ($i = 0; $i < count($sarr1); $i++) {
		if (!in_array($sarr1[$i], $sarr1u)) { $sarr1u[] = $sarr1[$i];}
	}
	$sarr1u = array_slice($sarr1u, 0, $keep_site_stats);
	$sarr2 = array();
	reset($sarr1u);
	while (list($k, $v) = each($sarr1u)) {
		$res = mysql_query("select sum(num) from 7statsite where date='$v'");
		$sarr2[$v] = mysql_result($res, 0);
	}
	$maxnum = 0;
	reset($sarr2);
	while (list($k, $v) = each($sarr2)) {
		$maxnum = $maxnum + $v;
	}
	$maxnum = round($maxnum);
	echo("\n<h1>Member Websites Shown:</h1><table border=0>");
	reset($sarr2);
	while (list($k, $v) = each($sarr2)) {
		$v = round($v);
		$px = 500 * ((($v * 100) / $maxnum) / 100);
		$px = round($px);
		echo("\n<tr><td>$k</td><td align=left>&nbsp;&nbsp; <b>$v Websites</b></td></tr>");
	}
	echo("\n</table><b>Total Websites Shown: $maxnum Websites</b></p><hr color=#018BC1>");
	echo("\n<p><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
<form name=\"form1\" method=\"post\" action=\"view.php\">
<tr>
<td><b>View Complete Stats By Day:</b><br>
<input type=\"text\" name=\"get_day\"> - YYYY-MM-DD<br>
<input type=\"submit\" name=\"submit\" value=\"  View  \"></td>
</tr>
</form></table></p>");



} elseif ($y == 14) {
	surplus();
	title();
	echo("<p>Your banner and text ad rotator is where you will add banners and text ads that were purchased by members. Make sure to fill in each form correctly so the members can view their statistics from within their accounts.</P>
<p><a href=ad_admin.php>Click Here to go to your Banner & Text Ad Rotator Manager</a></p>");



} elseif ($y == 15) {
	surplus();
	title();
	echo("<p>Your transaction manager will allow you to either credit or debit a members account.</p>
<p><a href=transaction_manager.php>Click Here to go to your Transaction Manager</a></p>");



} elseif ($y == 16) {
	surplus();
	title();
	if (is_numeric($_POST[fform]) && $_POST[action] == 'add') {
		if (get_magic_quotes_gpc() == 0) {
			$_POST[name] = addslashes($_POST[name]);
			$_POST[code] = addslashes($_POST[code]);
		}
		$res = mysql_query("INSERT INTO merchant_codes (name, code) VALUES ('$_POST[name]', '$_POST[code]')") or die ("There was a MySQL error, this was:<br>" . mysql_error());
		echo("<p>SUCCESS! Added new payment processor: $_POST[name].</p>");
	} elseif (is_numeric($_POST[fform]) && $_POST[action] == 'edit') {
		if (get_magic_quotes_gpc() == 0) {
			$_POST[name] = addslashes($_POST[name]);
			$_POST[code] = addslashes($_POST[code]);
		}
		//exit("UPDATE merchant_codes SET name='$_POST[name]', code='$_POST[code]' WHERE id=$_POST[fform]");
		$res = mysql_query("UPDATE merchant_codes SET name='$_POST[name]', code='$_POST[code]' WHERE id=$_POST[fform]") or die ("There was a MySQL error, this was:<br>" . mysql_error());
		echo("<p>SUCCESS! Edited payment processor: $_POST[name].</p>");
	} elseif (is_numeric($_POST[fform]) && $_POST[action] == 'delete') {
		$res = mysql_query("DELETE FROM merchant_codes WHERE id=$_POST[fform]") or die ("There was a MySQL error, this was:<br>" . mysql_error());
		echo("<p>SUCCESS! Deleted payment processor: $_POST[name].</p>");
	} else {
		echo("</p>You can add new payment processors using the form below. Use the preloaded Alertpay and Paypal codes for examples when setting up new processors.</p>");
		echo("<h1>Add A New Payment Processor</h1>");
		echo("\n
<table border=\"0\" cellspacing=\"0\" cellpadding=\"2\">
<form name=\"forma\" method=\"post\" action=\"$self_url" . "control/?y=16\">
  <tr>
    <td>Name:</td>
    <td><input type=\"text\" name=\"name\"></td>
  </tr>
  <tr>
    <td>Code:</td>
    <td><textarea name=\"code\" cols=\"60\" rows=\"10\" wrap=\"VIRTUAL\"></textarea></td>
  </tr>
  <tr>
    <td>Save:</td>
    <td><input type=\"hidden\" name=\"fform\" value=\"0\"><input type=\"hidden\" name=\"action\" value=\"add\"><input type=\"submit\" name=\"Submit\" value=\"Save\"></td>
  </tr>
</table>
</form></p><hr color=#018BC1>");
		$res = mysql_query("SELECT * FROM merchant_codes ORDER BY id");
		if (mysql_num_rows($res) >= 1) {
			echo("<h1>Edit Or Delete Payment Processors</h1>");
			for ($i = 0; $i < mysql_num_rows($res); $i++) {
				$id = mysql_result($res, $i, "id");
				$name = mysql_result($res, $i, "name");
				$code = mysql_result($res, $i, "code");
				echo("<p><table border=\"0\" cellspacing=\"0\" cellpadding=\"2\">
<form name=\"forma\" method=\"post\" action=\"$self_url" . "control/?y=16\">
  <tr>
    <td>Name:</td>
    <td><input type=\"text\" name=\"name\" value=\"$name\"></td>
  </tr>
  <tr>
    <td>Code:</td>
    <td><textarea name=\"code\" cols=\"60\" rows=\"10\" wrap=\"VIRTUAL\">$code</textarea></td>
  </tr>
  <tr>
    <td>Save:</td>
    <td><input type=\"hidden\" name=\"fform\" value=\"$id\"><input type=\"hidden\" name=\"action\" value=\"edit\"><input type=\"submit\" name=\"Submit\" value=\"Save\"></td>
  </tr></form>
 <form name=\"forma\" method=\"post\" action=\"$self_url" . "control/?y=16\">
  <tr>
    <td>Delete:</td>
    <td><input type=\"hidden\" name=\"fform\" value=\"$id\"><input type=\"hidden\" name=\"action\" value=\"delete\"><input type=\"submit\" name=\"Submit\" value=\"Delete\" onclick=\"return deleteThis();\"></td>
  </tr></form>
</table></p><br>");
			}
		} else {
			echo("<p>No merchants found!</p>");
		}
		echo("<p class=info><b>Coding Your Payment Processors:</b><br>
Make sure to use the following codes when adding in your payment processors...<br><br><b>[cost]</b> = Price of purchase<br><b>[description]</b> = Description of what is about to be purchased<br><b>[user]</b> = User ID of the member<br><b>[email]</b> = Members Email Address</p>");
	}



} elseif ($y == 17) {
	surplus();
	title();
	$pay_out_to = "\n<select name=pay_to>";
	while (list($m, $j) = each($payout_merchants)) {
		$pay_out_to = $pay_out_to . "\n<option value=$m>$j</option>";
	}
	$pay_out_to = $pay_out_to . "\n<option value=all>All Processors</option></select>";
	$last_month = date("m") - 1;
	$year_now = date("Y");
	if ($last_month == 0) {
		$last_month = 12;
		$year_now = $year_now - 1;
	}
	if (date("Y") >= 2020) {
		$years_to_show = "\n<input type=\"text\" size=\"4\" maxlength=\"4\" name=\"get_year\" value=\"" . date("Y") . "\">";
	} else {
		$years_to_show = "<select name=\"get_year\">\n
		<option value=\"2009\">2009</option>\n
		<option value=\"2010\">2010</option>\n
		<option value=\"2011\">2011</option>\n
		<option value=\"2012\">2012</option>\n
		<option value=\"2013\">2013</option>\n
		<option value=\"2014\">2014</option>\n
		<option value=\"2015\">2015</option>\n
		<option value=\"2012\">2016</option>\n
		<option value=\"2013\">2017</option>\n
		<option value=\"2014\">2018</option>\n
		<option value=\"2015\">2019</option>\n
		</select>";
	}
	echo("<script language=\"JavaScript\">\n<!--\nfunction confThis() {\nvar agree=confirm(\"CONFIRM PAYOUT\\nAre you sure you wish to payout all members over cashout minimum for the month $last_month/$year_now?\\nThis can not be undone!\");\nif (agree) {\nreturn true ;\n} else {\nreturn false ;\n}\n}\n// -->\n</script>");
	echo("<p>Use the below form to mass debit your members accounts. Always make sure you have manually paid out your members to their chosen payment processor BEFORE you use the Pay All Members form.</p>");
	print <<< HTMLJHGS
<p><table border="0" cellspacing="0" cellpadding="2">
<form name="forma" method="post" action="view.php">
  <tr>
    <td colspan="2"><b>Generate List of Members to be paid for last month ($last_month/$year_now)</b><br>Generates a list only, does not payout members.</td>
</tr>
<tr>
<td width="52%" height="48">Fees: <input name="fees" type="text" size="6">%<br>
Of Total Payout Figure
</td>
<td width="48%"><input type="hidden" name="generate" value="last_month_list"><input type="submit" name="submit" value="  Generate List  "></td>
</tr></form></table></p>
<p>&nbsp;</p>
<p><table border="0" cellspacing="0" cellpadding="2">
<form name="formb" method="post" action="view.php" onSubmit="return confThis();">
<tr>
<td colspan="3"><b>Pay all Members owed from last month ($last_month/$year_now)</b><br>This will payout all members with cash over the cashout minimum!</td>
</tr>
<tr>
<td width="40%" height="48">Fees: <input name="fees" type="text" size="6">%<br>
Of Total Payout Figure</td>
<td width="40%">Payment Processor:<br>$pay_out_to</td>
<td width="20%"><input type="hidden" name="generate" value="last_month_payout">
<input type="submit" name="submit" value="  Payout  "></td>
</tr></form></table></p>
<p>&nbsp;</p>
<p><table border="0" cellspacing="0" cellpadding="2">
<form name="formc" method="post" action="view.php">
<tr>
<td colspan="2"><b>Generate List of Members to be paid</b><br>
There will be an option to payout all members listed if it is not this current month.</td>
</tr>
<tr>
<td height="48">Month:
            <select name="get_month">
	          <option value="01">January</option>
	          <option value="02">February</option>
	          <option value="03">March</option>
	          <option value="04">April</option>
	          <option value="05">May</option>
	          <option value="06">June</option>
	          <option value="07">July</option>
	          <option value="08">August</option>
	          <option value="09">September</option>
	          <option value="10">October</option>
	          <option value="11">November</option>
	          <option value="12">December</option>
            </select></td>
<td>Year: $years_to_show</td>
</tr>
<tr>
<td width="50%" height="48"><input name="fees" type="text" size="6">%<br>
Of Total Payout Figure</td>
<td width="50%"><input type="hidden" name="generate" value="custom_list"><input type="submit" name="submit" value="  Generate List  "></td>
</tr></form></table></p>
HTMLJHGS;



} elseif ($y == 18) {
	surplus();
	title();
	echo("<p><a href=\"view.php?run=reset_credits\" target=\"_blank\">Reset Credit Surplus</a><br>
<a href=\"view.php?run=reset_cash\" target=\"_blank\">Reset Cash Surplus</a></p><br>");
	print <<< CHEATERFINDER
<h1>Cheater Look Up</h1>
<p><table cellpadding="0" cellspacing="0">
<form action="view.php" name="cfind" method="post">
<tr>
<td>Search:</td>
<td><input name="valza" type="text" size="44"></td>
</tr>
<tr>
<td>Look Up By:</td>
<td><select name="findz">
<option value="ip">Members that signed up with the IP Address</option>
<option value="mname">Members with the Name</option>
<option value="eaddr">Members using the Email Address</option>
<option value="edom">Members using the Email Domain</option>
<option value="psis">Members using the Password</option>
<option value="crds">Members with Credits above</option>
<option value="cash">Members with Cash above</option>
<option value="scrds">Sites with Credits above</option>
</select></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="hidden" name="find_em" value="ok_go">
<input type="submit" name="Submit" value="Search Database"></td>
</tr></form></table></p>

<p>&nbsp;</p>
<p class=info>Search Example:<br>
If you wish to find all members with the name &quot;<b>Bill</b>&quot;, enter <b>Bill</b> in the text field and select Find &quot;<b>Members with the Name</b>&quot;.
<br><br>
The email domain should be <b>some-site.com</b> with no <b>@</b> or <b>www.</b></p>
CHEATERFINDER;



} elseif ($y == 19) {
	surplus();
	title();
	echo("<p>From within your Paid To Click Management, you will be able to add, edit, and delete PTC Ads. Always make sure to fill in the forms correctly so your members can see their statistincs in their members area.</p>");
	echo("<p><a href=./ptc.php?page=addnew>Click Here to Add New PTC Campaign</a><br><a href=./editptc.php>Click Here to Edit Or Delete PTC Campaign(s)</a></p>");



} elseif ($y == 20) {
	surplus();
	title();

	if ($_POST['form'] == 'addnews' && $_POST['type'] != "") {
		if ($_POST['bansite'] == "") {
			echo("<p>Site url is blank!<br><a href=./index.php?y=20>Back to banned sites</a></p>");
			mysql_close;
			exit;
		} elseif (!eregi(".", $_POST['bansite'])) {
			echo("<p>Site url is invalid!<br><a href=./index.php?y=20>Back to banned sites</a></p>");
			mysql_close;
			exit;
		}
		if ($_POST['type'] == 'a' && !ereg("http://", $_POST['bansite'])) {
			echo("<p>Site url is invalid! You must include http:// with an affiliate URL<br><a href=./index.php?y=20>Back to banned sites</a></p>");
			mysql_close;
			exit;
		}
		$check = mysql_query("SELECT * FROM banned_sites WHERE domain='".$_POST['bansite']."'");
		if (mysql_num_rows($check) != 0) {
			echo("<p>Cannot ad the same site/domain twice<br><a href=./index.php?y=20>Back to banned sites</a></p>");
			mysql_close;
			exit;
		}
		$doit = mysql_query("INSERT INTO banned_sites (domain, type) VALUES ('".$_POST['bansite']."', '".$_POST['type']."')");
		echo("<p><b>Done!</b> Inserted Banned Site: ".$_POST['bansite']."<br><a href=./index.php?y=20>Back to banned sites</a></p>");
		mysql_close;
		exit;
	} elseif ($_POST['form'] == 'delete' && $_POST['bannedsite'] != "") {
		$delit = mysql_query("DELETE FROM banned_sites WHERE domain='".$_POST['bannedsite']."'");
		echo("<p><b>Done !</b> ".$_POST['bannedsite']." was deleted!<br><a href=./index.php?y=20>Back to banned sites</a></p>");
		mysql_close;
		exit;
	} else {

		if (!isset($is) || $is < 1 || !is_numeric($is)) {
			$is = 1;
		}
		$strt = ($is - 1) * $lim;
		$bquery = "select * from banned_sites";
		$cpages = $bquery;
		$bquery = $bquery . " order by id asc limit $strt, $lim";
		$res = mysql_query($bquery);
		$pages = ceil(mysql_num_rows(mysql_query($cpages)) / $lim);
		if ($pages > 1) {
			echo("<p>");
			for ($i = 1; $i <= $pages; $i++) {
				echo("|");
				if ($i == $is) {echo("<b>");}
				else {echo("<a href=./index.php?y=20&is=$i>");}
				echo("Page $i");
				if ($i == $is) {echo("</b>");}
				else {echo("</a>");}
				echo("|  ");
			}
			echo("</p>");
		}
		echo("<p><table width=100% border=0 cellpadding=2><tr style=\"background-color: #4DA0C6\">
<td align=center><b><font color=#FFFFFF>Banned Domain</font></b></td>
<td align=center><b><font color=#FFFFFF>Ban Type</font></b></td>
<td align=center><b><font color=#FFFFFF>Delete Ban</font></b></td>
</tr>");
		for ($i = 0; $i < mysql_num_rows($res); $i++) {
			$theculp = mysql_result($res, $i, "domain");
			$thetype = mysql_result($res, $i, "type");
			if ($thetype == 'd') {
				$showtype = 'Domain';
			} else {
				$showtype = 'Affiliate URL';
			}
			echo("<tr style=\"background-color: #F0F8FF\">
<td align=center>$theculp</td>
<td align=center>$showtype</td>
<td align=center><form action=\"./index.php?y=20\" method=POST onSubmit=\"return deleteIt()\"><input type=hidden name=bannedsite value=\"$theculp\"><input type=hidden name=form value=delete><input type=submit value=\"Delete\" onclick=\"return deleteThis();\"></td>
</form>
</tr>");
		}
		echo("</table></p>");

		echo("<h1>Add A New Website Ban</h1>
<p><table><form action=\"./index.php?y=20\" method=POST>
<tr>
<td>Ban Domain: </td>
<td><input type=text name=bansite></td>
</tr>
<tr>
<td>Ban Type: </td>
<td><select name=type><option value=d>Domain</option><option value=a>Affiliate URL</option></select></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type=hidden name=form value=addnews><input type=submit value=\"Ban Website\"></td></tr>");
		echo("</form></table></p>");
	}
	echo("<p class=info><b>Examples:</b><br>Enter somesite.com AND www.somesite.com with 'Type' set as 'Domain' to ban any URL from this domain.<br><br>
Enter http://www.somesite.com/get_paid.php and http://somesite.com/get_paid.php (both with http://) with 'Type' set to 'Affiliate URL' to ban only this affiliate url</p>");



} elseif ($y == 21) {
	surplus();
	title();
	if ($_POST['form'] == 'addnew') {
		if ($_POST['banemail'] == "") {
			echo("<p><b>Email address is blank or invalid</b><br><a href=./index.php?page=21>Back to banned emails</a></p>");
			mysql_close;
			exit;
		} elseif (!eregi("@", $_POST['banemail']) && !eregi(".", $_POST['banemail'])) {
			echo("<p><b>Email address is invalid</b><br><a href=./index.php?y=21>Back to banned emails</a></p>");
			mysql_close;
			exit;
		}
		$check = mysql_query("SELECT * FROM banned_emails WHERE value='".$_POST['banemail']."'");
		if (mysql_num_rows($check) != 0) {
			echo("<p><b>Cannot ad the same address twice</b><br><a href=./index.php?y=21>Back to banned emails</a></p>");
			mysql_close;
			exit;
		}

		$doit = mysql_query("INSERT INTO banned_emails (value) VALUES ('".$_POST['banemail']."')");
		echo("<p><b>Done!</b> Inserted Banned Email: ".$_POST['banemail']."<br><a href=./index.php?y=21>Back to banned emails</a></p>");
		mysql_close;
		exit;
	} elseif ($_POST['form'] == 'delete' && $_POST['bannedemail'] != "") {
		$delit = mysql_query("DELETE FROM banned_emails WHERE value='".$_POST['bannedemail']."'");
		echo("<p><b>Done!</b> ".$_POST['bannedemail']." was deleted!<br><a href=./index.php?y=21>Back to banned emails</a></p>");
		mysql_close;
		exit;
	} else {
		if (!isset($is) || $is < 1 || !is_numeric($is)) {
			$is = 1;
		}
		$strt = ($is - 1) * $lim;
		$bquery = "select * from banned_emails";
		$cpages = $bquery;
		$bquery = $bquery . " order by id asc limit $strt, $lim";
		$res = mysql_query($bquery);
		$pages = ceil(mysql_num_rows(mysql_query($cpages)) / $lim);
		if ($pages > 1) {
			echo("<p>");
			for ($i = 1; $i <= $pages; $i++) {
				echo("|");
				if ($i == $is) {echo("<b>");}
				else {echo("<a href=$ad_url/?y=21&is=$i>");}
				echo("Page $i");
				if ($i == $is) {echo("</b>");}
				else {echo("</a>");}
				echo("|  ");
			}
			echo("</p>");
		}
		echo("<p><table width=100% border=0 cellpadding=2><tr style=\"background-color: #4DA0C6\">
<td align=center><b><font color=#FFFFFF>Banned Email Addresses</font></b></td>
<td align=center><b><font color=#FFFFFF>Delete</font></b></td>
</tr>");
		for ($i = 0; $i < mysql_num_rows($res); $i++) {
			$theculp = mysql_result($res, $i, "value");
			echo("<tr style=\"background-color: #F0F8FF\">
<td align=center>$theculp</td>
<td align=center><form action=\"./index.php?y=21\" method=POST onSubmit=\"return deleteIt()\"><input type=hidden name=bannedemail value=\"$theculp\"><input type=hidden name=form value=delete><input type=submit value=\"Delete\" onclick=\"return deleteThis();\">
</td></tr>");
		}
		echo("</form></table>");

		echo("<h1>Add A New Email Ban</h1>
<p><table><form action=\"./index.php?y=21\" method=POST>
<tr>
<td>Email Address: </td>
<td><input type=text name=banemail></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type=hidden name=form value=addnew><input type=submit value=\"Ban Email\"></td>
</tr>");
		echo("</form></table>");
		echo("<p class=info><b>Example:<b><br>Enter *@hotmail.com to ban ALL hotmail.com email addresses<br>or<br>Enter the-banned-email@the-email-domain.com</p>");
	}



} elseif ($y == 22) {
	surplus();
	title();
	if ($_POST['form'] == 'addnew') {
		$addit = 'yes';
		if ($_POST['banipa'] == "") {
			$error = "<p><b>IP address is not valid</b><br><a href=./index.php?y=22>Back to banned IP's</a></p>";
			$addit = 'no';
		}
		$validip = 'no';
		if (is_string($_POST['banipa']) && ereg('^([0-9]{1,3})\.([0-9*]{1,3})\.' . '([0-9*]{1,3})\.([0-9*]{1,3})$', $_POST['banipa'], $sect)) {
			if ($sect[1] <= 255 && ($sect[2] <= 255 ||$sect[2] == '*') && ($sect[3] <= 255 ||$sect[3] == '*') && ($sect[4] <= 255 || $sect[4] == '*')) {
				if ($sect[1] <= 255 && $sect[2] <= 255 && $sect[3] <= 255 && $sect[4] == '*') {
					$recip = "$sect[1].$sect[2].$sect[3].*";
					$validip = 'yes';
				} elseif ($sect[1] <= 255 && $sect[2] <= 255 && $sect[3] == '*' && $sect[4] == '*') {
					$recip = "$sect[1].$sect[2].*.*";
					$validip = 'yes';
				} elseif ($sect[1] <= 255 && $sect[2] == '*' && $sect[3] == '*' && $sect[4] == '*') {
					$recip = "$sect[1].*.*.*";
					$validip = 'yes';
				} elseif ($sect[1] <= 255 && $sect[2] <= 255 && $sect[3] <= 255 && $sect[4] <= 255) {
					$recip = "$sect[1].$sect[2].$sect[3].$sect[4]";
					$validip = 'yes';
				} else {
					$validip = 'no';
					$recip = "";
				}
			}
		}
		if ($_POST['banipa'] != "" && $addit == 'yes' && $validip == 'yes' && $recip != "") {
			$check = mysql_query("SELECT * FROM banned_ipadds WHERE value='$recip'");
			if (mysql_num_rows($check) == 0) {
				$doit = mysql_query("INSERT INTO banned_ipadds (value) VALUES ('$recip')");
				echo("<p><b>Done!</b> Inserted Banned IP Address: $recip<br><a href=./index.php?y=22>Back to banned IP's</a></p>");
			}
		} else {
			echo("<p><b>Error!</b> $_POST[banipa] is an invalid IP Address!<br><a href=./index.php?y=22>Click here to go back.</a></p>");
		}
	} elseif ($_POST['form'] == 'delete' && $_POST['bannedipid'] != "") {
		$delit = mysql_query("DELETE FROM banned_ipadds WHERE id=$_POST[bannedipid]");
		echo("<p><b>Success!</b> The IP Addresses were deleted!<br><a href=./index.php?y=22>Back to banned IP's</a></p>");
	} else {
		if (!isset($is) || $is < 1 || !is_numeric($is)) {
			$is = 1;
		}
		$strt = ($is - 1) * $lim;
		$bquery = "select * from banned_ipadds";
		$cpages = $bquery;
		$bquery = $bquery . " order by id asc limit $strt, $lim";
		$res = mysql_query($bquery);
		$pages = ceil(mysql_num_rows(mysql_query($cpages)) / $lim);
		if ($pages > 1) {
			echo("<p>");
			for ($i = 1; $i <= $pages; $i++) {
				echo("|");
				if ($i == $is) {echo("<b>");}
				else {echo("<a href=./index.php?y=22&is=$i>");}
				echo("Page $i");
				if ($i == $is) {echo("</b>");}
				else {echo("</a>");}
				echo("|  ");
			}
			echo("</p>");
		}
		echo("<p><table width=100% border=0 cellpadding=2><tr style=\"background-color: #4DA0C6\">
<td align=center><b><font color=#FFFFFF>Banned IP Address</font></b></td>
<td align=center><b><font color=#FFFFFF>Delete</font></b></td>
</tr>");
		for ($i = 0; $i < mysql_num_rows($res); $i++) {
			$theculpid = mysql_result($res, $i, "id");
			$intval = mysql_result($res, $i, "value");
			echo("<tr style=\"background-color: #F0F8FF\">
<td align=center>$intval</td>
<td align=center><form action=\"./index.php?y=22\" method=POST onSubmit=\"return deleteIt()\"><input type=hidden name=bannedipid value=\"$theculpid\"><input type=hidden name=form value=delete><input type=submit value=\"Delete\" onclick=\"return deleteThis();\"></td>
</tr>");
		}
		echo("</form></table></p>");
		echo("<h1>Add A New IP Ban</h1>
<p><table>
<form action=\"./index.php?y=22\" method=POST>
<tr><td>IP Address: </td>
<td><input type=text name=banipa maxlength=31 size=31></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type=hidden name=form value=addnew><input type=submit value=\"Ban IP\"></td>
</tr>");
		echo("</form></table>");
		echo("<p class=info><b>Example:</b><br>
Enter 100.100.100.* to ban the IP range 100.100.100.0 - 100.100.100.255 from signing up for an account<br><br>
Enter 100.100.*.* to ban the ip addresses 100.100.0.0 through to 100.100.255.255<br><br>
Enter 100.*.*.* to ban the IP Addresses 100.0.0.0 - 100.255.255.255<br><br>
Valid IP Addresses:<br>
0-255.0-255.0-255.0-255<br>
0-255.0-255.0-255.*<br>
0-255.0-255.*.*<br>
0-255.*.*.*</p>");
	}



} elseif ($y == 23) {
	echo("<p>&nbsp;</p>
<h1>Logged Out</h1>
<p>You have successfully logged out of the admin area. <a href=index.php>Click Here</a> to log back in.</p>");
	session_destroy();
}



echo("</td>
</tr></table></center>");
echo("<p align=\"center\"><font color=\"#FFFFFF\"><b>Have ideas for features and additions but dont know where to turn?</b><br>Try <a href=mailto:sales@cobrascripts.com>CobraScripts</a> for the best coding, fastest service, and lowest coder prices available online.</font></p>
</body>
</html>");
mysql_close;
exit;
?>
