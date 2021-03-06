<?php

use Respect\Validation\Validator as v;

session_start();

if (!file_exists( __DIR__ . '/config.php')) { die("Error 500: configuration missing."); }
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

$container['auth'] = function ($container) {
    return new \Glued\Classes\Auth\Auth($container);
};


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

    // this is here so that we can use (i.e. see views/templates/partials/navigation.twig)
    // {{ auth.check }}, as set in classes/Auth/Auth.php, inside our templates.
    // NOTE: $container['auth'] closure must be before this view closure.
    // NOTE: we cant use $view->getEnvironment()->addGlobal('auth', $container->auth); 
    //       as this would do a sql query everytime we access the global
    // TODO: possibly change this into middleware later?
    $view->getEnvironment()->addGlobal('auth', [
        'check' => $container->auth->check(),
        'user' => $container->auth->user(),
    ]);

    $view->getEnvironment()->addGlobal('flash', $container->flash);

    return $view;
};


// database
$container['db'] = function ($container) {
    $db = $container['settings']['db'];
    $mysqli = new mysqli($db['host'], $db['username'], $db['password'], $db['database']);
    $mysqli->set_charset($db['charset']);
    $mysqli->query("SET collation_connection = ".$db['collation']);
    return $mysqli;
};

$container['db2'] = function ($container) {
    $mysqli = $container->get('db');
    $db2 = new \MysqliDb ($mysqli);
    return $db2;
};


$container['flash'] = function ($container) {
    return new \Slim\Flash\Messages();
};



// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};


$container['validator'] = function ($container) {
   return new Glued\Validation\Validator;
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

// our most sophisticated container
$container['HomeController'] = function ($container) {
    return new \Glued\Controllers\HomeController($container);
    // passing $container, not $container->view
};

// our most sophisticated container
$container['AuthController'] = function ($container) {
    return new \Glued\Controllers\Auth\AuthController($container);
    // passing $container, not $container->view
};


$container['csrf'] = function ($container) {
    return new \Slim\Csrf\Guard;
};




$app->add(new \Glued\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \Glued\Middleware\OldInputMiddleware($container));
$app->add(new \Glued\Middleware\CsrfViewMiddleware($container));


$app->add($container->csrf);

# path to validation rules (double slash = escaping)
v::with('Glued\\Validation\\Rules\\');


/*
 * INCLUDE ROUTES
 */

require __DIR__ . '/routes.php';

