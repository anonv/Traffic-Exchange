<?php
function get_main_content() {
	$mncontent = @mysql_result(@mysql_query("select content from html where type='1page'"), 0);
	echo($mncontent);
}
function uheader() {
	global $m_header, $ref;
	include($m_header);
}
function ufooter() {
	global $m_footer, $ref;
	include($m_footer);
}
function secheader() {
	global $mem_header, $usrid;
	include($mem_header);
}
function members_main_menu() {
	global $title, $fontface, $server_clock, $usrid,$self_url;
	$s_vals = file_reader("/memlinks.html");
	$s_vals = str_replace('[show_server_time]', $server_clock, $s_vals);
	$s_vals = str_replace('[session_name]', session_name(), $s_vals);
	$s_vals = str_replace('[session_id]', session_id(), $s_vals);
	$s_vals = str_replace('[self_url]',$self_url,$s_vals);
	echo($s_vals);
}
function secfooter() {
	global $mem_footer, $usrid;
	include($mem_footer);
}
function file_reader($fileurl) {
	$fileurl = $_SERVER['DOCUMENT_ROOT'] . $fileurl;
	$file = fopen($fileurl,'r') or die("File Doesn't Exist");
	$contents = fread($file,filesize($fileurl));
	fclose($file);
	return $contents;
}
function checkPTCdata($dateis) {
	@mysql_query("UPDATE ptc_orders SET date_done='$dateis' WHERE clicks_remain=0 && date_done='0000-00-00'");
	return;
}
function text($limit=1, $fstart="",$fend="") {
	$res = mysql_query("select text from tads order by rand() limit $limit");
	if (mysql_num_rows($res) > 0) {
		while ($r = mysql_fetch_array($res)) {
			$text = $r['text'];
			echo $fstart.$text.$fend;
		}
	}
	@mysql_free_result($res);
}
function get_referral($vid) {
	$query = "select ref_id from member_refs where mem_id=$vid";
	if ($res = mysql_query($query)) {
		if ($res1 = mysql_fetch_array($res)) {
			return $res1[0];
		}
	}
	@mysql_free_result($res);
}
function credit_ref_bonuses($par_id) {
	for ($i=0; $i < count($par_id); $i++) {
		$get_ref_data = mysql_query("SELECT acctype FROM user WHERE id=$par_id[$i] && status='Active'");
		if (mysql_num_rows($get_ref_data) != 0) {
			$refacc = mysql_result($get_ref_data, 0);
			$get_bonuses = mysql_result(mysql_query("SELECT rbonuses FROM acctype WHERE id=$refacc"), 0);
			$bonuses = explode(",", $get_bonuses);
			$givebonus = $bonuses[$i];
			if (!is_numeric($givebonus)) {$givebonus = 0; }
			@mysql_query("UPDATE user SET credits=credits+$givebonus, rbon_credits=rbon_credits+$givebonus, lifetime_credits=lifetime_credits+$givebonus WHERE id=$par_id[$i]");
			@mysql_query("update adminprops set value=value-$givebonus where field='surplu'");
		}
		@mysql_free_result($get_ref_data);
	}
}
function get_ref_levels($mid,$z) {
	global $tier;
	$squery = "select count(*),mem_id from member_refs where ref_id in ($mid) group by mem_id";
	if ($res = mysql_query($squery)) {
		$tier[$z] = mysql_num_rows($res);
		$res = mysql_fetch_array($res);
		$mquery = "select mem_id from member_refs where ref_id in ($mid)";
		if ($resultx = mysql_query($mquery)) {
			$z = 1;
			while ($rsvz = mysql_fetch_array($resultx)){
				$rr_id[$z] = $rsvz[0];
				$z++;
			}
		}
		return $rr_id;
	}
	@mysql_free_result($res);
}
function credit_r_bonuses($par_id,$type,$ammt) {
	$zzz = 0;
	for ($i=0; $i < count($par_id); $i++) {
		$zzz++;
		$get_ref_data = mysql_query("SELECT acctype FROM user WHERE id=$par_id[$i] && status='Active'");
		if (mysql_num_rows($get_ref_data) != 0) {
			$refacc = mysql_result($get_ref_data, 0);
			if ($type == 'credits') {
				$get_bonuses = mysql_result(mysql_query("SELECT levels FROM acctype WHERE id=$refacc"), 0);
			} else {
				$get_bonuses = mysql_result(mysql_query("SELECT ptc_levels FROM acctype WHERE id=$refacc"), 0);
			}
			$bonuses = explode(",", $get_bonuses);
			$givebonus = $bonuses[$i] / 100;
			$givebonus = round($givebonus, 2);
			$givebonus = $givebonus * $ammt;
			if ($zzz == 1) {
				$return_val = $givebonus;
			}
			if (!is_numeric($givebonus)) {$givebonus = 0; }
			if ($type == 'credits') {
				@mysql_query("UPDATE user SET credits=credits+$givebonus, crdsfrmallrefs=crdsfrmallrefs+$givebonus, lifetime_credits=lifetime_credits+$givebonus WHERE id=$par_id[$i]");
				@mysql_query("update adminprops set value=value-$givebonus where field='surplu'");
			} else {
				@mysql_query("UPDATE user SET cshfrmallrefs=cshfrmallrefs+$givebonus, roi_cash=roi_cash+$givebonus, lifetime_cash=lifetime_cash+$givebonus WHERE id=$par_id[$i]");
				@mysql_query("update adminprops set value=value-$givebonus where field='csurpl'");
				$get_refstats = mysql_query("SELECT * FROM monthly_stats WHERE usrid=$par_id[$i] && yearis=" . date("Y") . " && monthis=" . date("m"));
				if (mysql_num_rows($get_refstats) == 0) {
					@mysql_query("INSERT INTO monthly_stats (usrid, refptc_cash, tot_owed, monthis, yearis) VALUES ($par_id[$i], $givebonus, $givebonus, " . date("m") . ", " . date("Y") . ")") or die (mysql_error());
				} else {
					@mysql_query("UPDATE monthly_stats SET refptc_cash=refptc_cash+$givebonus, tot_owed=tot_owed+$givebonus WHERE usrid=$par_id[$i] && yearis=" . date("Y") . " && monthis=" . date("m")) or die (mysql_error());
				}
			}
		}
	}
	return $return_val;
}
function ref_shunt($memb_id) {
	$par_id=get_referral($memb_id);
	$query="SELECT mem_id FROM member_refs WHERE ref_id=$memb_id";
	$chv_id=array();
	$i=0;
	if ($res=mysql_query($query)) {
		while($id=mysql_fetch_array($res)) {
			$chv_id[$i] = $id[0];
			$i++;
		}
		$queryv="UPDATE member_refs SET ref_id=$par_id WHERE mem_id=";
		for ($i=0;$i<count($chv_id);$i++) {
			mysql_query($queryv.$chv_id[$i]);
		}
	}
	return 1;
}
function totalmembers() {
	$resz = mysql_query("SELECT id FROM user");
	return mysql_num_rows($resz);
}
function totalupgrademembers() {
	$resz = mysql_query("SELECT id FROM user WHERE acctype='2'");
	return mysql_num_rows($resz);
}
function totalsiteinrotation() {
	$resz = mysql_query("SELECT * FROM site");
	return mysql_num_rows($resz);
}
function totalsiteshowntoday() {
	$resum = mysql_query("SELECT SUM(pg_views) FROM 7stat WHERE date='".date('Y-m-d')."'");
	$sum = mysql_result($resum,0,0);
	$sum = empty($sum) ? 0 : $sum;
	return $sum;
}
function totalmembersufringnow() {
	$res = mysql_query("SELECT count(*) FROM 7stat WHERE date='".date('Y-m-d')."' AND time > '".date('H:i:s',(time()-30))."'");
	
	return mysql_result($res,0,0);
}
function totalpayout() {
	$resum = mysql_query("SELECT SUM(amount) FROM cashout_history WHERE amount>0");
	$sum = mysql_result($resum,0,0);
	$sum = empty($sum) ? 0 : $sum;
	$resum1 = mysql_query("SELECT SUM(amount) FROM investment_history WHERE amount>0 AND is_from='Upline Earnings'");
	$sum1 = mysql_result($resum1,0,0);
	$sum1 = empty($sum1) ? 0 : $sum1;
	$sum2 = '$ '.number_format(($sum1+$sum),2,'.',',');
	return $sum2;
}
?>
