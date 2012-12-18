<?php

require_once('lib/db.php');
require_once('lib/session.php');
require_once('lib/path.php');
require_once('lib/users.php');

$SETTINGS = array(
	'path' => getPath(),
	'url' => getURL()
);

if(file_exists('settings.cfg')) {
	setError('Konfiguration bereits vorhanden!');
	header("location: {$SETTINGS['url']}");
	exit();
}

if(ini_get('magic_quote_gpc'))
	setError('Magic Quotes müssen deaktiviert sein!');

if(isset($_POST['save']) && isset($_POST['hostname']) && isset($_POST['database']) && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['prefix']) && isset($_POST['adminusername']) && isset($_POST['adminpassword1']) && isset($_POST['adminpassword2']) && isset($_POST['lastname'])) {
	global $MYSQL, $SETTINGS;
	$hostname = trim($_POST['hostname']);
	$database = trim($_POST['database']);
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);
	$prefix = trim($_POST['prefix']);
	$adminusername = trim($_POST['adminusername']);
	$adminpassword1 = $_POST['adminpassword1'];
	$adminpassword2 = $_POST['adminpassword2'];
	$lastname = $_POST['lastname'];
	$error = true;
	$connection = @mysql_connect($hostname, $username, $password);
	if((strlen($hostname) == 0) || (strlen($database) == 0) || (strlen($username) == 0) || (strlen($password) == 0) || (strlen($adminusername) == 0) || (strlen(trim($adminpassword1)) == 0) || (strlen($lastname) == 0))
		setError('Leere Felder sind nicht erlaubt!');
	else if(strlen($adminusername) < 3)
		setError('Dein Admin-Benuztername ist zu kurz!');
	else if(strlen($adminpassword1) < 5)
		setError('Dein Passwort ist zu kurz!');
	else if($adminpassword1 != $adminpassword2)
		setError('Du hast das Passwort nicht richtig wiederholt!');
	else if(strlen($lastname) < 2)
		setError('Dein Nachname ist ungültig!');
	else if(!$connection)
		setError('Fehler beim Herstellen der Datenbankverbindung! (' . mysql_error() . ')');
	else {
		$error = false;
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
	'allow_register' => true
);
EOT
			);

			// load config file
			$MYSQL = array(
				'hostname' => $hostname,
				'database' => $database,
				'username' => $username,
				'password' => $password,
				'prefix'   => $prefix
			);

			// create account
			try {
				if(userExists($adminusername)) {
					if(!isUserPasswordCorrect($adminusername, $adminpassword1)) {
						setError('Der Admin-Benutzer existiert bereits, hat aber ein anderes Passwort!');
						$error = true;
					}
				} else if(!addUser($adminusername, $adminpassword1, $lastname)) {
					setError('Fehler beim Anlegen des Admin-Accounts!');
					$error = true;
				}
				if(!$error) {
					$id = isUserPasswordCorrect($adminusername, $adminpassword1);
					if(!setUserGroup($id, 'admin')) {
						setError('Der Admin-Account konnte der Admin-Gruppe nicht zugewiesen werden!');
						$error = true;
					}
				}
			} catch(Exception $e) {
				setError('Fehler: ' . $e->getMessage());
				$error = true;
			}

			if(!$error) {
				setInfo('Konfiguration erstellt!');
				header("location: {$SETTINGS['url']}/");
				exit();
			} else {
				if(file_exists('settings.cfg'))
					unlink('settings.cfg');
			}
		}
	}
}

$hostname = isset($_POST['hostname']) ? htmlentities($_POST['hostname']) : '';
$database = isset($_POST['database']) ? htmlentities($_POST['database']) : '';
$username = isset($_POST['username']) ? htmlentities($_POST['username']) : '';
$prefix = isset($_POST['prefix']) ? htmlentities($_POST['prefix']) : '';

$adminusername = isset($_POST['adminusername']) ? htmlentities($_POST['adminusername']) : '';
$lastname = isset($_POST['lastname']) ? htmlentities($_POST['lastname']) : '';

$TITLE = 'Erstkonfiguration';

$CONTENT = <<< EOT
<h2>Erstkonfiguration</h2>
<form method="post" action="{$SETTINGS['path']}/createcfg">
	<h3>MySQL-Konfiguration</h3>
	<table>
		<tr>
			<td>Hostname:</td>
			<td><input type="text" name="hostname" value="$hostname" size="64" /></td>
		</tr>
		<tr>
			<td>Datenbank:</td>
			<td><input type="text" name="database" value="$database" size="64" /></td>
		</tr>
		<tr>
			<td>Benutzername:</td>
			<td><input type="text" name="username" value="$username" size="64" /></td>
		</tr>
		<tr>
			<td>Passwort:</td>
			<td><input type="password" name="password" value="" size="64" /></td>
		</tr>
		<tr>
			<td>Tabellenprefix:</td>
			<td><input type="text" name="prefix" value="$prefix" size="64" /></td>
		</tr>
	</table>
	<h3>Administrator-Account</h3>
	<table>
		<tr>
			<td>Benutzername:</td>
			<td><input type="text" name="adminusername" value="$adminusername" size="64" /></td>
		</tr>
		<tr>
			<td>Passwort:</td>
			<td><input type="password" name="adminpassword1" value="" size="64" /></td>
		</tr>
		<tr>
			<td>Passwort wiederholen:</td>
			<td><input type="password" name="adminpassword2" value="" size="64" /></td>
		</tr>
		<tr>
			<td>Nachname:</td>
			<td><input type="text" name="lastname" value="$lastname" size="64" /></td>
		</tr>
	</table>

	<p><input type="submit" name="save" value="Speichern" /></p>
</form>
EOT;

include('lib/template.php');
exit();
