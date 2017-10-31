<?php /*
  Plugin Name:  Generate Post Thumbnails
  Plugin URI:   http://wordpress.shaldybina.com/plugins/generate-post-thumbnails/
  Description:  Tool for mass generation of Wordpress posts thumbnails using the post images.
  Version:      0.8
  Author:       Maria Shaldybina
  Author URI:   http://shaldybina.com/
*/
/*  Copyright 2010  Maria I Shaldybina

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
*/
class GeneratePostThumbnails {

	function GeneratePostThumbnails() { // initialization
		load_plugin_textdomain( 'generate-post-thumbnails', false, basename( dirname( __FILE__ ) ) . '/locale' );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		// Uncomment the following line if you want autogeneration of thumbnails after post was saved
		//add_action( 'save_post', array( $this, 'process_images' ) );
		add_action( 'wp_ajax_generate_post_thumbnails', array( $this, 'ajax_process_post' ) );
		add_action( 'wp_ajax_remove_post_thumbnails', array( $this, 'ajax_remove_featured_image' ) );
	}

	function add_admin_menu() { // admin menu item
		$page = add_management_page( __( 'Generate Thumbnails', 'generate-post-thumbnails' ), __( 'Generate Thumbnails', 'generate-post-thumbnails' ), 'administrator', 'generate-post-thumbnails', array( $this, 'admin_interface' ) );
		add_action( 'admin_print_scripts-' . $page, array( $this, 'admin_scripts' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'admin_styles' ) );
	}

	function admin_scripts() {
		if ( wp_script_is( 'jquery-ui-widget', 'registered' ) )
			wp_enqueue_script( 'jquery-ui-progressbar', plugins_url( 'jquery-ui/jquery.ui.progressbar.min.js', __FILE__ ), array( 'jquery-ui-core', 'jquery-ui-widget' ), '1.8.6' );
		else
			wp_enqueue_script( 'jquery-ui-progressbar', plugins_url( 'jquery-ui/jquery.ui.progressbar.min.1.7.2.js', __FILE__ ), array( 'jquery-ui-core' ), '1.7.2' );
	}

	function admin_styles() {
		wp_enqueue_style( 'jquery-ui-generatethumbs', plugins_url( 'jquery-ui/smoothness/jquery-ui-1.7.2.custom.css', __FILE__), array(), '1.7.2' );
	}

