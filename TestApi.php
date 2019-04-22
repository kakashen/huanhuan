<?php

require_once 'Common.php';
require_once 'Medoo.php';

class TestApiRequestData
{
    public $id;
}

class TestApiResponseData extends ResponseData
{
    public $data = [];
}

class TestApi extends API
{
    protected function run(Request $request, Response $response)
    {
        $rqData = new TestApiRequestData();
        $response->data = new TestApiResponseData();

        if (!isset($rqData->id)) {
            $response->data->code = 415;
            $response->data->data = 'search_box_name not set or not string!';
            return;
        }

        $model = new Model();
        $data = $model->select('os_commodityclass', '*');
        $response->data->data = $data;

    }

}
