<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr12
 *
 *  License: MIT
 *
 *  Countdown Widget
 */
class CountdownWidget extends WidgetBase {

    private $_smarty, 
            $_language, 
            $_countdown_language,
            $_cache,
            $_user;

    public function __construct($pages = array(), $user, $language, $countdown_language, $smarty, $cache) {

    	$this->_user = $user;
		$this->_language = $language;
		$this->_countdown_language = $countdown_language;
    	$this->_smarty = $smarty;
    	$this->_cache = $cache;
		
        parent::__construct($pages);
        
        // Get widget
        $widget_query = DB::getInstance()->query('SELECT `location`, `order` FROM nl2_widgets WHERE `name` = ?', array('Countdown'))->first();

        // Set widget variables
        $this->_module = 'Countdown';
        $this->_name = 'Countdown';
        $this->_location = isset($widget_query->location) ? $widget_query->location : null;
        $this->_description = 'Display a countdown on your website';
        $this->_order = isset($widget_query->order) ? $widget_query->order : null;

    }

    public function initialise() {

		// TODO: multiple countdowns
		$countdown = DB::getInstance()->query('SELECT name, description, expires FROM nl2_countdown ORDER BY id ASC LIMIT 1', array());

		if ($countdown->count()) {
		    $countdown = $countdown->first();

            $this->_smarty->assign(array(
                'COUNTDOWN_TITLE' => Output::getPurified($countdown->name),
                'COUNTDOWN_DESCRIPTION' => Output::getPurified($countdown->description),
                'COUNTDOWN_EXPIRES' => Output::getClean($countdown->expires)
            ));

        } else {
            $this->_smarty->assign('NO_COUNTDOWN_AVAILABLE', $this->_countdown_language->get('countdown', 'no_countdown'));
        }

		$this->_content = $this->_smarty->fetch('widgets/countdown/countdown.tpl');
    }
}
