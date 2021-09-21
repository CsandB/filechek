<?php
// Array of the files with an unique ID

$ar_b_file = json_decode(
    file_get_contents('b_file.json')
    , true);

$ar_frm_file = json_decode(
    file_get_contents('frm_file.json')
    , true);

$ar_services = [
    'firm.otdelenie1',
    'firm.otdelenie2',
];

$ar_entities = [
    'sprawka',
    'konsultazia',
    'komandirowka',
];

// Base URL of the application
define('BASE_URL', 'http://'.$_SERVER['HTTP_HOST'].'/about/');

// Path of the download-link.php file
define('DOWNLOAD_PATH', BASE_URL.'download-link.php');

// Path of the token directory to store keys
define('TOKEN_DIR', 'tokens');

// Authentication password to generate download links
define('OAUTH_PASSWORD', 'onepasswordforall');

