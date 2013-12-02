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
$my_cash = mysql_result($res, 0, "roi_cash");
$rxid = $usrid;
$sharec = mysql_result(mysql_query("select value from adminprops where field='sharec'"), 0);
$sharea = mysql_result(mysql_query("select value from adminprops where field='sharea'"), 0);
$res = mysql_query("select * from user where ref=$usrid order by id asc");
$accs = mysql_query("SELECT * FROM acctype WHERE id=$acctype");
$rpgebonus = mysql_result($accs, 0, "rpgebonus");
$r_cbons = mysql_result($accs, 0, "levels");
$r_cbons2 = explode(",", $r_cbons);
$acc_r_cbon = count($r_cbons2);
$r_cbonsa = mysql_result($accs, 0, "rbonuses");
$r_cbons2a = explode(",", $r_cbonsa);
$acc_r_cbona = count($r_cbons2a);
$r_cbonsb = mysql_result($accs, 0, "ptc_levels");
$r_cbons2b = explode(",", $r_cbonsb);
$acc_r_cbonb = count($r_cbons2b);
for($b=0;$b<$acc_r_cbon;$b++) {
	$nb=$b+1;
	$r_levs = $r_levs . "<b>Level $nb:</b> $r_cbons2[$b]%<br>";
}
for($c=0;$c<$acc_r_cbona;$c++) {
	$nbb=$c+1;
	$r_bonss = $r_bonss . "<b>Level $nbb:</b> $r_cbons2a[$c] credits<br>";
}
for($d=0;$d<$acc_r_cbonb;$d++) {
	$nba=$d+1;
	$ptc_levs = $ptc_levs . "<b>Level $nba:</b> $r_cbons2b[$d]%<br>";
}
secheader();

echo("<h4>Referral Bonuses & Statistics</h4>");

echo("<div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td></tr></table></div>");

?>

<p><br><b>Your Referral URL Is:</b><br>
<input type="text" value="<? echo("$self_url?ref=$usrid"); ?>" size="50"> 
<? if ($rpgebonus > 0) {echo("<br>You will earn <b>$rpgebonus</b> credit(s) per unique referral page view."); }?></p>

<p><b>Earn From Your Referrals</b><br>
You can earn from your referrals whenever they signup and surf. See the below information for complete details.<br>

<table width=98% border=0 cellpadding=2 style="margin-left:10px;">
<tr style="background-color: #4DA0C6">
<td width="25%" align=center><b><font color=#FFFFFF>Earn Credits (Surf & PTC)</b></font></td>
<td width="25%" align=center><b><font color=#FFFFFF>Earn PTC Cash</b></font></td>
<td width="25%" align=center><b><font color=#FFFFFF>New Referral Bonus</b></font></td>
<td width="25%" align=center><b><font color=#FFFFFF>Your Referral Count</b></font></td>
</tr>
<tr style="background-color: #F0F8FF">
<td><? echo($r_levs); ?></td>
<td><? echo($ptc_levs); ?></td>
<td><? echo($r_bonss); ?></td>
<td><?
if ($acc_r_cbon > $acc_r_cbona && $acc_r_cbon > $acc_r_cbonb) {$greatest = $acc_r_cbon; } elseif ($acc_r_cbona > $acc_r_cbon && $acc_r_cbona > $acc_r_cbonb) {$greatest = $acc_r_cbona; } elseif ($acc_r_cbonb > $acc_r_cbon && $acc_r_cbonb > $acc_r_cbona) {$greatest = $acc_r_cbonb; } else { $greatest = $acc_r_cbon; }
for($z=0;$z<$greatest;$z++) {
	$rxid=get_ref_levels($rxid,$z);
	if (count($rxid) >0){
		$rxid = join(",",$rxid);
		$qry = "select id from user where id in ($rxid)";
		$rss = mysql_query($qry);
		while($rs1 = mysql_fetch_array($rss)) {
			$rr_id[$z] .= $rs1[0]." , ";
		}
		$len = strlen($rr_id[$z]);
		$len-=2;
		$rr_id[$z] = substr($rr_id[$z],0,$len);
	}
}
for($i=0;$i<$greatest;$i++) {
	$n=$i+1;
	if ($tier[$i] == "") {$tier[$i] = 0; }
	echo("<b>Level $n:</b> $tier[$i] Referrals<br>");
}
?></td>
</tr>
</table></p>

