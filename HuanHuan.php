<?php

require_once 'Common.php';

class HuanHuan
{
    public static function main(): void
    {

        $response = new Response();
        unset($response->data);
        try {
            // ----- request -----
            $request = new Request();

            $request->api = explode('?', $_SERVER["REQUEST_URI"]);

            $api = explode('/', $request->api[0]);
            if (count($api) > 0 && $api[0] === '') {
                $api = array_pop($api);
            }
            $request->api = $api;
            $request->data = file_get_contents('php://input');
            $request->httpHeaders = $_SERVER;

            require_once $api . '.php';
            if (!class_exists($api)) {
                $response->httpStatus = HttpStatus::NOT_FOUND;
                $response->httpStatusMsg = "API Not Found";
            } else {
                $api = new  $api;
                $api->process($request, $response);
            }
        } catch (\Exception $e) {
            $response->httpStatus = HttpStatus::FAILED;
            $response->httpStatusMsg = $e->getMessage();

        }


        if ($response->httpStatus !== HttpStatus::SUC) {
            header("HTTP/1.1 " . $response->httpStatus . " " . $response->httpStatusMsg);
            return;
        }

        foreach ($response->httpHeaders as $header => $value) {
            header($header . ': ' . $value);
        }

        if (isset($response->data)) {
            file_put_contents('php://output', $response->data);
        }

    }
}

HuanHuan::main();



