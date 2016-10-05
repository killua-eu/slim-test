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
             //db::where('email',$input);
             //$a = db::getOne("users");
             //print_r($a); die();
    }


}