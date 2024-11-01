<?php

//adding action functions
add_action( 'admin_menu', 'sdb_setting_page_add_admin_menu' );
add_action( 'admin_init', 'sdb_setting_page_settings_init' );
add_action('admin_enqueue_scripts', 'sdb_color_picker');

//color picker
function sdb_color_picker($hook_suffix)
{
	wp_enqueue_style('wp-color-picker');
	wp_enqueue_script('bi-link-handle', plugins_url('color.js', __FILE__), array('wp-color-picker'), false, true);
}

//setup menu
function sdb_setting_page_add_admin_menu(  ) { 

	add_options_page( 'Simple Discount Badge', 'Simple Discount Badge', 'manage_options', 'simple_discount_badge', 'sdb_setting_page_options_page' );

}

//Creating menu
function sdb_setting_page_settings_init(  ) { 

	register_setting( 'sdb_settings', 'sdb_setting_page_settings','sdb_settings_sanitize' );

	add_settings_section(
		'sdb_setting_page_sdb_settings_section', 
		__( 'General', 'sdb_setting_page' ), 
		'sdb_setting_page_settings_section_callback', 
		'sdb_settings'
	);

	add_settings_field( 
		'sdb_setting_page_default_badge', 
		__( 'Hide Default Sale Badge', 'sdb_setting_page' ), 
		'sdb_setting_page_default_badge_render', 
		'sdb_settings', 
		'sdb_setting_page_sdb_settings_section' 
    );

    add_settings_field( 
		'sdb_setting_page_text_afternumber', 
		__( 'Badge Text', 'sdb_setting_page' ), 
		'sdb_setting_page_text_afternumber_render', 
		'sdb_settings', 
		'sdb_setting_page_sdb_settings_section' 
    );

    add_settings_field( 
		'sdb_setting_page_background', 
		__( 'Background Color', 'sdb_setting_page' ), 
		'sdb_setting_page_background_render', 
		'sdb_settings', 
		'sdb_setting_page_sdb_settings_section' 
    );

    add_settings_field( 
		'sdb_setting_page_text_color', 
		__( 'Text Color', 'sdb_setting_page' ), 
		'sdb_setting_page_text_color_render', 
		'sdb_settings', 
		'sdb_setting_page_sdb_settings_section' 
    );

    add_settings_field( 
		'sdb_setting_page_productpage_badge', 
		__( 'Show Badge On Product Pages', 'sdb_setting_page' ), 
		'sdb_setting_page_productpage_badge_render', 
		'sdb_settings', 
		'sdb_setting_page_sdb_settings_section' 
	);
    

}

//render setting fields
function sdb_setting_page_default_badge_render(  ) { 

	$options = get_option( 'sdb_setting_page_settings' );
	?>
	<input type='checkbox' name='sdb_setting_page_settings[sdb_setting_page_default_badge]' <?php checked( $options['sdb_setting_page_default_badge'], 1 ); ?> value='1'>
	<?php

}
function sdb_setting_page_productpage_badge_render(  ) { 

	$options = get_option( 'sdb_setting_page_settings' );
	?>
	<input type='checkbox' name='sdb_setting_page_settings[sdb_setting_page_productpage_badge]' <?php checked( $options['sdb_setting_page_productpage_badge'], 1 ); ?> value='1'>
	<?php

}
function sdb_setting_page_text_afternumber_render(){
    $options = get_option( 'sdb_setting_page_settings' );
	?>
	<input type='text' name='sdb_setting_page_settings[sdb_setting_page_text_afternumber]' value='<?php echo $options['sdb_setting_page_text_afternumber']; ?>'>
	<?php
}
function sdb_setting_page_background_render(){
    $options = get_option( 'sdb_setting_page_settings' );
    ?>
    <input name='sdb_setting_page_settings[sdb_setting_page_background]' type='text' value='<?php echo $options['sdb_setting_page_background']; ?>' class='the-color-field' />
	<?php  
}
function sdb_setting_page_text_color_render(){
    $options = get_option( 'sdb_setting_page_settings' );
    ?>
    <input name='sdb_setting_page_settings[sdb_setting_page_text_color]' type='text' value='<?php echo $options['sdb_setting_page_text_color']; ?>' class='the-color-field' />
	<?php 
}

//section rendering
function sdb_setting_page_settings_section_callback(  ) { 
	echo '<p>Customize basic things.</p>';
}

