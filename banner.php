<?php
session_start();
include('vars.php');
$simultaneousbanner=3;// number of maxbanners show once at a time
if(isset($_GET['style'])) $style= $_GET['style'];
if(isset($_GET['sb'])) $simultaneousbanner= $_GET['sb'];
function error($error){
	global $self_url,$style,$default_link,$default_banner;
	//global $SERVER_NAME,$self_url,$style,$default_link,$default_banner;
	if ($ban_error_notify == "yes") {
		$db = db("SELECT value FROM admin WHERE field='email'");
		$ad_mail = mysql_result($db, 0, "value");
		@mail($ad_mail,"Ad Rotator Error!","There was an error with the ad rotator:\n\n$error",$email_headers);
	}
	if ($style == "non_ssi") {
		print "document.write('<a href=\"$default_link\" target=\"_blank\"><img src=\"$default_banner\" border=0></a>');\n";
	} else {
		print "<a href=\"$default_link\" target=\"_blank\"><img src=\"$default_banner\" border=0></a>\n";
	}
	exit;
}
// MySQL Function
function db($query) {
	global $db_host,$db_name,$db_user,$db_pwd;
	($mysql_link = @mysql_connect($db_host,$db_user,$db_pwd)) or die (print "Error: Couldn't connect to database:<br><br>".mysql_error());
	@mysql_select_db($db_name,$mysql_link) or die (print "Error: Couldn't Select Database:<br><br>".mysql_error());
	($mysql_result = @mysql_query($query,$mysql_link)) or die (print "Error: Database Select Failed:<br><br>".mysql_error());
	@mysql_close($mysql_link) or die (print "Error: Couldn't close database".mysql_error());
	return $mysql_result;
}
// Display Ad Function
function display_banner(){
	global $self_url,$style,$simultaneousbanner;
	if(isset($_SESSION['banneron']) && count($_SESSION['banneron']) > $simultaneousbanner){
		$_SESSION['banneron'] =array();
	}
	$wheresql = '';
	if(isset($_SESSION['banneron']) && count($_SESSION['banneron']) > 0){
		$wheresql = " WHERE ad_id NOT IN(".implode(',', $_SESSION['banneron']).') ';
	}
	$db = db("SELECT * FROM ad_info $wheresql");
	$totalads = mysql_num_rows($db);
	if ($totalads == 0) {error("There are no banners/ads found in the database"); }
	while ($info = @mysql_fetch_row($db)){
		if (($info[11] == 0) || ($info[12] < $info[11])) {
			for ($i=1;$i<=$info[14];$i++){
				$valid[] = $info[0];
			}
		}
	}
	$num = count($valid);
	$num--;
	if ($num >= 1) {
		srand ((double) microtime() * 1000000);
		$rand_num = rand(0,$num);
		$rand_num = $valid[$rand_num];
	} else {
		$rand_num = $valid[0];
	}
	$_SESSION['banneron'][] = $rand_num;

	$db = db("SELECT * FROM ad_info WHERE ad_id = $rand_num");
	if ($info = mysql_fetch_row($db)){
		$exp = $info[12] + 1;
		$update = db("UPDATE ad_info SET num_exp = $exp WHERE ad_id = $rand_num");
		if ($info[2] == 1){
			if ($exp == $info[11]){
				error("The ad '$info[1] has reached its impression limit, $info[11].\n"."It will no longer be displayed unless you increase the\n"." number of impressions allowed");
			}
			if ($style != "non_ssi") {
				if ($info[15] == 1) {
					$temp = " target=\"_blank\"";
				}
				if ($info[8]) {
					print "<table border=0 cellpadding=0 cellspacing=0><tr><td align=center>\n";
				}
				if ($info[7]) {
					$info[7] = addslashes($info[7]);
					$temp2 = " onMouseOver=\"window.status='$info[7]'; return true\" onMouseOut=\"window.status=''; return true\"";
				}
				print "<a href=\"$self_url"."banner.php?action=r&id=$rand_num\"$temp"."$temp2>\n";
				print "<img src=\"$info[4]\" height=\"$info[5]\" width=\"$info[6]\" border=\"0\" alt=\"$info[7]\"></a>\n";
				if ($info[8]) {
					print "</td></tr><tr><td align=\"center\">$info[8]</td></tr></table>\n";
				}
			} else {
				if ($info[15] == 1){
					$temp = " target=\"_blank\"";
				}
				$info[9] = eregi_replace("'",'',$info[9]);
				$info[7] = eregi_replace("'","\'",$info[7]);
				$info[8] = eregi_replace("'","\\'",$info[8]);
				$info[14] = eregi_replace("'","\\'",$info[14]);
				if ($info[9]) {
					$temp2 = " onMouseOver=\"window.status=\\'$info[9]\\'; return true\" onMouseOut=\"window.status=\\'\\'; return true\"";
				}
				print "document.write('<a href=\"$self_url"."banner.php?action=r&id=$rand_num\"$temp"."$temp2>');\n";
				print "document.write('<img src=\"$info[4]\" width=\"$info[6]\"  height=\"$info[5]\" border=\"0\" alt=\"$info[7]\"></a>');\n";
				if ($info[8]) {
					print "document.write('</td></tr><tr><td align=\"center\">$info[8]</td></tr></table>');\n";
				}
			}
		} else {
			$info[10] = eregi_replace('<!-- Link URL -->',"$self_url"."banner.php?action=r&id=$rand_num",$info[10]);
			$info[10] = eregi_replace('href','target="_blank" href',$info[10]);
			if ($style != "non_ssi") {
				$info[10] = stripslashes($info[10]);
				print "$info[10]";
			} else {
				$info[10] = eregi_replace("'","\\'",$info[10]);
				print "document.write('<table align=\"right\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"468\" height=\"60\" bgcolor=\"black\"><tr><td align=\"center\" valign=\"middle\">');\n";
				print "document.write('$info[10]');\n";
				print "document.write('</td></tr></table>');\n";
			}
		}
	}
}
// Redirect Function
function redirect(){
	global $id;
	if (eregi_replace('[^0-9]','',$info[8])) {
		error("Invalid id given, bad character given, perhaps messed with link or just an old banner link?");
	}
	$db = db("SELECT num_clicks,link_url,type FROM ad_info WHERE ad_id = $id");
	if ($info = @mysql_fetch_row($db)){
		$num = $info[0] + 1;
		$update = db("UPDATE ad_info SET num_clicks = num_clicks+1 WHERE ad_id = $id");
		$stuff = $_SERVER['QUERY_STRING'];
		$stuff = eregi_replace("action=r&id=$id","",$stuff);
		if (($stuff) && ($info[2] == 2)) {
			header("Location: $info[1]?$stuff\n");
			exit;
		}
		header("Location: $info[1]\n");
		exit;
	}
}
// Display Group Function
function display_group() {
	global $style,$gid,$self_url,$simultaneousbanner;
	//global $PHP_SELF,$SERVER_NAME,$style,$gid,$DOCUMENT_ROOT,$self_url;
	if (eregi('[^0-9]',$gid)){
		error("Invalid group id given");
	}
	if(isset($_SESSION['banneron']) && count($_SESSION['banneron']) > $simultaneousbanner){
		$_SESSION['banneron'] =array();
	}
	$wheresql = '';
	if(isset($_SESSION['banneron']) && count($_SESSION['banneron']) > 0){
		$wheresql = " AND a.ad_id NOT IN(".implode(',', $_SESSION['banneron']).') ';
	}

	$db = db("SELECT a.ad_id,prob,num_allow_exp,num_exp,ad_prob FROM gp_info g,ad_info a WHERE gid = $gid AND g.ad_id = a.ad_id AND a.ad_id = g.ad_id $wheresql");
	while ($info = mysql_fetch_row($db)){
		if (($info[2] != 0) && ($info[3] <= $info[2])){
			for ($i=1;$i<=$info[4];$i++){
				$valid[] = $info[0];
			}
		}
		if ($info[2] == 0) {
			for ($i=1;$i<=$info[4];$i++){
				$valid[] = $info[0];
			}
		}
	}
	$num = count($valid);
	$num--;
	if (!$valid[0]) {
		error("Was not able to select any banners for group ($gid).\n"."Perhaps all banners have run out of inpressions?");
	}
	if ($num >= 1) {
		srand ((double) microtime() * 1000000);
		$rand_num = rand(0,$num);
		$rand_num = $valid[$rand_num];
	} else {
		$rand_num = $valid[0];
	}
	$_SESSION['banneron'][] = $rand_num;
	$db = db("SELECT * FROM ad_info WHERE ad_id = $rand_num");
	if ($info = mysql_fetch_row($db)) {
		$exp = $info[12] + 1;
		$update = db("UPDATE ad_info SET num_exp = $exp WHERE ad_id = $rand_num");
		if ($info[2] == 1) {
			if ($exp == $info[11]) {
				error("The advert '$info[1] has reached its impression limit, $info[11].\n"."It will no longer be displayed unless you increase the\n"." number of impressions allowed");
			}
			if ($style != "non_ssi") {
				if ($info[15] == 1) {
					$temp = " target=\"_blank\"";
				}
				if ($info[8]) {
					print "<table border=0 cellpadding=0 cellspacing=0><tr><td align=center>\n";
				}
				if ($info[9] != "") {
					$info[9] = addslashes($info[9]);
					$temp2 = " onMouseOver=\"window.status='$info[9]'; return true\" onMouseOut=\"window.status=''; return true\"";
				}
				print "<a href=\"$self_url"."banner.php?action=r&id=$rand_num\"$temp"."$temp2>\n";
				print "<img src=\"$info[4]\" height=\"$info[5]\" width=\"$info[6]\" border=\"0\" alt=\"$info[7]\"></a>\n";
				if ($info[8]) {
					print "</td></tr><tr><td align=\"center\">$info[8]</td></tr></table>\n";
				}
			} else {
				if ($info[15] == 1) {
					$temp = " target=\"_blank\"";
				}
				if ($info[8]) {
					print "document.write('<table border=0 cellpadding=0 cellspacing=0><tr><td align=center>');\n";
				}
				$info[9] = eregi_replace("'",'',$info[9]);
				$info[7] = eregi_replace("'","\'",$info[7]);
				$info[8] = eregi_replace("'","\\'",$info[8]);
				$info[14] = eregi_replace("'","\\'",$info[14]);
				if ($info[9]) {
					$temp2 = " onMouseOver=\"window.status=\\'$info[9]\\'; return true\" onMouseOut=\"window.status=\\'\\'; return true\"";
				}

				print "document.write('<a href=\"$self_url"."banner.php?action=r&id=$rand_num\"$temp"."$temp2>');\n";
				print "document.write('<img src=\"$info[4]\" width=\"$info[6]\"  height=\"$info[5]\" border=\"0\" alt=\"$info[7]\"></a>');\n";

				if ($info[8]) {
					print "document.write('</td></tr><tr><td align=\"center\">$info[8]</td></tr></table>');\n";
				}
			}
		} else {
			$info[10] = eregi_replace('<!-- Link URL -->',"$self_url"."banner.php?action=r&id=$rand_num",$info[10]);
			$info[10] = eregi_replace('href','target="_blank" href',$info[10]);
			if ($style != "non_ssi"){
				$info[10] = stripslashes($info[10]);
				print "$info[10]";
			} else {
				$info[10] = eregi_replace("'","\'",$info[10]);
				print "document.write('<table align=\"right\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"468\" height=\"60\" bgcolor=\"black\"><tr><td align=\"center\" valign=\"middle\">');\n";
				print "document.write('$info[10]');";
				print "document.write('</td></tr></table>');\n";
			}
		}
	}
}
if ($_GET['gid']){
	$gid = $_GET['gid'];
	display_group();
	$action = 1;
} elseif ($_GET['action'] == ""){
	display_banner();
} elseif ($_GET['action'] == "r"){
	$id = $_GET['id'];
	redirect();
} else {
	display_banner();
}
?>
