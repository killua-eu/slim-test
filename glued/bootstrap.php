<?php


session_start();
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/config.php';

/* 
NOTE: psr-4 autoloading is turend on in composer.json. The psr-4 entry
"Glued\\": "glued" correspond to the application name "Glued\" (the additional
backslash is for escaping) and the relative path to the "glued" directory.
psr-4 will autload things according to the following key: Glued=glued, 
Models=glued/Models, User=glued/Models/User.php, hence the following will work:

$user = new \Glued\Models\User;
print_r($user);
*/




/*
 * INSTANTIATE THE APP
 */

$app = new \Slim\App($config);


/*
 * DEPENDENCY INJECTION
 */


// We need to fetch the DI container and bind all the dependencies to it (view, db, etc.)
$container = $app->getContainer();


// twig templating (views)
$container['view'] = function ($container) {
    // Define the view and set the path to the Views directory,
    // turn of caching for development
    $view = new \Slim\Views\Twig(__DIR__ . '/Views', [
        'cache' => false,
    ]);

    // Allow to generate different urls to our views
    $view->addExtension(new \Slim\Views\TwigExtension(
        // passing our router here as we'll be
        // generating urls for links in twig views
        $container->router,
	$container->request->getUri()
    ));

    return $view;
};


// database
$container['db'] = function ($container) {
    $db = $container['settings']['db'];
    $mysqli = new mysqli($db['host'], $db['username'], $db['password'], $db['database']);
    $mysqli->set_charset($config['settings']['db']['charset']);
    $mysqli->query("SET collation_connection = ".$config['settings']['db']['collation']);
     return $mysqli;
};


// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};


// our own controller: the simplest thing
$container['PlainController'] = function ($container) {
    return new \Glued\Controllers\PlainController;
};


// our own more sophisticated controller
$container['UnsplitController'] = function ($container) {
    return new \Glued\Controllers\UnsplitController($container->view);
    // passing $container to HomeController is needed if
    // we want to use dependencies (i.e. TWIG) inside the
    // HomeController. If we do this, we naturally MUST
    // have a constructor, that will take the view in.

};




/*
 * INCLUDE ROUTES
 */

require __DIR__ . '/routes.php';

