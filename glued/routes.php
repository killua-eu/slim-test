<?php

/*
 * The home route [/]
*/

$app->get('/', function ($request, $response) {
   // Sample log message
   $this->logger->info("Slim-Skeleton '/' route");
   // Html page
   return 'A basic route returning a string and writing a log entry about it. Look at<br />
   - <a href="lala">here</a> for twig templating<br />
   - <a href="db1">here</a> for testing json queries to mysql<br />
   - <a href="db2">here</a> the same db test as above, just rewritten so that it uses DI, PS4 autoloading and separates the Model from View<br />
   - <a href="plain?name=HillyBilly">here</a> controller as DI<br />
   - <a href="unsplit">here</a> an unsplit controller as DI, with dependencies inside its methods<br />
   - <a href="home">here</a> a propper home controller. DI loaded, extending a common Controller class<br />

';
});

/*
 * The twig route [/lala]
*/

$app->get('/lala', function ($request, $response) {
   // to access the view item in the container ($this)
   return $this->view->render($response, 'full.twig');

});

/*
 * The DI using, PS4 autoloading and Model from View separating access to the database [/db2]
*/

$app->get('/db2', function ($request, $response) {
   $mapper = new Glued\Models\BookMapper($this->db);
   $books = $mapper->getBooks();
   print_r($books);
   return 'db2, should show the same as <a href="db1">db1</a>, if it doesn\'t, your connection configuration in either one is wrong.<br />';
});

/*
 * The almoast-functional way of rewriting db2 with explanations [/db1]
*/

$app->get('/db1', function ($request, $response) {

   // this is in the config.php
   $db['host']="127.0.0.1";
   $db['username']="god";
   $db['password']="*******";
   $db['database']="slim";
   $db['charset']="utf8";
   $db['collation']="utf8_general_ci";

   // this part is in the dependency injection container in glued.php
   // the $mysqli connection object is then passed to the Models/BookMapper.php file
   // or more precisely, to Models/Mapper.php which uses this object in its construct.
   //
   // Models/Mapper.php isn't explicitely mentioned in db2, because Models/BookMapper.php
   // extends the Mapper class. The reason why to do is that we dont want to write the
   // constructor over and over again for each WhaeteverMapper.php.
   $mysqli = new mysqli($db['host'],$db['username'],$db['password'],$db['database']);
   $mysqli->set_charset($db['charset']);
   $mysqli->query("SET collation_connection = ".$db['collation']);
   // This is the code in Models/BookMapper.php actual. Instead of rendering html right
   // away, an array is renturned, so that the result can be used in different views.
   $mysqli->query("SET collation_connection = ".$config['settings']['db']['collation']);
   $result = $mysqli->query(sprintf("SELECT * FROM `book` WHERE JSON_CONTAINS(tags,'%s')",
                      $mysqli->real_escape_string('["JavaScript"]')));

   printf("%d rows matching.\n", $mysqli->affected_rows);
   while ($myrow = $result->fetch_assoc()) {
         $results[]=$myrow;
     };

   print_r($results);
   $result->close();


return 'db1, should show the same as <a href="db2">db2</a>, if it doesn\'t, your connection configuration in either one is wrong.<br />';
});

/*
 * Showing off controller:method call (see also bootstrap.php to see the DI of the PlainController,
 * UnsplitController and HomeController)
*/
$app->get('/plain', 'PlainController:index');
$app->get('/unsplit', 'UnsplitController:index');
$app->get('/home', 'HomeController:index')->setName('home'); 

$app->get('/auth/signup', 'AuthController:getSignUp')->setName('auth.signup');
$app->post('/auth/signup', 'AuthController:postSignUp'); // we only need to set the name once for an uri, hence here not a setName again

$app->get('/auth/signin', 'AuthController:getSignIn')->setName('auth.signin');
$app->post('/auth/signin', 'AuthController:postSignIn'); // we only need to set the name once for an uri, hence here not a setName again

