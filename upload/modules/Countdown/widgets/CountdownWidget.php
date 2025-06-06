<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 *
 *  Countdown Widget
 */
class CountdownWidget extends WidgetBase {

    private Language $_language;
    private Language $_countdown_language;
    private Cache $_cache;
    private User $_user;

    public function __construct(User $user, Language $language, Language $countdown_language, TemplateEngine $engine, Cache $cache) {

    	$this->_user = $user;
		$this->_language = $language;
		$this->_countdown_language = $countdown_language;
    	$this->_engine = $engine;
    	$this->_cache = $cache;

        // Set widget variables
        $this->_module = 'Countdown';
        $this->_name = 'Countdown';
        $this->_description = 'Display a countdown on your website';
    }

    public function initialise(): void {

		// TODO: multiple countdowns
		$countdown = DB::getInstance()->query('SELECT name, description, expires FROM nl2_countdown ORDER BY id ASC LIMIT 1');

		if ($countdown->count()) {
		    $countdown = $countdown->first();

            $this->_engine->addVariables(array(
                'COUNTDOWN_TITLE' => Output::getPurified($countdown->name),
                'COUNTDOWN_DESCRIPTION' => Output::getPurified($countdown->description),
                'COUNTDOWN_EXPIRES' => Output::getClean($countdown->expires)
            ));

        } else {
            $this->_engine->addVariable('NO_COUNTDOWN_AVAILABLE', $this->_countdown_language->get('countdown', 'no_countdown'));
        }

		$this->_content = $this->_engine->fetch('widgets/countdown/countdown.tpl');
    }
}
