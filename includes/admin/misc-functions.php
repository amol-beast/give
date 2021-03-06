<?php

/**
 * Gets a number of posts and displays them as options
 *
 * @param  array $query_args Optional. Overrides defaults.
 * @param  bool  $force      Force the pages to be loaded even if not on settings
 *
 * @see: https://github.com/WebDevStudios/CMB2/wiki/Adding-your-own-field-types
 * @return array An array of options that matches the CMB2 options array
 */
function give_cmb2_get_post_options( $query_args, $force = false ) {

	$post_options = array( '' => '' ); // Blank option

	if ( ( ! isset( $_GET['page'] ) || 'give-settings' != $_GET['page'] ) && ! $force ) {
		return $post_options;
	}

	$args = wp_parse_args(
		$query_args, array(
			'post_type'   => 'page',
			'numberposts' => 10,
		)
	);

	$posts = get_posts( $args );

	if ( $posts ) {
		foreach ( $posts as $post ) {

			$post_options[ $post->ID ] = $post->post_title;

		}
	}

	return $post_options;
}


/**
 * Featured Image Sizes
 *
 * Outputs an array for the "Featured Image Size" option found under Settings > Display Options.
 *
 * @since 1.4
 *
 * @global $_wp_additional_image_sizes
 *
 * @return array $sizes
 */
function give_get_featured_image_sizes() {
	global $_wp_additional_image_sizes;

	$sizes            = array();
	$get_sizes        = get_intermediate_image_sizes();
	$core_image_sizes = array( 'thumbnail', 'medium', 'medium_large', 'large' );

	// This will help us to filter special characters from a string
	$filter_slug_items = array( '_', '-' );

	foreach ( $get_sizes as $_size ) {

		// Converting image size slug to title case
		$sizes[ $_size ] = give_slug_to_title( $_size, $filter_slug_items );

		if ( in_array( $_size, $core_image_sizes ) ) {
			$sizes[ $_size ] .= ' (' . get_option( "{$_size}_size_w" ) . 'x' . get_option( "{$_size}_size_h" );
		} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
			$sizes[ $_size ] .= " ({$_wp_additional_image_sizes[ $_size ]['width']} x {$_wp_additional_image_sizes[ $_size ]['height']}";
		}

		// Based on the above image height check, label the respective resolution as responsive
		if ( ( array_key_exists( $_size, $_wp_additional_image_sizes ) && ! $_wp_additional_image_sizes[ $_size ]['crop'] ) || ( in_array( $_size, $core_image_sizes ) && ! get_option( "{$_size}_crop" ) ) ) {
			$sizes[ $_size ] .= ' - responsive';
		}

		$sizes[ $_size ] .= ')';

	}

	return apply_filters( 'give_get_featured_image_sizes', $sizes );
}


/**
 *  Slug to Title
 *
 *  Converts a string with hyphen(-) or underscores(_) or any special character to a string with Title case
 *
 * @since 1.8.8
 *
 * @param string $string
 * @param array  $filters
 *
 * @return string $string
 */
function give_slug_to_title( $string, $filters = array() ) {

	foreach ( $filters as $filter_item ) {
		$string = str_replace( $filter_item, ' ', $string );
	}

	// Return updated string after converting it to title case
	return ucwords( $string );

}


/**
 * Display the API Keys
 *
 * @since       1.0
 * @return      void
 */
function give_api_callback() {

	if ( ! current_user_can( 'manage_give_settings' ) ) {
		return;
	}

	/**
	 * Fires before displaying API keys.
	 *
	 * @since 1.0
	 */
	do_action( 'give_tools_api_keys_before' );

	require_once GIVE_PLUGIN_DIR . 'includes/admin/class-api-keys-table.php';

	$api_keys_table = new Give_API_Keys_Table();
	$api_keys_table->prepare_items();
	$api_keys_table->display();
	?>
	<span class="give-metabox-description api-description">
		<?php
		echo sprintf(
		/* translators: 1: http://docs.givewp.com/api 2: http://docs.givewp.com/addon-zapier */
			__( 'You can create API keys for individual users within their profile edit screen. API keys allow users to use the <a href="%1$s" target="_blank">Give REST API</a> to retrieve donation data in JSON or XML for external applications or devices, such as <a href="%2$s" target="_blank">Zapier</a>.', 'give' ),
			esc_url( 'http://docs.givewp.com/api' ),
			esc_url( 'http://docs.givewp.com/addon-zapier' )
		);
		?>
	</span>
	<?php

	/**
	 * Fires after displaying API keys.
	 *
	 * @since 1.0
	 */
	do_action( 'give_tools_api_keys_after' );
}


/**
 * Hide char in string
 *
 * @param string $str
 * @param int    $show_char_count
 * @param string $replace
 *
 * @return string
 * @since 2.5.0
 *
 */
function give_hide_char( $str, $show_char_count, $replace = '*' ) {
	return str_repeat(
		$replace,
		strlen( $str ) - $show_char_count ) . substr( $str, - $show_char_count, $show_char_count
	);
}


/**
 *  Format marKdown formatted string.
 *
 * @param string $readme Markdown format string
 *
 * @return string
 * @since 2.5.0
 *
 */
function give_get_format_md( $readme ) {
	$readme = preg_replace( '/`(.*?)`/', '<code>\\1</code>', $readme );
	$readme = preg_replace( '/[\040]\*\*(.*?)\*\*/', ' <strong>\\1</strong>', $readme );
	$readme = preg_replace( '/[\040]\*(.*?)\*/', ' <em>\\1</em>', $readme );
	$readme = preg_replace( '/= (.*?) =/', '<h4>\\1</h4>', $readme );
	$readme = preg_replace( '/\[(.*?)\]\((.*?)\)/', '<a href="\\2">\\1</a>', $readme );

	return $readme;
}