	function admin_interface() { // admin page
		$success_message = __( 'Thumbnails generation process is finished. Processed posts: %d', 'generate-post-thumbnails' );
?>
<div class="wrap">
<div class="icon32" id="icon-tools"><br/></div>
	<h2><?php _e( 'Thumbnails Generation', 'generate-post-thumbnails' ); ?></h2>
	<div class="metabox-holder">
	<div class="updated"><p>PLEASE NOTE: This plugin performs mass manipulation. Backup your database and files before using it.</p></div>
	<noscript><div class="error"><p><?php _e( 'Javascript is disabled, please enable javascript for proper plugin work.', 'generate-post-thumbnails' ); ?></p></div></noscript>
	<?php if ( !current_theme_supports( 'post-thumbnails' ) ) { /* theme should support post-thumbnails*/ ?>
	<div class="error"><p><strong><?php _e( 'Plugin warning', 'generate-post-thumbnails' ); ?>:</strong> <?php _e( 'Your current theme does not support thumbnails. You need to adjust your theme in order to use this plugin. Please read plugin page for more information. Settings will appear on this page once you enable thumbnails in your theme.', 'generate-post-thumbnails' ); ?></p></div>
	<?php
	} // endif theme supports thumbnails
	else {
		global $wpdb;
		$posts = array();
		$posts = $wpdb->get_results( "select ID from $wpdb->posts where post_type = 'post' and post_status != 'auto-draft'" );
		$posts_count = count( $posts );
		$posts_ids = array();
		foreach ( $posts as $post ) {
			$posts_ids[] = $post->ID;
		}
		$message = '';
		?>
		<div id="message" class="updated" <?php if ( empty($message) ) : ?>style="display: none;"<?php endif; ?>><?php echo $message; ?></div>
		<?php if ( ! ( ( $uploads = wp_upload_dir( current_time( 'mysql' ) ) ) && false === $uploads['error'] ) ) : ?>
		<p class="error"><?php _e( 'Wordpress uploads directory is not accessible. External images are not supported.', 'generate-post-thumbnails' ); ?></p>
		<?php endif; ?>
		<form id="generate_thumbs_form" action="?page=generate-post-thumbnails" method="POST">
			<input type="hidden" name="generate-thumbnails-submit" value="1" />
			<?php wp_nonce_field( 'generate-thumbnails' ); ?>
			<div class="postbox">
				<h3><?php _e( 'Thumbnails generation settings', 'generate-post-thumbnails' ); ?></h3>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e( 'Image number in the post', 'generate-post-thumbnails' ); ?>:</th>
						<td>
							<input type="text" name="imagenumber" id="imagenumber" value="1" size="2"/>
							<label for="imagenumber"><?php _e( 'Sequence number of the image in the post to be stored as a post thumbnail. Ex. 1 for the first post image, 2 for the second, etc. If there is no image at the given number, existing thumbnail will be removed.', 'generate-post-thumbnails' ); ?></label><br />
						</td>
					</tr>
				</table>
			</div>
			<input id="generate-thumbnail-button" name="Submit" value="<?php _e( 'Generate thumbnails', 'generate-post-thumbnails' ); ?>" type="submit" class="button"/>
			<script type="text/javascript">
				var errors = {
				'1': 'Image from post body was successfully assigned as featured image',
				'2': 'External image was successfully uploaded and assigned as featured image',
				'5': 'Post has featured image already. Skipping',
				'6': 'No image found in the post',
				'100': 'No post found with such ID',
				'101': 'Incorrect image number',
				'102': 'Uploading directory is not accessible',
				'103': 'External server is not responding',
				'104': 'Remote server does not return image',
				'105': 'Uplaod was not successful',
				'106': 'Can not get file type from uploaded image',
				'200': 'Can not assign featured image',
				'500': 'Featured Image was removed',
				'501': 'Post has no Featured Image',
				'1000': 'You don not have rights to change Featured Image for the post'
				};
				jQuery(document).ready(function($) {
						function ProcessThumbnails(event) {
							event.preventDefault();
							$("#generate-thumbnail-button").attr('disabled', true);
							$("#remove-thumbnail-button").attr('disabled', true);
							$("#message").html("<?php _e( 'Please wait until the process is finished. This process may take up to several minutes, depending on the number of posts and server capacity.', 'generate-post-thumbnails' ); ?>");
							$("#message").show();
							$("#gt_progressbar").progressbar({ value: 0 });
							$("#gt_progressbar_percent").html("0%");
							$("#gpt-log").val('');
							var gt_count       = 1;
							var gt_percent     = 0;
							var gt_posts       = [<?php echo implode(", ", $posts_ids); ?>];
							var gt_total       = gt_posts.length;
							var gt_overwrite   = $("#overwrite").is(':checked') ? 'overwrite' : 'skip';
							var gt_imagenumber = $("#imagenumber").val();

							function EditThumbnails(post_id) {
								$.post(ajaxurl, {action: event.data.action, post_id: post_id, overwrite: gt_overwrite, imagenumber: gt_imagenumber}, function(response){
										gt_percent = (gt_total > 0) ? (gt_count / gt_total) * 100 : 0;
										$("#gt_progressbar").progressbar("value", gt_percent);
										$("#gt_progressbar_percent").html(Math.round(gt_percent) + "%");
										log_text = 'Post: '+post_id+'. '+errors[response]+'...\n';
										$("#gpt-log").val($("#gpt-log").val()+log_text);
										gt_count++;
										if (gt_posts.length) {
											EditThumbnails(gt_posts.shift());
										} else {
											$("#message").html("<?php echo js_escape(sprintf($success_message, $posts_count)); ?>");
											$("#generate-thumbnail-button").attr('disabled', false);
											$("#remove-thumbnail-button").attr('disabled', false);
										}
									});
							}
							EditThumbnails(gt_posts.shift());
						};
						$("#generate_thumbs_form").submit({action: "generate_post_thumbnails"}, ProcessThumbnails);
						$("#remove_thumbs_form").submit({action: "remove_post_thumbnails"}, ProcessThumbnails);
					});
			</script>
		</form>
		<div id="gt_progressbar" style="position:relative;width:80%;margin-top:20px;">
			<label id="gt_progressbar_percent" style="position:absolute;left:50%;top:5px;margin-left:-20px;"></label>
		</div>
		<label>Live log:</label><br/>
		<textarea rows="10" cols="70" id="gpt-log"></textarea>
		<p>
			<form id="remove_thumbs_form" action="?page=generate-post-thumbnails" method="POST">
				<input id="remove-thumbnail-button" name="Remove" value="<?php _e( 'Remove all featured images from posts', 'generate-post-thumbnails' ); ?>" type="submit" class="button" onclick="return confirm('ARE YOU SURE YOU WANT TO REMOVE ALL FEATURED IMAGES?');"/>
			</form>
		</p>
		<?php
			} // endif theme supports thumbnails
		?>
	</div>
</div>
<?php
	} // endfunction admin_interface

