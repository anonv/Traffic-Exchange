<?php
session_start();
include("../vars.php");
include("adminauth.incl.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
adminAuth();
if ($_GET['find'] == 'go_to' && $_GET['select_val'] >= 1) {
	if ($_GET['select_val'] == 1) {
		header("location:index.php?y=3&u=".trim($_GET['f_value']));
		mysql_close;
		exit;
	} elseif ($_GET['select_val'] == 2) {
		$raz = mysql_query("SELECT id FROM user WHERE email='" . trim($_GET['f_value']) . "'");
		if (mysql_num_rows($raz) == 0) {
			header("location:index.php?y=3&u=0");
			mysql_close;
			exit;
		} else {
			$usrid = mysql_result($raz, 0, "id");
			header("location:index.php?y=3&u=$usrid");
			mysql_close;
			exit; }
	} elseif ($_GET['select_val'] == 3) {
		header("location:index.php?y=4&u=".trim($_GET['f_value']));
		mysql_close;
		exit;
	} elseif ($_GET['select_val'] == 4) {
		header("location:index.php?y=4&a=3");
		mysql_close;
		exit;
	}
} elseif ($_POST['generate'] == 'last_month_list') {
	$fees = round($_POST['fees'] / 100, 2);
	if (!is_numeric($fees) || $fees > 1) {
		$fees = 0;
	} elseif ($fees < 0) {
		$fees = 0;
	}
	$last_month = date("m") - 1;
	$year_now = date("Y");
	if ($last_month == 0) {
		$year_now = $year_now - 1;
		$last_month = 12;
	}
	$resu = mysql_query("SELECT * FROM user ORDER BY pay_to");
	if (mysql_num_rows($resu) != 0) {
		for ($i = 0; $i < mysql_num_rows($resu); $i++) {
			$uid = mysql_result($resu, $i, "id");
			$my_cash = mysql_result($resu, $i, "roi_cash");
			$acctype = mysql_result($resu, $i, "acctype");
			$pay_to = mysql_result($resu, $i, "pay_to");
			$payout_address = mysql_result($resu, $i, "payout_address");
			if ($payout_address == "") {
				$payout_address = "No payout address found! - " . mysql_result($resu, $i, "email");
			}
			$cashout_min = mysql_result(mysql_query("SELECT cashout FROM acctype WHERE id=$acctype"), 0);
			$resm = mysql_query("SELECT * FROM monthly_stats WHERE monthis=$last_month && yearis=$year_now && usrid=$uid && paidout='no' && finalized='no'");
			if (mysql_num_rows($resm) != 0) {
				$dont_proc = "no";
				$dont_proc_ne = "no";
				$stats_owed = mysql_result($resm, 0, "tot_owed");
				$paid_out = mysql_result($resm, 0, "paid_out");
				$stats_owed = $stats_owed - $paid_out;
				if ($stats_owed < 0) {
					$stats_owed = 0;
				}
				$reminder = 0;
				if (ereg(".", $stats_owed)) {
					$dummy = explode(".", $stats_owed);
					$stats_owed = $dummy[0];
					if ($dummy[1] > 0) {
						$reminder = "0.$dummy[1]";
					}
				}
				if ($my_cash < $stats_owed) {
					$dont_proc = "yes";
					$no_processed = $no_processed . "<br><br>Member: $uid has errors, please manually pay this person<br>";
				}
				if ($cashout_min > $stats_owed) {
					$dont_proc_ne = "yes";
					$no_processed_ne = $no_processed_ne . "Member: $uid - NO PAYOUT ONLY \$$stats_owed EARNED<br>";
				}
				if ($dont_proc == 'no' && $dont_proc_ne == 'no') {
					$fee_amt = round($stats_owed * $fees, 2);
					$stats_owed = $stats_owed - $fee_amt;
					$processed = $processed . "Member $uid - $payout_merchants[$pay_to]: $payout_address - \$$stats_owed<br>";
				}
			}
		}
		echo("<font size=2 face=$fontface>Payouts:<br>$processed<br><br><br>Not Enough Earned:<br>$no_processed_ne<br><br><br>Errors:<br>$no_processed<br><br><center><a href=\"./index.php?y=17\">Back to admin area</a></center></font>");
	} else {
		header("location:index.php?y=17&error=no-users-found");
		mysql_close;
		exit;
	}
} elseif ($_POST['generate'] == 'last_month_payout') {
	$pay_tf = trim($_POST['pay_to']);
	if ($pay_tf == 'all') {
		$pay_t_sql = " ORDER BY pay_to";
	} elseif (is_numeric($pay_tf)) {
		$pay_t_sql = " WHERE pay_to=$pay_tf ORDER BY pay_to";
	} else {
		$pay_t_sql = " ORDER BY pay_to";
	}
	$fees = round($_POST['fees'] / 100, 2);
	if (!is_numeric($fees) || $fees > 1) {
		$fees = 0;
	} elseif ($fees < 0) {
		$fees = 0;
	}
	$last_month = date("m") - 1;
	$year_now = date("Y");
	if ($last_month == 0) {
		$year_now = $year_now - 1;
		$last_month = 12;
	}
	exit("SELECT * FROM user $pay_t_sql");
	$resu = mysql_query("SELECT * FROM user $pay_t_sql");
	if (mysql_num_rows($resu) != 0) {
		for ($i = 0; $i < mysql_num_rows($resu); $i++) {
			$uid = mysql_result($resu, $i, "id");
			$my_cash = mysql_result($resu, $i, "roi_cash");
			$acctype = mysql_result($resu, $i, "acctype");
			$pay_to = mysql_result($resu, $i, "pay_to");
			$payout_address = mysql_result($resu, $i, "payout_address");
			if ($payout_address == "") {
				$payout_address = "No payout address found! - " . mysql_result($resu, $i, "email");
			}
			$cashout_min = mysql_result(mysql_query("SELECT cashout FROM acctype WHERE id=$acctype"), 0);
			$resm = mysql_query("SELECT * FROM monthly_stats WHERE monthis=$last_month && yearis=$year_now && usrid=$uid && paidout='no' && finalized='no'");
			if (mysql_num_rows($resm) != 0) {
				$dont_proc = "no";
				$dont_proc_ne = "no";
				$stats_owedo = mysql_result($resm, 0, "tot_owed");
				$paid_out = mysql_result($resm, 0, "paid_out");
				$stats_owedo = $stats_owedo - $paid_out;
				if ($stats_owedo < 0) {
					$stats_owed = 0;
				}
				$reminder = 0;
				if (ereg(".", $stats_owedo)) {
					$dummy = explode(".", $stats_owedo);
					$stats_owed = $dummy[0];
					if ($dummy[1] > 0) {
						$reminder = "0.$dummy[1]";
					}
				}
				if ($my_cash < $stats_owed) {
					$dont_proc = "yes";
					$no_processed = $no_processed . "<br><br>Member: $uid has errors, please manually pay this person<br>";
				}
				if ($cashout_min > $stats_owed) {
					$dont_proc_ne = "yes";
					$no_processed_ne = $no_processed_ne . "Member: $uid - NO PAYOUT ONLY \$$stats_owed EARNED - Cronjob will transfer<br>";
				}
				if ($dont_proc == 'no' && $dont_proc_ne == 'no') {
					$date_now = date("Y-m-d");
					$fee_amt = round($stats_owed * $fees, 2);
					$stats_owed_plus = $stats_owed - $fee_amt;
					$processed = $processed . "Member $uid - \$$stats_owed_plus was paidout!<br>";
					$do_stats = mysql_query("UPDATE monthly_stats SET paid_out=paid_out+$stats_owedo, paidout='yes', finalized='yes' WHERE monthis=$last_month && yearis=$year_now && usrid=$uid && paidout='no' && finalized='no'");
					$upd = mysql_query("INSERT INTO cashout_history (usrid, amount, descr, pay_merch, paid_to, cdate) VALUES ($uid, $stats_owed, 'Mass Payout', '$pay_processor', '$payout_address', '$date_now')") or die (mysql_error());
					$upd = mysql_query("UPDATE user SET roi_cash=roi_cash-$stats_owed, lifetime_paid=lifetime_paid+$stats_owed WHERE id=$uid") or die (mysql_error());
					$csures = mysql_query("UPDATE adminprops SET value=value-$stats_owed_plus WHERE field='csurpl'");
					if ($reminder > 0) {
						$resume_stats = mysql_query("SELECT * FROM monthly_stats WHERE monthis=" . date("m") . " && yearis=" . date("Y") . " && usrid=$uid && paidout='no' && finalized='no'");
						if (mysql_num_rows($resume_stats) != 0) {
							$updsts = mysql_query("UPDATE monthly_stats SET past_earnings=past_earnings+$reminder, tot_owed=tot_owed+$reminder WHERE usrid=$uid && monthis=" . date("m") . " && yearis=" . date("Y")) or die(mysql_error());
						} else {
							$updsts = mysql_query("INSERT INTO monthly_stats (usrid, past_earnings, tot_owed, monthis, yearis) VALUES ($uid, $reminder, $reminder, " . date("m") . ", " . date("Y") . ")") or die(mysql_error());
						}
					}
				}
			}
		}
		echo("<font size=2 face=$fontface>Payouts (processed):<br>$processed<br><br><br>Not Enough Earned (Cronjob will transfer):<br>$no_processed_ne<br><br><br>Errors (May require manual fixing):<br>$no_processed<br><br><center><a href=\"index.php?y=17\">Back to admin area</a></center></font>");
	} else {
		header("location:index.php?y=17&error=no-users-found");
		mysql_close;
		exit;
	}
} elseif ($_POST['generate'] == 'custom_list') {
	$fees = round($_POST['fees'] / 100, 2);
	if (!is_numeric($fees) || $fees > 1) {
		$fees = 0;
	} elseif ($fees < 0) {
		$fees = 0;
	}
	$last_month = $_POST['get_month'];
	$year_now = $_POST['get_year'];
	$resu = mysql_query("SELECT * FROM user ORDER BY pay_to");
	if (mysql_num_rows($resu) != 0) {
		for ($i = 0; $i < mysql_num_rows($resu); $i++) {
			$uid = mysql_result($resu, $i, "id");
			$my_cash = mysql_result($resu, $i, "roi_cash");
			$acctype = mysql_result($resu, $i, "acctype");
			$pay_to = mysql_result($resu, $i, "pay_to");
			$payout_address = mysql_result($resu, $i, "payout_address");
			if ($payout_address == "") {
				$payout_address = "No payout address found! - " . mysql_result($resu, $i, "email");
			}
			$cashout_min = mysql_result(mysql_query("SELECT cashout FROM acctype WHERE id=$acctype"), 0);
			$resm = mysql_query("SELECT * FROM monthly_stats WHERE monthis=$last_month && yearis=$year_now && usrid=$uid && paidout='no' && finalized='no'");
			if (mysql_num_rows($resm) != 0) {
				$dont_proc = "no";
				$dont_proc_ne = "no";
				$stats_owed = mysql_result($resm, 0, "tot_owed");
				$paid_out = mysql_result($resm, 0, "paid_out");
				$stats_owed = $stats_owed - $paid_out;
				if ($stats_owed < 0) {
					$stats_owed = 0;
				}
				$reminder = 0;
				if (ereg(".", $stats_owed)) {
					$dummy = explode(".", $stats_owed);
					$stats_owed = $dummy[0];
					if ($dummy[1] > 0) {
						$reminder = "0.$dummy[1]";
					}
				}
				if ($my_cash < $stats_owed) {
					$dont_proc = "yes";
					$no_processed = $no_processed . "<br><br>Member: $uid has errors, please manually pay this person<br>";
				}
				if ($cashout_min > $stats_owed) {
					$dont_proc_ne = "yes";
					$no_processed_ne = $no_processed_ne . "Member $uid - NO PAYOUT ONLY \$$stats_owed EARNED<br>";
				}
				if ($dont_proc == 'no' && $dont_proc_ne == 'no') {
					$fee_amt = round($stats_owed * $fees, 2);
					$stats_owed = $stats_owed - $fee_amt;
					$processed = $processed . "Member $uid - $payout_merchants[$pay_to]: $payout_address - \$$stats_owed<br>";
				}
			}
		}
		echo("<font size=2 face=$fontface>Payouts:<br>$processed<br><br><br>Not Enough Earned:<br>$no_processed_ne<br><br><br>Errors:<br>$no_processed<br><br><center><a href=\"./index.php?y=17\">Back to admin area</a></center></font>");
		if ($processed != "" && $last_month != date("m")) {
			echo("<br><br><form name=\"formz\" method=\"post\" target=\"_blank\">
<table width=\"603\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">
  <tr>
    <td><div align=\"center\"><input name=\"fees\" type=\"hidden\" value=\"" . $_POST['fees'] . "\"></div></td>
    <td width=\"354\"><div align=\"center\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">Payment Processor :
<select name=pay_to>");
			while (list($m, $j) = each($payout_merchants)) {
				echo("<option value=$m>$j</option>");
			}
			echo("<option value=all>All Processors</option></select>");
			echo("</font></div></td>
    <td width=\"116\"><div align=\"center\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">
     <input type=\"hidden\" name=\"get_month\" value=\"$last_month\"><input type=\"hidden\" name=\"get_year\" value=\"$year_now\"><input type=\"submit\" name=\"submit\" value=\"  Payout  \">
    </font></div></td>
  </tr>
</table>
</form>");
		}
	} else {
		header("location:index.php?y=17&error=no-users-found");
		mysql_close;
		exit;
	}
} elseif ($_POST['submit'] == '  View  ') {
	if ($_POST['get_day'] == "") {
		$_POST['get_day'] = date("Y-m-d");
	}
	$res = mysql_query("select * from 7stat where date='".$_POST['get_day']."' order by num DESC, usrid");
	if (mysql_num_rows($res) != 0) {
		echo("<html><head><title>$title</title></head><body><table width=100% align=center style=\"font-family: $fontface; font-size: 10pt\"><tr>
<td><strong><font size=2 face=$fontface>User</font></strong></td>
    <td><strong><font size=2 face=$fontface>Credits Earned</font></strong></td>
    <td><strong><font size=2 face=$fontface>Page Views</font></strong></td>
    <td><strong><font size=2 face=$fontface>Last Surf Time</font></strong></td>
	<td><strong><font size=2 face=$fontface>Paid</font></strong></td>
  </tr>");
		for ($i = 0; $i < mysql_num_rows($res); $i++) {
			$usr = mysql_result($res, $i, "usrid");
			$num = mysql_result($res, $i, "num");
			$pg_views = mysql_result($res, $i, "pg_views");
			$last_surftime = mysql_result($res, $i, "time");
			$received_pay = mysql_result($res, $i, "received_pay");
			echo("<tr><td>$usr</td><td>$num</td><td>$pg_views</td><td>$last_surftime</td><td>$received_pay</td></tr>");
		}
		echo("</table><br><center><font size=2 face=$fontface><a href=\"index.php?y=13\">Back to admin area</a></font></center></body></html>");
	} else {
		header("location: index.php?y=13&error=no-users-found");
		mysql_close;
		exit;
	}
} elseif ($_POST['submit'] == '  Payout  ' && is_numeric($_POST['get_year']) && is_numeric($_POST['get_month'])) {
	$pay_processor = trim($_POST['processor']);
	if ($pay_processor == "") {
		$pay_processor = "N/A";
	}
	$fees = round($_POST['fees'] / 100, 2);
	if (!is_numeric($fees) || $fees > 1) {
		$fees = 0;
	} elseif ($fees < 0) {
		$fees = 0;
	}
	$last_month = $_POST['get_month'];
	$year_now = $_POST['get_year'];
	if ($last_month == 0) {
		$year_now = $year_now - 1;
		$last_month = 12;
	}
	$resu = mysql_query("SELECT * FROM user");
	if (mysql_num_rows($resu) != 0) {
		for ($i = 0; $i < mysql_num_rows($resu); $i++) {
			$uid = mysql_result($resu, $i, "id");
			$my_cash = mysql_result($resu, $i, "roi_cash");
			$acctype = mysql_result($resu, $i, "acctype");
			$payout_address = mysql_result($resu, $i, "payout_address");
			if ($payout_address == "") {
				$payout_address = "No payout address found! - " . mysql_result($resu, $i, "email");
			}
			$cashout_min = mysql_result(mysql_query("SELECT cashout FROM acctype WHERE id=$acctype"), 0);
			$resm = mysql_query("SELECT * FROM monthly_stats WHERE monthis=$last_month && yearis=$year_now && usrid=$uid && paidout='no' && finalized='no'");
			if (mysql_num_rows($resm) != 0) {
				$dont_proc = "no";
				$dont_proc_ne = "no";
				$stats_owedo = mysql_result($resm, 0, "tot_owed");
				$paid_out = mysql_result($resm, 0, "paid_out");
				$stats_owedo = $stats_owedo - $paid_out;
				if ($stats_owedo < 0) {
					$stats_owed = 0;
				}
				$reminder = 0;
				if (ereg(".", $stats_owedo)) {
					$dummy = explode(".", $stats_owedo);
					$stats_owed = $dummy[0];
					if ($dummy[1] > 0) {
						$reminder = "0.$dummy[1]";
					}
				}
				if ($my_cash < $stats_owed) {
					$dont_proc = "yes";
					$no_processed = $no_processed . "<br><br>Member: $uid has errors, please manually pay this person<br>";
				}
				if ($cashout_min > $stats_owed) {
					$dont_proc_ne = "yes";
					$no_processed_ne = $no_processed_ne . "Member: $uid - NO PAYOUT ONLY \$$stats_owed EARNED - Cronjob will transfer<br>";
				}
				if ($dont_proc == 'no' && $dont_proc_ne == 'no') {
					$date_now = date("Y-m-d");
					$fee_amt = round($stats_owed * $fees, 2);
					$stats_owed_plus = $stats_owed - $fee_amt;
					$processed = $processed . "Member $uid - \$$stats_owed_plus was paidout!<br>";
					$do_stats = mysql_query("UPDATE monthly_stats SET paid_out=paid_out+$stats_owedo, paidout='yes', finalized='yes' WHERE monthis=$last_month && yearis=$year_now && usrid=$uid && paidout='no' && finalized='no'");
					$upd = mysql_query("INSERT INTO cashout_history (usrid, amount, descr, pay_merch, paid_to, cdate) VALUES ($uid, $stats_owed, 'Mass Payout', '$payout_merchants[$pay_to]', '$payout_address', '$date_now')") or die (mysql_error());
					$upd = mysql_query("UPDATE user SET roi_cash=roi_cash-$stats_owed, lifetime_paid=lifetime_paid+$stats_owed WHERE id=$uid") or die (mysql_error());
					$csures = mysql_query("UPDATE adminprops SET value=value-$stats_owed_plus WHERE field='csurpl'");
					if ($reminder > 0) {
						$resume_stats = mysql_query("SELECT * FROM monthly_stats WHERE monthis=" . date("m") . " && yearis=" . date("Y") . " && usrid=$uid && paidout='no' && finalized='no'");
						if (mysql_num_rows($resume_stats) != 0) {
							$updsts = mysql_query("UPDATE monthly_stats SET past_earnings=past_earnings+$reminder, tot_owed=tot_owed+$reminder WHERE usrid=$uid && monthis=" . date("m") . " && yearis=" . date("Y")) or die(mysql_error());
						} else {
							$updsts = mysql_query("INSERT INTO monthly_stats (usrid, past_earnings, tot_owed, monthis, yearis) VALUES ($uid, $reminder, $reminder, " . date("m") . ", " . date("Y") . ")") or die(mysql_error());
						}
					}
				}
			}
		}
		echo("<font size=2 face=$fontface>Payouts (processed):<br>$processed<br><br><br>Not Enough Earned:<br>$no_processed_ne<br><br><br>Errors (May require manual fixing):<br>$no_processed<br><br><center><a href=\"index.php?y=17\">Back to admin area</a></center></font>");
	} else {
		header("location:index.php?y=17&error=no-users-found");
		mysql_close;
		exit;
	}
} elseif ($_GET['download'] == 'mysql_backup') {
	if (strtolower(ini_get('safe_mode'))=='on'){
		echo "<h3><font color=red face=tahoma>Your server is running in safe mode. The backup feature provided will not run in safe mode. Please use the MySQL backup features provided by your hosting company.</h3></font>";
		echo("<br><form><input type=\"button\" value=\"Go Back\" onclick=\"history.go(-1)\"></form>");
		mysql_close;
		exit;
	}
	set_time_limit(0);
	if (file_exists("/usr/local/bin/mysqldump")){
		$mysqldumppath="/usr/local/bin";}
		if (file_exists("/usr/bin/mysqldump")){
			$mysqldumppath="/usr/bin";}
			if (file_exists("/usr/local/mysql/bin/mysqldump")){
				$mysqldumppath="/usr/local/mysql/bin";}
				if (file_exists("/usr/bin/gzip")){
					$gzippath='/usr/bin';}
					if (file_exists("/bin/gzip")){
						$gzippath='/bin';}
						$cur_time=date("Y-m-d H:i");
						if (file_exists($mysqldumppath."/mysqldump") and file_exists($gzippath."/gzip")){
							$gzipext=".gz";}
							header("Content-disposition: filename=$db_name$gzipext");
							header("Content-type: application/octetstream");
							header("Pragma: no-cache");
							header("Expires: 0");
							if ($gzipext){
								passthru($mysqldumppath."/mysqldump -fqh$db_host -u$db_user -p$db_pwd $db_name | ".$gzippath."/gzip -f");
								mysql_close;
								exit;}
								if ($mysqldumppath){
									passthru($mysqldumppath."/mysqldump -fqh$db_host -u$db_user -p$db_pwd $db_name");
									mysql_close;
									exit;}
									flush();
									function backupd($table) {
										$def = "";
										$def .= "DROP TABLE IF EXISTS $table;\n";
										$def .= "CREATE TABLE $table (\n";
										$result = mysql_query("SHOW FIELDS FROM $table") or die("Table $table not existing in database");
										while($row = mysql_fetch_array($result)) {
											$def .= "    $row[Field] $row[Type]";
											if ($row["Default"] != "") $def .= " DEFAULT '$row[Default]'";
											if ($row["Null"] != "YES") $def .= " NOT NULL";
											if ($row[Extra] != "") $def .= " $row[Extra]";
											$def .= ",\n";
										}
										$def = ereg_replace(",\n$","", $def);
										$result = mysql_query("SHOW KEYS FROM $table");
										while($row = mysql_fetch_array($result)) {
											$kname=$row[Key_name];
											if(($kname != "PRIMARY") && ($row[Non_unique] == 0)) $kname="UNIQUE|$kname";
											if(!isset($index[$kname])) $index[$kname] = array();
											$index[$kname][] = $row[Column_name];
										}
										while(list($x, $columns) = @each($index)) {
											$def .= ",\n";
											if($x == "PRIMARY") $def .= "   PRIMARY KEY (" . implode($columns, ", ") . ")";
											else if (substr($x,0,6) == "UNIQUE") $def .= "   UNIQUE ".substr($x,7)." (" . implode($columns, ", ") . ")";
											else $def .= "   KEY $x (" . implode($columns, ", ") . ")";
										}
										$def .= "\n);";
										return (stripslashes($def));
									}

									function backupcontent($table) {
										$content="";
										$result = mysql_query("SELECT * FROM $table");
										while($row = mysql_fetch_row($result)) {
											$insert = "INSERT INTO $table VALUES (";
											for($j=0; $j<mysql_num_fields($result);$j++) {
												if(!isset($row[$j])) $insert .= "NULL,";
												else if($row[$j] != "") $insert .= "'".addslashes($row[$j])."',";
												else $insert .= "'',";
											}
											$insert = ereg_replace(",$","",$insert);
											$insert .= ");\n";
											echo $insert;
										}
									}
									$cur_time=date("Y-m-d H:i");
									$tablez = mysql_query("show tables");
									$numb_tables = @mysql_num_rows($tablez);
									$i = 0;
									while($i < $numb_tables) {
										$table = mysql_tablename($tablez, $i);
										echo backupd($table);
										echo "\n\n";
										backupcontent($table);
										echo "\n\n";
										$i++;
									}
} elseif ($_GET['run'] == 'reset_cash') {
	$cdosurp = mysql_query("UPDATE adminprops SET value=0 where field='csurpl'");
	echo("<script>alert('Cash surplus successfully set back to $0.00!'); window.close();</script>");
	mysql_close;
	exit;
} elseif ($_GET['run'] == 'reset_credits') {
	$cdosurp = mysql_query("UPDATE adminprops SET value=0 where field='surplu'");
	echo("<script>alert('Credit surplus successfully set back to 0!'); window.close();</script>");
	mysql_close;
	exit;
} elseif ($_GET['run'] == 'mysql_optimizer') {
	$repair_sql = 'REPAIR TABLE `7stat` , `7statsite` , `abuse` , `acctype` , `ad_info` , `admin` , `adminprops` , `banned_emails` , `banned_ipadds` , `banned_sites` , `banner` , `cashout_history` , `comission_history` , `faq` , `gp_info` , `gp_name` , `html` , `investment_history` , `member_refs` , `merchant_codes` , `monthly_stats` , `other_history` , `ptc_orders` , `ptc_tracking` , `referstats` , `sellcredit` , `site` , `tads` , `user` ';
	$optim_sql = 'OPTIMIZE TABLE `7stat` , `7statsite` , `abuse` , `acctype` , `ad_info` , `admin` , `adminprops` , `banned_emails` , `banned_ipadds` , `banned_sites` , `banner` , `cashout_history` , `comission_history` , `faq` , `gp_info` , `gp_name` , `html` , `investment_history` , `member_refs` , `merchant_codes` , `monthly_stats` , `other_history` , `ptc_orders` , `ptc_tracking` , `referstats` , `sellcredit` , `site` , `tads` , `user` ';
	$repr = mysql_query($repair_sql);
	$optim = mysql_query($optim_sql);
	echo("<script>alert('MySQL Database was successfully repaired and optimized!'); window.close();</script>");
	mysql_close;
	exit;
} elseif ($_GET['run'] == 'cronjob') {
	include("admincron.php");
	echo("<script>alert('CronJob was successfully completed - $the_day!'); window.close();</script>");
	mysql_close;
	exit;
} elseif ($_GET['download'] == 'mysql_text_backup') {
	if(file_exists("database_backup.sql")) {
		$output=system("mysqldump -u ".$db_user." -p".$db_pwd." ".$db_name." --opt > database_backup.sql");
		header("Location: database_backup.sql");
		mysql_close;
		exit;
	} else {
		echo("<script>alert('The database_backup.sql file could not be found\\nEnsure this file is in your site admin folder and try again!'); window.close();</script>");
		mysql_close;
		exit;
	}
} elseif ($_POST['find_em'] == 'ok_go' && $_POST['valza'] != "") {
	if ($_POST['findz'] == 'ip') {
		$query = "SELECT * FROM user WHERE (ip_address='$_POST[valza]' || ip_address LIKE '%$_POST[valza]%')";
	} elseif ($_POST['findz'] == 'mname') {
		$query = "SELECT * FROM user WHERE name LIKE '$_POST[valza]'";
	} elseif ($_POST['findz'] == 'psis') {
		$query = "SELECT * FROM user WHERE passwd LIKE '$_POST[valza]'";
	} elseif ($_POST['findz'] == 'edom') {
		$query = "SELECT * FROM user WHERE email LIKE '%$_POST[valza]%'";
	} elseif ($_POST['findz'] == 'crds' && is_numeric($_POST['valza'])) {
		$query = "SELECT * FROM user WHERE credits>=$_POST[valza] ORDER BY credits DESC, id ASC";
	} elseif ($_POST['findz'] == 'cash' && is_numeric($_POST['valza'])) {
		$query = "SELECT * FROM user WHERE roi_cash>=$_POST[valza] ORDER BY roi_cash DESC, id ASC";
	} elseif ($_POST['findz'] == 'scrds' && is_numeric($_POST['valza'])) {
		$query = "SELECT * FROM site WHERE credits>=$_POST[valza] ORDER BY credits DESC, id ASC";
	} elseif ($_POST['findz'] == 'eaddr') {
		$query = "SELECT * FROM user WHERE email LIKE '$_POST[valza]%'";
	}
	//exit($query);
	$getres = mysql_query($query);
	if (mysql_num_rows($getres) >= 1) {
		if ($_POST['findz'] == 'scrds') {
			echo("<br><br>
			<center><font size=4 face=tahoma><b>Search Results...</b></font><br><br><br>
			<table width=85% cellpadding=0 cellspacing=0 border=1>
			<tr><td><font size=2 face=tahoma><b>Site ID</b></font></td>
			<td><font size=2 face=tahoma><b>Owner</b></font></td>
			<td><font size=2 face=tahoma><b>URL</b></font></td>
			<td><font size=2 face=tahoma><b>Credits</b></font></td></tr>");
			for ($i = 0; $i < mysql_num_rows($getres); $i++) {
				$id = mysql_result($getres, $i, "id");
				$usrid = mysql_result($getres, $i, "usrid");
				$credits = mysql_result($getres, $i, "credits");
				$assigned = @mysql_result($getres, $i, "auto_assigned");
				$assigned = $assigned * 100;
				$surl = mysql_result($getres, $i, "url");
				echo("\n<tr><td><font size=2 face=tahoma>$id</font></td>
				<td><font size=2 face=tahoma><a href=\"./index.php?y=3&u=$usrid\">User $usrid</a></font></td>
				<td><font size=1 face=tahoma><a href=\"./index.php?y=4&u=$id\">$surl</a></font></td>
				<td><font size=2 face=tahoma>$credits</font></td></tr>");
			}
			echo("</table></center><br><br><font size=2 face=tahoma><a href=\"./index.php?y=18\">Search Again</a></font><br>");
			mysql_close;
			exit;
		} else {
			echo("<br><br>
			<center><font size=4 face=tahoma><b>Search Results...</b></font><br><br><br>
			<table width=85% cellpadding=0 cellspacing=0 border=1>
			<tr><td><font size=2 face=tahoma><b>Member</b></font></td>
			<td><font size=2 face=tahoma><b>Email</b></font></td>
			<td><font size=2 face=tahoma><b>Name & Password</b></font></td>
			<td><font size=2 face=tahoma><b>Earnings</b></font></td>
			<td><font size=2 face=tahoma><b>IP Address</b></font></td></tr>");
			for ($i = 0; $i < mysql_num_rows($getres); $i++) {
				$id = mysql_result($getres, $i, "id");
				$passw = mysql_result($getres, $i, "passwd");
				$email = mysql_result($getres, $i, "email");
				$name = mysql_result($getres, $i, "name");
				$credits = mysql_result($getres, $i, "credits");
				$cash = mysql_result($getres, $i, "roi_cash");
				$ip_address = mysql_result($getres, $i, "ip_address");
				echo("\n<tr><td><font size=2 face=tahoma><a href=\"./index.php?y=3&u=$id\">User $id</a></font></td>
				<td><font size=2 face=tahoma><a href=\"./index.php?y=3&u=$id\">$email</a></font></td>
				<td><font size=1 face=tahoma>$name<br>Password: $passw</font></td>
				<td><font size=2 face=tahoma>Cash : \$$cash<br>Credits : $credits</font></td>
				<td><font size=2 face=tahoma>$ip_address</font></td></tr>");
			}
			echo("</table></center><br><br><font size=2 face=tahoma><a href=\"./index.php?y=18\">Search Again</a></font><br>");
			mysql_close;
			exit;
		}
	} else {
		echo("<br><br><font size=2 face=tahoma><a href=\"./index.php?y=18\">No Results Found - Search Again</a></font><br>");
		mysql_close;
		exit;
	}
} else {
	header("location:index.php?error");
	mysql_close;
	exit;
}
?>
