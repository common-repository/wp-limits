<?php

/*
 * Plugin Name: Wp Limits
 * Description: Update the WordPress default memory limit , time and max upload file size.
 * Version: 1.0
 * Author: WP virtuoso
 */

class wpl_change_limits {

	var $wpl_min_memory_limit = 32;
	var $wpl_min_time_limit= 120;
	var $wpl_min_upload_limit = 10;
	var $wpl_install_limit = 64;

	var $wpl_error = null;
  var $wpl_page_name = 'wp_define_limits';

	var $wpl_errors_mem_limit = array(
		'unable' => 'wpl_error: Memory limit cannot be changed. Please speak to your web host about "changing the default php memory limit".',
		'minimum' => 'Please set the memory limit to at least %limit%Mb.',
	);
var $wpl_errors_time_limit = array(
	'unable' => 'wpl_error: Time limit cannot be changed. Please speak to your web host about "changing the default php memory limit".',
	'minimum' => 'Please set the Time limit to at least 90 seconds.',
);

var $wpl_errors_upload_limit = array(
	'unable' => 'wpl_error: Max Upload File Size limit cannot be changed. Please speak to your web host about "changing the default php memory limit".',
	'minimum' => 'Please set the maximum upload file size limit to at least 20MB.',
);


	function wpl_init() {
		//set limit
		$this->wpl_set_limit();
		$this->wpl_set_time_limit();
		$this->wpl_set_upload_max_filesize();
		//redirect user?
		if($redirect = get_option('change_memory_limit_redirect')) {
			delete_option('change_memory_limit_redirect');
			header("Location: " . $redirect);
			exit();
		}
		//activation hook
		register_activation_hook(__FILE__, array(&$this, 'wp_limits_install'));
		//add menu hook
     add_action('admin_menu', array(&$this, 'wpl_admin_menu'));
		//add other hooks?
		if(isset($_GET['page']) && $_GET['page'] == $this->wpl_page_name) {
			add_action('admin_init', array(&$this, 'wp_limits_admin_logic'));
			add_action('admin_notices', array(&$this, 'wpl_admin_wpl_error'));
		}
		add_filter( 'upload_size_limit', array( $this, 'wpl_set_upload_max_filesize' ));

	}

	 function wpl_get_memory_usage($decimal=2) {
		//set vars
		$usage = null;
		//get usage?
		if(function_exists('memory_get_usage')) {
			$usage = memory_get_usage();
			$usage = $usage / (1024 * 1024);
			$usage = number_format($usage, $decimal, '.', '');
		}
		//return
		return $usage;

}
  function wpl_get_assigned_mem_limit(){
    $mem_limit= ini_get('memory_limit');
    return $mem_limit;

}

	function wpl_get_limit() {
		//set vars
		$limit = null;
		//get limit?
		if(function_exists('ini_get')) {
			$limit = (int) ini_get('memory_limit');
		}
		//return
		return $limit ? $limit : null;
	}

