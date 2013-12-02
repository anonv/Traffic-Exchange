<?php
session_start();
//exit(print_r($_POST));
include('../vars.php');
if(isset($_GET[action])) $action= $_GET['action'];
if(isset($_POST[action])) $action= $_POST['action'];
if (isset($_REQUEST['_SESSION'])) {
	header("Location: $self_url/control/index.php?e=sess-invasion");
	@session_destroy();
	@mysql_close;
	exit;
}
if (!isset($_SESSION['asess_name']) || !isset($_SESSION['asess_passwd'])) {
	header("Location: /control/index.php?error");
	exit;
} else {
	$db = db("select value from admin where field='login'");
	$dblogin = mysql_result($db, 0);
	$dbx = db("select value from admin where field='passwd'");
	$dbpasswd = mysql_result($dbx, 0);
	if ($_SESSION['asess_name'] != $dblogin || $_SESSION['asess_passwd'] != md5($dbpasswd)) {
		header("Location: /control/index.php?re-login");
		session_destroy();
		exit;
	}
}
function db($query) {
	global $db_host,$db_name,$db_user,$db_pwd;
	($mysql_link = @mysql_connect($db_host,$db_user,$db_pwd)) or die (print "Error: Couldn't connect to database:<br><br>".mysql_error());
	@mysql_select_db($db_name,$mysql_link) or die (print "Error: Couldn't Select Database:<br><br>".mysql_error());
	($mysql_result = @mysql_query($query,$mysql_link)) or die (print "Error: Database Select Failed:<br><br>".mysql_error());
	@mysql_close($mysql_link) or die (print "Error: Couldn't close database".mysql_error());
	return $mysql_result;
}



// Error Function
function error_ad($error) {
print "<p><center><table width=700 height=200 bgcolor=#FFFFFF><tr><td>";
	print "<h1>Error</h1>
<p>Sorry but you just encountered an error. The error is, $error. Please go back and fix this.</p>\n";
print "</td></tr></table></center></p>";
	footer1();
	exit;
}



// System Error
function sys_error_ad($error) {
print "<p><center><table width=700 height=200 bgcolor=#FFFFFF><tr><td>";
	print "<h1>System Error</h1>
<p>Sorry but you just encountered an error. The error is, $error. Please go back and fix this.</p>\n";
print "</td></tr></table></center></p>";
	footer1();
	exit;
}



//Header Function
function header1() {
	global $title;
	print "<html>\n<head>\n<title>$title Banner & Text Ad Rotator</title>\n<link href=\"style.css\" rel=\"stylesheet\" type=\"text/css\">\n<script language=\"JavaScript\">\n<!--\ndefaultStatus = '$title Banner & Text Ad Rotator Admin Area';\n//-->\n</script>\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n</head>\n<body>\n";


print "<p align=\"center\"><a href=\"".$_SERVER['PHP_SELF']."\">Banner & Text Rotator Main Page</a> - <a href=./index.php>Main Website Admin Area</a></p>\n";

}



// footer Function
function footer1() {
	global $action;
echo("<p align=\"center\">");
	if ($action != "") {
$backkg = "<a href=\"".$_SERVER['PHP_SELF']."\">Back To Rotator Main Page</a>";
	} else {
$backkg = "<a href=./index.php>Back Main Admin Area</a>";
	}
	print "$backkg</p>\n</body>\n</html>";
}



// Main Admin Control Panel
function main_menu() {
	header1();
?>
<script language="JavaScript">
<!--
var content=new Array()
content[0]='<h1>Add New Banner</h1><p>Add a new banner here.</p>'
content[1]='<h1>Add New Text Ad</h1><p>Add a new text ad here.</p>'
content[2]='<h1>Modify Ads</h1><p>Modify text ads or banner ads here.</p>'
content[4]='<h1>Remove Ads</h1><p>Delete expired text ad or banner ads here.</p>'
content[9]='<h1>View Statistics</h1><p>All advert stats are seen here.</p>'
content[10]='<h1>Make Your Selection</h1><p>Use the navagtion to the left.</p>'


function regenerate(){
	window.location.reload()
}
function regenerate2(){
	if (document.layers)
	setTimeout("window.onresize=regenerate",450)
}

function changetext(whichcontent){
	if (document.all)
	descriptions.innerHTML='<font face="Verdana"><small>'+whichcontent+'<font></small>'
	else if (document.layers){
		document.d1.document.d2.document.write('<font face="Verdana"><small>'+whichcontent+'</small></font>')
		document.d1.document.d2.document.close()
	}
}
//-->
</script>

<p align="center"><table width="700" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr> 
<td colspan="2"><center><h1>Banner & Text Ad Rotator Admin Area</h1></center></td>
  </tr>
  <tr> 
    <td width="200"><table width="153" border="0" cellpadding="0" cellspacing="0">
        <tr> 
<form action="<? print $_SERVER['PHP_SELF']; ?>" onMouseOver="changetext(content[0])" onMouseOut="changetext(content[10])" method="POST">
                 <td width="153"> <input type=hidden name=action value="add_plain">
                  <input type="submit" value="Add New Banner" style="color: white; font-size: 10pt; font-family: Tahoma; font-weight: bold; background-color: navy; height: 26px; width: 160px; cursor: hand;">
              </td></form>
        </tr>
        <tr> 
          <form action="<? print $_SERVER['PHP_SELF']; ?>" onMouseOver="changetext(content[1])" onMouseOut="changetext(content[10])" method="POST">
                            <td><input type=hidden name=action value="add_rich">
                            <input type="submit" value="Add New Text Ad" style="color: white; font-size: 10pt; font-family: Tahoma; font-weight: bold; background-color: navy; height: 26px; width: 160px; cursor: hand;">
                        </td></form>
        </tr>
        <tr> 
          
<form action="<? print $_SERVER['PHP_SELF']; ?>" onMouseOver="changetext(content[2])" onMouseOut="changetext(content[10])" method="POST">
<td><input type=hidden name=action value="modify">
                            <input type="submit" value="Modify Ads" style="color: white; font-size: 10pt; font-family: Tahoma; font-weight: bold; background-color: navy; height: 26px; width: 160px; cursor: hand;">
                        
		  </td></form>
        </tr>
        <tr> 
<form action="<? print $_SERVER['PHP_SELF']; ?>" onMouseOver="changetext(content[4])" onMouseOut="changetext(content[10])" method="POST">
<td> <input type=hidden name=action value="delete_banner">
                            <input type="submit" value="Remove Ads" style="color: white; font-size: 10pt; font-family: Tahoma; font-weight: bold; background-color: navy; height: 26px; width: 160px; cursor: hand;">
   </td></form>
        </tr>
        <tr> 
          <form action="<? print $_SERVER['PHP_SELF']; ?>" onMouseOver="changetext(content[9])" onMouseOut="changetext(content[10])" method="POST">
                          <td>
                            <input type=hidden name=action value="view_stats">
                            <input type="submit" value="Ad Stats" style="color: white; font-size: 10pt; font-family: Tahoma; font-weight: bold; background-color: navy; height: 26px; width: 160px; cursor: hand;">
                        </td></form>
        </tr>
      </table></td>
<td width="500" height="200"><p> 
<ilayer id="d1" width="155" height="155" border=1> 
<layer id="d2" width="155" height="155" border=1> 
<div id="descriptions"> </div>
</layer>
</ilayer>
</td></tr></table>

<?
footer1();
}



