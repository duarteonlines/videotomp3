<?php

function load(string $controller, string $action){
    $controllerNamespace = "app\\controllers\\{$controller}";
    try{
    if(!class_exists($controllerNamespace)){
        throw new Exception("Controller não existe");
    }

    $controllerInstance = new $controllerNamespace;

    if(!method_exists($controllerInstance, $action)){
        throw new Exception("Metodo não existe");
    }

    $controllerInstance->$action((object)$_REQUEST);
    } catch(Exception $e){
        echo $e->getMessage();
    }
}

$router = [
    'GET' => [
        '/' => fn() => load('MpegController', 'index'),
    ],
    'POST' => [
        '/teste' => fn() => load('MpegController', 'teste'),
        '/videoconverter' => fn() => load('MpegController', 'converter')
    ]
];