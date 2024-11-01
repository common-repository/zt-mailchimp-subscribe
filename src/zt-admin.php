<?php

class ZT_Admin {
	public function __construct() {
		$this->hooks();
	}

	public function hooks() {
		// Create Admin Setting Menu
		add_action( 'admin_menu', array( $this, 'admin_page' ) );
		add_action( 'admin_init', array( $this, 'settings' ) );
	}

	public function admin_page() {
		add_submenu_page(
			'tools.php',
			esc_html__( 'Mailchimp Subscribe', 'zt-mailcimp-subscribe' ),
			esc_html__( 'Mailchimp Subscribe', 'zt-mailcimp-subscribe' ),
			'manage_options',
			'mailchimp-subscribe',
			array( $this, 'setting_form' )
		);
	}

	public function setting_form() {
		?>
		<div class="wrap">
			<h2><?php echo get_admin_page_title(); ?></h2>

            <p class="help"><?php esc_html_e('Use the shortcode [zt-mailchimp-subscribe] to display this form inside a post, page or text widget.', 'zt-mailcimp-subscribe'); ?></p>

			<form action="<?php echo admin_url( 'options.php' ); ?>" method="post">
				<?php
					settings_fields( 'zt_options_page' );
					do_settings_sections( 'zt_options_section' );
					submit_button();
				?>
			</form>

            <p class="help"><?php esc_html_e('The API key for connecting with your MailChimp account.', 'zt-mailcimp-subscribe'); ?> <a href="<?php echo esc_url( 'https://admin.mailchimp.com/account/api' ); ?>" target="_blank"><?php esc_html_e('Get your API key here.', 'zt-mailcimp-subscribe'); ?></a></p>
            <p class="help"><?php esc_html_e('How to find your List ID?', 'zt-mailcimp-subscribe'); ?> <a href="<?php echo esc_url( 'https://mailchimp.com/help/find-your-list-id/' ); ?>" target="_blank"><?php esc_html_e('Get your List ID here.', 'zt-mailcimp-subscribe'); ?></a></p>
		</div>
		<?php
	}

	public function settings() {
		register_setting( 'zt_options_page', 'zt_options' );
		add_settings_section( 'general', '', '', 'zt_options_section' );

		// Add settings field
		$array_fields = array(
			array(
				'id'    => 'api_key',
				'title' => esc_html__('API Key', 'zt-mailcimp-subscribe'),
				'args'  => array( 'type_field' => 'text', 'option_name' => 'api_key', 'value' => '' )
			),
			array(
				'id'    => 'list_id',
				'title' => esc_html__('List ID', 'zt-mailcimp-subscribe'),
				'args'  => array( 'type_field' => 'text', 'option_name' => 'list_id', 'value' => '' )
			),
		);

		foreach ( $array_fields as $item ) {
			$item['args']['page'] = 'zt';
			$this->add_setting_field( $item['id'], $item['title'], array( $this, 'setting_field_callback' ), 'zt_options_section', 'general', $item['args'] );
		}
	}

	public function add_setting_field( $id, $title, $callback, $page, $section, $args ) {
		add_settings_field( $id, $title, $callback, $page, $section, $args );
	}

	public function setting_field_callback( $args ) {
		$val = get_option( $args['page'] . '_options' );
		$val = ! empty( $val[ $args['option_name'] ] ) ? $val[ $args['option_name'] ] : null;

		if ( $args['type_field'] == 'select' ) : ?>
			<select name="<?php echo esc_attr( $args['page'] . '_options[' . $args['option_name'] . ']' ); ?>"
			        id="<?php echo esc_attr( $args['option_name'] ); ?>">
				<?php foreach ( $args['value'] as $key => $item ) : ?>
					<option value="<?php echo esc_attr( $item ); ?>" <?php selected( $val, $item ); ?>><?php echo esc_html( $item ); ?></option>
				<?php endforeach; ?>
			</select>
		<?php elseif ( $args['type_field'] == 'checkbox' ) : ?>
			<input type="checkbox" id="<?php echo esc_attr( $args['option_name'] ); ?>"
			       name="<?php echo esc_attr( $args['page'] . '_options[' . $args['option_name'] . ']' ); ?>"
			       value="<?php echo esc_attr( $args['value'] ); ?>" <?php checked( $val, $args['value'] ); ?>>
		<?php elseif ( $args['type_field'] == 'text' ) : ?>
			<input type="text" id="<?php echo esc_attr( $args['option_name'] ); ?>"
			       name="<?php echo esc_attr( $args['page'] . '_options[' . $args['option_name'] . ']' ); ?>"
			       value="<?php echo esc_attr( $val ); ?>">
		<?php endif;
	}
}

new ZT_Admin();