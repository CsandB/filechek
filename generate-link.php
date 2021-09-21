<?php
// Подключается configuration file
require_once 'config.php';

// Grab the password from the query string
$oauthPass = trim($_SERVER['QUERY_STRING']);

// Верификация oauth password
if ($oauthPass != OAUTH_PASSWORD) {
    echo "false";
    // Return 404 error, if not a correct path
    header("HTTP/1.0 404 Not Found");
    exit;
} else {
    // Создаются ссылки для скачивания, для их вывода на экран
    $download_links = [];

    // Если файлы , информация о файлах есть
    if (is_array($files)) {
        foreach ($files as $fid => $file) {
            /*
             * Encode the file ID
             * Шифруется, генерируется по какому-то
             * критерию дополнительная соль
             * Например:
             * B_FILE_ID,
             * MODULE_ID,
             * ENTITY_ID
             * USER_ID - кем открыта страница,кто автор ссылки
             * TIME_UNIX - дата и время открытия страницы и создания ссылки
             *
             * */
            $fid = base64_encode($fid);

            /*
             * Generate new unique key
             * Дополнительная обфускация
             *
             * Генерация какой-то /короткой/ ссылки, в
             * строке которой содержится инфо о правах
             *
             * */
            $key = uniqid(time().'-key', true);

            // Генерация ссылки для скачивания
            $download_link = DOWNLOAD_PATH."?fid=$fid&key=".$key;

            // Добавление ссылки
            $download_links[] = array(
                'link' => $download_link,
            );

            /*
             * Create a protected directory to store keys
             *
             * */
            if ( ! is_dir(TOKEN_DIR)) {
                mkdir(TOKEN_DIR);
                $file = fopen(TOKEN_DIR.'/.htaccess', 'w');
                fwrite($file, "Order allow,deny\nDeny from all");
                fclose($file);
            }

            /*
             * Запись в файл или в таблицу
             *         (внимание)
             * уникальной ссылки на один и тот-же файл, то есть
             * fid - постоянный, генерируется из названия файла,
             *      кроме названия файла может добавлятся
             *      B_FILE_ID, MODULE_ID, ENTITY_ID
             *   => RklEMTIzNDU=
             *
             * key
             *     - переменная строка, может не записываться
             *          в файл или в таблицу, но может содержать
             *          какой-то набор информации для анализа
             *          источника утечки, например время и какой-то USER_ID
             *
             * Write the key to the keys list
             * */
            $file = fopen(TOKEN_DIR.'/keys', 'a');
            fwrite($file, "{$key}\n");
            fclose($file);
        }
    }
}
?>

<!-- List all the download links
http://localhost/about/download-link.php?fid=RklEMTIzNDU=&key=1632210465-key61498e21ec14c3.76131739

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
<?php } ?>
