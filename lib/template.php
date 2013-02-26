<?php

header('content-type: text/html; charset=utf-8');
header('cache-control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('pragma: no-cache');
header('expires: Thu, 19 Nov 1981 08:52:00 GMT');

$uri = substr($_SERVER['REQUEST_URI'], strlen($SETTINGS['path']));
$end = strpos($uri, '/', 1);
if($end === false)
	$end = strlen($uri);
$uri = substr($uri, 0, $end);

$navbar = '';
if(isset($nav)) {
	$navbar = '<ul id="nav">';
	foreach($nav as $name => $url) {
		$htmlname = htmlentities($name, 0, 'UTF-8');
		$htmlurl = '';
		$active = '';
		if(is_array($url)) {
			$htmlurl = $url[0];
			if((array_search($uri, $url) !== false) || (array_search($_SERVER['REQUEST_URI'], $url) !== false))
				$active = ' class="active"';
		} else {
			$htmlurl = htmlentities($url);
			if(($url == $uri) || ($SETTINGS['path'] . $url == $_SERVER['REQUEST_URI']))
				$active = ' class="active"';
		}
		$navbar .= "<li$active><a href=\"{$SETTINGS['path']}$htmlurl\">$htmlname</a></li>\n";
	}
	$navbar .= '</ul>';
}

$error = getError();
$warning = false;
$info = getInfo();

$boxes = '';
if($error !== false) {
	$error_html = htmlentities($error, 0, 'UTF-8');
	$boxes .= "<p class=\"error\"><img src=\"{$SETTINGS['path']}/images/icons/error.png\" alt=\"Fehler:\" /> $error_html</p>\n";
}

if($warning !== false) {
	$warning_html = htmlentities($warning, 0, 'UTF-8');
	$boxes .= "<p class=\"warning\"><img src=\"{$SETTINGS['path']}/images/icons/error.png\" alt=\"Warnung:\" /> $warning_html</p>\n";
}

if($info !== false) {
	$info_html = htmlentities($info, 0, 'UTF-8');
	$boxes .= "<p class=\"info\"><img src=\"{$SETTINGS['path']}/images/icons/information.png\" alt=\"Information:\" /> $info_html</p>\n";
}

if(!isset($TITLEHTML))
	$TITLEHTML = htmlentities(isset($TITLE) ? "$TITLE | eVOC - Englisch Vokabeltrainer" : "eVOC | Englisch Vokabeltrainer", 0, 'UTF-8');

echo(<<< EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de" xml:lang="de">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>$TITLEHTML</title>
		<link type="text/css" rel="stylesheet" href="{$SETTINGS['path']}/css/main.css" />
	</head>
	<body>
		<div id="header">
			<div class="wrapper">
				<h1>eVOC - Englisch Vokabeltrainer</h1>
$navbar
			</div>
		</div>

		<hr />

		<div id="content">
			<div class="wrapper">
				{$boxes}{$CONTENT}
			</div>
		</div>

		<hr />

		<div id="footer">
			<div class="copy">&copy; <a href="http://hackyourlife.tk">hackyourlife</a></div>
		</div>
		<!-- Piwik --> 
		<script type="text/javascript">
		var pkBaseURL = (("https:" == document.location.protocol) ? "https://stat.patrickbuchner.tk/" : "http://stat.patrickbuchner.tk/");
		document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
		</script><script type="text/javascript">
		try {
		var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 1);
		piwikTracker.trackPageView();
		piwikTracker.enableLinkTracking();
		} catch(err) {}
		</script><noscript><p><img src="http://stat.patrickbuchner.tk/piwik.php?idsite=1" style="border:0" alt="" /></p></noscript>
		<!-- End Piwik Tracking Code -->
	</body>
</html>
EOT
);