// Add banner
function add_plain_display() {

print "<p><center><table border=\"0\" cellspacing=\"1\" cellpadding=\"8\" bgcolor=\"#FFFFFF\" width=\"700\">
<form action='".$_SERVER['PHP_SELF']."' method=\"POST\">
<tr>
<td colspan=2><h1>Add Banner To Rotator</h1>
<p>All the fields make with a <b><font color=\"#ff0000\">*</font></b> are required.</p></td>
</tr>
<tr>
<td><b>Banner Name: <font color=\"#ff0000\">*</font></b><br>
Short desription of the banner.</td>
<td><input type=text name=name maxlength=30 size=28>
</td>
</tr>
<tr>
<td>
<b>Link Url: <font color=\"#ff0000\">*</font></b><br>
The Address where the banner redirects to.</td>
<td><input type=text name=link size=28 value=\"http://\"></td>
</tr>
<tr>
<td>
<b>Banner URL <font color=\"#ff0000\">*</font>:</b><br>
This is the address of the banner image.</td>
<td>
<input type=text name=banner size=28 value=\"http://\">
</td>
</tr>
<tr>
<td><b>Height: <font color=\"#ff0000\">*</font></b><br>
The height of the banner.</td>
<td><input type=text name=heigth maxlength=3 size=3 value=60></td>
</tr>
<tr>
<td>
<b>Width: <font color=\"#ff0000\">*</font></b><br>
The width of the banner.</td>
<td><input type=text name=width maxlength=3 size=3 value=468></td>
</tr>
<tr>
<td><b>Alt Tag: <font color=\"#ff0000\">*</font></b><br>
The text that appears when mouse put over banner.</td>
<td><input type=text name=alt maxlength=50 size=28></td>
</tr>
<tr>
<td>
<b>Number of allowed exposures:</b><br>
Leave blank for unlimited number of exposures</td>
<td><input type=text name=num_exp maxlength=9 size=10></td>
</tr>
<tr>
<td>
<b>Probability of banner showing: <font color=\"#ff0000\">*</font></b><br>
Increase the probability of this banner appearing.</td>
<td>
		<select name=prob>
			<option value=\"1\" selected>1 - Equal chance of appearing
			<option value=\"2\">2 
			<option value=\"3\">3
			<option value=\"4\">4 
			<option value=\"5\">5 - 5 times more likely to appear 
		</select>
</td>
</tr>
<tr>
<td><b>Open in a new window: <font color=\"#ff0000\">*</font></b><br>
When banner clicked should link open in new URL?</td>
<td>
		<select name=window>
			<option value=\"1\">Yes
			<option value=\"2\">No
		</select>
</td>
</tr>
<tr>
<td><b>Member ID #:</b><br>
Allow advertiser to view stats of this banner, leave blank if N/A.</td>
<td><input type=text name=advert_login maxlength=11 size=11></td>
</tr>
<tr>
<td>&nbsp;</td>
<td>
<input type=hidden name=\"action\" value=\"add_plain_veify\">
<input type=\"submit\" value=\"Add Banner\">
<input type=\"reset\" value=\"Reset\">
</td>
</tr>
</form></table></center></p>\n";
}




