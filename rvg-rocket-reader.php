<?php
$rr_version      = '1.4.1';
$rr_release_date = '04/20/2014';
$rr_use_popup    = 'N';
/**
 * @package Rocket Reader
 * @version 1.4.1
 */
 
/*
Plugin Name: Rocket Reader
Plugin URI: http://cagewebdev.com/rocket-reader/
Description: Adds a control to read the text of posts and pages using a speed reading technique
Author: CAGE Web Design | Rolf van Gelder, Eindhoven, The Netherlands
Version: 1.4.1
Author URI: http://cagewebdev.com
*/

/*****************************************************************************

    ADD THE LANGUAGE SUPPORT (LOCALIZATION)

******************************************************************************/
function rr_action_init()
{   // TEXT DOMAIN
    load_plugin_textdomain('rocket-reader-speed-reader', false, dirname(plugin_basename(__FILE__)).'/languages');
}
// INIT HOOK
add_action('init', 'rr_action_init');


/********************************************************************************************

	ADD THE OPTIONS ITEM TO THE SETTINGS MENU

*********************************************************************************************/
function rr_admin_menu()
{	
	if (function_exists('add_options_page'))
	{	add_options_page(__('Rocket Reader Opts','rocket-reader-speed-reader'), __('Rocket Reader Opts','rocket-reader-speed-reader'), 'manage_options', 'rr_admin', 'rr_options_page');
    }
} // rrr_admin_menu()
add_action( 'admin_menu', 'rr_admin_menu' );


