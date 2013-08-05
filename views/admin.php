<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Advanced_Responsive_Video_Embedder
 * @author    Nicolas Jonas
 * @license   GPL-3.0+
 * @link      http://nextgenthemes.com
 * @copyright 2013 Nicolas Jonas
 */

$options = get_option( 'arve_options', array() );

?>
<div class="wrap">

	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<form method="post" action="options.php">
		<?php settings_fields('arve_plugin_options'); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">Default Mode:</th>
				<td>
					<select name="arve_options[mode]" size="1">
					  <option<?php selected( $options['mode'], 'normal'); ?> value="normal"><?php _e('Normal', 'ngt-arve'); ?></option>
					  <option<?php selected( $options['mode'], 'thumbnail'); ?> value="thumbnail"><?php _e('Thumbnail', 'ngt-arve'); ?></option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="video_maxwidth"><?php _e('Video Maximal Width', 'ngt-arve'); ?></label></th>
				<td>
					<input name="arve_options[video_maxwidth]" type="text" value="<?php echo $options['video_maxwidth'] ?>" class="small-text"><br>
					<span class='description'><?php _e('Not needed, if you set this to "0" your videos will me the maximum size of the container they are in. If your Page has a big width you might want to set this.', 'ngt-arve'); ?></span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="fakethumb"><?php _e('Fake Thumbnails', 'ngt-arve'); ?></label></th>
				<td>
					<input name="arve_options[fakethumb]" type="checkbox" value="1" <?php checked( 1, $options['fakethumb'] ); ?> /><br>
					<span class='description'><?php _e('Loads the actual Videoplayer as "background image" to for thumbnails to emulate the feature Youtube, Dailymotion, and Bliptv have. If not enabled thumbnails are displayed black or you can choose a image below.', 'ngt-arve'); ?></span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="autoplay"><?php _e('Autoplay all', 'ngt-arve'); ?></label></th>
				<td>
					<input name="arve_options[autoplay]" type="checkbox" value="1" <?php checked( 1, $options['autoplay'] ); ?> /><br>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label><?php _e('Thumbnail Size', 'ngt-arve'); ?></label></th>
				<td>
					<label for="arve_options[thumb_width]"><?php _e('Width', 'ngt-arve'); ?></label>
					<input name="arve_options[thumb_width]" type="text" value="<?php echo $options['thumb_width'] ?>" class="small-text"><br>

					<label for="arve_options[thumb_height]"><?php _e('Height', 'ngt-arve'); ?></label>
					<input name="arve_options[thumb_height]" type="text" value="<?php echo $options['thumb_height'] ?>" class="small-text"><br>
					<span class="description"><?php _e('Needed! Must be 50+ to work.', 'ngt-arve'); ?></span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="custom_thumb_image"><?php _e('Custom Thumbnail Image', 'ngt-arve'); ?></label></th>
				<td>
					<input name="arve_options[custom_thumb_image]" type="text" value="<?php echo $options['custom_thumb_image'] ?>" class="large-text"><br>
					<span class='description'><?php _e('To be used instead of black background. Upload a 16:10 Image with a size bigger or equal the thumbnials size you want to use into your WordPress and paste the URL of it here.', 'ngt-arve'); ?></span>
				</td>
			</tr>
			<tr>
				<th>
					<?php submit_button( __('Reset options', 'ngt-arve'), 'secondary', 'arve_options[reset]' ); ?>
				</th>
				<td>
					<?php submit_button(); ?>
				</td>
			</tr>
		</table>
		
		<h3><?php _e('Change shortcode tags', 'ngt-arve'); ?></h3>
		<p>
			<?php _e('You might need this to prevent conflicts with other plugins you want to use. At least 3 alphanumec characters with optional underscores are needed!', 'ngt-arve'); ?>
		</p>
		
		<table class="form-table">
			<?php
			
			foreach ( $options['shortcodes'] as $key => $val ) {
			
				$title = ucwords( $key ) . ' tag';
				
				echo '
				<tr valign="top">
					<th scope="row"><label for="arve_options[shortcodes][' . $key . ']">' . $title . '</label></th>
					<td><input type="text" name="arve_options[shortcodes][' . $key . ']" value="' . $val . '"></td>
				</tr>';
			}
			
			?>

			<tr>
				<th></th>
				<td>
					<?php submit_button(); ?>
				</td>
			</tr>
		</table>
	</form>
</div>