// Check Banner
function add_plain_check() {
	//global $name,$link,$banner,$heigth,$width,$alt,$num_exp,$prob,$window,$advert_login;
	$name = $_POST['name'];
	$link = $_POST['link'];
	$banner = $_POST['banner'];
	$heigth = $_POST['heigth'];
	$width = $_POST['width'];
	$alt = $_POST['alt'];
	$num_exp = $_POST['num_exp'];
	$prob = $_POST['prob'];
	$window = $_POST['window'];
	$advert_login = $_POST['advert_login'];

	if ((!$name) || (!$link) || (!$banner) || (!$heigth) || (!$width) || (!$prob) || (!$window)) {
		error_ad("You left a required field blank");
	}
	if (!ereg("^http://[a-zA-Z0-9~.:/]*.[a-zA-Z]{2,3}",$link)) {
		error_ad("You did not enter a valid link url");
	}
	if (!ereg("^http://[a-zA-Z0-9~.:/]*.[a-zA-Z]{2,3}",$banner)) {
		error_ad("You did not enter a valid banner url");
	}
	if (!eregi_replace('[^0-9]','',$heigth)){
		error_ad("You did not enter a numberical value for heigth");
	}
	if (!eregi_replace('[^0-9]','',$width)){
		error_ad("You did not enter a numberical value for width($width)");
	}
	if (!$alt){
		error_ad("You left the ALT tag filed empty");
	}
	if (($advert_login)){
		if (!is_numeric($advert_login)){
			error_ad("The advertiser userid must be a number!");
		}
	} else {
		$advert_login = 0;
	}
	if (!empty($num_exp)) {
		if ($num_exp <= 0) {
			error_ad("number impressions must be greater than 0");
		}
	}
}



// Confirm Banner
function banner_confirm($id,$name,$link,$banner,$heigth,$width,$alt,$text,$mouse_text,$num_exp,$prob,$window,$advert_login,$action) {
	$mouse_text = stripslashes($mouse_text);
	$text = stripslashes($text);
	$alt = stripslashes($alt);
	$mouse_text = eregi_replace('"',"'",$mouse_text);
	$text = eregi_replace('"',"'",$text);
	$alt = eregi_replace('"',"'",$alt);
print "<p><center><table border=\"0\" cellspacing=\"1\" cellpadding=\"8\" bgcolor=\"#FFFFFF\" width=\"700\">";
	if ($action == "view_detailed"){
print "<tr><td colspan=2>";
		print "<h1>Detailed Banner Info</h1>
<p>Here is all the information that has been stored. If you wish to modify this 
please go back and select \"Modify Banner\".</p>\n";
print "</td></tr>";
	} elseif ($action == "delete_banner_select"){
print "<tr><td colspan=2>";
		print "<h1>Confirm Banner Delete</h1>
<p>Are you certain you want to delete this banner. There
is no coming back once a banner is deleted. Please make sure you 
have the correct banner before confirming.</p>\n";
print "</td></tr>";
		$action = "delete_banner_confirm";
	} else {
print "<tr><td colspan=2>";
		print "<h1>Confirm</h1>
<p>Please make sure that all info below is correct.</p>\n";
print "</td></tr>";
	}
	print "<form action='".$_SERVER['PHP_SELF']."' method=\"POST\">
<tr>
<td><b>Banner Name: <font color=\"#ff0000\">*</font></b></td>
<td><input type=hidden name=name value=\"$name\">$name</td>
</tr>
<tr>
<td><b>Link Url: <font color=\"#ff0000\">*</font></b></td>
<td><input type=hidden name=link value=\"$link\">$link</td>
</tr>
<tr>
<td><b>Banner URL <font color=\"#ff0000\">*</font>:</b></td>
<td><input type=hidden name=banner size=28 value=\"$banner\">$banner</td>
</tr>
<tr>
<td><b>Height: <font color=\"#ff0000\">*</font></b></td>
<td><input type=hidden name=heigth value=$heigth>$heigth</td>
</tr>
<tr>
<td><b>Width: <font color=\"#ff0000\">*</font></b></td>
<td><input type=hidden name=width value=$width>$width</td>
</tr>
<tr>
<td><b>Alt Tag: <font color=\"#ff0000\">*</font></b></td>
<td><input type=hidden name=alt value=\"$alt\">$alt</td>
</tr>
<tr>
<td><b>Number of allowed exposures:</b></td>
<td><input type=hidden name=num_exp value=$num_exp>\n";
	if ($num_exp != 0) {
		print $num_exp;
	} else {
		print "unlimited";
	}
	print "</td>
</tr>
<tr>
<td><b>Probability of banner showing: <font color=\"#ff0000\">*</font></b></td>
<td><input type=hidden name=prob value=$prob>$prob</td>
</tr>
<tr>
<td><b>Open in a new window: <font color=\"#ff0000\">*</font></b><input type=hidden name=window value=$window></td>
<td>\n";
	if ($window == 1){
		print "Yes";
	} else {
		print "No";
	}
	print "</td>
</tr>
<tr>
<td><b>Member ID #:</b></td><td bgcolor=\"#ffffff\"><input type=hidden name=advert_login value=\"$advert_login\">$advert_login</td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type=hidden name=\"action\" value=\"$action\">
<input type=hidden name=\"id\" value=\"$id\">
<input type=\"submit\" value=\"Confirm Banner\">
<input type=\"reset\" value=\"Reset\"></td>
</tr></form></table></center></p>\n";
}



