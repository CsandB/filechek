<?php /** @noinspection AutoloadingIssuesInspection */

use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;


/**
 * Class RunTest
 *
 * @package Local\RunTest
 *
 * @property-read integer $id    Идентификатор файла
 * @property-read string  $hash  Идентификатор объекта
 */
abstract class RunTest
{

    /**
     * Список необходимых модулей
     *
     * @var array $necessaryModules
     */
    protected static $necessaryModules = [];
    /**
     * ID текущего файла
     *
     * @var int $fileId
     */
    protected $fileId;
    /**
     * Текущий файл
     *
     * @var array $file
     */
    protected $file;
    /**
     * Действие над текущим файлом
     * get url file
     * get file
     *
     * @var string $action
     */
    protected $action;
    /**
     * Ошибки при выполнении
     *
     * @var array $errors
     */
    protected $errors = [];

    function __construct()
    {
        $this->fileId = $this->getFileId();
        $this->file = '';
    }

    /**
     * @return int
     */
    public function getFileId(): int
    {
        $request = Context::getCurrent()->getRequest();

        return $request->getPost('id');
    }

    /**
     * @return array
     */
    public function getFile()
    {
        $arFile['file'] = CFile::GetFileArray($this->fileId);
        $arFile['TO_PRINT'] = CHTTP::URN2URI($arFile["SRC"]);
        $arFile['TO_GET'] = '';

        return $arFile;
    }

    // Выводим ошибки
    // todo ::showError($err) ['ERRORS'] = $this->errors;

    /**
     * Обрабатываем действие
     */
    protected function prepareAction()
    {
        switch ($this->action) {
            case 'print':
                break;
            case 'get':
                $this->actionGet();
                break;
            default:
                break;
        }
    }

    /**
     * Действие Скачать
     */
    protected function actionGet()
    {
        $request = Context::getCurrent()->getRequest();
        $cookie = $request->getCookieRawList();

        /* todo
          [BITRIX_SM_GUEST_ID] => 9852
          [BITRIX_SM_TIME_ZONE] => -180
            [BITRIX_SM_UIDH] => f3cad70823fa3209eca8d33b64660f3
            [BITRIX_SM_UIDL] => admin
            [BITRIX_SM_SALE_UID] => 3
            [BITRIX_SM_LOGIN] => admin
            [BITRIX_SM_SOUND_LOGIN_PLAYED] => Y
            [PHPSESSID] => 81e7386234b8b7f9de8d5690edf46c
          */

        return true;
    }

    /**
     * Действие Ссылка для скачивания
     */
    protected function actionPrint()
    {
        $request = Context::getCurrent()->getRequest();
        if ($this->action === 'print') {

            $initiator = static::currentUserId();

            //работа с сущностями
            $status = static::getOne(['ID' => $initiator]);

            $this->getData($initiator, $status);

            // Генерируем файл
            $this->makeFile();
        }

        return true;
    }

    /**
     * Идентификатор текущего пользователя
     *
     * @return int
     */
    public static function currentUserId(): ?int
    {
        global $USER;
        if (is_object($USER) && $USER->IsAuthorized()) {
            return $USER->GetID();
        }

        return null;
    }

    public static function getOne($param, $additionalParams = [])
    {
        if ( ! $param) {
            return null;
        }

        static::includeNecessaryModules();

        if (isArrayAssoc($param)) {
            $params = [
                'filter' => $param,
            ];
            if (array_key_exists('select', $additionalParams)) {
                $params['select'] = $additionalParams['select'];
            }

            $res = static::getList($params, $additionalParams);
            $arRes = $res ? $res->Fetch() : null;

        }


        return null;
    }

    /**
     * Подключить необходимые для работы модули
     *
     * @return boolean
     */
    protected static function includeNecessaryModules(): bool
    {
        $result = true;
        foreach (static::$necessaryModules as $moduleName) {
            if ( ! static::includeModule($moduleName)) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Подключить модуль
     *
     * @param string $moduleName
     *
     * @return bool
     */
    public static function includeModule($moduleName)
    {
        try {
            if (Loader::includeModule($moduleName)) {
                return true;
            }
            $err = Loc::getMessage('MODULE_NOT_INSTALL',
                ['#MODULE_NAME#' => $moduleName]);

            //::showError($err);
            return false;
        } catch (LoaderException $e) {
            //::showError($e);
            return false;
        }
    }


    /**
     * Получить результат запроса списка согласно указанным параметрам
     *
     * @param array $params           Параметры запроса
     * @param array $additionalParams Дополнительные параметры
     *
     * @return array|bool|integer|CDBResult|CIBlockResult
     */
    abstract public static function getList(
        $params = [],
        $additionalParams = []
    );

    /**
     * Получаем данные о пользователе
     * и возможности скачать файл $this->fileId
     */
    protected function getData($initiator, $status)
    {

        return true;
    }


    protected function makeFile()
    {
        /** @var $GLOBALS ['APPLICATION'] \CAllMain */
        $GLOBALS['APPLICATION']->RestartBuffer();

        $file = '/path/to/file/outside/www/secret.pdf';

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: '.filesize($file));
        ob_clean();
        flush();
        readfile($file);
        exit;
    }


}
