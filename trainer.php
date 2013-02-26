<?php

require_once('lib/settings.php');
require_once('lib/db.php');
require_once('lib/users.php');
require_once('lib/session.php');
require_once('lib/login.php');
require_once('lib/voc.php');

if(!connect_mysql()) {
	exit();
}

include('lib/loginfilter.php');
include('lib/navbar.php');

if(!isset($_SESSION['i'])) {
	$_SESSION['i'] = $_SESSION['userinfo']->interval;
	$_SESSION['intervalchanged'] = false;
}

$interval = $_SESSION['i'];
if(isset($_REQUEST['i']))
	$interval = intval($_REQUEST['i']);
if($interval < 0)
	$interval = 1;
$_SESSION['i'] = $interval;

// set interval
if(isset($_POST['setinterval'])) {
	if($_SESSION['userinfo']->interval != $_SESSION['i']) {
		if(setUserInterval($_SESSION['userid'], $interval)) {
			$_SESSION['intervalchanged'] = true;
			$_SESSION['userinfo']->interval = $interval;
			setInfo('Zeitraum wurde gespeichert!');
		} else
			setError('Zeitraum wurde nicht gespeichert!');
	}
	header("location: {$SETTINGS['url']}/trainer");
	exit();
}

$options = '';
$intervals = array(1, 2, 3, 4, 6, 8, 10, 16, 32, 64);
foreach($intervals as $i) {
	$n = ($i != 1) ? 'n' : '';
	$active = $i == $interval ? ' selected="selected"' : '';
	$options .= "<option value=\"$i\"$active>$i Woche$n</option>\n";
}

function checkVoc($voc, $ref) {
	$ref = strtolower(trim($ref));
	$voc = strtolower(trim($voc));
	$ref = str_replace('-', ' ', $ref); // remove '-'
	$voc = str_replace('-', ' ', $voc);
	$ref = preg_replace('/\\s+/', ' ', $ref); // ignore whitespace count
	$voc = preg_replace('/\\s+/', ' ', $voc);

	if($voc == $ref)
		return true;
	if((strlen($voc) <= 4) && ($voc == $ref))
		return true;
	if((strlen($voc) > 4) && (strpos($ref, $voc) !== false))
		return true;
	return false;
}

$answertext = '';
if(isset($_POST['voc']) && isset($_SESSION['voc'])) {
	$voc = strtolower(trim($_POST['voc']));
	$ref = strtolower($_SESSION['voc']->english);
	$correct = checkVoc($voc, $ref);

	if(!$correct) {
		foreach($_SESSION['vocs'] as $v) {
			if(checkVoc($voc, $v->english)) {
				$correct = true;
				break;
			}
		}
	}

	$sent = htmlspecialchars(trim($_POST['voc']), 0, 'UTF-8');
	if($correct)
		$answertext = "<p class=\"correct\"><span>Richtig</span><br />$sent</p>";
	else
		$answertext = "<p class=\"wrong\"><span>Falsch</span><br />$sent</p>";

	$stats = getUserInfo($_SESSION['userid']);
	if($correct)
		$stats->correct++;
	else
		$stats->wrong++;
	updateUserStats($_SESSION['userid'], $stats->correct, $stats->wrong);
	$_SESSION['userinfo'] = $stats;

	$answertext .= <<< EOT
<table class="voc list">
	<tbody>
EOT;

	foreach($_SESSION['vocs'] as $v) {
		$english = htmlspecialchars($v->english, 0, 'UTF-8');
		$german = htmlspecialchars($v->german, 0, 'UTF-8');
		$id = $_SESSION['voc']->id;
		$answertext .= <<< EOT
		<tr>
			<td><a href="{$SETTINGS['path']}/mod/$id">$german</a></td>
			<td><a href="{$SETTINGS['path']}/mod/$id">$english</a></td>
		</tr>
EOT;
	}
	$answertext .= <<< EOT
	</tbody>
</table>
EOT;
}

if(!isset($_SESSION['voc']) || isset($_POST['voc']) || $_SESSION['intervalchanged']) {
	if($_SESSION['intervalchanged'])
		$_SESSION['intervalchanged'] = false;
	$interval_start = $interval;
	$voc = getVocByTime($interval * 7);
	$count = 0;
	while($interval <= 64) {
		$count = getVocByTimeCount($interval * 7);
		if($count === false) {
			setError('Fehler beim suchen!');
			$count = 0;
			break;
		}
		if($count >= 10)
			break;
		$interval++;
	}
	if($count < 10) {
		setError('Nicht genügend Vokabeln zum trainieren!');
		unset($_SESSION['voc']);
	} else {
		$voc = getVocByTime($interval * 7);
		if($voc === false)
			setError('Fehler beim laden der Vokabeln!');
		else {
			$_SESSION['voc'] = $voc;
			$vocs = getVocsByGerman($voc->german);
			if($vocs === false) {
				setError('Fehler beim suchen!');
				unset($_SESSION['voc']);
			} else {
				if($interval != $interval_start)
					setInfo("Aufgrund mangelnder Vokabeln wurde der Zeitraum auf $interval Wochen erhöht");
				$_SESSION['vocs'] = $vocs;
			}
		}
	}
}

$table = '';
if(isset($_SESSION['voc'])) {
	$english = htmlspecialchars($_SESSION['voc']->english, 0, 'UTF-8');
	$german = htmlspecialchars($_SESSION['voc']->german, 0, 'UTF-8');
	$table = <<< EOT
<form method="post" action="{$SETTINGS['path']}/trainer">
	<table class="trainer">
		<thead>
			<tr>
				<th>Deutsch</th>
				<th>Englisch</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>$german</td>
				<td><input type="text" name="voc" id="input" value="" autocomplete="off" /></td>
			</tr>
			<tr>
				<td colspan="2" class="center"><input type="submit" name="check" value="Check &raquo;" /></td>
			</tr>
		</tbody>
	</table>
</form>
<script type="text/javascript">//<![CDATA[
	document.getElementById('input').focus();
//]]>
</script>
EOT;
}

$lastname = htmlspecialchars($_SESSION['userinfo']->lastname, 0, 'UTF-8');
$total = $_SESSION['userinfo']->correct + $_SESSION['userinfo']->wrong;
$percents = ($total != 0) ? ($_SESSION['userinfo']->correct / $total) * 100.0 : 0;

// OUTPUT
$TITLE = 'Trainer';
$CONTENT = <<< EOT
<h2>Who can we ask? ... $lastname!</h2>
$table
$answertext
<div class="stats">
	<h3>Statistik</h3>
	<div class="meter"><div style="width: $percents%;" /></div></div>
</div>
<form method="post" action="{$SETTINGS['path']}/trainer">
	<p>Pr&uuml;fe die letzten
		<select name="i">
$options
		</select>
		<input type="submit" name="setinterval" value="Go &raquo;" />
	</p>
</form>
EOT;

include('lib/template.php');
