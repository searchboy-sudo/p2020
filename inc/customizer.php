<?php
/**
 * Theme Customizer
 *
 * @package p2020
 */

namespace P2020;

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	$wp_customize->add_setting( 'p2020_theme_options[alternate_color]', [
		'default'           => '#3498db',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	] );
	$wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'alternate_color', [
		'label'    => __( 'Alternate Color', 'p2020' ),
		'section'  => 'colors',
		'settings' => 'p2020_theme_options[alternate_color]',
	] ) );

	$wp_customize->add_setting( 'p2020_theme_options[link_color]', [
		'default'           => '#3498db',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	] );
	$wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'link_color', [
		'label'    => __( 'Link Color', 'p2020' ),
		'section'  => 'colors',
		'settings' => 'p2020_theme_options[link_color]',
	] ) );
}

add_action( 'customize_register', 'P2020\customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function customize_preview_js() {
	wp_enqueue_script( 'p2020_customizer', get_template_directory_uri() . '/js/customizer.js', [ 'customize-preview' ], '20130304', true );
}

add_action( 'customize_preview_init', 'P2020\customize_preview_js' );

/**
 * Add styles for color options
 */
function color_styles() {
	$options = get_theme_mod( 'p2020_theme_options' );

	if ( ! isset( $options ) ) {
		return;
	}

	$style = '<style type="text/css">';

	if ( '#3498db' != $options['alternate_color'] ) {
		$style .= '.o2 .o2-app-page-title, .o2 .o2-app-new-post h2, #o2-expand-editor { background-color: ' . $options['alternate_color'] . '}';
	}

	if ( '#3498db' != $options['link_color'] ) {
		$style .= 'a, a:hover, a:visited { color: ' . $options['link_color'] . '}';
	}

	$style .= '</style>';

	//phpcs:ignore WordPress.Security.EscapeOutput
	echo $style;
}

add_filter( 'wp_head', 'P2020\color_styles' );

function disable_nonrelevant_sections( $wp_customize ) {
	// Remove "Homepage Settings".
	$wp_customize->remove_section( 'static_front_page' );

	// Remove "Additional CSS" (WP Core).
	$wp_customize->remove_section( 'custom_css' );

	// Remove "Additional CSS" upgrade nudge when on Free plan (Jetpack_Custom_CSS_Customizer).
	// https://opengrok.a8c.com/source/xref/trunk/wp-content/mu-plugins/custom-css-in-customizer.php#285
	$wp_customize->remove_section( 'css_nudge' );
}

// Keep the priority high enough so our callback is ran after everything else.
add_action( 'customize_register', 'P2020\disable_nonrelevant_sections', 1000 );

/**
 * Disable "Additional CSS" added in Jetpack_Custom_CSS_Customizer.
 *
 * https://opengrok.a8c.com/source/xref/trunk/wp-content/mu-plugins/custom-css-in-customizer.php#15
 */
function enable_custom_customizer() {
	return false;
}

add_filter( 'enable_custom_customizer', 'P2020\enable_custom_customizer' );
