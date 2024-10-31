<?php
/*
Plugin Name: s0cial_submit
Plugin URI: http://wordpress.org/extend/plugins/s0cial-submit/
Description: include submit buttons for digg and reddit on each post
Author: Oliver C Dodd
Version: 1.0.2
Author URI: http://01001111.net
  
  Copyright (c) 2009 Oliver C Dodd - http://01001111.net
  
  Permission is hereby granted,free of charge,to any person obtaining a 
  copy of this software and associated documentation files (the "Software"),
  to deal in the Software without restriction,including without limitation
  the rights to use,copy,modify,merge,publish,distribute,sublicense,
  and/or sell copies of the Software,and to permit persons to whom the 
  Software is furnished to do so,subject to the following conditions:
  
  The above copyright notice and this permission notice shall be included in
  all copies or substantial portions of the Software.
  
  THE SOFTWARE IS PROVIDED "AS IS",WITHOUT WARRANTY OF ANY KIND,EXPRESS OR
  IMPLIED,INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL 
  THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,DAMAGES OR OTHER
  LIABILITY,WHETHER IN AN ACTION OF CONTRACT,TORT OR OTHERWISE,ARISING
  FROM,OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
  DEALINGS IN THE SOFTWARE.
*/
class s0cial_submit {
	/*-VARIABLES----------------------------------------------------------*/
	private $digg = true;
	private $dynamicDiggButtons = false;
	private $reddit = true;
	private $divClass = "s0cial_submit";
	
	const option_key = "s0cial_submit";
	
	/*-CONSTRUCT----------------------------------------------------------*/
	//public function __construct($u,$t="s0cial_submit",$d="")
	public function s0cial_submit($options) {
		foreach($options as $k => $v)
			@($this->$k = $v);
	}
	
	/*-GET OPTIONS--------------------------------------------------------*/
	public static function getOptions() {
		return !($options = get_option(s0cial_submit::option_key))
			? $options = array(
				'digg'			=> true,
				'dynamicDiggButtons'	=> false,
				'reddit'		=> true,
				'divClass'		=> "s0cial_submit")
			: $options;
	}
	
	/*-OUTPUT-------------------------------------------------------------*/
	public function content($the_content) {
		// only apply these links to posts
		if (is_page())
			return $the_content;
		//get title and permalink
		$u = get_permalink();
		$t = get_the_title();
		$uu = urlencode($u);
		$ut = urlencode($t);
		$l = "";
		if ($this->digg) {
			if ($this->dynamicDiggButtons) {
				$l = '<a class="DiggThisButton DiggCompact"></a>';
			} else {
				$img = "<img src='http://widgets.digg.com/img/button/diggThisCompact.png' alt='DiggThis' />";
				$l .= "<a class='DiggThisButton' href='http://digg.com/submit?url=$uu&title=$ut'>$img</a>";
			}
		}
		if ($this->reddit) {
			$img = "<img src='http://www.reddit.com/static/spreddit7.gif' alt='submit to reddit' />";
			$l .= "<a class='RedditButton' href='http://www.reddit.com/submit?url=$uu&title=$ut'>$img</a>";
		}
		return $the_content.($l ? "<div class='$this->divClass'>$l</div>" : "");
	}
	
	public function footer() {
		if ($this->digg && $this->dynamicDiggButtons) {
			echo '
			<script type="text/javascript">
				(function() {
				var s = document.createElement("SCRIPT");
				var s1 = document.getElementsByTagName("SCRIPT")[0];
				s.type = "text/javascript";
				s.async = true;
				s.src = "http://widgets.digg.com/buttons.js";
				s1.parentNode.insertBefore(s, s1);
				})();
			</script>';
		}
	}
	
	/*-INPUTS-------------------------------------------------------------*/
	public static function inputId($id) { return __CLASS__."-$id"; }
	
	public static function i_checkbox($id,$options) {
		$eid = self::inputId($id);
		echo "
		<input type='hidden' name='$eid' value='0' />
		<input type='checkbox' name='$eid' value='1' ".(
			$options[$id] ? "checked='checked'" : "")." />";
	}
	
	public static function i_text($id,$options) {
		$eid = self::inputId($id);
		echo "<input type='text' name='$eid' id='$eid' value='{$options[$id]}' />";
	}
	
	public static function i_submit() {
		$eid = self::inputId('submit');
		echo "<input type='submit' id='$eid' name='$eid' value='Update Options' />";
	}
}
/*-OPTIONS--------------------------------------------------------------------*/
function s0cial_submit_plugin_menu() {
	add_options_page('s0cial_submit', 's0cial_submit', 'manage_options', 's0cial_submit', 's0cial_submit_options');
}
function s0cial_submit_options()
{
	$options = s0cial_submit::getOptions();
	if($_POST['s0cial_submit-submit'])
	{
		$options = array(
			'digg'			=> $_POST['s0cial_submit-digg'],
			'dynamicDiggButtons'	=> $_POST['s0cial_submit-dynamicDiggButtons'],
			'reddit'		=> $_POST['s0cial_submit-reddit'],
			'divClass'		=> $_POST['s0cial_submit-divClass'],
		);
		update_option(s0cial_submit::option_key,$options);
	}
	?>
	<form method="POST">
	<p> Digg?: <?php s0cial_submit::i_checkbox('digg',$options); ?>
	</p>
	<p> Digg Javascript?: <?php s0cial_submit::i_checkbox('dynamicDiggButtons',$options); ?>
	</p>
	<p> Reddit?: <?php s0cial_submit::i_checkbox('reddit',$options); ?>
	</p>
	<p> Div Class: <?php s0cial_submit::i_text('divClass',$options); ?>
	</p>
	<p> <?php s0cial_submit::i_submit(); ?>
	</p>
	</form>
	<?php
}
/*-PLUGIN---------------------------------------------------------------------*/
function plugin_s0cial_submit_content($the_content) {
	$ss = new s0cial_submit(s0cial_submit::getOptions());
	return $ss->content($the_content);
}
function plugin_s0cial_submit_footer($the_content) {
	$ss = new s0cial_submit(s0cial_submit::getOptions());
	return $ss->footer();
}
add_filter("the_content","plugin_s0cial_submit_content");
add_filter("wp_footer","plugin_s0cial_submit_footer");
add_action("admin_menu", "s0cial_submit_plugin_menu");
?>
