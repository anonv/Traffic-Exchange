<?php
include("vars.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
$todaysdate = date("Y-m-d");
$last_cronjob = mysql_result(mysql_query("select value from admin where field='lstcrn'"), 0);
$the_day = strftime("%Y-%m-%d", strtotime("$todaysdate + 1 days ago"));
$ressa = mysql_query("SELECT * FROM user WHERE acctype!=1 && upgrade_ends<='$the_day'");
if ($the_day > $last_cronjob && mysql_num_rows($ressa) != 0) {
 for ($vv = 0; $vv < mysql_num_rows($ressa); $vv++) {
  $usrid = mysql_result($ressa, $vv, "id");
  $uemail = mysql_result($ressa, $vv, "email");
  $users_name = mysql_result($ressa, $vv, "name");
  @mysql_query("UPDATE user SET acctype=1, upgrade_ends='' WHERE id=$usrid");
  mail($uemail, "$title Upgraded Membership Expired!", "Hello $users_name,\n\nYour $title Upgraded Membership has just expired. If you wish to continue upgraded status, login to your account and go to the upgrade area.\n\n$self_url\n\nRegards\n\n$title Admin", $email_headers);
  $message_ins = $message_ins . "Member: $usrid\n";
 }
 mail($private_sys_email, "$title Upgraded Membership(s) Expired!", "Hi $title System here...\n\nThe following account have been set to free status:\n\n" . $message_ins, $email_headers);
}
if ($the_day > $last_cronjob && ($roi_conversion_ratio_free > 0 || $roi_conversion_ratio_pro > 0)) {
 $get_current = mysql_query("SELECT * FROM 7stat WHERE received_pay='yes' && date='$the_day'");
 if (mysql_num_rows($get_current) != 0) {
  $the_mem = mysql_result($get_current, 0, "usrid");
  $amt_ern_tod = mysql_result($get_current, 0, "num");
  $g_memm = mysql_query("SELECT * FROM user WHERE id=$the_mem");
  if (mysql_num_rows($g_memm) != 0) {
   $my_investmn = mysql_result($g_memm, 0, "invested");
   $my_acctype = mysql_result($g_memm, 0, "acctype");
   if ($my_acctype == 1 && $roi_conversion_ratio_free > 0) {
    $roin_rate = round($roi_conversion_ratio_free / 100, 3);
    $roi_return_paid = $my_investmn * $roin_rate;
    $mssg = $mssg . "Member: $the_mem\nInvested: \$$my_investmn\nJust Earned: \$$roi_return_paid\nCredits earned surfing today: $amt_ern_tod\n\n";
   } elseif ($my_acctype != 1 && $roi_conversion_ratio_pro > 0) {
    $roin_rate = round($roi_conversion_ratio_pro / 100, 3);
    $roi_return_paid = $my_investmn * $roin_rate;
    $mssg = $mssg . "Member: $the_mem\nInvested: \$$my_investmn\nJust Earned: \$$roi_return_paid\nCredits earned surfing today: $amt_ern_tod\n\n";
   }
  }
 }
 $res = mysql_query("SELECT * FROM user WHERE invested>0 && lastroi<'$the_day' ORDER BY id") or die (mysql_error());
 if (mysql_num_rows($res) != 0) {
  for ($i = 0; $i < mysql_num_rows($res); $i++) {
   $theusr = mysql_result($res, $i, "id");
   $my_invest = mysql_result($res, $i, "invested");
   $my_acct = mysql_result($res, $i, "acctype");
   if ($my_acct == 1) {
    $roi_rate = round($roi_conversion_ratio_free / 100, 3);
    $roi_credit_return = $my_invest * $roi_rate;
    $get_stats = mysql_query("SELECT num FROM 7stat WHERE num>=$min_credits_to_earn_free && date='$the_day' && received_pay='no' && usrid=$theusr") or die (mysql_error());
   } else {
    $roi_rate = round($roi_conversion_ratio_pro / 100, 3);
    $roi_credit_return = $my_invest * $roi_rate;
    $get_stats = mysql_query("SELECT num FROM 7stat WHERE num>=$min_credits_to_earn_pro && date='$the_day' && received_pay='no' && usrid=$theusr") or die (mysql_error());
   }
   if (mysql_num_rows($get_stats) != 0) {
    $amt_is = mysql_result($get_stats, 0, "num");
    $mssg = $mssg . "Member: $theusr\nInvested: \$$my_invest\nJust Earned: \$$roi_credit_return\nCredits earned surfing today: $amt_is\n\n";
    $updusr = mysql_query("UPDATE user SET roi_cash=roi_cash+$roi_credit_return, lifetot_roi=lifetot_roi+$roi_credit_return, lifetime_cash=lifetime_cash+$roi_credit_return, lastroi='$the_day' WHERE id=$theusr") or die(mysql_error());
    $get_stats = mysql_query("UPDATE 7stat SET received_pay='yes' WHERE usrid=$theusr && date='$the_day' && received_pay='no'") or die (mysql_error());
    $csures = mysql_query("UPDATE adminprops SET value=value-$roi_credit_return WHERE field='csurpl'");
    $nns = explode('-', $the_day);
    $yearis = $nns[0];
    $monthis = $nns[1];
    $get_stats = mysql_query("SELECT * FROM monthly_stats WHERE usrid=$theusr && monthis=$monthis && yearis=$yearis");
    if (mysql_num_rows($get_stats) != 0) {
     $updsts = mysql_query("UPDATE monthly_stats SET roi_earned=roi_earned+$roi_credit_return, days_paid_roi=days_paid_roi+1, tot_owed=tot_owed+$roi_credit_return, this_month='$the_day' WHERE usrid=$theusr && monthis=$monthis && yearis=$yearis") or die(mysql_error());
    } else {
     $updsts = mysql_query("INSERT INTO monthly_stats (usrid, days_paid_roi, roi_earned, tot_owed, monthis, yearis, this_month) VALUES ($theusr, 1, $roi_credit_return, $roi_credit_return, $monthis, $yearis, '$the_day')") or die(mysql_error());
    }
    $tot_paidto++;
   }
  }
  if ($email_admin_when_roi == 1 && $tot_paidto > 0) {
   mail($private_sys_email, "$title ROI Just Paid For $the_day", "Hi System here..\n\nHere are the daily $roi_conversion_ratio% Payouts processed for server time yesterday ($the_day)\n\n" . $mssg, $email_headers);
  }
 }
 if ($max_invest_days > 0) {
  $get_olds = mysql_query("SELECT * FROM investment_history WHERE expired='no'");
  for ($t = 0; $t < mysql_num_rows($get_olds); $t++) {
   $purch_id = mysql_result($get_olds, $t, "id");
   $purch_user = mysql_result($get_olds, $t, "usrid");
   $purch_amt = mysql_result($get_olds, $t, "amount");
   $purch_date = mysql_result($get_olds, $t, "adate");
   $del_invest_date = strftime("%Y-%m-%d", strtotime("$the_day + $max_invest_days days ago"));
   if ($del_invest_date >= $purch_date) {
    $get_usr = mysql_query("UPDATE user SET invested=invested-$purch_amt WHERE id=$purch_user");
    $update = mysql_query("UPDATE investment_history SET expired='yes' WHERE id=$purch_id");
    mail($private_sys_email, "$title Member $purch_user $upgrade_title(s) Expired - $the_day", "Hi System here..\n\n\$$purch_amt of Member $purch_user's $upgrade_title have expired.\n\nDate Purchsed: $purch_date\n\nDate Expired: $the_day\n\n$title System", $email_headers);
   }
  }
 }
 if (date("d") == 01) {
  $last_month = date("m") - 1;
  $year_now = date("Y");
  if ($last_month == 0) {
   $last_month = 12;
   $year_now = $year_now - 1;
  }
  $resu = mysql_query("SELECT * FROM user");
  if (mysql_num_rows($resu) != 0) {
   for ($xv = 0; $xv < mysql_num_rows($resu); $xv++) {
    $uid = mysql_result($resu, $xv, "id");
    $my_cash = mysql_result($resu, $xv, "roi_cash");
    $acctype = mysql_result($resu, $xv, "acctype");
    $cashout_min = mysql_result(mysql_query("SELECT cashout FROM acctype WHERE id=$acctype"), 0);
    $resm = mysql_query("SELECT * FROM monthly_stats WHERE monthis=$last_month && yearis=$year_now && usrid=$uid && paidout='no' && finalized='no'");
    if (mysql_num_rows($resm) != 0) {
     $dont_proc = "no";
     $dont_proc_ne = "no";
     $stats_owed = mysql_result($resm, 0, "tot_owed");
     $paid_out = mysql_result($resm, 0, "paid_out");
     $stats_owed = $stats_owed - $paid_out;
     if (ereg(".", $stats_owed)) {
      $dummy = explode(".", $stats_owed);
      $stats_owed_rounded = $dummy[0];
     }
     if ($my_cash < $stats_owed) {
      $dont_proc = "yes";
      $mail_error = $mail_error . "User ID: $uid has the unexpected error:\n\n\$$my_cash is in their account and \$$stats_owed is owed this month...\n\n";
     }
     if ($cashout_min > $stats_owed_rounded) {
      $dont_proc_ne = "yes";
     }
     if ($dont_proc_ne == 'yes' && $dont_proc == 'no') {
      $trnsfer = mysql_query("UPDATE monthly_stats SET paid_out=paid_out+$stats_owed, paidout='yes', finalized='yes' WHERE monthis=$last_month && yearis=$year_now && usrid=$uid && paidout='no' && finalized='no'");
      $resume_stats = mysql_query("SELECT * FROM monthly_stats WHERE monthis=" . date("m") . " && yearis=" . date("Y") . " && usrid=$uid && paidout='no' && finalized='no'");
      if (mysql_num_rows($resume_stats) != 0) {
       $updsts = mysql_query("UPDATE monthly_stats SET past_earnings=past_earnings+$stats_owed, tot_owed=tot_owed+$stats_owed WHERE usrid=$uid && monthis=" . date("m") . " && yearis=" . date("Y")) or die(mysql_error());
      } else {
       $updsts = mysql_query("INSERT INTO monthly_stats (usrid, past_earnings, tot_owed, monthis, yearis) VALUES ($uid, $stats_owed, $stats_owed, " . date("m") . ", " . date("Y") . ")") or die(mysql_error());
      }
     }
    }
   }
   if ($dont_proc == "yes" && $mail_error != "") {
    mail($private_sys_email, "$title Member Earnings Error", "Hi $title System here..\n\n" . $mail_error . "You will need to rectify these account through your $title Admin Area.\n\n$title System", $email_headers);
   }
  }
 }
}
if ($keep_stats > 0) {
 $del_user_date = strftime("%Y-%m-%d", strtotime("$the_day + $keep_stats days ago"));
 $qwwresqw = mysql_query("delete from 7stat where date<'$del_user_date'");
}
if ($keep_site_stats > 0) {
 $del_site_date = strftime("%Y-%m-%d", strtotime("$the_day + $keep_site_stats days ago"));
 $qwraasqw = mysql_query("delete from 7statsite where date<'$del_site_date'");
}
if ($keep_refpage_stats > 0) {
 $refpagedate = strftime("%Y-%m-%d", strtotime("$the_day + $keep_refpage_stats days ago"));
 $uuio = mysql_query("DELETE FROM referstats WHERE cdate<'$refpagedate'");
}
if ($the_day > $last_cronjob) {
 $upd_cronjob = mysql_query("UPDATE admin SET value='$the_day' where field='lstcrn'");
}
// code for sending weekly mail
if($sendweeklymail> 0 && $sendweeklymail <= (time()-(7*24*60*60))){
	mysql_query("UPDATE adminprops SET value='".time()."' where field='sendweeklymail'");
	$sub = "Weekly Statistics from $title";
	$wrs = mysql_query("SELECT * FROM user WHERE status='Active'");
	if(mysql_num_rows($wrs) > 0){
		while ($r=mysql_fetch_array($wrs)){
			$msg = "
Hello $r[name],
Here is your weekly statistics from $title...
 
Account ID: #$r[id]
Account Balance: $".number_format($r[roi_cash],2,'.',',')."
Account Credits: $r[credits]
 
Login to your account for complete account statistics. You can do that with your following information...
 
http://$siteurl
Login: $r[email]
Password: $r[passwd]
 
Thank you for being a member,
Admin - $title
http://$siteurl			
			";
			@mail("$r[name] <$r[email]>", $sub, $msg, $email_headers);
		}
	}
}
$sql_repair = mysql_query("REPAIR TABLE `7stat` , `7statsite` , `abuse` , `acctype` , `ad_info` , `admin` , `adminprops` , `banned_emails` , `banned_ipadds` , `banned_sites` , `banner` , `cashout_history` , `comission_history` , `faq` , `gp_info` , `gp_name` , `html` , `investment_history` , `member_refs` , `merchant_codes` , `monthly_stats` , `other_history` , `ptc_orders` , `ptc_tracking` , `referstats` , `sellcredit` , `site` , `tads` , `user` ");
$sql_optimize = mysql_query("OPTIMIZE TABLE `7stat` , `7statsite` , `abuse` , `acctype` , `ad_info` , `admin` , `adminprops` , `banned_emails` , `banned_ipadds` , `banned_sites` , `banner` , `cashout_history` , `comission_history` , `faq` , `gp_info` , `gp_name` , `html` , `investment_history` , `member_refs` , `merchant_codes` , `monthly_stats` , `other_history` , `ptc_orders` , `ptc_tracking` , `referstats` , `sellcredit` , `site` , `tads` , `user` ");
mysql_close;
exit;
?>