// Add Banner Confirmed
function add_plain_add() {
	//global $name,$link,$banner,$heigth,$width,$alt,$text,$mouse_text,$num_exp,$prob,$window,$advert_login;
	
	$name = $_POST['name'];
	$link = $_POST['link'];
	$banner = $_POST['banner'];
	$heigth = $_POST['heigth'];
	$width = $_POST['width'];
	$alt = $_POST['alt'];
	$num_exp = $_POST['num_exp'];
	$prob = $_POST['prob'];
	$window = $_POST['window'];
	$advert_login = $_POST['advert_login'];
	$mouse_text = stripslashes($_POST['mouse_text']);
	$text = stripslashes($text);
	$alt = stripslashes($alt);
	$mouse_text = eregi_replace('"',"'",$mouse_text);
	$text = eregi_replace('"',"'",$text);
	$alt = eregi_replace('"',"'",$alt);
	if ($num_exp == ""){
		$num_exp = 0;
	}
	$date = date("j/M/Y");
	$db = db("INSERT INTO ad_info (ad_name,type,link_url,banner_url,h,w,alt,bottom_text,mouse_text,num_allow_exp,num_exp,num_clicks,prob,win,adv_user,date) values (\"$name\",1,\"$link\",\"$banner\",\"$heigth\",\"$width\",\"$alt\",\"$text\",\"$mouse_text\",\"$num_exp\",0,0,\"$prob\",\"$window\",\"$advert_login\",\"$date\");");
	print "<p><center><table width=700 bgcolor=FFFFFF height=200><tr><td>
<h1>Banner Added</h1>
<p>Your banner has been successfully added.</p>\n";
	print "</td></tr></table></center></p>\n";



}
// Add RM
function add_rich_display() {
	print "<p><center><table border=\"0\" cellspacing=\"1\" cellpadding=\"8\" bgcolor=\"#FFFFFF\" width=\"700\"><form action='".$_SERVER['PHP_SELF']."' method=\"POST\">
<tr><td clspan=2>
<h1>Add New Text Ad</h1>
<p>All the fields make with a <b><font color=\"#ff0000\">*</font></b> are required.</p></td></tr>\n";
	print "<tr>
<td><b>Text Ad Name: <font color=\"#ff0000\">*</font></b><br>
Short desription of the text ad.</td>
<td><input type=text name=name maxlength=30 size=28></td>
</tr>
<tr>
<td><b>Link Url:</b><br>
Location to redirect to, after being clicked.</td>
<td><input type=text name=link size=28></td>
</tr>
<tr><td bgcolor=\"#ffffff\" colspan=2>
<b>HTML:</b> <b><font color=\"#ff0000\">*</font></b><br>
You should ONLY replace the website title.<br>The tag <i>&lt;!-- Link URL --></i> should remain instead of using a url. The link url is added above.<br>
<textarea name=html cols=55 rows=8><a href=\"<!-- Link URL -->\">WEBSITE TITLE HERE</a></textarea>
</td>
</tr>
<tr>
<td><b>Number of allowed exposures:</b><br>
Leave blank for unlimited number of exposures</td>
<td><input type=text name=num_exp maxlength=9 size=10></td>
</tr>
<tr>
<td><b>Probability of ad showing: <font color=\"#ff0000\">*</font></b><br>
Increase the probability of this text ad appearing.</td>
<td>
		<select name=prob>
			<option value=\"1\" selected>1 - Equal Chance
			<option value=\"2\">2 
			<option value=\"3\">3
			<option value=\"4\">4 
			<option value=\"5\">5 - 5 Times The Chance		</select>
</td>
</tr>
<tr>
<td><b>Advertiser Member ID #:</b><br>
Allow advertisers to view stats of this of this ad, leave blank if N/A.</td>
<td><input type=text name=advert_login maxlength=11 size=11></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type=hidden name=\"action\" value=\"add_rich_verify\">
<input type=\"submit\" value=\"Add Text Ad\">
<input type=\"reset\" value=\"Reset\"></td>
</tr>
</form></table></center></p>\n";
}



// RM Check
function add_rich_check(){
	//global $name,$link,$html,$num_exp,$prob,$advert_login;
	$name = $_POST['name'];
	$link = $_POST['link'];
	$html = $_POST['html'];
	$num_exp = $_POST['num_exp'];
	$prob = $_POST['prob'];
	$advert_login = $_POST['advert_login'];
	if (($link) && (!ereg("^http://[a-zA-Z0-9~.:/]*.[a-zA-Z]{2,3}",$link))) {
		error_ad("you did not enter a valid link url");
	}
	if (!$html){
		error_ad("you did not enter in any html to rotate");
	}
	if (!$name){
		error_ad("you did not enter in a name for the banner");
	}
	if (!$prob){
		error_ad("you did not enter in a prob value");
	}
	if ($advert_login){
		if (!is_numeric($advert_login)){
			error_ad("The advertiser user ID must be a number!");
		}
	} else {
		$advert_login = 0;
	}
	if ($num_exp){
		if ($num_exp <= 0){
			error_ad("number impressions must be greater than 0");
		}
	}
	if (eregi('<!-- Link URL -->',$html) && (!$link)){
		error_ad("you cannot have the special tag, <i>&lt;!-- Link URL --></i> unless you set the value for 'Link URL'");
	}
}
// RM Verify
function banner_rich_confirm($id,$name,$link,$html,$num_exp,$prob,$advert_login,$action){
	$html = stripslashes($html);
	$name = eregi_replace('<',"&lt;",$name);
print "<p><center><table border=\"0\" cellspacing=\"1\" cellpadding=\"8\" bgcolor=\"#FFFFFF\" width=\"700\">";
	if ($action == "view_detailed"){
print "<tr><td colspan=2>";
		print "<h1>Detailed Ad Info!!</h1>
<p>Here is all the information that has been stored. If you wish to modify this 
please go back and select \"Modify\".</p>\n";
print "</td></tr>";
	} elseif ($action == "delete_banner_select"){
print "<tr><td colspan=2>";
		print "<h1>Confirm Ad Delete</h1>
<p>Are you certain you want to delete this Ad? There
is no coming back once an ad is deleted. Please make sure you 
have the correct ad before confirming.</p>\n";
print "</td></tr>";
		$action = "delete_banner_confirm";
	} else  {
print "<tr><td colspan=2>";
		print "<h1>Confirm</h1>
<p>Please make sure that all info below is correct.</p>\n";
print "</td></tr>";
	}

	print "<form action='".$_SERVER['PHP_SELF']."' method=\"POST\">
<tr>
<td><b>Text Ad Name: <font color=\"#ff0000\">*</font></b><br></td>
<td><input type=hidden name=name value=\"$name\">$name</td>
</tr>
<tr>
<td><b>Link Url:</b><br></td>
<td><input type=hidden name=link value=\"$link\">$link</td>
</tr>
<tr>
<td colspan=2>
<b>HTML:</b> <b><font color=\"#ff0000\">*</font></b><br>
<textarea name=html cols=55 rows=8>$html</textarea></td>
</tr>
<tr>
<td><b>Number of allowed exposures:</b><br></td>
<td><input type=hidden name=num_exp value=$num_exp>\n";
	if ($num_exp != 0) {
		print $num_exp;
	} else {
		print "unlimited";
	}
	print "</td>
</tr>
<tr>
<td><b>Probability of ad showing: <font color=\"#ff0000\">*</font></b></td>
<td><input type=hidden name=prob value=$prob>$prob</td>
</tr>
<tr>
<td><b>Advertiser User ID:</b></td>
<td><input type=hidden name=advert_login value=\"$advert_login\">$advert_login</td>
</tr>
<tr>
<td colspan=\"2\">\n";
	if (!eregi('<!-- Link URL -->',$html)){
		print "<font color=\"#ff0000\"><b>Important:</b></font> The special tag, <i>&lt;!-- Link URL --></i> was not found. This
means the script cannot track clicks unless you use the Link generator</td>
</tr>\n";
print "<tr><td colspan=\"2\">";
	}
	if ($action == "view_detailed"){
		print " &nbsp\n";
	} elseif ($action == "delete_banner_confirm") {
		print "<input type=hidden name=\"action\" value=\"$action\">
<input type=hidden name=\"id\" value=\"$_POST[id]\">
<input type=\"submit\" value=\"Confirm Delete Text Ad\">\n";
	} else {
		print "<input type=hidden name=\"action\" value=\"$action\">
<input type=hidden name=\"id\" value=\"$_POST[id]\">
<input type=\"submit\" value=\"Confirm Text Ad\">
<input type=\"reset\" value=\"Reset\">\n";
	}
	print "</td></tr></form></table></center></p>\n";
}




