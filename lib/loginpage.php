<?php

$TITLE = 'eVOC | Engisch Vokabeltrainer';

$URI = $_SERVER['REQUEST_URI'];

$registerlink = $SETTINGS['allow_register'] ? "<a href=\"{$SETTINGS['path']}/register\" rel=\"nofollow\">Noch kein Account?</a><br />" : '';

// main content
$CONTENT = <<< EOT
<h2>Zugriff verweigert</h2>
<h3>Login</h3>
<form method="post" action="$URI">
	<p>
		<input type="text" name="username" id="username" placeholder="Username" /><br />
		<input type="password" name="password" placeholder="Password" />
		<input type="submit" name="login" value="Login &raquo;" />
	</p>
	<p>
		$registerlink
		<a href="{$SETTINGS['path']}/lostpassword" rel="nofollow">Passwort vergessen?</a>
	</p>
</form>
<script type="text/javascript">// <![CDATA[
	document.getElementById('username').focus();
// ]]>
</script>
EOT;
