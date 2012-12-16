<?php

require_once('lib/db.php');
require_once('lib/session.php');

if(file_exists('settings.cfg')) {
	setError('Konfiguration bereits vorhanden!');
	header('location: /');
	exit();
}

if(isset($_POST['save']) && isset($_POST['hostname']) && isset($_POST['database']) && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['prefix'])) {
	$hostname = trim($_POST['hostname']);
	$database = trim($_POST['database']);
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);
	$prefix = trim($_POST['prefix']);
	$error = false;
	$connection = @mysql_connect($hostname, $username, $password);
	if(!$connection) {
		setError('Fehler beim Herstellen der Datenbankverbindung! (' . mysql_error() . ')');
		$error = true;
	} else {
		if(!mysql_select_db($database, $connection)) {
			setError('Fehler beim selektieren der Datenbank! (' . mysql_error() . ')');
			$error = true;
		}
	}

	if(!$error) {
		// create tables
		$query = str_replace('%{PREFIX}', $prefix, file_get_contents('evoc.sql'));
		$queries = explode(';', $query);
		$error = false;
		foreach($queries as $query) {
			$query = trim($query);
			if(strlen($query) == 0)
				continue;
			if(!mysql_query($query)) {
				setError('Fehler beim anlegen der Tabellen! (' . mysql_error() . ')');
				$error = true;
				break;
			}
		}

		if(!$error) {
		// create config file
		file_put_contents('settings.cfg', <<< EOT
<?php
\$MYSQL = array(
	'hostname' => '$hostname',
	'database' => '$database',
	'username' => '$username',
	'password' => '$password',
	'prefix'   => '$prefix'
);
\$SETTINGS = array(
	'allow_register' => 'true'
);
EOT
			);
			setInfo('Konfiguration erstellt!');
			header('location: /');
			exit();
		}
	}
}

$hostname = isset($_POST['hostname']) ? htmlentities($_POST['hostname']) : '';
$database = isset($_POST['database']) ? htmlentities($_POST['database']) : '';
$username = isset($_POST['username']) ? htmlentities($_POST['username']) : '';
$prefix = isset($_POST['prefix']) ? htmlentities($_POST['prefix']) : '';

$TITLE = 'Erstkonfiguration | eVOC: Englisch Vokabeltrainer';

$CONTENT = <<< EOT
<h2>Erstkonfiguration</h2>
<form method="post" action="/createcfg">
	<table>
		<tr>
			<td>MySQL-Hostname</td>
			<td><input type="text" name="hostname" value="$hostname" size="64" /></td>
		</tr>
		<tr>
			<td>MySQL-Datenbank</td>
			<td><input type="text" name="database" value="$database" size="64" /></td>
		</tr>
		<tr>
			<td>MySQL-Benutzer</td>
			<td><input type="text" name="username" value="$username" size="64" /></td>
		</tr>
		<tr>
			<td>MySQL-Passwort</td>
			<td><input type="password" name="password" value="" size="64" /></td>
		</tr>
		<tr>
			<td>MySQL-Tabellenprefix</td>
			<td><input type="text" name="prefix" value="$prefix" size="64" /></td>
		</tr>
		<tr>
			<td><input type="submit" name="save" value="Speichern" /></td>
			<td></td>
		</tr>
	</table>
</form>
EOT;

include('lib/template.php');
exit();