// RM Done
function add_rich_add() {
	//global $name,$link,$html,$num_exp,$prob,$window,$advert_login,$advert_pass;
	
	$name = $_POST['name'];
	$link = $_POST['link'];
	$html = $_POST['html'];
	$num_exp = $_POST['num_exp'];
	$prob = $_POST['prob'];
	$window = $_POST['window'];
	$advert_login = $_POST['advert_login'];
	$advert_pass = $_POST['advert_pass'];
	$html = addslashes($html);
	$html = eregi_replace("\r",'',$html);
	$html = eregi_replace("\n",'',$html);
	if ($num_exp == ""){
		$num_exp = 0;
	}
	$date = date("j/M/Y");
	$db = db("insert into ad_info (ad_name,type,link_url,banner_url,h,w,alt,bottom_text,mouse_text,html,num_allow_exp,num_exp,num_clicks,prob,win,adv_user,date) values (\"$name\",2,\"$link\",\"$banner\",\"$heigth\",\"$width\",\"$alt\",\"$text\",\"$mouse_text\",\"$html\",\"$num_exp\",0,0,\"$prob\",\"$window\",$advert_login,\"$date\");");
print "<p><center><table border=\"0\" cellspacing=\"1\" cellpadding=\"8\" bgcolor=\"#FFFFFF\" width=\"700\"><tr><td>";
	print "<h1>Text Ad Added</h1>
<p>Your text ad has been successfully added.</p>\n";
print "</td></tr></table></center></p>";

}



// Modify Advertisement
function modify_banner_select(){
	global $action;
print "<p><center><table border=\"0\" cellspacing=\"1\" cellpadding=\"8\" bgcolor=\"#FFFFFF\" width=\"700\">";
	if ($action == "delete_banner"){
		$action = "delete_banner_select";
print "<tr><td colspan=3>";
		print "<h1>Delete Advertisement</h1>
<p>Please select the ad you wish to delete, please be careful.</p>\n";
print "</td></tr>";
	} else {
		$action = "modify_banner_display";
print "<tr><td colspan=3>";
		print "<h1>Modify Advertisement</h1>
<p>Please select an ad to modify from the list below.</p>\n";
print "</td></tr>";
	}
	print "<form action='".$_SERVER['PHP_SELF']."' method=\"POST\">
<tr>
<td><b>Advertisement Name:</b></td>
<td><b>Type:</b>
<td><b>Member ID #:</b></td>
<td align=left><b>Select</b></td>
</tr>\n";
	$i = 1;
	$db = db("select ad_name,type,ad_id,adv_user from ad_info");
	while ($info = mysql_fetch_row($db)) {
		print "<tr>
<td>$info[0]</td>
<td>\n";
		if ($info[1] == 1) {
			print "Banner";
		} else {
			print "Text";
		}
		print "</td>
<td align=left>#".$info[3]."</td>
<td align=left><input type=radio name=id value=\"$info[2]\"></td></tr>\n";
		$f = 1;
		$i++;
	}
	if (!$f){
		print "<tr>
<td colspan=3>No Ads Available</td></tr>\n";
	}
	print "<tr>
<td>&nbsp</td>
<td>&nbsp</td>
<td>&nbsp</td>
<td><input type=hidden name=action value='$action'>
<input type=submit value=\"Modify Ad\"></td>\n";
	print "</form></table></center></p>\n";
}



// Selected Banner
function modify_banner_display(){
	$id = $_POST['id'];
	if (!$id){
		error_ad("you did not select an ad to modify");
	}
	$db = db("SELECT ad_name,type,link_url,banner_url,h,w,alt,bottom_text,mouse_text,html,num_allow_exp,num_exp,num_clicks,prob,win,adv_user FROM ad_info WHERE ad_id = $id");
	$info = mysql_fetch_row($db);
	if ($info[1] == 1){
		modify_display_plain($info,$id);
	} else {
		modify_display_rich($info,$id);
	}
}



