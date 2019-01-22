<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

$libraryPath = __DIR__."/library";

require $libraryPath . "/XenForo/Autoloader.php";

XenForo_Autoloader::getInstance()->setupAutoloader($libraryPath);

XenForo_Application::initialize($libraryPath);
XenForo_Application::set('page_start_time', microtime(true));

XenForo_Session::startPublicSession();

$visitor = XenForo_Visitor::getInstance();

if (empty($visitor) || $visitor->user_id === 0 || $visitor->is_banned !== 0) {
    http_response_code(401);
    die('unauthorized');
}

if ($visitor->is_admin !== 1 && $visitor->is_moderator !== 1) {
    $secondary = $visitor->secondary_group_ids;

    if (strstr($secondary, ",")) {
        if (!in_array("12", explode(",", $secondary))) {
            http_response_code(401);
            die('not a customer');
        }
    } else {
        if ($secondary != "12") {
            http_response_code(401);
            die('not a customer');
        }
    }
}

$file = __DIR__ . "/../webauth/bin/ChromaCheats.Loader.exe";

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header("Content-Disposition: attachment; filename=\"" . getRandomFileName() . ".exe" . "\";");
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file));

readfile($file);

function getRandomFileName()
{
    $length = mt_rand(2, 4);

    return bin2hex(random_bytes($length));
}
