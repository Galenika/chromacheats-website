<?php

display_all_errors();

require_once __DIR__."/MinimalRouter.php";

$router = new Router(__DIR__."/routes/");

$router->add_route("", "/HandleRequest.php");

if (!$router->handle_request())
{
    $router->throw();
}

function display_all_errors()
{
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}

?>