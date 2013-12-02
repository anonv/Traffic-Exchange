<?php
session_start();
include("../vars.php");
include("../headfoot.php");
include("../auth.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
$date = date("Y-m-d");
$res = checkauth();
$usrid = mysql_result($res, 0, "id");
$myref = mysql_result($res, 0, "ref");
if ($_GET['banid'] != "") {
	$gptc = mysql_query("SELECT * FROM ptc_orders WHERE linkid='$_GET[banid]'");
	if (mysql_num_rows($gptc) != 0) {
		$ad_type = mysql_result($gptc, 0, "type2");
		$cashclick = mysql_result($gptc, 0, "cash_click");
		$creditclick = mysql_result($gptc, 0, "credit_click");
		$ad_timer = mysql_result($gptc, 0, "adtimer");
		$day_lock = mysql_result($gptc, 0, "day_lock");
		$clks_remain = mysql_result($gptc, 0, "clicks_remain");
		$order_datax = mysql_fetch_array($gptc);
	} else {
		echo("<html>\n<head>\n<title>$title Paid to Click</title>\n</head><body onLoad=\"alert('Ad not found!'); window.close();\"></body></html>");
		mysql_close;
		exit;
	}
} else {
	echo("<html>\n<head>\n<title>$title Paid to Click</title>\n</head><body onLoad=\"alert('Invalid link in pb!'); window.close();\"></body></html>");
	mysql_close;
	exit;
}
if ($clks_remain <= 0) {
	echo("<html>\n<head>\n<title>$title Paid to Click</title>\n</head><body onLoad=\"alert('This ad has expired!'); window.close();\"></body></html>");
	mysql_close;
	exit;
}
if ($day_lock >= 1) {
	$the_sql_day = strftime("%Y-%m-%d", strtotime("$date + $day_lock days ago"));
	$and_date_sql = " AND (cdate>'$the_sql_day')";
} else {
	$and_date_sql = "";
}
$query1 = mysql_query("SELECT * FROM ptc_tracking WHERE (userid=$usrid) AND (banlinkid='$_GET[banid]')$and_date_sql");
if ($_GET['action'] == 'open' && $_GET['vc'] == $_SESSION['sess_data']['verif_data'] && urlencode($_GET['site']) == $_SESSION['sess_data']['ptcurlis']) {
	if (mysql_num_rows($query1) == 0) {
		$_SESSION['sess_data']['verif_data'] = md5($_GET['vc']);
		$_SESSION['sess_data']['c_linkid'] = $_GET['banid'];
		$_SESSION['sess_data']['ptctime'] = time();
		if (!isset($s_delay)) {$s_delay = $ad_timer;}
		$ptcbar_timer = "<input type=button name=show_c style=\"border: 0px;border-style: none;Color: #FFFFFF;background-color: #000000;width:20px; height:20px;\" readonly>";
		$ptcbarhtmla = file_reader("/bars.html");
		$ptcbarhtmla = str_replace('[timer]', $ptcbar_timer, $ptcbarhtmla);
		$ptcbarhtmla = str_replace('[userid]', $usrid, $ptcbarhtmla);
		echo("<html>\n<head>\n<title>PTC Bar</title>
  <link rel=\"stylesheet\" type=\"text/css\" href=\"/bar.css\">
  <script language=\"JavaScript\">
   <!--
    if (parent.location.href == self.location.href) {
	 window.location.href = './ptc.php?".session_name()."=".session_id()."';
	}
	var next_to_go=true;
	var waitdelay=45;
	var adcounter=45;
	var originaltime=45;
	var org_delay;
	function start_timer(g_time, g_del) {
	 waitdelay = g_time;
	 adcounter = g_time;
	 originaltime = g_time;
	 org_delay = g_del;
	 start_countdown();
	}
	function start_countdown() {\nif (next_to_go) {\nif (adcounter>=1) {\ndocument.c_form.show_c.value=\"\" + adcounter-- + \"\";\ntimerID=setTimeout(\"start_countdown()\",1000);\n}\nelse {\nif (waitdelay==originaltime){\nlocation.href=\"./ptcbar.php?banid=$_GET[banid]&action=final_validate&fvc=" . $_SESSION['sess_data']['verif_data'] . "&".session_name()."=".session_id()."\";\n}\nelse{\nlocation.href=\"./ptcbar.php?banid=$_GET[banid]&action=final_validate&fvc=" . $_SESSION['sess_data']['verif_data'] . "&".session_name()."=".session_id()."\";\n}\n}\n}\n}\n\n//--></script>\n</head>\n<body onLoad=\"start_timer($s_delay, $ad_timer);\" class=\"bar\">\n<form name=\"c_form\">");
		echo($ptcbarhtmla);
		echo("</form>\n</body>\n</html>");
		mysql_close;
		exit;
	} else {
		echo("<html>\n<head>\n<title>$title Paid to Click</title>\n</head><body onLoad=\"alert('You have already claimed credit for this ad!'); window.close();\"></body></html>");
		mysql_close;
		exit;
	}
} elseif ($_GET['action'] == 'final_validate' && $_GET['fvc'] == $_SESSION['sess_data']['verif_data']) {
	if (!isset($_SESSION['sess_data']['ptctime']) || (time() - $_SESSION['sess_data']['ptctime']) >= $ad_timer) {
		$_SESSION['sess_data']['ptctime'] = "";
	} else {
		$_SESSION['sess_data']['ptctime'] = "";
		header("Location: paidtoclick.php?adid=$_GET[banid]&action=start&".session_name()."=".session_id()."");
		mysql_close;
		exit;
	}
	if (mysql_num_rows($query1) == 0 && $clks_remain >= 1) {
		if ($ad_type == 'cash') {
			$get_stats = mysql_query("SELECT * FROM monthly_stats WHERE usrid=$usrid && yearis=" . date("Y") . " && monthis=" . date("m"));
			if (mysql_num_rows($get_stats) == 0) {
				@mysql_query("INSERT INTO monthly_stats (usrid, ptc_cash_e, tot_owed, monthis, yearis) VALUES ($usrid, $cashclick, $cashclick, " . date("m") . ", " . date("Y") . ")") or die (mysql_error());
			} else {
				@mysql_query("UPDATE monthly_stats SET ptc_cash_e=ptc_cash_e+$cashclick, tot_owed=tot_owed+$cashclick WHERE usrid=$usrid && yearis=" . date("Y") . " && monthis=" . date("m")) or die (mysql_error());
			}
			@mysql_query("UPDATE user SET roi_cash=roi_cash+$cashclick, ptc_clicks=ptc_clicks+1, ptc_cash=ptc_cash+$cashclick, lifetime_cash=lifetime_cash+$cashclick WHERE id=$usrid");
			$dgiven = "\$$cashclick";
			if ($myref >= 1) {
				$accs = mysql_query("SELECT id, ptc_levels FROM acctype");
				for ($i = 0; $i < mysql_num_rows($accs); $i++) {
					$accids = mysql_result($accs, $i, "id");
					$r_bons = mysql_result($accs, $i, "ptc_levels");
					$r_bons2[$accids] = explode(",", $r_bons);
					$acc_r_bon[$accids] = count($r_bons2[$accids]);
				}
				if ($acc_r_bon[2] > $acc_r_bon[1]) {$greatest = 2; } elseif ($acc_r_bon[1] > $acc_r_bon[2]) {$greatest = 1; } else {$greatest = 2; }
				if ($myref >= 1 && ($acc_r_bon[1] > 0 || $acc_r_bon[2] > 0)) {
					$cc = 0;
					$ref_id[$cc]=$myref;
					for ($v = 0; $v < ($acc_r_bon[$greatest] - 1); $v++) {
						$myref = get_referral($myref);
						if (!$myref || $myref == 0)
						break;
						++$cc;
						$ref_id[$cc] = $myref;
					}
					$givento_ref = credit_r_bonuses($ref_id,"cash",$cashclick);
				}
				@mysql_query("UPDATE user SET cshtoref=cshtoref+$givento_ref WHERE id=$usrid");
			}
		} else {
			@mysql_query("UPDATE user SET credits=credits+$creditclick, ptc_clicks=ptc_clicks+1, ptc_crds=ptc_crds+$creditclick, lifetime_credits=lifetime_credits+$creditclick WHERE id=$usrid");
			$dgiven = "$creditclick credit(s)";
			if ($myref >= 1) {
				$accs = mysql_query("SELECT * FROM acctype");
				for ($i = 0; $i < mysql_num_rows($accs); $i++) {
					$accids = mysql_result($accs, $i, "id");
					$r_bons = mysql_result($accs, $i, "levels");
					$r_bons2[$accids] = explode(",", $r_bons);
					$acc_r_bon[$accids] = count($r_bons2[$accids]);
				}
				if ($acc_r_bon[2] > $acc_r_bon[1]) {$greatest = 2; } elseif ($acc_r_bon[1] > $acc_r_bon[2]) {$greatest = 1; } else {$greatest = 2; }
				if ($myref >= 1 && ($acc_r_bon[1] > 0 || $acc_r_bon[2] > 0)) {
					$cc = 0;
					$ref_id[$cc]=$myref;
					for ($v = 0; $v < ($acc_r_bon[$greatest] - 1); $v++) {
						$myref = get_referral($myref);
						if(!$myref || $myref == 0)
						break;
						++$cc;
						$ref_id[$cc] = $myref;
					}
					$givento_ref = credit_r_bonuses($ref_id,"credits",$creditclick);
				}
				@mysql_query("UPDATE user SET toref=toref+$givento_ref WHERE id=$usrid");
			}
		}
		$chkcc = mysql_query("SELECT * FROM ptc_tracking WHERE userid=$usrid AND banlinkid='$_GET[banid]'");
		if (mysql_num_rows($chkcc) == 0 && mysql_num_rows($query1) == 0) {
			$updtrack = mysql_query("INSERT INTO ptc_tracking VALUES ($usrid, '$_GET[banid]', '$date')");
		} elseif (mysql_num_rows($chkcc) != 0 && mysql_num_rows($query1) == 0) {
			$updtrack = mysql_query("UPDATE ptc_tracking SET cdate='$date' WHERE userid=$usrid && banlinkid='$_GET[banid]'");
		}
		@mysql_query("UPDATE ptc_orders SET clicks_remain=clicks_remain-1 WHERE linkid='$_GET[banid]'");
		$ptcbarhtmlb = file_reader("/bars_done.html");
		$ptcbarhtmlb = str_replace('[userid]', $usrid, $ptcbarhtmlb);
		$ptcbarhtmlb = str_replace('[amount]', $dgiven, $ptcbarhtmlb);
		$_SESSION['sess_data']['verif_data'] = "";
		$_SESSION['sess_data']['c_linkid'] = "";
		echo("<html>\n<head>\n<title>$title Paid to Click</title>\n<link rel=\"stylesheet\" type=\"text/css\" href=\"/bar.css\">\n</head>\n<body class=\"bar\">\n<center>\n");
		echo($ptcbarhtmlb);
                               //echo("<br>You may now close this window.");
		echo("</center>\n</body>\n</html>");
		mysql_close;
		exit;
	} else {
		echo("<html>\n<head>\n<title>$title Paid to Click</title>\n</head><body onLoad=\"alert('You have already claimed credit for this ad!'); window.close();\"></body></html>");
		mysql_close;
		exit;
	}
} else {
	echo("<html>\n<head>\n<title>$title Paid to Click</title>\n</head><body onLoad=\"alert('Invalid link!'); window.close();\"></body></html>");
	mysql_close;
	exit;
}
?>
