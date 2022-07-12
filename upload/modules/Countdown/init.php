<?php 
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 *
 *  Countdown initialisation file
 */

// Initialise module language
$countdown_language = new Language(ROOT_PATH . '/modules/Countdown/language', LANGUAGE);

require_once ROOT_PATH . '/modules/Countdown/module.php';
$module = new Countdown_Module($pages, $language, $countdown_language);
