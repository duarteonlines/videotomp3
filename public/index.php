<?php
require '../vendor/autoload.php';
require '../routes/router.php';


try{
    $uri = parse_url($_SERVER['REQUEST_URI'])['path'];
    $request = $_SERVER['REQUEST_METHOD'];
   
    if(!isset($router[$request])){
        throw new Exception("A rota não existe");
    }

    if(!array_key_exists($uri, $router[$request])){
        throw new Exception("A rota não existe");

    }
    $controller = $router[$request][$uri];
    $controller();

} catch(Excpetion $e){
    echo $e->getMessage();
}





















// function cors()
// {
//     if (isset($_SERVER['HTTP_ORIGIN'])) {
//         header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
//         header('Access-Control-Allow-Credentials: true');
//         header('Access-Control-Max-Age: 86400');
//     }

//     if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
//         if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
//             header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//         if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
//             header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
//         exit(0);
//     }
// }