// Modify Banner
function modify_display_plain($info,$id){
print "<p><center><table border=\"0\" cellspacing=\"1\" cellpadding=\"8\" bgcolor=\"#FFFFFF\" width=\"700\">";
print "<tr><td colspan=2>";
	print "<h1>Modify Banner Ad</h1>
	Please make changes then press 'Makes Changes' when done.</td></tr>\n";
	print "
	<form action='".$_SERVER['PHP_SELF']."' method=\"POST\">
<tr>
<td><b>Banner Name: <font color=\"#ff0000\">*</font></b><br>
Short desription of the banner.</td>
<td><input type=text name=name maxlength=30 size=28 value=\"$info[0]\"></td>
</tr>
<tr>
<td>
<b>Link Url: <font color=\"#ff0000\">*</font></b><br>
The Address where the banner redirects to.</td>
<td><input type=text name=link size=28  value=\"$info[2]\"></td>
</tr>
<tr>
<td><b>Banner URL <font color=\"#ff0000\">*</font>:</b><br>
This is the address of the banner image.</td>
<td><input type=text name=banner size=28 value=\"$info[3]\"></td>
</tr>
<tr>
<td>
<b>Height: <font color=\"#ff0000\">*</font></b><br>
The height of the banner.</td>
<td><input type=text name=heigth maxlength=3 size=3  value=\"$info[4]\">
</td>
</tr>
<tr>
<td>
<b>Width: <font color=\"#ff0000\">*</font></b><br>
The width of the banner.</td>
<td><input type=text name=width maxlength=3 size=3  value=\"$info[5]\">
</td>
</tr>
<tr>
<td>
<b>Alt Tag: <font color=\"#ff0000\">*</font></b><br>
The text that appears when mouse put over banner.</td>
<td><input type=text name=alt maxlength=120 size=28  value=\"$info[6]\"></td>
</tr>
<tr>
<td>
<b>Number of allowed exposures:</b><br>
Leave blank for unlimited number of exposures</td>
<td><input type=text name=num_exp maxlength=9 size=10  value=\"";
	if ($info[10] != "0"){
		print $info[10];
	}

	print "\">
	</td>
</tr>
<tr>
<td><b>Probability of banner showing: <font color=\"#ff0000\">*</font></b><br>
Increase the probability of this banner appearing.</td>
<td>
		<select name=prob>\n";
	for ($i=1;$i<=5;$i++){
		if ($info[13] == $i){
			print "<option value=\"$i\" selected>$i\n";
		} else {
			print "<option value=\"$i\">$i\n";
		}
	}

	print "
		</select>
	</td>
</tr>
<tr><td><b>Open in a new window: <font color=\"#ff0000\">*</font></b><br>
When banner clicked should link open in new URL?</td>
<td>
		<select name=window>\n";

	if ($info[14] == 1){
		print "<option value=\"1\" selected>Yes
			<option value=\"2\">No\n";
	}else {
		print "<option value=\"1\">Yes
			<option value=\"2\" selected>No\n";
	}
	print "
		</select>
	</td>
</tr>
<tr><td>
<b>Advertiser Member ID #:</b><br>
Allow advertisers to view stats of this of this banner, leave blank if N/A.</td>
<td><input type=text name=advert_login maxlength=20 size=28 value=\"$info[15]\"></td>
</tr>
<tr>
<td colspan=\"2\">
<input type=hidden name=\"action\" value=\"modify_plain_check\">
<input type=hidden name=\"id\" value=\"$id\">
<input type=\"submit\" value=\"Modify Banner\">
<input type=\"reset\" value=\"Reset\">
</td>
</tr>
</form></table></center></p>\n";
}



// Modify Banner Confirmed
function modify_plain_add(){
	//global $id,$name,$link,$banner,$heigth,$width,$alt,$text,$mouse_text,$num_exp,$prob,$window,$advert_login;
	$id = $_POST[id];
	$name = $_POST['name'];
	$link = $_POST['link'];
	$banner = $_POST['banner'];
	$heigth = $_POST['heigth'];
	$width = $_POST['width'];
	$alt = $_POST['alt'];
	$text = $_POST['text'];
	$mouse_text = $_POST['mouse_text'];
	$num_exp = $_POST['num_exp'];
	$prob = $_POST['prob'];
	$window = $_POST['window'];
	$advert_login = $_POST['advert_login'];
	$mouse_text = stripslashes($mouse_text);
	$text = stripslashes($text);
	$alt = stripslashes($alt);
	$mouse_text = eregi_replace('"',"'",$mouse_text);
	$text = eregi_replace('"',"'",$text);
	$alt = eregi_replace('"',"'",$alt);
	$name = eregi_replace('<',"&lt;",$name);
	if ($num_exp == "") {
		$num_allow_exp = 0;
	} else {
		$num_allow_exp = $num_exp;
	}
	$db = db("UPDATE ad_info SET ad_name = \"$name\",type = 1,link_url = \"$link\",banner_url = \"$banner\",h=\"$heigth\",w=\"$width\",alt=\"$alt\",bottom_text=\"$text\",mouse_text=\"$mouse_text\",html=\"\",num_allow_exp=\"$num_allow_exp\",prob=$prob,win=$window,adv_user=$advert_login WHERE ad_id=$id");
print "<p><center><table width=700 height=200 bgcolor=#FFFFFF><tr><td>";
	print "<h1>Banner Modified</h1>
<p>You have successfully modified the banner.</p>\n";
	print "</form></td></tr></table></center></p>\n";
}



