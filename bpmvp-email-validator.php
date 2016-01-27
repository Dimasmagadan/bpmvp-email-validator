<?php
/*
Plugin Name: Byteplant Email Validator
Version: 1.1
Description: Email validation plugin
Author: support@byteplant.com
Text Domain: bpmvp-email-validator
Domain Path: /languages
*/

function bpmvp_load_textdomain() {
  load_plugin_textdomain( 'bpmvp-email-validator', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' ); 
}
add_action( 'plugins_loaded', 'bpmvp_load_textdomain' );

function bpmvp_add_admin_menu() { 
	add_options_page( __('Byteplant Email Validator', 'bpmvp-email-validator' ), __('Byteplant Email Validator', 'bpmvp-email-validator' ), 'manage_options', 'byteplant_email_validator', 'bpmvp_options_page' );
}
add_action( 'admin_menu', 'bpmvp_add_admin_menu' );

function bpmvp_settings_init() { 

	register_setting( 'byteplant_email_validator', 'bpmvp_settings' );

	add_settings_section(
		'bpmvp_pluginPage_section',
		__( 'Email Validator options', 'bpmvp-email-validator' ),
		'bpmvp_settings_section_callback',
		'byteplant_email_validator'
	);

	add_settings_field(
		'bpmvp_api_key',
		__( 'API key', 'bpmvp-email-validator' ),
		'bpmvp_text_field_0_render',
		'byteplant_email_validator', 
		'bpmvp_pluginPage_section' 
	);

	add_settings_field(
		'bpmvp_reg_check',
		__( 'Validate email on registration', 'bpmvp-email-validator' ),
		'bpmvp_text_field_1_render',
		'byteplant_email_validator', 
		'bpmvp_pluginPage_section' 
	);

	add_settings_field(
		'bpmvp_comments_check',
		__( 'Validate email for comments', 'bpmvp-email-validator' ),
		'bpmvp_text_field_2_render',
		'byteplant_email_validator', 
		'bpmvp_pluginPage_section' 
	);

	add_settings_field(
		'bpmvp_is_email_check',
		__( 'Hook to is_email() function', 'bpmvp-email-validator' ),
		'bpmvp_text_field_3_render',
		'byteplant_email_validator', 
		'bpmvp_pluginPage_section' 
	);
}
add_action( 'admin_init', 'bpmvp_settings_init' );

function bpmvp_text_field_0_render() { 
	$options = get_option( 'bpmvp_settings' );
	?>
	<input type='text' name='bpmvp_settings[bpmvp_api_key]' value='<?php echo $options['bpmvp_api_key']; ?>'>
	<?php
}

function bpmvp_text_field_1_render() { 
	$options = get_option( 'bpmvp_settings', array(
		'bpmvp_reg_check' => 0
	));
	?>
	<input type='checkbox' name='bpmvp_settings[bpmvp_reg_check]' <?php checked( 1, $options['bpmvp_reg_check'] ); ?> value='1'>
	<?php
}

function bpmvp_text_field_2_render() { 
	$options = get_option( 'bpmvp_settings', array(
		'bpmvp_comments_check' => 0
	));
	?>
	<input type='checkbox' name='bpmvp_settings[bpmvp_comments_check]' <?php checked( 1, $options['bpmvp_comments_check'] ); ?> value='1'>
	<?php
}

function bpmvp_text_field_3_render() { 
	$options = get_option( 'bpmvp_settings', array(
		'bpmvp_is_email_check' => 0
	));
	?>
	<input type='checkbox' name='bpmvp_settings[bpmvp_is_email_check]' <?php checked( 1, $options['bpmvp_is_email_check'] ); ?> value='1'>
	<?php
}

function bpmvp_settings_section_callback() { 
	$options = get_option( 'bpmvp_settings', array(
		'bpmvp_api_key' => ''
	));

	_e('You can use <a href="http://www.email-validator.net" target="_blank">our service</a> to validate email addresses in real-time. The email address verification process includes these steps:<br>
Syntax verification<br>
DNS validation including MX record lookup<br>
Disposable email address (DEA) detection<br>
SMTP connectivity and availability checking<br>
Temporary unavailability detection<br>
Mailbox existence checking<br>
Catch-All testing<br>
Greylisting detection', 'bpmvp-email-validator' );

	if( empty($options['bpmvp_api_key'])){
		echo '<br><br>';
		_e('You can register for a <a href="http://www.email-validator.net/email-validation-online-api-get-free-trial.html" target="_blank">free API key</a> (limited to 100 address checks per month).<br>
If you want to verify more than 100 addresses per month, please have a look at our pay-as-you-go pricing model and the <a href="http://www.email-validator.net/email-address-verification-pricing.html" target="_blank">subscription plan</a> we offer.', 'bpmvp-email-validator' );
	}

}

function bpmvp_options_page() { 
	?>
	<form action='options.php' method='post'>
		
		<h2><?php _e('Byteplant Email Validator', 'bpmvp-email-validator' ); ?></h2>
		
		<?php
		settings_fields( 'byteplant_email_validator' );
		do_settings_sections( 'byteplant_email_validator' );
		submit_button();
		?>
	</form>

	<div class="card bpmvp-card" style="">
		<h2><?php _e('Validate Single Email', 'bpmvp-email-validator'); ?></h2>
		<p><?php _e('Use this section to validate single email adress', 'bpmvp-email-validator'); ?></p>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><?php _e('Email to validate', 'bpmvp-email-validator'); ?></th>
					<td>
						<input id="bpmvp-mail" value="" type="text">
					</td>
				</tr>
			</tbody>
		</table>
		<p id="bpmvp-message"></p>
		<p class="submit">
			<input name="submit" id="bpmvp-button-validate" class="button button-primary" value="<?php _e('Validate', 'bpmvp-email-validator'); ?>" type="submit">
		</p>
		<ul id="bpmvp-list"></ul>
	</div>
	<?php
}

function bpmvp_scripts($hook){

  	if ( 'settings_page_byteplant_email_validator' != $hook ) {
   	return;
   }

   wp_register_style( 'bpmvp_main_style', plugins_url('/css/bpmvp_style.css', __FILE__) );
	wp_register_script('bpmvp_main_script', plugins_url('js/bpmvp_script.js', __FILE__), array( 'jquery', 'underscore' ), '1.0', true);

	$js_vars = array();
	$js_vars[200] = __('OK - Valid Address', 'bpmvp-email-validator');
	$js_vars[207] = __('OK - Catch-All Active', 'bpmvp-email-validator');
	$js_vars[215] = __('OK - Catch-All Test Delayed', 'bpmvp-email-validator');

	$js_vars[302] = __('Local Address', 'bpmvp-email-validator');
	$js_vars[303] = __('IP Address Literal', 'bpmvp-email-validator');
	$js_vars[305] = __('Disposable Address', 'bpmvp-email-validator');
	$js_vars[308] = __('Role Address', 'bpmvp-email-validator');
	$js_vars[313] = __('Server Unavailable', 'bpmvp-email-validator');
	$js_vars[314] = __('Address Unavailable', 'bpmvp-email-validator');
	$js_vars[316] = __('Duplicate Address', 'bpmvp-email-validator');
	$js_vars[317] = __('Server Reject', 'bpmvp-email-validator');

	$js_vars[401] = __('Bad Address', 'bpmvp-email-validator');
	$js_vars[404] = __('Domain Not Fully Qualified', 'bpmvp-email-validator');
	$js_vars[406] = __('MX Lookup Error', 'bpmvp-email-validator');
	$js_vars[409] = __('No-Reply Address', 'bpmvp-email-validator');
	$js_vars[410] = __('Address Rejected', 'bpmvp-email-validator');
	$js_vars[413] = __('Server Unavailable', 'bpmvp-email-validator');
	$js_vars[414] = __('Address Unavailable', 'bpmvp-email-validator');
	$js_vars[420] = __('Domain Name Misspelled', 'bpmvp-email-validator');

	$js_vars[114] = __('Validation Delayed', 'bpmvp-email-validator');
	$js_vars[118] = __('Rate Limit Exceeded', 'bpmvp-email-validator');
	$js_vars[119] = __('API Key Invalid or Depleted', 'bpmvp-email-validator');
	$js_vars[121] = __('Task Accepted', 'bpmvp-email-validator');

	$js_vars[800] = __('Email Address Missing', 'bpmvp-email-validator');
	$js_vars[801] = __('Service Unavailable', 'bpmvp-email-validator');

	$js_vars['tpl'] = '<li><span><%- bp[status] %></span><%- mail %></li>';

	$options = get_option( 'bpmvp_settings', array( 'bpmvp_api_key' => '' ) );
	$js_vars['key'] = $options['bpmvp_api_key'];

	wp_localize_script( 'bpmvp_main_script', 'bp', $js_vars);
	wp_enqueue_script( 'bpmvp_main_script' );
	wp_enqueue_style('bpmvp_main_style' );
}
add_action( 'admin_enqueue_scripts', 'bpmvp_scripts' );

function bpmvp_user_signup_check( $mail ){

	$options = get_option( 'bpmvp_settings', array(
		'bpmvp_reg_check' => 0
	));

	if( $options['bpmvp_reg_check'] == 1 ){
		if( !bpmvp_validate_email($mail) ){
			return false;
		}
	}

	return $mail;
}
add_filter('user_registration_email', 'bpmvp_user_signup_check');

function bpmvp_validate_email($mail){
	$options = get_option( 'bpmvp_settings', array(
		'bpmvp_api_key' => ''
	));

	$url = 'http://api.email-validator.net/api/verify';

	$args = array(
		'method' => 'POST',
		'timeout' => 45,
		'blocking' => true,
		'headers' => array(),
		'cookies' => array()
	);
	$args['body'] = array(
		'EmailAddress' => $mail,		
		'APIKey' => $options['bpmvp_api_key'],		
		'scope' => 'wpplugin'
	);

	$response = wp_remote_post( $url, $args );

	if(is_wp_error($response )){
		// if service is unavailable we will let users register
		return true;
	}

	$result = json_decode( wp_remote_retrieve_body( $response ) );
	if( in_array( $result->status, array(200,207,215) ) ){
		return true;
	}

	return false;
}

function bpmvp_is_email_filter($passed, $email, $context){
	if($passed){
		$options = get_option( 'bpmvp_settings', array(
			'bpmvp_is_email_check' => 0
		));
		if($options['bpmvp_is_email_check'] ==1 && !bpmvp_validate_email($email) ){
			// return __('Invalid email', 'bpmvp-email-validator');
			return false;
		}
	} 

	return $email;
}
add_filter( 'is_email', 'bpmvp_is_email_filter', 10, 3 );

function bpmvp_preprocess_comment_filter($approved, $commentdata){

	if( is_user_logged_in() ){
		return $approved;
	}

	if( isset($commentdata['comment_author_email']) ){
		$options = get_option( 'bpmvp_settings', array(
			'bpmvp_api_key' => '',
			'bpmvp_reg_check' => 0,
			'bpmvp_comments_check' => 0,
		));

		if( $options['bpmvp_comments_check'] == 1 && !bpmvp_validate_email($commentdata['comment_author_email']) ){
			$approved = 0;
		}
	}

	return $approved;
}
add_filter( 'pre_comment_approved', 'bpmvp_preprocess_comment_filter', 10, 2 );
