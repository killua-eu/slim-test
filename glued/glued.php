<?php


session_start();
require __DIR__ . '/../vendor/autoload.php';

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

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
    ]
]);


/*
 * DEPENDENCY INJECTION
 */

// First we need to fetch the DI container
$container = $app->getContainer();

// and bind dependencies to the container.
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


/*
 * INCLUDE ROUTES
 */

require __DIR__ . '/routes.php';