/********************************************************************************************

	CREATE THE OPTIONS PAGE

*********************************************************************************************/
function rr_options_page()
{
	global $rr_version, $rr_release_date, $wpdb, $rr_use_popup;
?>
<style type="text/css">
#rr_options_form {
	margin: 40px;
}
</style>
<?php
	if (isset($_POST['action']) && $_POST['action']=='save_options')
	{
		$usepopup = 'N';
		if(isset($_REQUEST['rr_use_popup'])) $usepopup = $_REQUEST['rr_use_popup'];
		update_option('rr_wpm', $_REQUEST['rr_wpm']);
		update_option('rr_use_popup', $usepopup);
		update_option('rr_cont_bgcolor', $_REQUEST['rr_cont_bgcolor']);
		update_option('rr_cont_bordercolor', $_REQUEST['rr_cont_bordercolor']);		
		update_option('rr_textcolor', $_REQUEST['rr_textcolor']);
		update_option('rr_bgcolor', $_REQUEST['rr_bgcolor']);
		update_option('rr_bordercolor', $_REQUEST['rr_bordercolor']);		
		update_option('rr_fpc', $_REQUEST['rr_fpc']);
		echo "<div class='updated'><p><strong>".__('Rocket Reader OPTIONS UPDATED!','rocket-reader-speed-reader')."</strong></p></div>";
	}
	else if (isset($_POST['action']) && ($_POST['action']=='show_all' || $_POST['action']=='hide_all'))
	{
		// FROM v1.2.2
		$sql = "
		SELECT `ID`
		FROM $wpdb->posts
		WHERE (`post_type` = 'page' OR `post_type` = 'post')
		AND `post_status` = 'publish'
		";
		$results = $wpdb -> get_results($sql);
		for ($i=0; $i<count($results); $i++)
		{
			// DELETE DEPRECIATED SETTING (from v1.2.2)
			delete_post_meta($results[$i]->ID, 'disable_rocket_reader');
			if($_POST['action']=='show_all')
			{	update_post_meta($results[$i]->ID, 'enable_rocket_reader', 'Y');
			}
			else
			{	delete_post_meta($results[$i]->ID, 'enable_rocket_reader');
			}
		}
		if($_POST['action']=='show_all')
			$msg = 'The Rocket Reader has been ADDED to ALL posts and pages';
		else
			$msg = 'The Rocket Reader has been DELETED from ALL posts and pages';
?>
<script type="text/javascript">
alert('<?php echo $msg;?>');
</script>
<?php
	}

	$rr_wpm = get_option('rr_wpm');
	if(!$rr_wpm) $rr_wpm = '300';

	$rr_use_popup = get_option('rr_use_popup');
	if(!$rr_use_popup) $rr_use_popup = 'N';

	$rr_cont_bgcolor = get_option('rr_cont_bgcolor');
	if(!$rr_cont_bgcolor) $rr_cont_bgcolor = '#E2E2E2';

	$rr_cont_bordercolor = get_option('rr_cont_bordercolor');
	if(!$rr_cont_bordercolor) $rr_cont_bordercolor = '#333';
	
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
  <em><strong>
  <?php _e('A Speed Reading Plugin: read the content of a web page / blog up to 80 percent faster','rocket-reader-speed-reader')?>
  </strong></em>
  </p>
  <p>
    <?php _e('Author','rocket-reader-speed-reader')?>
    : <strong>Rolf van Gelder - CAGE Web Design, Eindhoven,
    <?php _e('The Netherlands','rocket-reader-speed-reader')?>
    </strong><br>
    Website: <a href="http://cagewebdev.com" target="_blank">http://cagewebdev.com</a><br />
    <?php _e('Plugin page','rocket-reader-speed-reader')?>
    : <a href="http://cagewebdev.com/rocket-reader/" target="_blank">http://cagewebdev.com/rocket-reader/</a><br />
    <?php _e('Download page','rocket-reader-speed-reader')?>
    : <a href="http://wordpress.org/plugins/rvg-rocket-reader/" target="_blank">http://wordpress.org/plugins/rvg-rocket-reader/</a> </p>
  <br />
  <hr />
  <br />
  <h2>
    <?php _e('Rocket Reader Options','rocket-reader-speed-reader')?>
    :</h2>
  <form action="" method="post" name="options_form">
    <input name="action" type="hidden" value="save_options" />
    <label for="rr_wpm">
      <?php _e('Initial <strong>Number Of Words Per Minute</strong> (will be overridden by the user\'s cookie, if set)','rocket-reader-speed-reader')?>
      :</label>
    <br />
    <input type="text" name="rr_wpm" id="rr_wpm" size="8" value="<?php echo $rr_wpm; ?>" />
    <br />
    <br />
    <label for="rr_wpm">
      <?php _e('Use a <strong>Popup Window</strong> for displaying the animated text','rocket-reader-speed-reader')?>
      :</label>
    <br />
    <input name="rr_use_popup" id="rr_use_popup" type="checkbox" value="Y" />
    <br />
    <br />
    <hr />
    <br />
    <strong>
    <?php _e('The following colors should be in "#FFF" or "#FFFFFF" format!','rocket-reader-speed-reader')?>
    </strong><br />
    <br />
    <label for="rr_cont_bgcolor"><strong>
      <?php _e('Background color container','rocket-reader-speed-reader')?>
      :</strong></label>
    <br />
    <input type="text" name="rr_cont_bgcolor" id="rr_cont_bgcolor" size="8" value="<?php echo $rr_cont_bgcolor; ?>" />
    <br />
    <br />
    <label for="rr_cont_bordercolor"><strong>
      <?php _e('Border color container','rocket-reader-speed-reader')?>
      :</strong></label>
    <br />
    <input type="text" name="rr_cont_bordercolor" id="rr_cont_bordercolor" size="8" value="<?php echo $rr_cont_bordercolor; ?>" />
    <br />
    <br />
    <label for="rr_textcolor"><strong>
      <?php _e('Text color','rocket-reader-speed-reader')?>
      :</strong></label>
    <br />
    <input type="text" name="rr_textcolor" id="rr_textcolor" size="8" value="<?php echo $rr_textcolor; ?>" />
    <br />
    <br />
    <label for="rr_bgcolor"><strong>
      <?php _e('Background color','rocket-reader-speed-reader')?>
      :</strong></label>
    <br />
    <input type="text" name="rr_bgcolor" id="rr_bgcolor" size="8" value="<?php echo $rr_bgcolor; ?>" />
    <br />
    <br />
    <label for="rr_bordercolor"><strong>
      <?php _e('Border color','rocket-reader-speed-reader')?>
      :</strong></label>
    <br />
    <input type="text" name="rr_bordercolor" id="rr_bordercolor" size="8" value="<?php echo $rr_bordercolor; ?>" />
    <br />
    <br />
    <label for="rr_fpc"><strong>
      <?php _e('Focal point color','rocket-reader-speed-reader')?>
      :</strong></label>
    <br />
    <input type="text" name="rr_fpc" id="rr_fpc" size="8" value="<?php echo $rr_fpc; ?>" />
    <br />
    <br />
    <input name="btn_save" type="submit" value="<?php _e('save options','rocket-reader-speed-reader')?>" class="button-primary button-large" />
    <input name="btn_cancel" type="button" value="<?php _e('cancel','rocket-reader-speed-reader')?>" class="button" onclick="history.go(-1);" />
  </form>
  <?php
  if($rr_use_popup == 'Y')
  {
  ?>
  <script type="text/javascript">
  jQuery("#rr_use_popup").prop("checked", true);
  </script>
  <?php
  }
  ?>
  <br />
  <hr />
  <br />
  <?php
	// FROM v1.2.2
