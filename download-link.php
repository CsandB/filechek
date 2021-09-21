<?php
// Подключается configuration file
require_once 'config.php';

// Get the file ID & key from the URL
$fid = base64_decode(trim($_GET['fid']));
$key = trim($_GET['key']);

/*
 * Какие-то вычисления , возможно проверка вхождения в группу допускаемых
 * но в данном случае вычисляется, время истечения доступности файла из EXPIRATION_TIME
 * Calculate link expiration time
 * */
$currentTime = time();

$expTime = strtotime(EXPIRATION_TIME,
    explode('-', $key)[0]
);

/*
 * Получение данных из файла или таблицы о файле
 *
 * Retrieve the keys from the tokens file
 * */
$keys = file(TOKEN_DIR.'/keys');
$match = false;

/*
 * механика получения и обработки из файла
 *
 *
 * Loop through the keys to find a match
 * When the match is found, remove it
 * */
foreach ($keys as &$one) {
    if (rtrim($one) == $key) {
        $match = true;
        $one = '';
    }
}

/* Записываем обратно все ключи, как-то отфильтрованные, например
 * Put the remaining keys back into the tokens file
 * */
file_put_contents(TOKEN_DIR.'/keys', $keys);

// If match found and the link is not expired
if ($match !== false && $currentTime <= $expTime) {
    // If the file is found in the file's array
    if ( ! empty($files[$fid])) {
        // Get the file data
        $contentType = $files[$fid]['content_type'];
        $fileName = $files[$fid]['suggested_name'];
        $filePath = $files[$fid]['file_path'];

        // Force the browser to download the file
        if ($files[$fid]['type'] == 'remote_file') {
            $file = fopen($filePath, 'r');
            header("Content-Type:text/plain");
            header("Content-Disposition: attachment; filename=\"{$fileName}\"");
            fpassthru($file);
        } else {
            header("Content-Description: File Transfer");
            header("Content-type: {$contentType}");
            header("Content-Disposition: attachment; filename=\"{$fileName}\"");
            header("Content-Length: ".filesize($filePath));
            header('Pragma: public');
            header("Expires: 0");
            readfile($filePath);
        }
        exit;
    } else {
        $response = 'Download link is not valid.';
    }
} else {
    // If the file has been downloaded already or time expired
    $response = 'Download link is expired.';
}
?>

<html>
<head>
    <title><?php echo $response; ?></title>
</head>
<body>
<h1><?php echo $response; ?></h1>
</body>
</html>
