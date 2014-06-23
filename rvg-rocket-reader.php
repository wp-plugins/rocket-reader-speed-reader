<?php
$rr_version      = '1.1.4 (beta)';
$rr_release_date = '06/23/2014';
/**
 * @package Rocket Reader
 * @version 1.1.4
 */
 
/*
Plugin Name: Rocket Reader
Plugin URI: http://cagewebdev.com/rocket-reader/
Description: Adds a control to read the text of posts and pages using a speed reading technique
Author: CAGE Web Design | Rolf van Gelder, Eindhoven, The Netherlands
Version: 1.1.4
Author URI: http://cagewebdev.com
*/

/********************************************************************************************

	ADD THE OPTIONS ITEM TO THE SETTINGS MENU

*********************************************************************************************/
function rr_admin_menu()
{	
	if (function_exists('add_options_page'))
	{	add_options_page(__('Rocket Reader Opts'), __('Rocket Reader Opts'), 'manage_options', 'rr_admin', 'rr_options_page');
    }
} // rrr_admin_menu()
add_action( 'admin_menu', 'rr_admin_menu' );


/********************************************************************************************

	CREATE THE OPTIONS PAGE

*********************************************************************************************/
function rr_options_page()
{
	global $rr_version, $rr_release_date, $wpdb;
?>
<style type="text/css">
#rr_options_form {
	margin: 40px;
}
</style>
<?php
	if (isset($_POST['action']) && $_POST['action']=='save_options')
	{
		update_option('rr_wpm', $_REQUEST['rr_wpm']);
		update_option('rr_textcolor', $_REQUEST['rr_textcolor']);
		update_option('rr_bgcolor', $_REQUEST['rr_bgcolor']);
		update_option('rr_bordercolor', $_REQUEST['rr_bordercolor']);		
		update_option('rr_fpc', $_REQUEST['rr_fpc']);
		echo "<div class='updated'><p><strong>Rocket Reader OPTIONS UPDATED!</strong>";
	}

	$rr_wpm = get_option('rr_wpm');
	if(!$rr_wpm) $rr_wpm = '300';
	
	$rr_textcolor = get_option('rr_textcolor');
	if(!$rr_textcolor) $rr_textcolor = '#000000';
	
	$rr_bgcolor = get_option('rr_bgcolor');
	if(!$rr_bgcolor) $rr_bgcolor = '#EFEFEF';
	
	$rr_bordercolor = get_option('rr_bordercolor');
	if(!$rr_bordercolor) $rr_bordercolor = '#000000';	
		
	$rr_fpc = get_option('rr_fpc');
	if(!$rr_fpc) $rr_fpc = '#FF0000';
	?>
<style type="text/css">
#rr_options_form {
	margin: 40px;
}
</style>
<div id="rr_options_form">
  <p>
  <h1>Rocket Reader v<?php echo $rr_version; ?></h1>
  <em><strong>A Speed Reading Plugin: read the content of a web page / blog up to 80% faster!</strong></em>
  </p>
  <p>Author: <strong>Rolf van Gelder - CAGE Web Design, Eindhoven, The Netherlands</strong><br>
    Website: <a href="http://cagewebdev.com" target="_blank">http://cagewebdev.com</a><br />
    Plugin page: <a href="http://cagewebdev.com/rocket-reader/" target="_blank">http://cagewebdev.com/rocket-reader/</a><br />
    Download page: <a href="http://wordpress.org/plugins/rvg-rocket-reader/" target="_blank">http://wordpress.org/plugins/rvg-rocket-reader/</a> </p>
  <br />
  <hr />
  <br />
  <h2>Rocket Reader Options:</h2>
  <form action="" method="post" name="options_form">
    <input name="action" type="hidden" value="save_options" />
    <label for="rr_wpm">Initial <strong>Number Of Words Per Minute</strong> (will be overridden by the user's cookie, if set):</label>
    <br />
    <input type="text" name="rr_wpm" id="rr_wpm" size="8" value="<?php echo $rr_wpm; ?>" />
    <br />
    <br />
    <hr />
    <br />
    <strong>The following colors should be in "#FFF" or "#FFFFFF" format!</strong><br />
    <br />
    <label for="rr_textcolor"><strong>Text color:</strong></label>
    <br />
    <input type="text" name="rr_textcolor" id="rr_textcolor" size="8" value="<?php echo $rr_textcolor; ?>" />
    <br />
    <br />
    <label for="rr_bgcolor"><strong>Background color:</strong></label>
    <br />
    <input type="text" name="rr_bgcolor" id="rr_bgcolor" size="8" value="<?php echo $rr_bgcolor; ?>" />
    <br />
    <br />
    <label for="rr_bordercolor"><strong>Border color:</strong></label>
    <br />
    <input type="text" name="rr_bordercolor" id="rr_bordercolor" size="8" value="<?php echo $rr_bordercolor; ?>" />
    <br />
    <br />
    <label for="rr_fpc"><strong>Focal point color:</strong></label>
    <br />
    <input type="text" name="rr_fpc" id="rr_fpc" size="8" value="<?php echo $rr_fpc; ?>" />
    <br />
    <br />
    <input name="btn_save" type="submit" value="save options" class="button-primary button-large" />
    <input name="btn_cancel" type="button" value="cancel" class="button" onclick="history.go(-1);" />
  </form>
</div>
<?php
} // rr_options_page()


