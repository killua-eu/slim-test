<?php


// we're setting up our own class so that we can use the Respect\Validation
// more easily.

namespace Glued\Validation\Rules;

use Glued\Controllers\Controller as c;
use Respect\Validation\Rules\AbstractRule;


class EmailAvailable extends AbstractRule
{

    public function validate($input)
    {
             return false;
             //$container = $app->getContainer();
             //$container->db2->where('email',$input);
             //$a = $container->db2->getOne("users");
             //print_r($a); die();
    }


}