<?php

abstract class Api
{
    public $apiName = ''; //movie

    protected $method = ''; //GET|POST|PUT|DELETE

    public $requestUri = [];
    public $requestParams = [];

    protected $action = ''; //Название метод для выполнения


    public function __construct() {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

        //Массив GET параметров разделенных слешем
        $this->requestUri = explode('/', trim($_SERVER['REQUEST_URI'],'/'));
        $this->requestParams = $_REQUEST;

        //Определение метода запроса
        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                throw new Exception("Unexpected Header");
            }
        }
    }

    // public function jsonSerialize() {
    //     return $this->array;
    // }

    public function run() {
        //Первые 2 элемента массива URI должны быть "api" и название таблицы
        if(array_shift($this->requestUri) !== 'api' || array_shift($this->requestUri) !== $this->apiName){
            throw new RuntimeException('API Not Found', 404);
        }
        //Определение действия для обработки
        $this->action = $this->getAction();

        //Если метод(действие) определен в дочернем классе API
        if (method_exists($this, $this->action)) {
            return $this->{$this->action}();
        } else {
            throw new RuntimeException('Invalid Method', 405);
        }
    }

    protected function response($data, $status = 500) {
        header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));
        $dataJson = json_encode(new ArrayValue($data));
        return $dataJson;
    }

    private function requestStatus($code) {
        $status = array(
            200 => 'OK',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        );
        return ($status[$code])?$status[$code]:$status[500];
    }

    protected function getAction()
    {
        $method = $this->method;
        switch ($method) {
            case 'GET':
                if($this->requestUri){
                    return 'view';
                } else {
                    return 'all';
                }
                break;
            case 'POST':
                if($this->requestUri){
                    return 'update';
                }
                else {
                    return 'add';
                }
                break;
            // case 'PUT':
            //     return 'update';
            //     break;
            case 'DELETE':
                return 'delete';
                break;
            default:
                return null;
        }
    }

    abstract protected function all();
    abstract protected function view();
    abstract protected function add();
    abstract protected function update();
    abstract protected function delete();
}

class ArrayValue implements JsonSerializable {
    public function __construct(array $array) {
        $this->array = $array;
    }

    public function jsonSerialize() {
        return $this->array;
    }
}