/********************************************************************************************

	LOAD JAVASCRIPTS AND STYLESHEETS

*********************************************************************************************/
function rr_load_scripts_styles()
{
	wp_enqueue_script('rr_rocket', plugin_dir_url(__FILE__) . 'rr_rocket.js',array('jquery'),'0.1',true);
	wp_enqueue_style ('rr', plugin_dir_url(__FILE__) . 'rr_rocket.css',false,'0.1','all');
	
} // rr_load_scripts_styles()


/********************************************************************************************

	ADD THE ROCKET READER CONTROL TO THE CONTENT

*********************************************************************************************/
function rr_add_control($content)
{
	global $wpdb;
	global $rr_version;

	// v1.1.4 - Check if this post / page is disabled by a custom field ('disable_rocket_reader')
	$sql = "
	SELECT `meta_value`
	FROM	$wpdb->postmeta
	WHERE	`post_id`  = ".get_the_ID()."
	AND		`meta_key` = 'disable_rocket_reader'
	";
	
	$results = $wpdb -> get_results($sql);
	if(isset($results[0]) && $results[0]->meta_value == "Y") return $content;

	// v1.1.4 - Index the 'clean' content from the post, straight from the database
	$sql = "
	SELECT	`post_content`, `post_type`
	FROM	$wpdb->posts
	WHERE	`ID` = ".get_the_ID()."
	";
	
	$results = $wpdb -> get_results($sql);
	
	$c = $results[0]->post_content;
	$t = $results[0]->post_type;
	
	// v1.1.4 - Only add the control to pages and posts
	if(!$c || ($t != 'page' && $t != 'post')) return $content;
	
	$c = strip_tags($c);
	$c = str_replace("&nbsp;",  "",    $c);	// space
	$c = str_replace("&#8211;", "xyx", $c);	// dash
	$c = str_replace("&#8216;", "",    $c);	// single quote
	$c = str_replace("&#8217;", "",    $c);	// single quote
	$c = str_replace("&#8220;", "",    $c);	// double quote
	$c = str_replace("&#8221;", "",    $c);	// double quote
	$c = str_replace("&#8230;", "yxy", $c);	// ...
	$c = str_replace("&#8243;", "",    $c);	// double quote

	// SPLIT THE POST CONTENT INTO (UNICODE) WORDS AND FILL A JAVASCRIPT ARRAY WITH THEM
	// (HAVE TO DO THIS BECAUSE JAVASCRIPT DOESN'T SUPPORT UNICODE REGEX YET...)
	$return = '<script type="text/javascript">
	var words'.get_the_ID().' = [';
	preg_match_all("/[\p{L}\p{M}\{0-9}\{-|?|!|%}]+/u", $c, $result, PREG_PATTERN_ORDER);
	for ($i = 0; $i < count($result[0]); $i++)
	{	$w = $result[0][$i];
		if($i) $return .= ',';
		$w = str_replace("xyx", '-', $w);
		$w = str_replace("yxy", "...", $w);
		$w = str_replace("amp", "&", $w);
		$w = str_replace("lt", "<", $w);
		$w = str_replace("gt", ">", $w);
		$return .= '"'.$w.'"';
	}
	$return .= ']</script>';

	$return .= '
<div id="rr_wrapper'.get_the_ID().'" class="rr_wrapper">
	<div id="rr_credits'.get_the_ID().'" class="rr_credits">Rocket Reader v'.$rr_version.', a plugin by Rolf van Gelder (<a href="http://cagewebdev.com/rocket-reader/" target="_blank">http://cagewebdev.com/rocket-reader/</a>)</div>
	<div id="rr_reading_pane'.get_the_ID().'" class="rr_reading_pane">
		<div id="rr_word'.get_the_ID().'" class="rr_word"></div>
	</div>
	<div id="rr_button_container">
		<div id="rr_btn_play'.get_the_ID().'" class="rr_btn_play">
			<button onclick="rr_play('.get_the_ID().');">Read with ROCKET READER!</button>
		</div>	
		<div id="rr_playing_controls'.get_the_ID().'" class="rr_playing_controls">	
			<div id="rr_btn_close'.get_the_ID().'" class="rr_button">
			  <button onclick="rr_close();" title="close">&times;</button>
			</div>
			<div id="rr_btn_pause'.get_the_ID().'" class="rr_button">
			  <button onclick="rr_pause();" title="pause">||</button>
			</div>
			<div id="rr_btn_resume'.get_the_ID().'" class="rr_button">
			  <button onclick="rr_resume('.get_the_ID().');" title="resume">&gt;</button>
			</div>
			<div id="rr_btn_plus'.get_the_ID().'" class="rr_button">
			  <button onclick="rr_plus();" title="increase speed">+</button>
			</div>
			<div id="rr_btn_minus'.get_the_ID().'" class="rr_button">
			  <button onclick="rr_minus();" title="decrease speed">-</button>
			</div>
			<div id="rr_btn_bold'.get_the_ID().'" class="rr_button">
			  <button onclick="rr_font_weight();" title="bold on/off">b</button>
			</div>			
		</div>	
	</div>
	<div id="rr_delay'.get_the_ID().'"></div>
	<br clear="all">	
</div>
<div id="rr_content'.get_the_ID().'">  
  '.$content.' 
</div>';
	return $return;
} // rr_add_content()


/********************************************************************************************

	ADD THE THE INITIAL SETTINGS FROM THE DATABASE TO THE HEADER

*********************************************************************************************/
function init_rocket_reader()
{
	global $rr_version, $rr_release_date;
	
	$initWPM = get_option("rr_wpm");
	if(!$initWPM) $initWPM = 300;
	
	$initTextcolor = get_option("rr_textcolor");
	if(!$initTextcolor) $initTextcolor = "#000000";
	
	$initBgcolor = get_option("rr_bgcolor");
	if(!$initBgcolor) $initBgcolor = "#EFEFEF";
	
	$initBordercolor = get_option('rr_bordercolor');
	if(!$initBordercolor) $rr_bordercolor = '#000000';
	
	$initFPcolor = get_option("rr_fpc");
	if(!$initFPcolor) $initFPcolor = "#FF0000";
	
	$output = '
<!-- START Rocket Reader v' . $rr_version . ' [' . $rr_release_date . '] | http://cagewebdev.com/rocket-reader | CAGE Web Design | Rolf van Gelder -->
<script type="text/javascript">
var rr_init_WPM = '.$initWPM.';
var rr_init_textcolor = "'.$initTextcolor.'";
var rr_init_bgcolor = "'.$initBgcolor.'";
var rr_init_bordercolor = "'.$initBordercolor.'";
var rr_init_fp_color = "'.$initFPcolor.'";
</script>
<!-- END Rocket Reader -->

';

  echo $output;
}

add_action('wp_enqueue_scripts','rr_load_scripts_styles');
add_filter('the_content', 'rr_add_control' );
add_action('wp_head','init_rocket_reader');
?>
