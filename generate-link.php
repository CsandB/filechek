<?php

$ar_b_file = $ar_frm_file = $ar_services = $ar_entities = [];

// Подключается configuration file, читаются данные $ar_b_file
require_once 'config.php';

// какой-то сервис и сущность
$currentService = $ar_services[rand(0, count($ar_services) - 1)];
$currentEntity = $ar_entities[rand(0, count($ar_entities) - 1)];

// Создаются ссылки для скачивания, для их вывода на экран
$download_links = [];

// Чтобы потом проверить, есть этот файл в frm.file таблице, берем ИД имеющихся
$arexistFirmFileIDs = array_column($ar_frm_file, 'B_FILE_ID');


/*
Если файлы которые записываются в b_file
  , информация о файлах есть, пишем в frm_file
*/
if (is_array($ar_b_file)) {
    foreach ($ar_b_file as $fid => $file) {

        /*
         * Генерация какой-то /короткой/ ссылки, в
         * строке которой содержится инфо о правах
         *
         * */
        $arKey = [
            'B_FILE_ID'      => $fid,
            'MODULE_ID'      => $currentService,
            'ENTITY_ID'      => $currentEntity,
            'passwordglobal' => OAUTH_PASSWORD,
        ];
        $keyhash = base64_encode(serialize($arKey));


        // Генерация ссылки для скачивания
        $download_link = DOWNLOAD_PATH."?fid=$fid&hash=".$keyhash;

        // Добавление ссылки
        $download_links[] = array(
            'link' => $download_link,
        );


        /*
         * Create a protected directory to store keys
         *
         * */
        if ( ! is_dir(TOKEN_DIR)) {
            if ( ! mkdir($concurrentDirectory = TOKEN_DIR)
                && ! is_dir($concurrentDirectory)
            ) {
                throw new RuntimeException(sprintf('Directory "%s" was not created',
                    $concurrentDirectory));
            }
            $file = fopen(TOKEN_DIR.'/.htaccess', 'w');
            fwrite($file, "Order allow,deny\nDeny from all");
            fclose($file);
        }
        $keys = file(TOKEN_DIR.'/filehash');

        if ( ! in_array($keyhash, $keys)) {
            /*
             * Запись в файл или в таблицу
             *         (внимание)
             * Write the filehash to the keys list
             * */
            $file = fopen(TOKEN_DIR.'/filehash', 'a');
            fwrite($file, "{$keyhash}\n");
            fclose($file);

            //если запись о файле в таблице есть, пропускаем
            if ( ! in_array($fid, $arexistFirmFileIDs)) {
                // запись, добавление
            }

        }


    }
}

?>

<!-- List all the download links

    http://localhost/about/download-link.php?fid=3&hash=YTo0OntzOjk6IkJfRklMRV9JRCI7aTozO3M6OToiTU9EVUxFX0lEIjtzOjE1OiJmaXJtLm90ZGVsZW5pZTIiO3M6OToiRU5USVRZX0lEIjtzOjEyOiJrb21hbmRpcm93a2EiO3M6MTQ6InBhc3N3b3JkZ2xvYmFsIjtzOjE3OiJvbmVwYXNzd29yZGZvcmFsbCI7fQ==
    http://localhost/about/download-link.php?fid=4&hash=YTo0OntzOjk6IkJfRklMRV9JRCI7aTo0O3M6OToiTU9EVUxFX0lEIjtzOjE1OiJmaXJtLm90ZGVsZW5pZTIiO3M6OToiRU5USVRZX0lEIjtzOjEyOiJrb21hbmRpcm93a2EiO3M6MTQ6InBhc3N3b3JkZ2xvYmFsIjtzOjE3OiJvbmVwYXNzd29yZGZvcmFsbCI7fQ==
    http://localhost/about/download-link.php?fid=5&hash=YTo0OntzOjk6IkJfRklMRV9JRCI7aTo1O3M6OToiTU9EVUxFX0lEIjtzOjE1OiJmaXJtLm90ZGVsZW5pZTIiO3M6OToiRU5USVRZX0lEIjtzOjEyOiJrb21hbmRpcm93a2EiO3M6MTQ6InBhc3N3b3JkZ2xvYmFsIjtzOjE3OiJvbmVwYXNzd29yZGZvcmFsbCI7fQ==


about/tokens/keys
    1632210465-key61498e21ec14c3.76131739
-->
<?php if ( ! empty($download_links)) { ?>
    <ul>
        <?php foreach ($download_links as $download) { ?>
            <li>
                <a href="<?php echo $download['link']; ?>"><?php echo $download['link']; ?></a>
            </li>
        <?php } ?>
    </ul>
<?php } else { ?>
    <p>Links are not found...</p>
<?php }

$data['cookies'] = $_COOKIE;
file_get_contents('localhost/about?fid=3&hash=YTo0OntzOjk6IkJfRklMRV9JRCI7aTo0O3M6OToiTU9EVUxFX0lEIjtzOjE1OiJmaXJtLm90ZGVsZW5pZTIiO3M6OToiRU5USVRZX0lEIjtzOjExOiJrb25zdWx0YXppYSI7czoxNDoicGFzc3dvcmRnbG9iYWwiO3M6MTc6Im9uZXBhc3N3b3JkZm9yYWxsIjt9',
    false, stream_context_create(
        array(
            'http' => array(
                'method'  => 'GET',
                'content' => http_build_query($data),
                'timeout' => 10,
            ),
        )
    ));

?>
