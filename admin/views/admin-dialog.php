<div id="arve-form">
	<div class="arve-dialog">

		<label for="arve-url">URL/Embed Code</label><br>

		<small class="description">
			<?php _e('For Blip.tv, Videojug, Movieweb, Gametrailers, Yahoo!, Spike, Comedycentral and general iframe embed codes paste the embed code, for all others paste the URL! Ustream: If your Address bar URL not contains a number, click the Share->URL icons and paste that URL.', $this->plugin_slug ); ?></span>
		</small>

		<textarea id="arve-url" rows="4" value=""></textarea><br>

		<div class="arve-input-group">
			<label for="arve-align"><?php _e('Align', $this->plugin_slug ); ?></label>
			<select id="arve-align">
				<option value=""></option>
				<option value="left"><?php _e('left', $this->plugin_slug ); ?></option>
				<option value="right"><?php _e('right', $this->plugin_slug ); ?></option>
				<option value="center"><?php _e('center', $this->plugin_slug ); ?></option>
			</select>
		</div>

		<div class="arve-input-group">
			<label for="arve-mode"><?php _e('Mode', $this->plugin_slug ); ?></label>
			<select id="arve-mode">
				<option value=""></option>
				<option value="lazyload"><?php _e('Lazyload', $this->plugin_slug ); ?></option>
				<option value="normal"><?php _e('Normal', $this->plugin_slug ); ?></option>
				<option value="thumbnail"><?php _e('Thumbnail', $this->plugin_slug ); ?></option>
			</select>
		</div>

		<div class="arve-input-group">
			<label for="arve-autoplay"><?php _e('Autoplay', $this->plugin_slug ); ?></label>
			<select id="arve-autoplay">
				<option value=""></option>
				<option value="yes"><?php _e('yes', $this->plugin_slug ); ?></option>
				<option value="no"><?php _e('no', $this->plugin_slug ); ?></option>
			</select>
		</div>

		<div class="arve-input-group">
			<label for="arve-maxwidth"><?php _e('Maximal width (px)', $this->plugin_slug ); ?></label>
			<input type="text" id="arve-maxwidth" value="" />
		</div>

		<div class="arve-input-group">
			<label for="arve-aspect_ratio"><?php _e('Aspect ratio (4:3)', $this->plugin_slug ); ?></label>
			<input type="text" id="arve-aspect_ratio" value="" />
		</div>

		<div class="clear"></div>

		<div class="arve-input-group arve-input-group-full">
			<label for="arve-parameters"><?php _e('URL Parameters', $this->plugin_slug ); ?></label>
			<input type="text" id="arve-parameters" class="arve-input-full" value="" />
		</div>

		<div id="arve-shortcode" class="arve-shortcode-display">-</div>

		<div class="arve-submit-wrap">
			<input type="button" id="arve-submit" class="button-primary arve-button-primary" value="Insert Shortcode" name="submit" />
		</div>

		<div style="display: none;">

			<label for="arve-provider"><?php _e('Provider', $this->plugin_slug ); ?></label>
			<select id="arve-provider">
				<option value=""></option>
				<?php

				foreach( $this->options['shortcodes'] as $key => $val ) {

					printf( '<option value="%s">%s</option>', esc_attr( $val ), esc_html( $key ) );
				}
				
				?>
			</select>

			<label for="arve-id"><?php _e('Video ID', $this->plugin_slug ); ?></label><br>
			<small class="description"><?php _e('If not filled in automatically after pasting the url above you have to insert the video ID in here.', $this->plugin_slug  ); ?></small>
			<input type="text" id="arve-id" value="" />

		</div><!-- end hidden div -->

	</div>
</div>