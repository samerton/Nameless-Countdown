<?php 
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr12
 *
 *  License: MIT
 *
 *  Countdown module class
 */

class Countdown_Module extends Module {
	private $_countdown_language, $_language;

	public function __construct($pages, $language, $countdown_language){
		$this->_countdown_language = $countdown_language;
		$this->_language = $language;

		$name = 'Countdown';
		$author = '<a href="https://samerton.me" target="_blank">Samerton</a>';
		$module_version = '1.0.2';
		$nameless_version = '2.0.0-pr12';

		parent::__construct($this, $name, $author, $module_version, $nameless_version);

		// Define URLs which belong to this module
		$pages->add('Countdown', '/panel/countdown', 'pages/panel/countdown.php');

	}

	public function onInstall(){
		try {
			$engine = Config::get('mysql/engine');
			$charset = Config::get('mysql/charset');
		} catch(Exception $e){
			$engine = 'InnoDB';
			$charset = 'utf8mb4';
		}

		if(!$engine || is_array($engine))
			$engine = 'InnoDB';

		if(!$charset || is_array($charset))
			$charset = 'latin1';

		$queries = new Queries();
		try {
			$queries->createTable("countdown", " `id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(256) NOT NULL, `description` text, `expires` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`id`)", "ENGINE=$engine DEFAULT CHARSET=$charset");
		} catch(Exception $e){
			// Error
		}
	}

	public function onUninstall(){
		DB::getInstance()->createQuery('DROP TABLE countdown');
	}

	public function onEnable(){

	}

	public function onDisable(){

	}

	public function onPageLoad($user, $pages, $cache, $smarty, $navs, $widgets, $template) {
		// Widgets
		require_once(__DIR__ . '/widgets/CountdownWidget.php');
		$countdown_pages = $widgets->getPages('Countdown');
		$widgets->add(new CountdownWidget($countdown_pages, $user, $this->_language, $this->_countdown_language, $smarty, $cache));

		if (defined('FRONT_END')) {
            $template->addJSScript('
                let countdownInterval;

                function getCountdownTime() {
                    const xDays = "' . $this->_countdown_language->get('countdown', 'x_days') . '";
                    const xHours = "' . $this->_countdown_language->get('countdown', 'x_hours') . '";
                    const xMinutes = "' . $this->_countdown_language->get('countdown', 'x_minutes') . '";
                    const xSeconds = "' . $this->_countdown_language->get('countdown', 'x_seconds') . '";

                    const format = "' . $this->_countdown_language->get('countdown', 'countdown_format') . '";

                    const getDays = (value) => xDays.replace("{x}", value);
                    const getHours = (value) => xHours.replace("{x}", value);
                    const getMinutes = (value) => xMinutes.replace("{x}", value);
                    const getSeconds = (value) => xSeconds.replace("{x}", value);

                    const padTime = (value) => String(value).padStart(2, "0");

                    const countdownElement = $("#countdown-value");

                    if (countdownElement) {
                        const countdownExpiresAt = countdownElement.data("expires");

                        if (!isNaN(countdownExpiresAt)) {
                            const countdownExpires = new Date(countdownExpiresAt * 1000).getTime();

                            const countdownTimeNow = new Date().getTime();
                            const countdownDiff = countdownExpires - countdownTimeNow;

                            const countdownDays = Math.floor(countdownDiff / (1000 * 60 * 60 * 24));
                            const countdownHours = Math.floor((countdownDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            const countdownMinutes = Math.floor((countdownDiff % (1000 * 60 * 60)) / (1000 * 60));
                            const countdownSeconds = Math.floor((countdownDiff % (1000 * 60)) / 1000);

                            countdownElement.html(format
                                                   .replace("{days}", getDays(countdownDays))
                                                   .replace("{hours}", getHours(padTime(countdownHours)))
                                                   .replace("{minutes}", getMinutes(padTime(countdownMinutes)))
                                                   .replace("{seconds}", getSeconds(padTime(countdownSeconds)))
                                                 );

                            if (countdownDiff < 0) {
                                clearInterval(countdownInterval);
                                countdownElement.html(format
                                                       .replace("{days}", getDays("0"))
                                                       .replace("{hours}", getHours("00"))
                                                       .replace("{minutes}", getMinutes("00"))
                                                       .replace("{seconds}", getSeconds("00"))
                                                     );
                            }
                        }
                    }
                }

                getCountdownTime();
                countdownInterval = setInterval(function() {
                    getCountdownTime();
                }, 1000);
            ');
        }

		if (defined('BACK_END')) {
			if($user->getMainGroup()->id == 2 || $user->hasPermission('admincp.countdown')){
				$cache->setCache('panel_sidebar');
				if(!$cache->isCached('countdown_order')){
					$order = 18;
					$cache->store('countdown_order', 18);
				} else {
					$order = $cache->retrieve('countdown_order');
				}

				$navs[2]->add('countdown_divider', mb_strtoupper($this->_countdown_language->get('countdown', 'countdown')), 'divider', 'top', null, $order, '');

                if (!$cache->isCached('countdown_icon')) {
                    $icon = '<i class="nav-icon fa fa-clock" aria-hidden="true"></i>';
                    $cache->store('countdown_icon', $icon);
                } else
                    $icon = $cache->retrieve('countdown_icon');

                $navs[2]->add('countdown', $this->_countdown_language->get('countdown', 'countdown'), URL::build('/panel/countdown'), 'top', null, ($order + 0.1), $icon);

			}
		}

		// AdminCP
		PermissionHandler::registerPermissions('Countdown', array(
			'admincp.countdown' => $this->_language->get('moderator', 'staff_cp') . ' &raquo; ' . $this->_countdown_language->get('countdown', 'countdown'),
		));
	}
}