//drawing settings 
function sdb_setting_page_options_page(  ) { 

        ?>
        	<div class="wrap">
        <h2>Simple Discount Badge</h2>
		<form action='options.php' method='post'>
        <?php
			settings_fields( 'sdb_settings' );
			do_settings_sections( 'sdb_settings' );
            submit_button();
			?>
        </form>
		<h2>Restore Default Settings</h2>
		<p> Click below button to restore the default settings.</p>

		<form method="post" action=<?php $_SERVER['PHP_SELF'] ?>>
			<?php wp_nonce_field(plugin_basename(__FILE__), 'nonce_sdb_reset'); ?>
			<button class="button" name="reset_form"> Restore Defaults</button>
		</form>
		<script data-name="BMC-Widget" src="https://cdnjs.buymeacoffee.com/1.0.0/widget.prod.min.js" data-id="zJvbHLe" data-description="Buy me a coffee!" data-message="If you like this plugin, you may consider buying me a coffee!" data-color="#FF813F" data-position="right" data-x_margin="22" data-y_margin="30"></script>
	
        </div>
		<?php

}

//validation input
function sdb_settings_sanitize($fields)
{
	$valid_fields = array();
    $options = get_option('sdb_setting_page_settings');
    
	// Validate Fields
	$text = trim($fields['sdb_setting_page_text_afternumber']);
    $text = strip_tags(stripslashes($text));
    
    $backgroundcolor = trim($fields['sdb_setting_page_background']);
    $backgroundcolor = strip_tags(stripslashes($backgroundcolor));
    
    $textcolor = trim($fields['sdb_setting_page_text_color']);
	$textcolor = strip_tags(stripslashes($textcolor));

    $showdefault = trim($fields['sdb_setting_page_default_badge']);
    $valid_fields['sdb_setting_page_default_badge'] = strip_tags(stripslashes($showdefault));

    $showonprodpage = trim($fields['sdb_setting_page_productpage_badge']);
    $valid_fields['sdb_setting_page_productpage_badge'] = strip_tags(stripslashes($showonprodpage));
    
if (sdb_check_text($text)===FALSE ) {
    add_settings_error('text_afternumber', 'text_afternumber_error', 'Invalid Text! Only Aplhabet, - , % , > and : allowed.', 'error');// $setting, $code, $message, $type
    $valid_fields['sdb_setting_page_text_afternumber']  = $options['sdb_setting_page_text_afternumber'];
} 
else{
    $valid_fields['sdb_setting_page_text_afternumber'] = $text;
}

if (sdb_check_color($backgroundcolor) === FALSE ) {
    add_settings_error('background_color', 'background_color_error', 'Invalid Background Color!', 'error'); // $setting, $code, $message, $type
    $valid_fields['sdb_setting_page_background']  = $options['sdb_setting_page_background'];
} else {
    $valid_fields['sdb_setting_page_background'] = $backgroundcolor;
}

if (sdb_check_color($textcolor) === FALSE ) {
    // Set the error message
    add_settings_error('text_color', 'text_color_error', 'Invalid Text Color!', 'error'); // $setting, $code, $message, $type
    // Get the previous valid value
    $valid_fields['sdb_setting_page_text_color']  = $options['sdb_setting_page_text_color'];
} else {
    $valid_fields['sdb_setting_page_text_color'] = $textcolor;
}

return apply_filters('sdb_settings_sanitize', $valid_fields, $fields);
}

//check color
function sdb_check_color($value)
{

	if (preg_match('/^#[a-f0-9]{6}$/i', $value)) { // if user insert a HEX color with #     
		return true;
	}

	return false;
}

//check allowed text input
function sdb_check_text($value)
{

	if (preg_match('/^[a-zA-Z-,>%: ]*$/', $value)) { // if user insert a HEX color with #     
		return true;
	}

	return false;
}

//setup restore function
add_action('init', 'sdb_restore_defaults');
function sdb_restore_defaults()
{
	//restore default
	if (isset($_POST['reset_form'])) {
		if (!isset($_POST['nonce_sdb_reset']) || !wp_verify_nonce($_POST['nonce_sdb_reset'], plugin_basename(__FILE__))) {
			die("Failed to verify Nonce");
		} else {
			sdb_defaults();
		}
	}
}
function sdb_defaults()
	{
		$defaults = array(
			'sdb_setting_page_default_badge' => '1',
			'sdb_setting_page_productpage_badge' => '1',
			'sdb_setting_page_text_afternumber' => '% off',
			'sdb_setting_page_background' => '#D9534F',
			'sdb_setting_page_text_color' => '#ffffff'
		);
		$setdefault = wp_parse_args(update_option('sdb_setting_page_settings', $defaults), $defaults);

		echo '<div class="notice notice-success is-dismissible">
	 <p>Settings has been restored!</p>
 </div>';
	}

?>