<?php
/*
Plugin Name: Mailchimp subscribe for WP
Description: MailChimp subscribe for WordPress by Artem Koliada. Adds ajax shortcode to your site.
Version: 1.0.2
Author: Artem Koliada
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.txt
Text Domain: zt-mailchimp-subscribe
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // don't access directly
};

define( 'ZT_MAILCHIMP_PATH', plugin_dir_path( __FILE__ ) );
define( 'ZT_MAILCHIMP_URL', plugin_dir_url( __FILE__ ) );

class ZT_Mailchimp_Subscribe {
	public function __construct() {
		$this->autoload();
		$this->hooks();
	}

	public function autoload() {
		require_once ZT_MAILCHIMP_PATH . '/src/zt-admin.php';
		require_once ZT_MAILCHIMP_PATH . '/src/zt-enqueue.php';
	}

	public function hooks() {

		// Add shortcode
		add_shortcode( 'zt-mailchimp-subscribe', array( $this, 'shortcode' ) );

		// Ajax Request
		if ( wp_doing_ajax() ) {
			add_action( 'wp_ajax_request_form_subscribe', array( $this, 'request_form_subscribe' ) );
			add_action( 'wp_ajax_nopriv_request_form_subscribe', array( $this, 'request_form_subscribe' ) );
		}
	}

	public function shortcode() {
		$output = '';

		$output .= '<div class="zt-subscribe">';
			$output .= '<form action="' . admin_url( 'admin-ajax.php' ) . '" method="POST" class="zt-subscribe__form">';
				$output .= '<input type="email" name="email_address" placeholder="' . esc_attr__('Email Address', 'zt-mailchimp-subscribe') . '" required>';
				$output .= '<input type="submit" value="' . esc_attr__('Submit', 'zt-mailchimp-subscribe') . '" >';
				$output .= '<input type="hidden" name="zt_nonce" value="' . wp_create_nonce('_ajax_nonce') . '" >';
			$output .= '</form>';

			$output .= '<div class="zt-subscribe__message"></div>';
		$output .= '</div>';

		return apply_filters( 'zt_output_shortcode', $output );
	}

	public function request_form_subscribe() {
		parse_str( $_POST['request'], $request );

		$message = array(
			'error'          => esc_html__('Something went wrong, try again!', 'zt-mailchimp-subscribe'),
			'mailchimp_data' => esc_html__('You have not the API Key or the list ID!', 'zt-mailchimp-subscribe'),
			'wrong_data'     => esc_html__('Typed the API Key or the list ID is wrong!', 'zt-mailchimp-subscribe'),
			'subscribed'     => esc_html__( 'You have already subscribed.', 'zt-mailchimp-subscribe'),
			'success'        => esc_html__( 'Thank you, you have been successfully subscribed.', 'zt-mailchimp-subscribe')
		);

		if ( ! wp_verify_nonce( $request['zt_nonce'], '_ajax_nonce' ) ) wp_die( $message['error'] );

		$address_email = $request['email_address'];

		$zt_options = get_option('zt_options');

		if ( empty( $zt_options['api_key'] ) || empty( $zt_options['list_id'] ) ) wp_die( $message['mailchimp_data'] );

		$apiKey = $zt_options['api_key'];
		$listId = $zt_options['list_id'];

		$data = array(
			'email_address' => $address_email,
			'status'        => 'subscribed'
		);

		$body = json_encode( $data );

		$options = array(
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'apikey ' . $apiKey
			),
			'body' => $body
		);

		$dc = explode( '-', $apiKey )[1];

		$url = 'https://' . $dc . '.api.mailchimp.com/3.0/lists/' . $listId . '/members/';
		$response = wp_remote_post( $url, $options );

		if ( $response['response']['code'] == '200' ) {
			wp_die( $message['success'] );
		} elseif ( $response['response']['code'] == '400' ) {
			wp_die( json_decode( $response['body'] )->detail );
		} elseif ( $response['response']['code'] == '404' ) {
			wp_die( $message['wrong_data'] );
		}
	}
}

new ZT_Mailchimp_Subscribe();