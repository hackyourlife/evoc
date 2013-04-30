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

include('lib/navbar.php');

$CONTENT = <<< EOT
<h2>Vokabeln anzeigen seit - Druckansicht</h2>
<form method="get" action="." onsubmit="go(); return false;">
	<table style="width: auto;">
		<tbody>
			<tr>
				<td><input type="text" id="day" placeholder="Tag" /></td>
				<td><input type="text" id="month" placeholder="Monat" /></td>
				<td><input type="text" id="year" placeholder="Jahr" /></td>
				<td><input type="submit" value="zeigen" /></td>
			</tr>
		</tbody>
	</table>
</form>
<script type="text/javascript">//<![CDATA[
document.getElementById('day').focus();
function go() {
	var day = parseInt(document.getElementById('day').value);
	var month = parseInt(document.getElementById('month').value);
	var year = parseInt(document.getElementById('year').value);
	if(isNaN(day) || isNaN(month) || isNaN(year))
		return false;
	document.location.href = 'since/' + day + '/' + month + '/' + year;
}
//]></script>
</script>
EOT;

include('lib/template.php');
