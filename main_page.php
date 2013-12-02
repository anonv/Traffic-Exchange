<div id="icons">

<div class="icon"><h3>Testimonials</h3>
Read what members have to say about this service.<br />
<a href="<? print ("$self_url"); ?>signup.php?show=testimonials<? if ($ref) {echo("&ref=$ref"); }?>" title="Member Testimonials">click here...</a></div>

<div class="icon"><h3>Terms of Use</h3>
Rules must be followed in order to be a member.<br />
<a href="<? print ("$self_url"); ?>signup.php?show=terms<? if ($ref) {echo("&ref=$ref"); }?>" title="Terms Of Use">click here...</a></div>

<div class="icon"><h3>Contact</h3>
Use the contact form to say whats on your mind.<br />
<a href="<? print ("$self_url"); ?>contact.php<? if ($ref) {echo("?ref=$ref"); }?>" title="Contact Form">click here...</a></div>

<div style="clear: both"></div>
</div>


<h4>Welcome To <? print ("$title"); ?></h4>

<? if ($iearned_n != "") {echo("<p>$iearned_n</p>"); } ?>

<p><? get_main_content(); ?></p>

<p align="center"><br><a href="<? print ("$self_url"); ?>signup.php<? if ($ref) {echo("?ref=$ref"); }?>" title="Signup"><img src="<? print ("$self_url"); ?>images/signup.gif" alt="Signup" border="0"></a></p>
