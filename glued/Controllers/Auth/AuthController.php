<?php
namespace Glued\Controllers\Auth;

use Glued\Controllers\Controller; // needed because Auth is in a directory below
use Glued\Models\Mapper;
use Respect\Validation\Validator as v;


class AuthController extends Controller
{



    public function getSignOut($request, $response)
    {
        $this->container->auth->signout();
        return $response->withRedirect($this->container->router->pathFor('home'));

    }




    public function getSignIn($request, $response)
    {
        return $this->container->view->render($response, 'auth/signin.twig');
    }

    public function postSignIn($request, $response) 
    {
        // emailAvailable() needs $this->container as a parameter to get to use DI database connection
        $validation = $this->container->validator->validate($request, [
             'email' => v::noWhitespace()->notEmpty()->email(),
             'password' => v::noWhitespace()->notEmpty(),
        ]);

        if ($validation->failed()) {
           // on validation failure redirect back, 
           // the rest of the function won't happen
          return $response->withRedirect($this->container->router->pathFor('auth.signin'));
        }

        $auth = $this->container->auth->attempt(
            $request->getParam('email'),
            $request->getParam('password')
        );

        if(!$auth) {
             return $response->withRedirect($this->container->router->pathFor('auth.signin'));
        }

         return $response->withRedirect($this->container->router->pathFor('home'));

        //var_dump($request->getParams());
        /*
        $data = Array ("email"     => $request->getParam('email'),
                       "name"      => $request->getParam('name'),
                       "password"  => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
                      );
        //print_r($data);
        $user = $this->container->db2->insert ('users', $data);
         */

/*
        if ($user)
              $this->container->logger->info("Auth: user ".$data['email']." created");
        else
              $this->container->logger->warn("Auth: user creation ".$data['email']." failed");
        return $response->withRedirect($this->container->router->pathFor('home'));
*/
    }



    public function getSignUp($request, $response)
    {
        return $this->container->view->render($response, 'auth/signup.twig');
    }

    public function postSignUp($request, $response) 
    {
        // emailAvailable() needs $this->container as a parameter to get to use DI database connection
        $validation = $this->container->validator->validate($request, [
             'email' => v::noWhitespace()->notEmpty()->email()->emailAvailable($this->container),
             'name'  => v::noWhitespace()->notEmpty()->alpha(),
             'password' => v::noWhitespace()->notEmpty(),
        ]);

        if ($validation->failed()) {
           // on validation failure redirect back, 
           // the rest of the function won't happen
          return $response->withRedirect($this->container->router->pathFor('auth.signup'));
        }

        //var_dump($request->getParams());
        $data = Array ("email"     => $request->getParam('email'),
                       "name"      => $request->getParam('name'),
                       "password"  => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
                      );
        //print_r($data);
        $user = $this->container->db2->insert ('users', $data);
        if ($user)
              $this->container->logger->info("Auth: user ".$data['email']." created");
        else
              $this->container->logger->warn("Auth: user creation ".$data['email']." failed");

        $this->container->auth->attempt($data['email'], $request->getParam('password')); // signin on signup
        return $response->withRedirect($this->container->router->pathFor('home'));

    }



}