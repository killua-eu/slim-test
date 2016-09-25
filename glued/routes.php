<?php

$app->get('/', function ($request, $response) {
   return 'A basic route returning just a string. Look <a href="lala">here</a> for twig templating.';
});

$app->get('/db', function ($request, $response) {

   $mysqli = new mysqli($config['settings']['db']['host'],
                        $config['settings']['db']['username'],
                        $config['settings']['db']['password'],
			$config['settings']['db']['database']);
   $mysqli->set_charset($config['settings']['db']['charset']);
   $mysqli->query("SET collation_connection = ".$config['settings']['db']['collation']);

   //$x='["JavaScript"]';
   //$x=$mysqli->real_escape_string($x);
   //$result = $mysqli->query("SELECT * FROM `book` WHERE JSON_CONTAINS(tags,'$x')");

   $result = $mysqli->query(sprintf("SELECT * FROM `book` WHERE JSON_CONTAINS(tags,'%s')",
                      $mysqli->real_escape_string('["JavaScript"]')));


   printf("%d rows matching.\n", $mysqli->affected_rows);
   while ($myrow = $result->fetch_assoc()) {
        print_r($myrow);
    }

   $result->close();
   return 'db';
});

$app->get('/lala', function ($request, $response) {
   // to access the view item in the container ($this)
   return $this->view->render($response, 'full.twig');

});