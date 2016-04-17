<?php

require_once dirname(__FILE__) . '/vendor/autoload.php';
require_once 'config.php';

use Slim\Slim;
use RedBeanPHP\R;

$app = new Slim();
$app->response->headers->set('Content-Type', 'application/json');
$app->response->headers->set('Access-Control-Allow-Origin','*');

$cnx = 'mysql:host=' . DB_HOST . ';dbname=' . DB_BASE;
R::setup($cnx, DB_USER, DB_PASS);

$req = $app->getInstance()->request();
$app->baseUrl = $req->getUrl() . $req->getRootUri();
unset($req);

$app->get('/', function() {
    $data = [
        'error' => true,
        'message' => 'Not available'
    ];
    
    echo json_encode($data);
});

$app->get('/get-url', function() use($app) {
    echo json_encode($app->baseUrl);
});

require_once 'lib/db_func.php';
require_once 'lib/departures.php';
require_once 'routes/_init.php';

$app->run();
R::close();