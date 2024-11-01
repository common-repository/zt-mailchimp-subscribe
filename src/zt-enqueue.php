<?php

class ZT_Enqueue {

	public function __construct() {
		$this->hooks();
	}

	public function hooks() {
		// Front End Enqueue Scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'front_enqueue_scripts' ) );

		// Admin Enqueue Scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	public function front_enqueue_scripts() {
		wp_enqueue_script('zt-subscribe-script', ZT_MAILCHIMP_URL . 'assets/js/main.js', array('jquery') );
	}

	public function admin_enqueue_scripts() {

	}
}

new ZT_Enqueue();