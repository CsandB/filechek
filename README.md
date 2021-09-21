Модели данных

    b_file.json
        ...

    frm_file.json
    {
        "103": {
            "B_FILE_ID": "3",
            "MODULE_ID": "firm.otdelenie1",
            "ENTITY_ID": "sprawka"
        },
        "104": {
            "B_FILE_ID": "4",
            "MODULE_ID": "firm.otdelenie1",
            "ENTITY_ID": "konsultazia"
        },
        "105": {
            "B_FILE_ID": "5",
            "MODULE_ID": "firm.otdelenie2",
            "ENTITY_ID": "komandirowka"
        }
    }

Как проверять

    $ cd каталог_корня_сайта
    $ git clone https://github.com/CsandB/filechek.

    1. в браузере localhost/about/download-link.php
    2  запросом каким-нибудь wget, curl-O или например

    $data['cookies'] = $_COOKIE;
    file_get_contents('localhost/about?fid=3&hash=YTo0OntzOjk6IkJfRklMRV9JRCI7aTo0O3M6OToiTU9EVUxFX0lEIjtzOjE1OiJmaXJtLm90ZGVsZW5pZTIiO3M6OToiRU5USVRZX0lEIjtzOjExOiJrb25zdWx0YXppYSI7czoxNDoicGFzc3dvcmRnbG9iYWwiO3M6MTc6Im9uZXBhc3N3b3JkZm9yYWxsIjt9', false, stream_context_create(
        array(
            'http' => array(
                'method'  => 'GET',
                'content' => http_build_query($data),
                'timeout' => 10
            )
        )
    ));
