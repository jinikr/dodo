<?php
namespace App\Middlewares;

class Auth//implements MiddlewareInterface

{
    /**
     * @param $app
     */
    public static function call($app)
    {
        try
        {
            //throw new \Exception('error auth');
            return true;
        } catch (\Exception $e) {
            $app->response
                ->setStatusCode(403, 'Forbidden')
                ->setContentType('application/json')
                ->setJsonContent(array(
                    'error'   => true,
                    'status'  => 403,
                    'message' => $e->getMessage()
                ))->send();

            return false;
        }
    }
}
