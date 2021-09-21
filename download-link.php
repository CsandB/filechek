<?php
// Подключается configuration file
require_once 'config.php';

// Get the file ID & key from the URL
$fid = (int)$_GET['fid'];
$keyhash = trim($_GET['hash']);

//$keyhash=
/*
 * Получение данных из файла или таблицы о файле
 *
 * Retrieve the keys from the tokens file
 * */
$keys = file(TOKEN_DIR.'/filehash');
$match = $existFirmFile = false;

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

    $arKey = unserialize(base64_decode($one));

    array_walk($ar_frm_file, function ($v, $k) use ($arKey) {
        if (
            $v['B_FILE_ID'] == $arKey['B_FILE_ID']
            && $v['MODULE_ID'] == $arKey['MODULE_ID']
            && $v['ENTITY_ID'] == $arKey['ENTITY_ID']
        ) {
            $existFirmFile = true;
        }
    });

    // If match found
    if ($match !== false && $existFirmFile !== false) {
        // If the file is found in the file's array
        if ( ! empty($ar_b_file[$fid])) {
            // Get the file data
            $contentType = $ar_b_file[$fid]['CONTENT_TYPE'];
            $fileName = $ar_b_file[$fid]['ORIGINAL_NAME'];
            $filePath = sprintf('%s%s',
                ! empty($ar_b_file[$fid]['SUBDIR']) ? $ar_b_file[$fid]['SUBDIR']
                    .'/' : '',
                $ar_b_file[$fid]['FILE_NAME']
            );          //to-do SET absolute name maybe instead $files[$fid]['file_path'];

            // Force the browser to download the file
            if ($ar_b_file[$fid]['type'] == 'remote_file') {
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
    }
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
