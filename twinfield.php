<?php
/*
Plugin Name: Twinfield
Plugin URI: http://jjlammers.com/wordpress/twinfield
Description: A simple wordpress plugin to login to Twinfield
Version: 1.1
Author: J.J. Lammers
Author URI: http://jjlammers.com
License: GPL
*/
add_action('init','Twinfield');

/* Runs when plugin is activated */
register_activation_hook(__FILE__,'twf_install'); 
//WP_PLUGIN_URL .'/soapwid/'.$question.'.xml')
/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'twf_remove' );
/*
// use widgets_init action hook to execute custom function
add_action( 'widgets_init', 'twf_widgetexample_register_widgets' );

//register our widget
function twf_widgetexample_register_widgets() {
    register_widget( 'twf_info_widget' );
}
*/
function twf_session() {
	if (get_option('twf_debug') == "checked"){
		echo "---Start: Twinfield Debug---";
		echo "<br/>";
		echo get_option("twf_organisation_data");
		echo "<br/>";
		echo get_option("twf_company_data");
		echo "<br/>";
		echo "---End Twinfield Debug---";
	}
	else {
		//echo get_option('twf_debug');
	}	
}

function twf_install() {
/* Creates new database field */
	add_option("twf_organisation_data", '', '', 'yes');
	add_option("twf_debug", '', '', 'yes');
}

// Deletes the Twinfield option fields
function twf_remove() {
/*	$strSort = "ID";
        $wp_user_search = $wpdb->get_col( $wpdb->prepare("SELECT $wpdb->users.ID FROM $wpdb->users ORDER BY %s ASC", $strSort ));	
	//$wp_user_search = $wpdb->get_results("SELECT ID FROM $wpdb->users ORDER BY ID");
        //print_r($wp_user_search);
	foreach ( $wp_user_search as $userid ) {
		$user_id = (int) $userid->ID;
		delete_user_meta( $user_id, 'twf_usr_user', '' );
		delete_user_meta( $user_id, 'twf_usr_pwd', '' );
		delete_user_meta( $user_id, 'twf_usr_company', '' );
	} */
	delete_option('twf_organisation_data');
	delete_option('twf_debug');
}

if ( is_admin() ){
/* Call the html code */
	add_action('admin_menu', 'Twinfield_admin_menu');

	function Twinfield_admin_menu() {
		add_options_page('Twinfield', 'Twinfield', 'administrator', 'Twinfield', 'Twinfield_html_page');
	}
}

function Twinfield_html_page() {
	echo'<div>';
	echo'<h2>Twinfield Options</h2>';
	echo'<form method="post" action="options.php">';
	wp_nonce_field('update-options');

	echo'<table width="510">';
	echo'<tr valign="top">';
	echo'<th colspan="2" scope="row">Twinfield credentials</th>';
	echo'</tr>';
	echo'<tr>';
	echo'<td>Organisation:</td>';
	echo'<td width="406">';
		echo'<input name="twf_organisation_data" type="text" id="twf_organisation_data" value="'. get_option('twf_organisation_data') .'" />';
	echo'</td>';
	echo'</tr>';
	echo'<tr>';
	echo'<td>Show debug information:</td>';
	echo'<td width="406">';
		echo'<input name="twf_debug" type="checkbox" id="twf_debug" value="checked"'. get_option('twf_debug').'/>';
	echo'</td>';
	echo'</tr>';
	echo'</table>';

	echo'<input type="hidden" name="action" value="update" />';
	echo'<input type="hidden" name="page_options" value="twf_organisation_data, twf_debug" />';

	echo'<p>';
	echo '<input type="submit" value="Save Changes" />';
	echo '<input type="button" value="Validate" />';
	echo'</p>';

	echo'</form>';
	echo'</div>';
}

/* Add the post form to the user/profile edit page in the admin. */
add_action( 'show_user_profile', 'twf_user' );
add_action( 'edit_user_profile', 'twf_user' );
add_action( 'add_user_profile', 'twf_user' );

/* Function for displaying an extra form on the user edit page. */
function twf_user( $user ) {

    /* Get the current user's favorite post. */
    //get_user_meta( $user->ID, 'favorite_post', true );

	echo '<h3>Twinfield credentials</h3>';
	echo '<table class="form-table" width="510">';
	echo '<tbody>';
	echo '<tr>';
		echo '<th><label for="twf_usr_user">Username:</label></th>';
		echo '<td><input name="twf_usr_user" type="text" id="twf_usr_user" value="'. get_user_meta( $user->ID, 'twf_usr_user', true ) .'" /></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<th><label for="twf_usr_pwd">Password:</label></th>';
		$twf_pwd = twf_decode(get_user_meta( $user->ID, 'twf_usr_pwd', true ), 'TWF_' . get_option('twf_organisation_data').get_user_meta( $user->ID, 'twf_usr_user', true ));
		echo '<td><input name="twf_usr_pwd" type="password" id="twf_usr_pwd" value="'. $twf_pwd .'" /></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<th><label for="twf_organisation_data">Organisation:</label></th>';
		echo '<td>'. get_option('twf_organisation_data') . '</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<th><label for="twf_usr_company">Company code:</label></th>';
		echo '<td><input name="twf_usr_company" type="text" id="twf_usr_company" value="'. get_user_meta( $user->ID, 'twf_usr_company', true ) .'" /></td>';
	echo '</tr>';
	echo '</tbody>';
	echo '</table>';
}

