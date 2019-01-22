<?php

class ChromaCheats_ControllerPublic_Index extends XenForo_ControllerPublic_Abstract
{
    public static function view(XenForo_ControllerPublic_Abstract $controller, XenForo_ControllerResponse_Abstract &$response)
    {
        if (isset($_POST['user'])) {
            $username = trim($_POST['user'], ', ');

            $userModel = XenForo_Model::create('XenForo_Model_User');
            $user = $userModel->getUserByName($username);

            if (false === $user) {
                $response->params['error'] = 'Error! User does not exist';
                return;
            }

            $userId = intval($user['user_id']);

            if ($userId < 0) {
                $response->params['error'] = 'Error! Wrong UserId';
                return;
            }

            $sql = new mysqli('+', '+', '+', '+', 0);

            if (!$sql) {
                $response->params['error'] = 'Error! Can not connect to database';
                return;
            }

            $query = $sql->query("SELECT `hardware_id` FROM `auth_user` WHERE `xf_user_id` = " . $userId);

            if (!$query || $query->num_rows == 0) {
                $response->params['error'] = 'Error! No HardwareId set';
                return;
            }

            $hwid = $query->fetch_row()[0];

            $query->close();

            $sql->query("UPDATE `auth_hwid` SET `hash` = '-1' WHERE `auth_hwid`.`hardware_id` = " . $hwid . ";");

            $sql->close();

            $response->params['response'] = 'Success!';
        }
    }
}