// Modify RM
function modify_display_rich($info,$id){
	$info[9] = stripslashes($info[9]);
print "<p><center><table width=700 height=200 bgcolor=#FFFFFF>";
echo "<form method='POST'>";
	print "<tr><td colspan=2>
<h1>Modify Banner</h1>
<p>Please make required changes, then press 'Makes Changes' to effect changes.</td>
</tr>
<tr><td>
<b>Text Ad Name: <font color=\"#ff0000\">*</font></b><br>
Short desription of the text ad.</td>
<td><input type=text name=name maxlength=30 size=28 value=\"$info[0]\"></td>
</tr>
<tr>
<td>
<b>Link Url:</b><br>
Location to redirect to, after being clicked.</td>
<td><input type=text name=link size=28 value=\"$info[2]\"></td>
</tr>
<tr><td colspan=2>
<b>HTML:</b> <b><font color=\"#ff0000\">*</font></b><br>
You should ONLY replace the website title.<br>The tag <i>&lt;!-- Link URL --></i> should remain instead of using a url. The link url is added above.<br>
<textarea name=html cols=55 rows=8>$info[9]</textarea></td>
</tr>
<tr>
<td>
<b>Number of allowed exposures:</b><br>
Leave blank for unlimited number of exposures</td>
<td><input type=text name=num_exp maxlength=9 size=10  value=\"";
	if ($info[10] != "0"){
		print $info[10];
	}
	print "\"></td>
</tr>
<tr><td><b>Probability of banner showing: <font color=\"#ff0000\">*</font></b><br>Increase the probability of this banner appearing.</td>
<td><select name=prob>\n";
	for ($i=1;$i<=5;$i++){
		if ($info[13] == $i){
			print "<option value=\"$i\" selected>$i\n";
		} else {
			print "<option value=\"$i\">$i\n";
		}
	}
	print "</select></td>
</tr>
<tr><td>
<b>Advertiser Member ID:</b><br>
Allow advertisers to view stats of this of this banner, leave blank if N/A.</td>
<td><input type=text name=advert_login maxlength=20 size=28 value=\"$info[15]\"></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type=hidden name=\"action\" value=\"modify_rich_check\">
<input type=hidden name=\"id\" value=\"$id\">
<input type=\"submit\" value=\"Modify Text Ad\">
<input type=\"reset\" value=\"Reset\">
</td></tr></form></table></center></p>\n";
}



function modify_rich_add(){
	//global $id,$name,$link,$html,$num_exp,$prob,$window,$advert_login,$advert_pass;
	
	$id = $_POST['id'];
	$name = $_POST['name'];
	$link = $_POST['link'];
	$html = $_POST['html'];
	$num_exp = $_POST['num_exp'];
	$prob = $_POST['prob'];
	$window = $_POST['window'];
	$advert_login = $_POST['advert_login'];
	$advert_pass = $_POST['advert_pass'];
	$html = stripslashes($html);
	$html = addslashes($html);
	$html = eregi_replace("\n","",$html);
	$html = eregi_replace("\r","",$html);
	if ($num_exp == "") {
		$num_allow_exp = 0;
	} else {
		$num_allow_exp = $num_exp;
	}
	$db = db("UPDATE ad_info SET ad_name = \"$name\",type = 2,link_url = \"$link\",html=\"$html\",num_allow_exp=\"$num_allow_exp\",prob=$prob,adv_user=\"$advert_login\" WHERE ad_id=$id");
print "<p><center><table width=700 height=200 bgcolor=#FFFFFF><tr><td>";
	print "<h1>Text Ad Modified</h1>
<p>You have successfully modified the Text Ad Modified.</p>\n";
	print "</form></td></tr></table></center></p>\n";
}



function view_stats(){
	print "<p><center><table width=700 bgcolor=#FFFFFF>
<tr>
<td colspan=6><h1>Advertisement Statistics</h1>
<p>Below will show you statistics for each ad.</p></td>
</tr>
<form action='".$_SERVER['PHP_SELF']."' method=\"POST\">
	<tr><td><b>Ad Name:</b></td>
	<td><b>Type:</b></td>
	<td><b>Exposures:</b></td>
	<td><b>Allowed Exposures:</b></td>
	<td><b>Clicks:</b></td>
	<td><b>CTR</b></td>
	</tr>\n";
	$db = db("SELECT ad_id,ad_name,type,num_exp,num_allow_exp,num_clicks from ad_info ORDER BY ad_id ASC");
	while ($info = mysql_fetch_row($db)){
		print "<tr><td>$info[1]</td><td>\n";
		if ($info[2] == 1){
			print "Banner";
		} else {
			print "Text";
		}
		print "</td><td>$info[3]</td>\n";
		if ($info[4] == 0){
			print "</td><td>Unlimited</td>\n";
		} else {
			print "</td><td>$info[4]</td>\n";
		}
		print "<td>$info[5]</td>\n";
		if (($info[5] == 0) || ($info[3] == 0)){
			$ctr = 0;
		} else {
			$ctr = $info[5] / $info[3];
			$ctr = $ctr * 100;
			$ctr = substr($ctr,0,5);
		}
		print "<td>$ctr %</td></tr>\n";
		$found = 1;
	}
	if (!$found){
		print "<tr><td colspan=6>No Ads Were Found</td></tr></form></table></center></p>\n";
	} else {
		print "<tr><td colspan=6>&nbsp;</td></tr></form></table></center></p>\n";
	}
}




