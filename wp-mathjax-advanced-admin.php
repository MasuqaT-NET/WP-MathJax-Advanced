<?php

/*
 * The contents of this file are subject to the GPL License, Version 3.0.
 *
 * Copyright (C) 2013, Phillip Lord, Newcastle University
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

class WP_MathJax_Advanced_Admin {

	static $admin_tags = array(
		'input' => array(
			'type' => array(),
			'name' => array(),
			'id' => array(),
			'disabled' => array(),
			'value' => array(),
			'checked' => array(),
		),
		'select' => array(
			'name' => array(),
			'id' => array(),
		),
		'option' => array(
			'value' => array(),
			'selected' => array(),
		),
	);

	function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_page_init' ) );
	}

	function admin_page_init() {
		add_options_page( 'WP MathJax Advanced', 'WP MathJax Advanced', 'manage_options', 'mjmasuqat-wp-mathjax-advanced', array( $this, 'plugin_options_menu' ) );
	}

	function plugin_options_menu() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) ); //xss ok
		}

		$this->table_head();

		// save options if this is a valid post
		if ( isset( $_POST['mjmasuqat_wp_mathjax_advanced_save_field'] ) && // input var okay
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mjmasuqat_wp_mathjax_advanced_save_field'] ) ), 'mjmasuqat_wp_mathjax_advanced_save_action' ) // input var okay
		) {
			echo "<div class='updated settings-error' id='etting-error-settings_updated'><p><strong>Settings saved.</strong></p></div>\n";
			$this->admin_save();
		}

		$checked_force_load = '';

		if ( get_option( 'mjmasuqat_mathjax_force_load' ) ) {
			$checked_force_load = 'checked="true"';
		}

		$this->admin_table_row( 'Force Load',
			'Force the MathJax JavaScript to be loaded on every post. This removes the need to use the [mathjax] shortcode.',
			"<input type='checkbox' name='mjmasuqat_mathjax_force_load' id='mjmasuqat_mathjax_force_load' value='1' $checked_force_load />",
			''
		);

		$selected_inline  = get_option( 'mjmasuqat_wp_mathjax_advanced_inline' ) === 'inline' ? 'selected="true"' : '';
		$selected_display = get_option( 'mjmasuqat_wp_mathjax_advanced_inline' ) === 'display' ? 'selected="true"' : '';

		$use_cdn = get_option( 'mjmasuqat_mathjax_use_cdn', true ) ? 'checked="true"' : '';

		$this->admin_table_row( 'Use MathJax CDN Service?',
			'Allows use of the MathJax hosted content delivery network. By using this, you are agreeing to the  <a href="http://www.mathjax.org/download/mathjax-cdn-terms-of-service/">MathJax CDN Terms of Service</a>.',
			"<input type='checkbox' name='mjmasuqat_mathjax_use_cdn' id='use_cdn' value='1' $use_cdn/>",
			'use_cdn'
		);

		$custom_location_disabled = get_option( 'mjmasuqat_mathjax_use_cdn', true ) ? 'disabled="disabled"' : '';
		$custom_location          = "value='" . esc_attr( get_option( 'mjmasuqat_mathjax_custom_location', '' ) ) . "'";

		$this->admin_table_row( 'Custom MathJax location?',
			'If you are not using the MathJax CDN enter the location of your MathJax script.',
			"<input type='textbox' name='mjmasuqat_mathjax_custom_location' id='mjmasuqat_mathjax_custom_location' $custom_location $custom_location_disabled>",
			'mjmasuqat_mathjax_custom_location'
		);

		$options = $this->config_options();

		$select_string = "<select name='mjmasuqat_mathjax_config' id='mjmasuqat_mathjax_config'>\n";

		foreach ( $options as $i ) {
			$selected = get_option( 'mjmasuqat_mathjax_config', 'default' ) === $i ? "selected='true'" : '';
			$select_string .= "<option value='$i' " . esc_attr( $selected ) . ">$i</option>\n";
		}

		$select_string .= '</select>';

		$this->admin_table_row( 'MathJax Configuration',
			"See the <a href='http://docs.mathjax.org/en/v1.1-latest/configuration.html#loading'>MathJax documentation</a> for more details.",
			$select_string,
			'mjmasuqat_mathjax_config'
		);

		$this->table_foot();
	}

	function config_options() {
		$options = array(
			'default',
			'Accessible',
			'TeX-AMS_HTML',
			'TeX-AMS-MML_HTMLorMML',
		);

		return $options;
	}

	function admin_save() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			check_ajax_referer( 'mjmasuqat_wp_mathjax_advanced_save_field', 'security' );
		}

		update_option( 'mjmasuqat_mathjax_force_load', array_key_exists( 'mjmasuqat_mathjax_force_load', $_POST ) ); // input var okay

		update_option( 'mjmasuqat_mathjax_use_cdn', array_key_exists( 'mjmasuqat_mathjax_use_cdn', $_POST ) ); // input var okay

		if ( array_key_exists( 'mjmasuqat_mathjax_custom_location', $_POST ) && isset( $_POST['mjmasuqat_mathjax_custom_location'] ) ) { // input var okay
			update_option( 'mjmasuqat_mathjax_custom_location', esc_url_raw( wp_unslash( $_POST['mjmasuqat_mathjax_custom_location'] ) ) ); // input var okay
		}

		if ( array_key_exists( 'mjmasuqat_mathjax_config', $_POST ) && isset( $_POST['mjmasuqat_mathjax_config'] ) && // input var okay
			in_array( sanitize_text_field( wp_unslash( $_POST['mjmasuqat_mathjax_config'] ) ), $this->config_options(), true ) // input var okay
		) {
			update_option( 'mjmasuqat_mathjax_config', sanitize_text_field( wp_unslash( $_POST['mjmasuqat_mathjax_config'] ) ) ); // input var okay
		}
	}

	function table_head() {
		?>
		<div class='wrap' id='wp-mathjax-advanced-options'>
			<h2>WP MathJax Advanced</h2>
			<form id='wpmathjaxadvanced' name='wpmathjaxadvanced' action='' method='POST'>
				<?php wp_nonce_field( 'mjmasuqat_wp_mathjax_advanced_save_action', 'mjmasuqat_wp_mathjax_advanced_save_field', true ); ?>
			<table class='form-table'>
			<caption class='screen-reader-text'>The following lists configuration options for the WP MathJax Advanced plugin.</caption>
		<?php
	}

	function table_foot() {
		?>
		</table>

		<p class="submit"><input type="submit" class="button button-primary" value="Save Changes"/></p>
		</form>

		</div>
		<script type="text/javascript">
		jQuery(function($) {
			if (typeof($.fn.prop) !== 'function') {
				return; // ignore this for sites with jquery < 1.6
			}
			// enable or disable the cdn input field when checking/unchuecking the "use cdn" checkbox
			var cdn_check = $('#use_cdn'),
			cdn_location = $('#mjmasuqat_mathjax_custom_location');

			cdn_check.change(function() {
				var checked = cdn_check.is(':checked');
				cdn_location.prop('disabled', checked);
			});
		});
		</script>
	<?php
	}

	function admin_table_row( $head, $comment, $input, $input_id ) {
		?>
			<tr valign="top">
					<th scope="row">
						<label for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $head ); ?></label>
					</th>
					<td>
						<?php echo wp_kses( $input, self::$admin_tags ); ?>
						<p class="description"><?php echo wp_kses_post( $comment ); ?></p>
					</td>
				</tr>
<?php
	}
} // class

function wp_mathjax_advanced_admin_init() {
	global $wp_mathjax_advanced_admin;
	$wp_mathjax_advanced_admin = new WP_MathJax_Advanced_Admin();
}

if ( is_admin() ) {
	wp_mathjax_advanced_admin_init();
}
