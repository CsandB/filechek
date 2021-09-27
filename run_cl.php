<?php /** @noinspection AutoloadingIssuesInspection */
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC', 'Y');
define('NO_AGENT_CHECK', true);
define('DisableEventsCheck', true);
define('NOT_CHECK_PERMISSIONS', true);
//include_once "_prolog_before_.php";
if (false !== strpos($_SERVER['HTTP_USER_AGENT'], "Mozilla")) {
    require_once($_SERVER['DOCUMENT_ROOT']
        ."/bitrix/modules/main/include/prolog_before.php");
} else {
    require_once "_prolog_before_.php";
}


use Bitrix\Main\HttpRequest;
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
class Run
{

    /**
     * Список необходимых модулей
     *
     * @var array $necessaryModules
     */
    static $necessaryModules = ['main', 'crm'];
    /**
     * ID текущего файла
     *
     * @var int $fileId
     */
    public $fileId;
    /**
     * Текущий файл
     *
     * @var array $file
     */
    public $file;
    /**
     * Действие над текущим файлом
     * get url file
     * get file
     *
     * @var string $action
     */
    public $action;
    public $hash;
    /**
     * Ошибки при выполнении
     *
     * @var array $errors
     */
    public $errors = [];
    /**
     * @var HttpRequest $request
     */
    protected $request = array();

    private $debug = true;

    function __construct()
    {
        $this->request = Context::getCurrent()->getRequest();
        //$this->action='';

        static::includeNecessaryModules();
        $this->prepareRequestData();

        $this->prepareAction();

    }

    /**
     * Подключить необходимые для работы модули
     *
     * @return boolean
     */
    static function includeNecessaryModules(): bool
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

    protected function prepareRequestData()
    {
        $this->fileId = $this->getFileId();
        $this->file = $this->getFile();
        $this->action = $this->request->get('action') !== null
            ?
            $this->request->get('action')
            :
            'print';
    }

    // Выводим ошибки
    // todo ::showError($err) ['ERRORS'] = $this->errors;

    /**
     * @return int
     */
    public function getFileId(): int
    {
        if ($this->debug) {
            return 104;
        }

        return $this->request->get('id');

    }

    /**
     * @return array
     */
    public function getFile()
    {
        $arFile['file'] = CFile::GetFileArray($this->fileId);
        $arFile['TO_PRINT'] = CHTTP::URN2URI($arFile['file']["SRC"]);
        $arFile['SRC'] = $arFile['file']["SRC"];
        $arFile['PATH'] = $_SERVER['DOCUMENT_ROOT'].$arFile['file']["SRC"];

        return $arFile;
    }

    /**
     * Обрабатываем действие
     */
    function prepareAction()
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
    function actionGet()
    {
        static::getOne();
        CFile::ViewByUser(
            $this->file['file']['file'],
            [
                "force_download" => true,
                "cache_time"     => 86400,
            ]);

        exit;
    }

    public static function getOne($param = [], $additionalParams = [])
    {
        if (empty($param)) {

            $currentUserId = \CCrmSecurityHelper::GetCurrentUserID()
                ?
                \CCrmSecurityHelper::GetCurrentUserID()
                :
                static::currentUserId();

            $userPermissions
                = new \CCrmPerms($currentUserId);

            return $userPermissions;
        }


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
     * Идентификатор текущего пользователя
     *
     * @return int
     * @todo
     */
    public static function currentUserId(): ?int
    {
        global $USER;
        if (isset($USER)
            && ((get_class($USER) === 'CUser')
                || ($USER instanceof CUser))
        ) {
            if (is_object($USER) && $USER->IsAuthorized()) {
                return $USER->GetID();
            }
        }

        return null;
    }

    /**
     * Действие Ссылка для скачивания
     */
    function actionPrint()
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
     * Получить результат запроса списка согласно указанным параметрам
     *
     * @param array $params           Параметры запроса
     * @param array $additionalParams Дополнительные параметры
     *
     * @return array|bool|integer|CDBResult|CIBlockResult
     */
    /*public function getList(
        $params = [],
        $additionalParams = []
    )*/

    /**
     * Получаем данные о пользователе
     * и возможности скачать файл $this->fileId
     */
    function getData($initiator, $status)
    {

        return true;
    }

    function makeFile()
    {
        return true;
    }


}


$retc = new Run();

$ret = $retc->makeFile();