function view_detailed(){
	global $action;
	$id = $_POST['id'];
	if (!$id){
		error_ad("you did not select a banner to view");
	}
	if ($action != "delete_banner_select"){
		$action = "view_detailed";
	}
	$db = db("SELECT ad_id,ad_name,link_url,banner_url,h,w,alt,bottom_text,mouse_text,num_exp,prob,win,adv_user,type,html FROM ad_info WHERE ad_id = \"$id\"");
	$info = mysql_fetch_row($db);
	if ($info[13] == 1){
		banner_confirm($id,$info[1],$info[2],$info[3],$info[4],$info[5],$info[6],$info[7],$info[8],$info[9],$info[10],$info[11],$info[12],$action);
	} else {
		banner_rich_confirm($id,$info[1],$info[2],$info[14],$info[9],$info[10],$info[12],$action);
	}
}




function delete_now(){
	if(isset($_POST[id]))$id = $_POST[id];
	if(isset($_GET[id]))$id = $_GET[id];
	if (eregi('[^0-9]',$id)){
		error_ad("invalid id($id)");
	}
	$db = db("SELECT ad_id FROM ad_info WHERE ad_id = $id");
	if (!$if = mysql_fetch_row($db)){
		error_ad ("no such advertisement");
	}
	$db = db("DELETE FROM ad_info WHERE ad_id = $id");
	print "<p><center><table width=700 bgcolor=FFFFFF height=200><tr><td>
<h1>Advertisement Deleted</h1>
<p>Your advertisement has been successfully deleted.</p>\n";
	print "</td></tr></table></center></p>\n";
}



// ===============================
function get_html_group(){
	global $self_url;
	$gid =$_POST['gid'];
?>
<table border="0" cellspacing="1" cellpadding="8" bgcolor="#000000" width="350">
<tr><td bgcolor="#ffffff">
	<b>Group HTML:</b>
	This is the HTML that is needed to display a group, both SSI and NON-SSI methods are shown<P>
	SSI method:
	<textarea cols="50" rows="4">&lt;?
<? 
print "\$gid = $gid;
include(\"".$_SERVER['DOCUMENT_ROOT']."/banner.php"; ?>");
?&gt;</textarea><P>
Non SSI method:
<textarea cols="50" rows="4">&lt;script language="JavaScript" src="<? print "$self_url"."banner.php?gid=$gid&"; ?>style=non_ssi"&gt;&lt;/script&gt;</textarea>
</td></tr>
<tr><td bgcolor="#ffffff">
<input type=button OnClick="javascript:history.go(-1)" value="<< Back">
</td></tr>
</table>
<?	
}
if ($action == ""){
	main_menu();
	exit;
}
header1();
if ($action == "modify_plain_check") {
	add_plain_check();
	$action = "modify_plain_add";
	//exit(print_r($_POST));
	banner_confirm($_POST[id],$_POST[name],$_POST[link],$_POST[banner],$_POST[heigth],$_POST[width],$_POST[alt],$_POST[text],$_POST[mouse_text],$_POST[num_exp],$_POST[prob],$_POST[window],$_POST[advert_login],$action);
	footer1();
	exit;
}
if ($action == "modify_plain_add") {
	modify_plain_add();
}
if ($action == "add_plain") {
	add_plain_display();
}
if ($action == "add_plain_veify") {
	add_plain_check();
	$action = "add_plain_add";
	//exit(print_r($_POST));
	banner_confirm($_POST[id],$_POST[name],$_POST[link],$_POST[banner],$_POST[heigth],$_POST[width],$_POST[alt],$_POST[text],$_POST[mouse_text],$_POST[num_exp],$_POST[prob],$_POST[window],$_POST[advert_login],$action);
//	banner_confirm($id,$name,$link,$banner,$heigth,$width,$alt,$text,$mouse_text,$num_exp,$prob,$window,$advert_login,$action);
	footer1();
	exit;
}
if ($action == "add_plain_add") {
	add_plain_check();
	add_plain_add();
}
if ($action == "add_rich") {
	add_rich_display();
}
if ($action == "add_rich_verify") {
	add_rich_check();
	$action = "add_rich_add";
	banner_rich_confirm($_POST[id],$_POST[name],$_POST[link],$_POST[html],$_POST[num_exp],$_POST[prob],$_POST[advert_login],$action);
	footer1();
	exit;
}
if ($action == "add_rich_add") {
	add_rich_check();
	add_rich_add();
}
if ($action == "modify_rich_check") {
	add_rich_check();
	$action = "modify_rich_add";
	banner_rich_confirm($_POST[id],$_POST[name],$_POST[link],$_POST[html],$_POST[num_exp],$_POST[prob],$_POST[advert_login],$action);
	footer1();
	exit;
}
if ($action == "modify_rich_add") {
	modify_rich_add();
}
if ($action == "modify") {
	modify_banner_select();
	footer1();
	exit;
}
if ($action == "modify_banner_display") {
	modify_banner_display();
}
if ($action == "view_stats"){
	view_stats();
}
if ($action == "Modify Banner") {
	modify_banner_display();
}
if ($action == "View Detailed") {
	view_detailed();
}
if ($action == "delete_banner") {
	modify_banner_select();
	footer1();
	exit;
}
if ($action == "delete_banner_select") {
	view_detailed();
}
if ($action == "delete_banner_confirm") {
	delete_now();
}
if ($action == "group_add"){
	group_add_display();
}
if ($action == "add_group_check"){
	add_group_check();
}
if ($action == "modify_group_select"){
	modify_group_select();
}
if ($action == "modify_group_display"){
	modify_group_display();
}
if ($action == "modify_group_check"){
	add_group_check();
}
if ($action == "delete_group_select"){
	modify_group_select();
}
if ($action == "delete_group_display"){
	delete_group_display();
}
if ($action == "delete_group_delete"){
	delete_group_delete();
}
if ($action == "get_html_select"){
	get_html_select();
}
if ($action == "get_html_link"){
	get_html_link();
}
if ($action == "get_html_group"){
	get_html_group();
}
footer1();
exit;
?>
