<?php
namespace Glued\Middleware;

class ValidationErrorsMiddleware extends Middleware
{

    public function __invoke($request, $response, $next) 
    {

        // getting the errors from session
        $this->container->view->getEnvironment()->addGlobal('validationerrors',$_SESSION['validationerrors']);
        unset($_SESSION['validationerrors']);
        $response = $next($request, $response);
        return $response;
    }

}