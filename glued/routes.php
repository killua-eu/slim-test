<?php

$app->get('/', function ($request, $response) {
   return 'A basic route returning just a string. Look <a href="lala">here</a> for twig templating.';
});

$app->get('/lala', function ($request, $response) {
   // to access the view item in the container ($this)
   return $this->view->render($response, 'full.twig');

});