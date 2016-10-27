<?php
namespace App\Controllers;

use App\traits\Template;

class Index extends \Phalcon\Mvc\Controller
{
    use Template;

    public function index()
    {

        $response = $this->response;
        $request  = $this->request;

        $assign = [
            'message'  => 'hello world',
        ];

        return $response->setContent(
            $this->show([
                'layout'   => './layout/normal.tpl',
                'contents' => './contents/main.tpl'
            ], $assign)
        );

    }

    public function info()
    {

        pr($_SERVER);
        phpinfo();

    }
}
