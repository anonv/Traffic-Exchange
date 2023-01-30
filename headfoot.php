<?php
function get_main_content() {
	global $mysqli;
	$resp = $mysqli->query("select content from html where type='1page'");
	$mncontent = $resp->fetch_assoc();
	echo $mncontent['content'];
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
	global $mysqli;
	$mysqli->query("UPDATE ptc_orders SET date_done='".$dateis."' WHERE clicks_remain=0 && date_done='0000-01-01'");
	return;
}
function text($limit=1, $fstart="",$fend="") {
	global $mysqli;
	$res = $mysqli->query("select text from tads order by rand() limit ".$limit);
	if ($res->num_rows > 0) {
		while ($r = $res->fetch_array(MYSQLI_BOTH)) {
			$text = $r['text'];
			echo $fstart.$text.$fend;
		}
	}
	$res->free();
}
function get_referral($vid) {
	global $mysqli;
	$query = "select ref_id from member_refs where mem_id=".$vid;
	if ($res = $mysqli->query($query)) {
		if ($res1 = $res->fetch_array(MYSQLI_BOTH)) {
			return $res1[0];
		}
	}
	$res->free();
}
function credit_ref_bonuses($par_id) {
	global $mysqli;
	for ($i=0; $i < count($par_id); $i++) {
		$get_ref_data = $mysqli->query("SELECT acctype FROM user WHERE id=".$par_id[$i]." && status='Active'");
		if ($get_ref_data->num_rows != 0) {
			$refacc =$mysqli->result($get_ref_data, 0);
			$get_bonuses =$mysqli->result($mysqli->query("SELECT rbonuses FROM acctype WHERE id=".$refacc), 0);
			$bonuses = explode(",", $get_bonuses);
			$givebonus = $bonuses[$i];
			if (!is_numeric($givebonus)) {$givebonus = 0; }
			$mysqli->query("UPDATE user SET credits=credits+".$givebonus.", rbon_credits=rbon_credits+".$givebonus.", lifetime_credits=lifetime_credits+".$givebonus." WHERE id=".$par_id[$i]);
			$mysqli->query("update adminprops set value=value-".$givebonus." where field='surplu'");
		}
		$get_ref_data->free();
	}
}
function get_ref_levels($mid,$z) {
	global $mysqli;
	global $tier;
	$squery = "select count(*),mem_id from member_refs where ref_id in (".$mid.") group by mem_id";
	if ($res = $mysqli->query($squery)) {
		$tier[$z] = $res->num_rows;
		$res = $res->fetch_array(MYSQLI_BOTH);
		$mquery = "select mem_id from member_refs where ref_id in (".$mid.")";
		if ($resultx = $mysqli->query($mquery)) {
			$z = 1;
			while ($rsvz = $resultx->fetch_array(MYSQLI_BOTH)){
				$rr_id[$z] = $rsvz[0];
				$z++;
			}
		}
		return $rr_id;
	}
	$res->free();
}
function credit_r_bonuses($par_id,$type,$ammt) {
	global $mysqli;
	$zzz = 0;
	for ($i=0; $i < count($par_id); $i++) {
		$zzz++;
		$get_ref_data = $mysqli->query("SELECT acctype FROM user WHERE id=".$par_id[$i]." && status='Active'");
		if ($get_ref_data->num_rows != 0) {
			$refacc =$mysqli->result($get_ref_data, 0);
			if ($type == 'credits') {
				$get_bonuses =$mysqli->result($mysqli->query("SELECT levels FROM acctype WHERE id=".$refacc), 0);
			} else {
				$get_bonuses =$mysqli->result($mysqli->query("SELECT ptc_levels FROM acctype WHERE id=".$refacc), 0);
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
				$mysqli->query("UPDATE user SET credits=credits+".$givebonus.", crdsfrmallrefs=crdsfrmallrefs+".$givebonus.", lifetime_credits=lifetime_credits+".$givebonus." WHERE id=".$par_id[$i]);
				$mysqli->query("update adminprops set value=value-".$givebonus." where field='surplu'");
			} else {
				$mysqli->query("UPDATE user SET cshfrmallrefs=cshfrmallrefs+".$givebonus.", roi_cash=roi_cash+".$givebonus.", lifetime_cash=lifetime_cash+".$givebonus." WHERE id=".$par_id[$i]);
				$mysqli->query("update adminprops set value=value-".$givebonus." where field='csurpl'");
				$get_refstats = $mysqli->query("SELECT * FROM monthly_stats WHERE usrid=".$par_id[$i]." && yearis=" . date("Y") . " && monthis=" . date("m"));
				if ($get_refstats->num_rows == 0) {
					$mysqli->query("INSERT INTO monthly_stats (usrid, refptc_cash, tot_owed, monthis, yearis) VALUES (".$par_id[$i].", ".$givebonus.", ".$givebonus.", " . date("m") . ", " . date("Y") . ")") or die (mysql_error());
				} else {
					$mysqli->query("UPDATE monthly_stats SET refptc_cash=refptc_cash+".$givebonus.", tot_owed=tot_owed+".$givebonus." WHERE usrid=".$par_id[$i]." && yearis=" . date("Y") . " && monthis=" . date("m")) or die (mysql_error());
				}
			}
		}
	}
	return $return_val;
}
function ref_shunt($memb_id) {
	global $mysqli;
	$par_id=get_referral($memb_id);
	$query="SELECT mem_id FROM member_refs WHERE ref_id=".$memb_id;
	$chv_id=array();
	$i=0;
	if ($res=$mysqli->query($query)) {
		while($id=$res->fetch_array(MYSQLI_BOTH)) {
			$chv_id[$i] = $id[0];
			$i++;
		}
		$queryv="UPDATE member_refs SET ref_id=".$par_id." WHERE mem_id=";
		for ($i=0;$i<count($chv_id);$i++) {
			$mysqli->query($queryv.$chv_id[$i]);
		}
	}
	return 1;
}
function totalmembers() {
	global $mysqli;
	$resz = $mysqli->query("SELECT id FROM user");
	return $resz->num_rows;
}
function totalupgrademembers() {
	global $mysqli;
	$resz = $mysqli->query("SELECT id FROM user WHERE acctype='2'");
	return $resz->num_rows;
}
function totalsiteinrotation() {
	global $mysqli;
	$resz = $mysqli->query("SELECT * FROM site");
	return $resz->num_rows;
}
function totalsiteshowntoday() {
	global $mysqli;
	$resum = $mysqli->query("SELECT SUM(pg_views) AS today_pg_views FROM 7stat WHERE date='".date('Y-m-d')."'");
	$sum =$resum->fetch_assoc();
	$sum['today_pg_views'] = empty($sum['today_pg_views']) ? 0 : $sum['today_pg_views'];
	return $sum['today_pg_views'];
}
function totalmembersurfingnow() {
	global $mysqli;
	$res = $mysqli->query("SELECT count(*) FROM 7stat WHERE date='".date('Y-m-d')."' AND time > '".date('H:i:s',(time()-30))."'");
	
	return $res->num_rows;
}
function totalpayout() {
	global $mysqli;
	$resum = $mysqli->query("SELECT SUM(amount) AS payout FROM cashout_history WHERE amount>0");
	$sum =$resum->fetch_assoc();
	$sum['payout'] = empty($sum['payout']) ? 0 : $sum['payout'];
	$resum1 = $mysqli->query("SELECT SUM(amount) AS payout2 FROM investment_history WHERE amount>0 AND is_from='Upline Earnings'");
	$sum1 =$resum1->fetch_assoc();
	$sum1['payout2'] = empty($sum1['payout2']) ? 0 : $sum1['payout2'];
	$sum2 = '$ '.number_format(($sum1['payout2']+$sum['payout']),2,'.',',');
	return $sum2;
}
?>