  function wpl_set_limit($limit=null) {
		//anything to process?
		if(!function_exists('ini_set')) {
			$this->wpl_error = $this->wpl_errors_mem_limit['unable'];
			return;
		}

		//set vars
		$old = $this->wpl_get_limit();
		$limit = (int) $limit ? $limit : get_option('change_memory');
		$is_admin = (bool) (isset($_GET['page']) && $_GET['page'] == $this->wpl_page_name);
		//update limit?
		if($limit > 0 && $old != $limit && ($is_admin || $limit > $old)) {
			//change setting
			@ini_set('memory_limit', $limit . 'M');
			//check new limit
			$new = $this->wpl_get_limit();
			//did it work?
			if(!$new || $new == $old) {
				$this->wpl_error = $this->wpl_errors_mem_limit['unable'];
			}
		}
	}

//Time limit

function wpl_current_time_limit(){
	$time_limit=null;
	if(function_exists('ini_get')) {
	$time_limit= (int) ini_get('max_execution_time');
}
    return $time_limit;
}
function wpl_set_time_limit(){
	//get time limit?
	if(!function_exists('ini_set')) {
		$this->wpl_error = $this->$erros_time_limit['unable'];
		return;
	}
	$old_time_limit = $this->wpl_current_time_limit();
	$time_limit = (int) $time_limit ? $time_limit : get_option('change_time_limit');
	$is_admin = (bool) (isset($_GET['page']) && $_GET['page'] == $this->wpl_page_name);

	if($time_limit > 0 && $old_time_limit != $time_limit && ($is_admin || $time_limit > $old_time_limit)) {
		//change setting
		@ini_set('max_execution_time', $time_limit);
		//check new limit
		$new_time_limit = $this->wpl_current_time_limit();
		//did it work?
		if(!$new_time_limit || $new_time_limit == $old_time_limit) {
			$this->wpl_error = $this->wpl_errors_time_limit['unable'];
		}
	}
}
//upload max filesize
function wpl_current_upload_max_fileszie(){
$upload_limit= get_option('change_upload_limit');
if(empty($upload_limit)){
	if(function_exists('ini_get')){
$upload_limit =	ini_get('upload_max_filesize');
}
}
return $upload_limit;
}


function wpl_set_upload_max_filesize(){
	//get time limit?
	if(!function_exists('ini_set')) {
		$this->wpl_error = $this->$erros_upload_limit['unable'];
		return;
	}
  $old_upload_limit = $this->wpl_current_upload_max_fileszie();
	$upload_limit = (int) $upload_limit ? $upload_limit : get_option('change_upload_limit');
	$is_admin = (bool) (isset($_GET['page']) && $_GET['page'] == $this->wpl_page_name);

	$upload_limit = $upload_limit *( 1024 *1024) ;

		//did it work?
		if(!$upload_limit || $upload_limit == $old_upload_limit) {
			$this->wpl_error = $this->wpl_errors_upload_limit['unable'];
}
	return $upload_limit;

}

	function wp_limits_install() {
		//set initial limit?
		if(!get_option('change_memory')) {
			$limit = define('WP_MEMORY_LIMIT') ? WP_MEMORY_LIMIT : 32;
			$limit = $limit > $this->wpl_install_limit ? $limit : $this->wpl_install_limit;
			update_option('memory_limit', $limit);
		}
		//set redirect url

if(!get_option('change_time_limit')){
	update_option('time_limit', $time_limit);

}
if(!get_option('change_upload_limit')){
	update_option('upload_limit', $upload_limit);

}
update_option('change_memory_limit_redirect', 'admin.php?page=' .$this->wpl_page_name);

}
	function wp_limits_admin_logic() {
		//update limit?
		if(isset($_POST['process']) && $_POST['process'] == $this->wpl_page_name) {
			//get limit
			$limit = (int) $_POST['memory_limit'];
			//valid update?
			if($limit < $this->wpl_min_memory_limit) {
				$this->wpl_error = str_replace('%limit%', $this->wpl_min_memory_limit, $this->wpl_errors['minimum']);
				return;
			}
			//update options
			update_option('change_memory', $limit);
			//set & check limit
			$this->wpl_set_limit($limit);
		}

		if(isset($_POST['process_time_limit']) && $_POST['process_time_limit'] == $this->wpl_page_name) {
			$time_limit = (int) $_POST['time_limit'];
			//valid update?
			if($time_limit < $this->wpl_min_time_limit) {
				$this->wpl_error = str_replace('%time_limit%', $this->wpl_min_time_limit, $this->wpl_errors['minimum']);
				return;
			}
			//update options
			update_option('change_time_limit', $time_limit);
			//set & check time limit
			$this->wpl_set_time_limit($time_limit);
			}

			if(isset($_POST['process_upload_limit']) && $_POST['process_upload_limit'] == $this->wpl_page_name) {
				$upload_limit = (int) $_POST['upload_limit'];
				//valid update?
				if($upload_limit < $this->wpl_min_upload_limit) {
					$this->wpl_error = str_replace('%upload_limit%', $this->wpl_min_upload_limit, $this->wpl_errors['minimum']);
					return;
				}
				//update options
				update_option('change_upload_limit', $upload_limit);
				//set & check upload filesize
				$this->wpl_set_upload_max_filesize($upload_limit);
				}
	}

