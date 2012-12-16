<?php

if(!isLoggedIn()) {
	if(isset($_POST['login'])) {
		$result = doLogin();
		if($result !== true) {
			include('lib/navbar.php');
			setError($result);
			include('lib/loginpage.php');
			include('lib/template.php');
			exit();
		}
	} else {
		include('lib/navbar.php');
		include('lib/loginpage.php');
		include('lib/template.php');
		exit();
	}
}
