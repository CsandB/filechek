<?php
// Array of the files with an unique ID
$files = array(
    'FID12345' => [
        'content_type'   => 'application/zip',
        'suggested_name' => 'tutorials-file.zip',
        'file_path'      => 'tempfile.zip',
        'type'           => 'local_file',
    ],
    'UID09876' => [
        'content_type'   => 'audio/mpeg',
        'suggested_name' => 'tune-tutorials.mp3',
        'file_path'      => 'https://www.dropbox.com/XXXXXXX/video.mp3?dl=1',
        'type'           => 'remote_file',
    ],

);

// Base URL of the application
define('BASE_URL', 'http://'.$_SERVER['HTTP_HOST'].'/about/');

// Path of the download-link.php file
define('DOWNLOAD_PATH', BASE_URL.'download-link.php');

// Path of the token directory to store keys
define('TOKEN_DIR', 'tokens');

// Authentication password to generate download links
define('OAUTH_PASSWORD', 'Tutorialswebsite');

// Expiration time of the link (examples: +1 year, +1 month, +5 days, +10 hours)
define('EXPIRATION_TIME', '+5 minutes');