<p><br><b>Your Referral List</b><br>
<?
if (mysql_num_rows($res) == 0) {
	echo("You do not have any referrals at this time.</p>");
} else {

echo("<table width=98% border=0 cellpadding=2 style=\"margin-left:10px;\">
<tr style=\"background-color: #4DA0C6\">
<td align=center><b><font color=#FFFFFF>ID #</font></b></td>
<td align=center><b><font color=#FFFFFF>Name</font></b></td>
<td align=center><b><font color=#FFFFFF>Email</font></b></td>
<td align=center><b><font color=#FFFFFF>Your Earned Credits</font></b></td>
<td align=center><b><font color=#FFFFFF>Your Earned Cash</font></b></td>
<td align=center><b><font color=#FFFFFF>Account</font></b></td>
<td align=center><b><font color=#FFFFFF>Upgrade Referral</font></b></td></tr>");
	for ($i = 0; $i < mysql_num_rows($res); $i++) {
		$refs_id = mysql_result($res, $i, "id");
		$name = mysql_result($res, $i, "name");
		$tref = mysql_result($res, $i, "toref");
		$cshtref = mysql_result($res, $i, "cshtoref");
		$commstoref = mysql_result($res, $i, "commstoref");
		$total_to_ref = $cshtref + $commstoref;
		$allow_contact = mysql_result($res, $i, "allow_contact");
		$ref_acc = mysql_result($res, $i, "acctype");
		$ptc_clicks = mysql_result($res, $i, "ptc_clicks");
		$lifetime_pages = mysql_result($res, $i, "lifetime_pages");
		if ($ref_acc == 1) {
			$refs_acc = "Free";
		} else {
			$refs_acc = "Upgraded";
		}
		if ($allow_contact == 'yes') {
			$ref_e = mysql_result($res, $i, "email");
			$ref_email = "<a href=\"mailto:$ref_e\">$ref_e</a>";
		} else {
			$ref_email = "Privacy Requested";
		}
		$tref = mysql_result($res, $i, "toref");
		$tref = round($tref, 3);
		$nn = $i + 1;
echo("<tr style=\"background-color: #F0F8FF\">
<td align=center>$nn</td>
<td align=center>$name</td>
<td align=center>$ref_email</td>
<td>Surf: $lifetime_pages pages<br>PTC: $ptc_clicks clicks<br><b>Totals: $tref credits</b></td>
<td>Comissions: \$$commstoref<br>PTC Cash: \$$cshtref<br><b>Totals: \$$total_to_ref</b></td>
<td align=center>$refs_acc</td>
<form method=\"post\" action=\"upgrade_downline.php\" name=\"upg_dl\"");
		if ($my_cash < $sharec || $allow_referral_upgrades == 0) {
			echo("Not Allowed");
		}
		echo("><td valign=middle align=center><input type=hidden name=refs_id value=$refs_id><input type=submit name=submit value=\" Upgrade Referral \"");
		if ($my_cash < $sharec && $allow_referral_upgrades != 0) {
			echo(" onClick=\"alert('To upgrade your downline you must have at least \$$sharec in your account.\\nYou only have \$$my_cash available!'); return false;\"");
		} elseif ($allow_referral_upgrades == 0) {
			echo(" onClick=\"alert('Sorry, not enabled on this site!'); return false;\"");
		}
		echo("  style=\"font-size: 11px; padding: 2px;\"></td></form></tr>");
	}
	echo("</table></p>");
}

echo("<h4>&nbsp;</h4><div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td></tr></table></div>");
secfooter();
mysql_close;
exit;
?>