	function wpl_admin_wpl_error() {
		if($this->wpl_error) {
			echo '<div class="wpl_error"><p>' . $this->wpl_error . '</p></div>';
		}
	}

  function wpl_admin_menu() {
      add_menu_page('WP Limits','WP Limits','manage_options', 'wp_define_limits', array(&$this,'wpl_admin_page') , plugin_dir_url(__FILE__) . 'images/ico.png');
      //add_options_page('Memory limit', 'Memory limit', 'manage_options', $this->wpl_page_name, array(&$this, 'admin_page'));
  }

	function wpl_admin_page() {
		//display form
		echo '<div class="wrap-wp-limit">' . "\n";
		echo '<h2 class="title">Change WordPress limits</h2>' . "\n";
		echo '<p class="description">This plugin allows you to increase the memory limit, time limit and max upload filesize without editing any WordPress files.</p>' . "\n";
		echo '<form class="wp-limit-form" method="post" action="admin.php?page=' . $this->wpl_page_name . '">' . "\n";
		echo '<input type="hidden" name="process" value="wp_define_limits"/>' . "\n";
		echo '<br />' . "\n";
		echo '<p class="labels"><b>Add Memory Limit</b> <input type="text" name="memory_limit" size="10" value="' . get_option('change_memory') . '" /> MB &nbsp; </p>' . "\n";
		echo '<br />' . "\n";
		echo '<input type="hidden" name="process_time_limit" value="wp_define_limits"/>' . "\n";
		echo '<p class="labels"><b>Add Time Limit</b> <input type="text" name="time_limit" size="10" value="' . get_option('change_time_limit') . '" /> Seconds &nbsp; </p>' . "\n";
		echo '<br />' . "\n";
		echo '<input type="hidden" name="process_upload_limit" value="wp_define_limits"/>' . "\n";
		echo '<p class="labels"><b>Add upload Maximum FileSize  </b> <input type="text" name="upload_limit" size="10" value="' . get_option('change_upload_limit') . '" /> MB &nbsp; </p>' . "\n";
		echo '<br />' . "\n";
		echo'<input class="wp-limit-submit" type="submit" value="Update" />';
	  echo '</form>' . "\n";
		echo '<div class="main-notice">';
		echo '<p class="notices">Your memory usage is approximately ' . ($this->wpl_get_memory_usage() ? $this->wpl_get_memory_usage(1) . 'MB' : 'unknown') . '.</p>' . "\n";
		echo '<p class="notices">Your current Memory Limit is ' . ($this->wpl_get_assigned_mem_limit() ? $this->wpl_get_assigned_mem_limit(1) . 'B' : 'unknown') . '.</p>' . "\n";
		echo '<p class="notices">Your current Time Limit is ' . ($this->wpl_current_time_limit() ? $this->wpl_current_time_limit(1) . 'seconds' : 'unknown') . '.</p>' . "\n";
		echo '<p class="notices">Your current Upload Maximum File Size is ' . ($this->wpl_current_upload_max_fileszie() ? $this->wpl_current_upload_max_fileszie(1) . 'MB' : 'unknown') . '.</p>' . "\n";
		echo '</div>' . "\n";
		echo'</div>';

	}


}

//load it!
$change_values = new wpl_change_limits();
$change_values->wpl_init();

function wpl_enque_styles() {
wp_register_style('wp-limit',plugin_dir_url(__FILE__) . 'styles/wp-limit.css');
wp_enqueue_style('wp-limit');

}
add_action( 'admin_init', 'wpl_enque_styles' );
//enqueu stylings
