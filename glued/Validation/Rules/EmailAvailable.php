<?php


// we're setting up our own class so that we can use the Respect\Validation
// more easily.

namespace Glued\Validation\Rules;

use Glued\Controllers\Controller as c;
use Respect\Validation\Rules\AbstractRule;


class EmailAvailable extends AbstractRule
{

    protected $container;
    public function __construct($container) 
    // using the shortcutted View and passing 
    {
        $this->container = $container;
    }




    public function validate($input)
    {
             //return false;
             $container = $app->getContainer();
             $this->container->db2->where('email',$input);
             $a = $this->container->db2->getOne("users");
             print_r($a); die();
    }


}