?>
  <strong>
  <?php _e('A quick way to ADD or DELETE the Rocket Reader to / from ALL posts / pages','rocket-reader-speed-reader')?>
  :</strong><br />
  (
  <?php _e('To show the Rocket Reader on a specific post / page: add a custom field named <strong>enable_rocket_reader</strong> and give it the value <strong>Y</strong>','rocket-reader-speed-reader')?>
  )<br />
  <br />
  <table border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td><form action="" method="post" name="show_all_form">
          <input name="action" type="hidden" value="show_all" />
          <input name="btn_save_sa" type="submit" value="<?php _e('ADD TO ALL','rocket-reader-speed-reader')?>" class="button-primary button-large" />
        </form></td>
      <td>&nbsp;&nbsp;&nbsp;</td>
      <td><form action="" method="post" name="hide_all_form">
          <input name="action" type="hidden" value="hide_all" />
          <input name="btn_save_ha" type="submit" value="<?php _e('DELETE FROM ALL','rocket-reader-speed-reader')?>" class="button-primary button-large" />
        </form></td>
    </tr>
  </table>
</div>
<?php
} // rr_options_page()


/********************************************************************************************

	LOAD JAVASCRIPTS AND STYLESHEETS

*********************************************************************************************/
function rr_load_scripts_styles()
{
	wp_enqueue_script('rr_rocket', plugin_dir_url(__FILE__) . 'js/rr_rocket.js',array('jquery'),'0.1',true);
	wp_enqueue_style ('rr', plugin_dir_url(__FILE__) . 'css/rr_rocket.css',false,'0.1','all');
} // rr_load_scripts_styles()


/********************************************************************************************

	ADD THE ROCKET READER CONTROL TO THE CONTENT

*********************************************************************************************/
function rr_add_control($content)
{
	global $wpdb;
	global $rr_version;
	global $rr_use_popup;

	// v1.1.4 - Check if this post / page is disabled by a custom field ('disable_rocket_reader')
	$sql = "
	SELECT `meta_value`
	FROM	$wpdb->postmeta
	WHERE	`post_id`  = ".get_the_ID()."
	AND		`meta_key` = 'enable_rocket_reader'
	";

	$results = $wpdb -> get_results($sql);
	if(!isset($results[0]) || $results[0]->meta_value != "Y") return $content;

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
	$ex = '';
	$return = $ex.'<script type="text/javascript"> var words'.get_the_ID().' = [';
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

	// v1.2.1
	if($rr_use_popup == 'Y')
	{
		$return .= '
<div align="right"><button type="button" id="rr_btn_play'.get_the_ID().'">ROCKET READER</button></div><br />
<div id="rr_wrapper'.get_the_ID().'" class="rr_wrapper_popup" postid="'.get_the_ID().'"></div>';
	}
	else
	{
		$return .= '
<div id="rr_wrapper'.get_the_ID().'" class="rr_wrapper" postid="'.get_the_ID().'"></div>';
	}

	$return .= '
<div id="rr_content'.get_the_ID().'"> '.$content.' </div>
	';
	
	return $return;
} // rr_add_content()


