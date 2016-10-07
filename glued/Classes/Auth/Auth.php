<?php

namespace Glued\Classes\Auth;

class Auth

{

    protected $container;
    public function __construct($container) 
    {
        $this->container = $container;
    }


    public function attempt($email,$password)
    {
             $this->container->db2->where('email',$email);
             $user = $this->container->db2->getOne("users");

             if (!$user) {
                 return false;
             }
             
             if (password_verify($password, $user['password'])) {
                 $_SESSION['user'] = $user->id;
                 return true;
             }

             return false;


    }
}