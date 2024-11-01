<?php
/*
Plugin Name: WP-Vote
Version: 1.0
Plugin URI: http://www.yabama.com
Description: Adds a button to your articles to offer and vote on Yabama.com.
Author: Yabama
Author URI: http://www.yabama.com
Modified version of the WP-Vote Plugin by
yabama
http://www.yabama.com
*/
$message = "";

if (!function_exists('smpligg_request_handler')) {
    function smpligg_request_handler() {
        global $message;

        if ($_POST['smpligg_action'] == "update options") {
            $smpligg_align_v = $_POST['smpligg_align_sl'];

    		if(get_option("smpligg_box_align")) {
    			update_option("smpligg_box_align", $smpligg_align_v);
    		} else {
    			add_option("smpligg_box_align", $smpligg_align_v);
    		}

            $message = '<br clear="all" /> <div id="message" class="updated fade"><p><strong>Options saved. </strong></p></div>';
        }
    }
}

if(!function_exists('smpligg_add_menu')) {
    function smpligg_add_menu () {
        add_options_page("Options Vote", "Options Vote", 8, basename(__FILE__), "smpligg_displayOptions");
    }
}

if (!function_exists('smpligg_displayOptions')) {
    function smpligg_displayOptions() {

        global $message;
        echo $message;

		print('<div class="wrap">');
		print('<h2>Options Vote</h2>');

        print ('<form name="smpligg_form" action="'. get_bloginfo("wpurl") . '/wp-admin/options-general.php?page=wp-vote.php' .'" method="post">');
?>

		<p>Placement of the vote button:
        <select name="smpligg_align_sl" id="smpligg_align_sl">
			<option value="Top Left"   <?php if (get_option("smpligg_box_align") == "Top Left") echo " selected"; ?> >Top Left</option>
			<option value="Top Right"   <?php if (get_option("smpligg_box_align") == "Top Right") echo " selected"; ?> >Top Right</option>
			<option value="Bottom Left"  <?php if (get_option("smpligg_box_align") == "Bottom Left") echo " selected"; ?> >Bottom Left</option>
			<option value="Bottom Right"  <?php if (get_option("smpligg_box_align") == "Bottom Right") echo " selected"; ?> >Bottom Right</option>
			<option value="Bottom Center"  <?php if (get_option("smpligg_box_align") == "None") echo " selected"; ?> >Bottom Center</option>
		</select><br /><br /> </p>

<?php
		print ('<p><input type="submit" value="Save &raquo;"></p>');
		print ('<input type="hidden" name="smpligg_action" value="update options" />');
		print('</form>');


    }
}


if (!function_exists('smpligg_votehtml')) {
	function smpligg_votehtml($float) {
		global $wp_query;
		$post = $wp_query->post;
		$permalink = get_permalink($post->ID);
        $title = urlencode($post->post_title);
if ($postid<1) $postid = get_the_ID();
	if ($images = get_children(array(
		'post_parent' => $postid,
		'post_type' => 'attachment',
		'numberposts' => 1,
		'post_mime_type' => 'image',)))
		foreach($images as $image) {
			$attachment=wp_get_attachment_image_src($image->ID, $size);
		}

		$votehtml = <<<CODE

    <span style="margin: 4px 6px 0px 0px; float: $float;">
<div align=$float>
	<script type="text/javascript">
	submit_url = "$permalink";
  images_url = "$attachment[0]";
	</script>
    <script type="text/javascript" src="http://www.yabama.com/v/evb/button.php"></script>
</div>
	</span>
CODE;
	return  $votehtml;
	}
}


if (!function_exists('smpligg_addbutton')) {
	function smpligg_addbutton($content) {

		if ( !is_feed() && !is_page() && !is_archive() && !is_search() && !is_404() ) {
    		if(! preg_match('|<!--vote-->|', $content)) {
    		    $smpligg_align = get_option("smpligg_box_align");
    		    if ($smpligg_align) {
                    switch ($smpligg_align) {
                        case "Top Left":
        		              return smpligg_votehtml("left").$content;
                              break;
                        case "Top Right":
        		              return smpligg_votehtml("Right").$content;
                              break;
                        case "Bottom Left":
        		              return $content.smpligg_votehtml("left");
                              break;
                        case "Bottom Right":
        		              return $content.smpligg_votehtml("right");
                              break;
                        case "Bottom Center":
        		              return $content.smpligg_votehtml("center");
                              break;

                        default:
        		              return smpligg_votehtml("left").$content;
                              break;
                    }
                } else {
        		      return smpligg_votehtml("left").$content;
                }

    		} else {
                  return str_replace('<!--vote-->', smpligg_votehtml(""), $content);
            }
        } else {
			return $content;
        }
	}
}

if (!function_exists('show_vote')) {
	function show_vote($float = "left") {
        global $post;
		$permalink = get_permalink($post->ID);
		echo <<<CODE

    <span style="margin: 0px 6px 0px 0px; float: $float;">

	<script type="text/javascript">
	submit_url = "$permalink";
  images_url = "$attachment[0]";
	</script>
    <script type="text/javascript" src="http://www.yabama.com/v/evb/button.php"></script>
	</span>
CODE;
    }
}

add_filter('the_content', 'smpligg_addbutton', 999);
add_action('admin_menu', 'smpligg_add_menu');
add_action('init', 'smpligg_request_handler');

?>
