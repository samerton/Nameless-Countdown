<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 *
 *  Countdown configuration page
 */

// Can the user view the panel?
if($user->getMainGroup()->id != 2 && !$user->handlePanelPageLoad('admincp.countdown')) {
    require_once(ROOT_PATH . '/403.php');
    die();
}

define('PAGE', 'panel');
define('PARENT_PAGE', 'countdown');
define('PANEL_PAGE', 'countdown');
$page_title = $countdown_language->get('countdown', 'countdown');
require_once ROOT_PATH . '/core/templates/backend_init.php';

if (Input::exists()) {
    if (Token::check()) {
        if (isset($_GET['action']) && $_GET['action'] == 'remove') {
            DB::getInstance()->delete('countdown', ['id', '=', 1]);
            die('Complete');
        }

        $validation = Validate::check($_POST, [
            'name' => [
                Validate::REQUIRED => true,
                Validate::MIN => 2,
                Validate::MAX => 256
            ],
            'expires' => [
                Validate::REQUIRED => true
            ]
        ])->messages([
            'name' => [
                Validate::REQUIRED => $countdown_language->get('countdown', 'name_required'),
                Validate::MIN => $countdown_language->get('countdown', 'name_minimum'),
                Validate::MAX => $countdown_language->get('countdown', 'name_maximum')
            ],
            'expires' => $countdown_language->get('countdown', 'expires_required')
        ]);

        if ($validation->passed()) {
            // TODO: multiple countdowns
            $content = [
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'expires' => strtotime($_POST['expires'])
            ];

            if (DB::getInstance()->query('SELECT * FROM nl2_countdown WHERE id = 1')->count()) {
                DB::getInstance()->update('countdown', 1, $content);
            } else {
                DB::getInstance()->insert('countdown', $content);
            }

            Session::flash('admin_countdown', $countdown_language->get('countdown', 'updated_successfully'));
            Redirect::to(URL::build('/panel/countdown'));

        } else {
            $errors = $validation->errors();
        }

    } else {
        // Invalid token
        $errors = [$language->get('general', 'invalid_token')];
    }
}

// Retrieve config
$countdown_config = DB::getInstance()->query('SELECT name, description, expires FROM nl2_countdown ORDER BY id ASC LIMIT 1');

if ($countdown_config->count()) {
    $countdown_config = $countdown_config->first();

    $smarty->assign(array(
        'COUNTDOWN_NAME_VALUE' => Output::getPurified($countdown_config->name),
        'COUNTDOWN_DESCRIPTION_VALUE' => Output::getPurified($countdown_config->description),
        'COUNTDOWN_EXPIRES_VALUE' => date('Y-m-d\TH:i', Output::getClean($countdown_config->expires))
    ));
} else {
    $smarty->assign(array(
        'COUNTDOWN_NAME_VALUE' => '',
        'COUNTDOWN_DESCRIPTION_VALUE' => '',
        'COUNTDOWN_EXPIRES_VALUE' => ''
    ));
}

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

if (Session::exists('admin_countdown'))
    $success = Session::flash('admin_countdown');

if (isset($success))
    $smarty->assign([
        'SUCCESS' => $success,
        'SUCCESS_TITLE' => $language->get('general', 'success')
    ]);

if (isset($errors) && count($errors))
    $smarty->assign([
        'ERRORS' => $errors,
        'ERRORS_TITLE' => $language->get('general', 'error')
    ]);

$smarty->assign([
    'PARENT_PAGE' => PARENT_PAGE,
    'DASHBOARD' => $language->get('admin', 'dashboard'),
    'COUNTDOWN' => $countdown_language->get('countdown', 'countdown'),
    'COUNTDOWN_NAME' => $countdown_language->get('countdown', 'countdown_name'),
    'COUNTDOWN_DESCRIPTION' => $countdown_language->get('countdown', 'countdown_description'),
    'COUNTDOWN_EXPIRES' => $countdown_language->get('countdown', 'countdown_expires'),
    'COUNTDOWN_EXPIRES_MIN' => date('Y-m-d\TH:i'),
    'PAGE' => PANEL_PAGE,
    'REMOVE_COUNTDOWN' => $countdown_language->get('countdown', 'remove_countdown'),
    'REMOVE_COUNTDOWN_ACTION' => URL::build('/panel/countdown', 'action=remove'),
    'REMOVE_COUNTDOWN_CONFIRM' => $language->get('general', 'are_you_sure'),
    'REMOVE_COUNTDOWN_CONFIRM_YES' => $language->get('general', 'yes'),
    'REMOVE_COUNTDOWN_CONFIRM_NO' => $language->get('general', 'no'),
    'TOKEN' => Token::get(),
    'SUBMIT' => $language->get('general', 'submit')
]);

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate('countdown/countdown.tpl', $smarty);
