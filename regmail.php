<?php
function regmail() {
	global $email_headers, $title, $siteurl;
	$datenowis = date("Y-m-d");
	$res = mysql_query("select id, email, credits from user where lastmail!='$datenowis'");
	if (mysql_num_rows($res) != 0) {
		$admail = mysql_result(mysql_query("select value from admin where field='email'"), 0);
		for ($i = 0; $i < mysql_num_rows($res); $i++) {
			$id = mysql_result($res, $i, "id");
			$email = mysql_result($res, $i, "email");
			$credits = mysql_result($res, $i, "credits");
			$subj = "$title Weekly Stats";
			$message = "$title account #$id weekly statistics as of $date\n\nAccount credits: $credits\n\n";
			$sres = mysql_query("select id, name, credits, totalhits, hitslastmail from site where usrid=$id");
			for ($si = 0; $si < mysql_num_rows($sres); $si++) {
				$sid = mysql_result($sres, $si, "id");
				$sname = mysql_result($sres, $si, "name");
				$scredits = mysql_result($sres, $si, "credits");
				$thits = mysql_result($sres, $si, "totalhits");
				$lhits = mysql_result($sres, $si, "hitslastmail");
				$message = $message . "Site: $sname\n\tCredits: $scredits\n\tHits this week: $lhits\n\tTotal hits: $thits\n\n";
				$sres2 = mysql_query("update site set hitslastmail=0 where id=$sid");
			}
			$res2 = mysql_query("update user set lastmail='$datenowis' where id=$id");
			$message = $message . "Regards\n\n$title Admin\nhttp://$siteurl/";
			mail($email, $subj, $message, $email_headers);
		}
	}
}
?>
