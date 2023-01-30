<div id="icons">

<div class="icon"><h3>Testimonials</h3>
Read what members have to say about this service.<br />
<a href="<?= $self_url; ?>signup.php?show=testimonials<?php if ($ref) {echo "&ref=".$ref; }?>" title="Member Testimonials">click here...</a></div>

<div class="icon"><h3>Terms of Use</h3>
Rules must be followed in order to be a member.<br />
<a href="<?= $self_url; ?>signup.php?show=terms<?php if ($ref) {echo "&ref=".$ref; }?>" title="Terms Of Use">click here...</a></div>

<div class="icon"><h3>Contact</h3>
Use the contact form to say whats on your mind.<br />
<a href="<?= $self_url; ?>contact.php<?php if ($ref) {echo "?ref=".$ref; }?>" title="Contact Form">click here...</a></div>

<div style="clear: both"></div>
</div>


<h4>Welcome To <?= $title; ?></h4>

<?php if (isset($iearned_n) && $iearned_n != "") {echo "<p>".$iearned_n."</p>"; } ?>

<p><?php get_main_content(); ?></p>

<p align="center"><br><a href="<?= $self_url; ?>signup.php<?php if ($ref) {echo "?ref=".$ref; }?>" title="Signup">
<svg fill="#000000" height="64px" width="64px" version="1.1" id="Filled_Icons" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g id="Add-User-Filled"> <path d="M21,14h-2v-3h-3V9h3V6h2v3h3v2h-3V14z"></path> <path d="M14,6c0,2.76-2.24,5-5,5S4,8.76,4,6s2.24-5,5-5S14,3.24,14,6z M17,23v-4c0-4.42-3.58-8-8-8h0c-4.42,0-8,3.58-8,8v4"></path> </g> </g></svg>
</a></p>


