<?php

namespace Zumba\Symbiosis\Test\Plugin\Registerable;

use \Zumba\Symbiosis\Framework\Plugin,
	\Zumba\Symbiosis\Framework\Registerable,
	\Zumba\Symbiosis\Event\Event;

class TestPlugin extends Plugin implements Registerable {

	public $priority = 1;

	public function getEvents() {
		return array(
			'register.1' => function(Event $e) {
				$data = $e->data();
				$data['called']++;
				$e->data($data);
			},
			'register.2' => array(
				array($this, 'register2'),
				array($this, 'register2')
			)
		);
	}

	public function register2(Event $e) {
		$data = $e->data();
		$data['called']++;
		$e->data($data);
	}

}
