<?php

require 'Medoo.php';

use Medoo\Medoo;

class Response
{
    public function __construct()
    {
        $this->httpStatus = HttpStatus::SUC;
    }

    public $data;
    public $httpStatus;
    public $httpStatusMsg;
    public $httpHeaders = [];
}

class HttpStatus
{
    const SUC = 200;
    const ARGS_FORMAT_ERROR = 400;
    const NOT_FOUND = 404;
    const ARGS_ERROR = 415;
    const FAILED = 500;
}

class Request
{
    public $data;
    public $api;
    public $httpHeaders = [];
}

class ResponseData
{
    public $code;

    public function __construct()
    {
        $this->code = 200;
    }
}

abstract class API
{
    public function process(Request $request, Response $response)
    {
        if ($this->beforeRun($request, $response)) {
            $this->run($request, $response);
            $this->afterRun($response);
        }
    }

    protected function beforeRun(Request $request, Response $response): bool
    {
        if (empty($request->data)) {
            $request->data = new \stdClass();
        } else {
            $request->data = json_decode($request->data);
        }

        if ($request->data === false) {
            $response->httpStatus = HttpStatus::ARGS_FORMAT_ERROR;
            $response->httpStatusMsg = 'PHP json_decode(request->data) error';
            return false;
        }
        return true;
    }

    abstract protected function run(Request $request, Response $response);

    protected function afterRun(Response $response)
    {
        if (!isset($response->data) || $response->data === null) {
            return;
        }

        $response->data = json_encode($response->data);

        if ($response->data === false) {
            unset($response->data);
            $response->httpStatus = HttpStatus::FAILED;
            $response->httpStatusMsg = 'PHP json_encode(response->data) error';
        }
    }
}

class Model extends Medoo
{
    public const database_type = 'mysql';
    public const database_name = 'local_huanxi';
    public const server = 'localhost';
    public const username = 'root';
    public const password = '123456';
    public const charset = 'utf8';
    public const collation = 'utf8_general_ci';
    public const port = 3306;

    public const option = [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    public function __construct()
    {
        parent::__construct([
            'database_type' => self::database_type,
            'database_name' => self::database_name,
            'server' => self::server,
            'username' => self::username,
            'password' => self::password,
            'charset' => self::charset,
            'collation' => self::collation,
            'port' => self::port,
            'option' => self::option
        ]);
    }


}