/********************************************************************************************

	ADD THE THE INITIAL SETTINGS FROM THE DATABASE TO THE HEADER

*********************************************************************************************/
function init_rocket_reader()
{
	global $rr_version, $rr_release_date, $rr_use_popup;
	
	$initWPM = get_option("rr_wpm");
	if(!$initWPM) $initWPM = 300;

	$rr_use_popup = get_option("rr_use_popup");
	if(!$rr_use_popup) $rr_use_popup = 'N';

	$initContBgcolor = get_option("rr_cont_bgcolor");
	if(!$initContBgcolor) $initContBgcolor = "#E2E2E2";
	
	$initContBordercolor = get_option("rr_cont_bordercolor");
	if(!$initContBordercolor) $initContBordercolor = "#333";	
	
	$initTextcolor = get_option("rr_textcolor");
	if(!$initTextcolor) $initTextcolor = "#000000";
	
	$initBgcolor = get_option("rr_bgcolor");
	if(!$initBgcolor) $initBgcolor = "#EFEFEF";
	
	$initBordercolor = get_option('rr_bordercolor');
	if(!$initBordercolor) $rr_bordercolor = '#000000';
	
	$initFPcolor = get_option("rr_fpc");
	if(!$initFPcolor) $initFPcolor = "#FF0000";

	$output = '
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
	';
	
	// PASS THE SETTINGS TO JAVASCRIPT
	$output .= '
<!-- START Rocket Reader v' . $rr_version . ' [' . $rr_release_date . '] | http://cagewebdev.com/rocket-reader | CAGE Web Design | Rolf van Gelder -->
<style>
.dlg-no-close .ui-dialog-titlebar-close {
	display: none;
}
.dlg-no-title .ui-dialog-titlebar {
	display: none;
}
</style>
<script type="text/javascript">
var rr_init_version          = "'.$rr_version.'";
var rr_init_WPM              = '.$initWPM.';
var rr_init_use_popup        = "'.$rr_use_popup.'";
// CONTAINER
var rr_init_cont_bgcolor     = "'.$initContBgcolor.'";
var rr_init_cont_bordercolor = "'.$initContBordercolor.'";
// READING PANEL
var rr_init_textcolor        = "'.$initTextcolor.'";
var rr_init_bgcolor          = "'.$initBgcolor.'";
var rr_init_bordercolor      = "'.$initBordercolor.'";
var rr_init_fp_color         = "'.$initFPcolor.'";
// READER TRANSLATIONS
var rr_speed                 = "'.__('speed','rocket-reader-speed-reader').'";
var rr_words                 = "'.__('words','rocket-reader-speed-reader').'";
var rr_minute                = "'.__('minute','rocket-reader-speed-reader').'";
var rr_bold_on_off           = "'.__('bold on/off','rocket-reader-speed-reader').'";
var rr_decrease_speed        = "'.__('decrease speed','rocket-reader-speed-reader').'";
var rr_increase_speed        = "'.__('increase speed','rocket-reader-speed-reader').'";
var rr_pause                 = "'.__('pause','rocket-reader-speed-reader').'";
var rr_resume                = "'.__('resume','rocket-reader-speed-reader').'";
var rr_close                 = "'.__('close','rocket-reader-speed-reader').'";
var rr_read_with_reader      = "'.__('Read this article with the Rocket Reader!','rocket-reader-speed-reader').'";
</script>
<!-- END Rocket Reader -->
';

  echo $output;
}

add_action('wp_enqueue_scripts','rr_load_scripts_styles');
add_action('wp_head','init_rocket_reader');
add_filter('the_content', 'rr_add_control' );
?>
