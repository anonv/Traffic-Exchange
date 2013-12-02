<?php
session_start();
//session_register("sess_name");
//session_register("sess_passwd");
//session_register("sess_data");
include("vars.php");
include("headfoot.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
if ($_POST['dosubmit'] == 'yes') {
	$error = "no";
	$name = trim($_POST['name']);
	$email = trim($_POST['email']);
	$myid = trim($_POST['my_id']);
	$subject = trim($_POST['subject']);
	$message = trim($_POST['message']);
	if ($name == "") {
		$error = "yes";
		$why = $why . "Your name is blank!<br>";
	}
	if ($email == "") {
		$error = "yes";
		$why = $why . "Your email is blank!<br>";
	} elseif (!ereg('@', $email) || !ereg('.', $email)) {
		$error = "yes";
		$why = $why . "Your email is invalid!<br>";
	}
	if ($myid != "") {
		if (!is_numeric($myid)) {
			$error = "yes";
			$why = $why . "Your $title ID number wasn't a valid number!<br>";
		} else {
			$get_mem = mysql_query("SELECT * FROM user WHERE id=$myid");
			if (mysql_num_rows($get_mem) == 0) {
				$error = "yes";
				$why = $why . "Your $title ID number wasn't found in our database!<br>";
			}
		}
	}
	if ($subject == "") {
		$error = "yes";
		$why = $why . "You must enter a subject!<br>";
	}
	if ($message == "") {
		$error = "yes";
		$why = $why . "You must enter your comments!<br>";
	} elseif (ereg("<", $message) || ereg(">", $message)) {
		$error = "yes";
		$why = $why . "Your message must not contain HTML characters!<br>";
	}
	if ($error == 'no') {
		@mail($private_sys_email, "$title Contact Request", "Send Reply To: $email\n\nName: $name\n$title Member ID: $myid\n\nSubject:\n$subject\n\nMessage:\n$message\n\n\nSubmitters IP Address: ".$_SERVER['REMOTE_ADDR']."\nUsing Web Browser: " . $_SERVER['HTTP_USER_AGENT'], $email_headers);
		uheader();
                                echo("<h4>Contact $title</h4>");
		if ($_SESSION['sess_name'] != "" && $_SESSION['sess_passwd'] != "") {
			members_main_menu($members_menu);
		}
		echo("<p>Your contact request has been received! $title Admin will contact you ASAP via the email you provided: <b>$email</b>.</p>");
		ufooter();
		mysql_close;
		exit;
	}
}
uheader();
                                echo("<h4>Contact $title</h4>");
if ($_SESSION['sess_name'] != "" && $_SESSION['sess_passwd'] != "") {
	members_main_menu($members_menu);
}
if ($error == 'yes') {
	echo("<p align=center><b>Please correct the following:</b><br><font color=red><b>$why</b></font><br></p>");
}
echo("<p><table border=\"0\" style=\"padding-left: 10px;\">
<form name=\"contact\" method=\"post\">
  <tr>
    <td>Name: </td>
    <td><input type=\"text\" name=\"name\" value=\"$name\"></td>
  </tr>
  <tr>
    <td>Email: </td>
    <td><input type=\"text\" name=\"email\" value=\"$email\"></td>
  </tr>
  <tr>
    <td>Account ID#: </td>
    <td><input name=\"my_id\" type=\"text\" size=\"6\" maxlength=\"11\" value=\"$myid\"></td>
  </tr>
  <tr>
    <td>Subject: </td>
    <td><input type=\"text\" name=\"subject\" value=\"$subject\"></td>
  </tr>
  <tr>
    <td>Message (No HTML!): </td>
    <td><textarea name=\"message\" cols=\"45\" rows=\"5\" wrap=\"VIRTUAL\">$message</textarea></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type=\"submit\" name=\"submit\" style=\"font-size: 11px; padding: 2px;\" value=\"  Send Message  \">
    <input type=\"hidden\" name=\"dosubmit\" value=\"yes\"></td>
  </tr>
</table>
</form></p>");
ufooter();
mysql_close;
exit;
?>