	function process_images( $post_id, $overwrite = 'skip', $imagenumber = 1 ) { // generating thumbnail for a single post
		$post = get_post($post_id);
		if ( !$post ) // if post was not found by id
			return 100;
		if ( $overwrite == 'skip' && has_post_thumbnail($post_id) ) // check if skip existing thumbnail
			return 5;
		$wud		  = wp_upload_dir();
		$upload_parts = parse_url( $wud['baseurl'] );
		$image		  = '';
		$imagenumber  = preg_replace( '/[^\d]/', '', $imagenumber );
		//imagenumber was not set or 0
		if ( empty($imagenumber) || $imagenumber == 0 )
			return 101;
		preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i', $post->post_content, $matches ); // search for uploaded images in the post
		if ( isset($matches) && isset($matches[1][$imagenumber-1]) && strlen(trim($matches[1][$imagenumber-1])) > 0 )
			$image = $matches[1][$imagenumber-1];
		else { // if image was not found in post
			return 6;
		}

		$saved_in_wordpress = false;

		if ( strpos( $image, $wud['baseurl'] ) !== false || ( strpos( $image, 'http:' ) !== 0 && isset( $upload_parts['path'] ) && strpos( $image, $upload_parts['path'] ) === 0 ) ) { // image was uploaded on server in wordpress uploads directory
			$parts = pathinfo($image);
			$attachments = array();
			global $wpdb;
			$attachments = $wpdb->get_results("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_wp_attachment_metadata' AND meta_value like '%" . $parts['basename'] . "%'");
			if ( is_array($attachments) && count($attachments) > 0 && isset( $attachments[0]->post_id ) ) { // image was found in Wordpress database
				$saved_in_wordpress = true;
				$attachment_id = $attachments[0]->post_id;
				$thumbnail_html = wp_get_attachment_image( $attachment_id, 'thumbnail' );
				if ( !empty($thumbnail_html) ) {
					update_post_meta( $post->ID, '_thumbnail_id', $attachment_id );
					return 1;
				}
			}
		}

		if ( !$saved_in_wordpress ) { // image is external

			if ( ! ( ( $uploads = wp_upload_dir( current_time('mysql') ) ) && false === $uploads['error'] ) )
				return 102; // upload dir is not accessible

			$content = '';
			$image = rawurldecode( preg_replace('/\?.*/', '', $image) );
			$name_parts = pathinfo($image);
			$filename = wp_unique_filename( $uploads['path'], $name_parts['basename'] );
			$unique_name_parts = pathinfo($filename);
			$newfile = $uploads['path'] . "/$filename";

			// try to upload

			if ( ini_get( 'allow_url_fopen' ) ) { // check php setting for remote file access
				$content = @file_get_contents( $image );
			}
			elseif ( function_exists( 'curl_init' ) ) { // curl library enabled
				$ch = curl_init();
				curl_setopt( $ch, CURLOPT_URL, $image );
				curl_setopt( $ch, CURLOPT_HEADER, 0 );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_8; en-us) AppleWebKit/531.9 (KHTML, like Gecko) Version/4.0.3 Safari/531.9' );
				$content = curl_exec( $ch );
				curl_close( $ch );
			}
			else { // custom connect
				$parsed_url = parse_url( $image );
				$host = $parsed_url['host'];
				$path = ( isset( $parsed_url['path'] ) ) ? $parsed_url['path'] : '/';
				$port = ( isset( $parsed_url['port'] ) ) ? $parsed_url['port'] : '80';
				$timeout = 10;
				if ( isset( $parsed_url['query'] ) )
					$path .= '?' . $parsed_url['query'];
				$fp = @fsockopen( $host, '80', $errno, $errstr, $timeout );

				if( !$fp )
					return 103; // give up on connecting to remote host

				fputs( $fp, "GET $path HTTP/1.0\r\n" .
					   "Host: $host\r\n" .
					   "User-Agent: Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_8; en-us) AppleWebKit/531.9 (KHTML, like Gecko) Version/4.0.3 Safari/531.9\r\n" .
					   "Accept: */*\r\n" .
					   "Accept-Language: en-us,en;q=0.5\r\n" .
					   "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n" .
					   "Keep-Alive: 300\r\n" .
					   "Connection: keep-alive\r\n" .
					   "Referer: http://$host\r\n\r\n");
				stream_set_timeout( $fp, $timeout );
				// retrieve the response from the remote server
				while ( $line = fread( $fp, 4096 ) ) {
					$content .= $line;
				}
				fclose( $fp );
				$pos     = strpos( $content, "\r\n\r\n" );
				$content = substr( $content, $pos + 4 );
			}

			if ( empty( $content ) ) // nothing was found
				return 104;

			file_put_contents( $newfile, $content ); // save image

			if (! file_exists( $newfile ) ) // upload was not successful
				return 105;

			// Set correct file permissions
			$stat = stat( dirname( $newfile ) );
			$perms = $stat['mode'] & 0000666;
			@chmod( $newfile, $perms );
			// get file type
			$wp_filetype = wp_check_filetype( $newfile );
			extract($wp_filetype);

			// No file type! No point to proceed further
			if ( ( !$type || !$ext ) && !current_user_can( 'unfiltered_upload' ) )
				return 106;
			$title = $unique_name_parts['filename'];
			$content = '';

			// use image exif/iptc data for title and caption defaults if possible
			if ( $image_meta = @wp_read_image_metadata($newfile) ) {
				if ( trim($image_meta['title']) )
					$title = $image_meta['title'];
				if ( trim($image_meta['caption']) )
					$content = $image_meta['caption'];
			}

			// Compute the URL
			$url = $uploads['url'] . "/$filename";

			// Construct the attachment array
			$attachment = array(
								'post_mime_type' => $type,
								'guid' => $url,
								'post_parent' => $post_id,
								'post_title' => $title,
								'post_content' => $content,
								);
			$thumb_id = wp_insert_attachment( $attachment, $newfile, $post_id );
			if ( !is_wp_error($thumb_id) ) {
				wp_update_attachment_metadata( $thumb_id, wp_generate_attachment_metadata( $thumb_id, $newfile ) );
				update_post_meta( $post->ID, '_thumbnail_id', $thumb_id );
				return 2;
			}
		}
		return 200;
	} // endfunction process_images

	function ajax_remove_featured_image()
	{
		if ( !current_user_can('manage_options') )
			die('1000');
		if (delete_post_meta( $_POST['post_id'], '_thumbnail_id' ))
			die('500');
		else
			die('501');
	}

	function ajax_process_post() { // dealing with ajax requests
		if ( !current_user_can('manage_options') )
			die('1000');
		die( strval($this->process_images( $_POST['post_id'], $_POST['overwrite'], $_POST['imagenumber'] ) ));
	}
} // endclass

add_action( 'init', 'GeneratePostThumbnails' );

function GeneratePostThumbnails() {
	global $GenearetPostThumbnails;
	$GeneratePostThumbnails = new GeneratePostThumbnails();
}
?>
