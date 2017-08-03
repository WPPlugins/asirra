<?php

if (isset($_POST['action']) && $_POST['action'] == 'update')
{
	unset($_POST['action']);
	$asirraPlugin->update_settings($_POST);
	include (dirname(__FILE__).'/crypt/cryptographp.cfg.php');
}

?>
<div class="wrap">
<h2>Asirra Options</h2>
<p>Asirra is a PHP library to protect your comments with an image-based HIP.
See <a href="http://research.microsoft.com/">http://research.microsoft.com/</a>.
When activated, comment posts will not be allowed unless the Asirra HIP
is correctly solved.
You must edit your comment.php template to include
<code>&lt;?php display_asirra(); ?&gt;</code>
somewhere within the &lt;form&gt; tag.</p>

<form name="cryptoptions" method="post" >
<input type="hidden" name="action" value="update" />
<fieldset class="options">
<table width="100%" cellspacing="2" cellpadding="5" class="editform">
</table>
</fieldset>
<!--
<p class="submit"><input type="submit" name="Submit" value="Update Options &raquo;" /></p>
-->
</form>
</div>
