<?php
namespace Glued\Middleware;

// The "you-have-to-be-not-authenticated" middleware
class GuestMiddleware extends Middleware
{

    public function __invoke($request, $response, $next) 
    {

        if ($this->container->auth->check()) {

          $this->container->flash->addMessage('info', 'You are already signed in.');
          return $response->withRedirect($this->container->router->pathFor('home'));

        }


        $response = $next($request, $response);
        return $response;
    }

}