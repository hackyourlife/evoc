<?php

if(!file_exists('settings.cfg')) {
	header('location: /createcfg');
	exit();
}

include('settings.cfg');