/* Add the update function to the user update hooks. */
add_action( 'personal_options_update', 'twf_user_update' );
add_action( 'edit_user_profile_update', 'twf_user_update' );

/* Function for updating the user's favorite post. */
function twf_user_update( $user_id ) {

    /* Check if the current user has permission to edit the user. */
    if ( !current_user_can( 'edit_user', $user_id ) )
        return false;

    /* Update the user's  data with Twinfield Credentials */
    update_user_meta( $user_id, 'twf_usr_user', $_POST['twf_usr_user'] );
    update_user_meta( $user_id, 'twf_usr_pwd',  twf_encode($_POST['twf_usr_pwd'], 'TWF_' . get_option('twf_organisation_data').$_POST['twf_usr_user']));	
    update_user_meta( $user_id, 'twf_usr_company', $_POST['twf_usr_company'] );
}

function twf_encode($string, $key) {
    $key = sha1($key);
    $strLen = strlen($string);
    $keyLen = strlen($key);
    for ($i = 0; $i < $strLen; $i++) {
        $ordStr = ord(substr($string,$i,1));
        if ($j == $keyLen) { $j = 0; }
        $ordKey = ord(substr($key,$j,1));
        $j++;
        $hash .= strrev(base_convert(dechex($ordStr + $ordKey),16,36));
    }
    return $hash;
}

function twf_decode($string,$key) {	
	$key = sha1($key);
	$strLen = strlen($string);
	$keyLen = strlen($key);
	for ($i = 0; $i < $strLen; $i+=2) {
		$ordStr = hexdec(base_convert(strrev(substr($string,$i,2)),36,16));
		if ($j == $keyLen) { $j = 0; }
		$ordKey = ord(substr($key,$j,1));
		$j++;
		$hash .= chr($ordStr - $ordKey);
	}
	return $hash;
}

// use widgets_init action hook to execute custom function
add_action( 'widgets_init', 'twf_widget_register_widgets' );

//register our widget
function twf_widget_register_widgets() {
    register_widget( 'twf_sso_widget_class' );
}

class twf_sso_widget_class extends WP_Widget {

    //process the new widget
    function twf_sso_widget_class() {
        $widget_ops = array( 
			'classname' => 'twf_sso_widget_class', 
			'description' => 'Twinfield - Single Sign On.' 
			); 
        $this->WP_Widget( 'twf_sso_widget_class', 'Twinfield - Single Sign On', $widget_ops );
    }
 
    //display the widget
    function widget($args, $instance) {
        extract($args);        
		$user = wp_get_current_user();		
		$defaults = array( 'twf_user' => '', 'twf_pwd' => '', 'twf_company' => '' ); 
		$instance = wp_parse_args( (array) $instance, $defaults );
		if (! ($user->ID == 0)) { 
			if( (get_user_meta( $user->ID, 'twf_usr_user', true ) == '')) { //|| !(get_user_meta( $user->ID, 'twf_usr_password', true ) =='' ) || !(get_option('twf_organisation_data') =='' ) ) {
				echo '<p>Your Twinfield connection has not been set-up correctly.</p>';
			}
			else {
				echo $before_widget;
				
				echo $before_title . 'Twinfield' . $after_title;			
				$twf_user = get_user_meta( $user->ID, 'twf_usr_user', true );
				$twf_pwd = get_user_meta( $user->ID, 'twf_usr_pwd', true );
				$twf_company = get_user_meta( $user->ID, 'twf_usr_company', true );
				$url = home_url();
				echo '<form method="post" action="' . WP_PLUGIN_URL . '/twinfield/sso.php">';
					echo '<input type="hidden" name="user" value="'. $twf_user .'">';					
					echo '<input type="hidden" name="password" value="'. esc_attr($twf_pwd) .'">';
					echo '<input type="hidden" name="organisation" value="'. esc_attr(get_option('twf_organisation_data')) .'">';
					echo '<input type="hidden" name="returnurl" value="'. $url .'">';
					echo '<input type="hidden" name="company" value="'. $twf_company .'">';
					echo '<input class="post-edit-link" type="submit" value="Twinfield Login"/>';
					echo '<br/>';
				echo '</form>';
			}
			if(!empty($_GET['msg'])) {
				echo 'Your Twinfield session has been: ' . $_GET['msg'];
				echo '<br/>';
			}
			echo $after_widget;
		}
    }
}


?>