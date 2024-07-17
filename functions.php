<?php
/**
 * Twenty Twenty functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

/**
 * Table of Contents:
 * Theme Support
 * Required Files
 * Register Styles
 * Register Scripts
 * Register Menus
 * Custom Logo
 * WP Body Open
 * Register Sidebars
 * Enqueue Block Editor Assets
 * Enqueue Classic Editor Styles
 * Block Editor Settings
 */

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 *
 * @since Twenty Twenty 1.0
 */
function twentytwenty_theme_support()
{

	// Add default posts and comments RSS feed links to head.
	add_theme_support('automatic-feed-links');

	// Custom background color.
	add_theme_support(
		'custom-background',
		array(
			'default-color' => 'f5efe0',
		)
	);

	// Set content-width.
	global $content_width;
	if (!isset($content_width)) {
		$content_width = 580;
	}

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support('post-thumbnails');

	// Set post thumbnail size.
	set_post_thumbnail_size(1200, 9999);

	// Add custom image size used in Cover Template.
	add_image_size('twentytwenty-fullscreen', 1980, 9999);

	// Custom logo.
	$logo_width = 120;
	$logo_height = 90;

	// If the retina setting is active, double the recommended width and height.
	if (get_theme_mod('retina_logo', false)) {
		$logo_width = floor($logo_width * 2);
		$logo_height = floor($logo_height * 2);
	}

	add_theme_support(
		'custom-logo',
		array(
			'height' => $logo_height,
			'width' => $logo_width,
			'flex-height' => true,
			'flex-width' => true,
		)
	);

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support('title-tag');

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'script',
			'style',
			'navigation-widgets',
		)
	);

	// Add support for full and wide align images.
	add_theme_support('align-wide');

	// Add support for responsive embeds.
	add_theme_support('responsive-embeds');

	/*
	 * Adds starter content to highlight the theme on fresh sites.
	 * This is done conditionally to avoid loading the starter content on every
	 * page load, as it is a one-off operation only needed once in the customizer.
	 */
	if (is_customize_preview()) {
		require get_template_directory() . '/inc/starter-content.php';
		add_theme_support('starter-content', twentytwenty_get_starter_content());
	}

	// Add theme support for selective refresh for widgets.
	add_theme_support('customize-selective-refresh-widgets');

	/*
	 * Adds `async` and `defer` support for scripts registered or enqueued
	 * by the theme.
	 */
	$loader = new TwentyTwenty_Script_Loader();
	if (version_compare($GLOBALS['wp_version'], '6.3', '<')) {
		add_filter('script_loader_tag', array($loader, 'filter_script_loader_tag'), 10, 2);
	} else {
		add_filter('print_scripts_array', array($loader, 'migrate_legacy_strategy_script_data'), 100);
	}
}

add_action('after_setup_theme', 'twentytwenty_theme_support');

/**
 * REQUIRED FILES
 * Include required files.
 */
require get_template_directory() . '/inc/template-tags.php';

// Handle SVG icons.
require get_template_directory() . '/classes/class-twentytwenty-svg-icons.php';
require get_template_directory() . '/inc/svg-icons.php';

// Handle Customizer settings.
require get_template_directory() . '/classes/class-twentytwenty-customize.php';

// Require Separator Control class.
require get_template_directory() . '/classes/class-twentytwenty-separator-control.php';

// Custom comment walker.
require get_template_directory() . '/classes/class-twentytwenty-walker-comment.php';

// Custom page walker.
require get_template_directory() . '/classes/class-twentytwenty-walker-page.php';

// Custom script loader class.
require get_template_directory() . '/classes/class-twentytwenty-script-loader.php';

// Non-latin language handling.
require get_template_directory() . '/classes/class-twentytwenty-non-latin-languages.php';

// Custom CSS.
require get_template_directory() . '/inc/custom-css.php';

// Block Patterns.
require get_template_directory() . '/inc/block-patterns.php';

/**
 * Register and Enqueue Styles.
 *
 * @since Twenty Twenty 1.0
 * @since Twenty Twenty 2.6 Enqueue the CSS file for the variable font.
 */
function twentytwenty_register_styles()
{

	$theme_version = wp_get_theme()->get('Version');

	wp_enqueue_style('twentytwenty-style', get_stylesheet_uri(), array(), $theme_version);
	wp_style_add_data('twentytwenty-style', 'rtl', 'replace');

	// Enqueue the CSS file for the variable font, Inter.
	wp_enqueue_style('twentytwenty-fonts', get_theme_file_uri('/assets/css/font-inter.css'), array(), wp_get_theme()->get('Version'), 'all');

	// Add output of Customizer settings as inline style.
	$customizer_css = twentytwenty_get_customizer_css('front-end');
	if ($customizer_css) {
		wp_add_inline_style('twentytwenty-style', $customizer_css);
	}

	// Add print CSS.
	wp_enqueue_style('twentytwenty-print-style', get_template_directory_uri() . '/print.css', null, $theme_version, 'print');
}

add_action('wp_enqueue_scripts', 'twentytwenty_register_styles');

/**
 * Register and Enqueue Scripts.
 *
 * @since Twenty Twenty 1.0
 */
function twentytwenty_register_scripts()
{

	$theme_version = wp_get_theme()->get('Version');

	if ((!is_admin()) && is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}

	/*
	 * This script is intentionally printed in the head because it involves the page header. The `defer` script loading
	 * strategy ensures that it does not block rendering; being in the head it will start loading earlier so that it
	 * will execute sooner once the DOM has loaded. The $args array is not used here to avoid unintentional footer
	 * placement in WP<6.3; the wp_script_add_data() call is used instead.
	 */
	wp_enqueue_script('twentytwenty-js', get_template_directory_uri() . '/assets/js/index.js', array(), $theme_version);
	wp_script_add_data('twentytwenty-js', 'strategy', 'defer');
}

add_action('wp_enqueue_scripts', 'twentytwenty_register_scripts');

/**
 * Fix skip link focus in IE11.
 *
 * This does not enqueue the script because it is tiny and because it is only for IE11,
 * thus it does not warrant having an entire dedicated blocking script being loaded.
 *
 * @since Twenty Twenty 1.0
 * @deprecated Twenty Twenty 2.3 Removed from wp_print_footer_scripts action.
 *
 * @link https://git.io/vWdr2
 */
function twentytwenty_skip_link_focus_fix()
{
	// The following is minified via `terser --compress --mangle -- assets/js/skip-link-focus-fix.js`.
	?>
	<script>
		/(trident|msie)/i.test(navigator.userAgent) && document.getElementById && window.addEventListener && window.addEventListener("hashchange", function () { var t, e = location.hash.substring(1); /^[A-z0-9_-]+$/.test(e) && (t = document.getElementById(e)) && (/^(?:a|select|input|button|textarea)$/i.test(t.tagName) || (t.tabIndex = -1), t.focus()) }, !1);
	</script>
	<?php
}

/**
 * Enqueue non-latin language styles.
 *
 * @since Twenty Twenty 1.0
 *
 * @return void
 */
function twentytwenty_non_latin_languages()
{
	$custom_css = TwentyTwenty_Non_Latin_Languages::get_non_latin_css('front-end');

	if ($custom_css) {
		wp_add_inline_style('twentytwenty-style', $custom_css);
	}
}

add_action('wp_enqueue_scripts', 'twentytwenty_non_latin_languages');

/**
 * Register navigation menus uses wp_nav_menu in five places.
 *
 * @since Twenty Twenty 1.0
 */
function twentytwenty_menus()
{

	$locations = array(
		'primary' => __('Desktop Horizontal Menu', 'twentytwenty'),
		'expanded' => __('Desktop Expanded Menu', 'twentytwenty'),
		'mobile' => __('Mobile Menu', 'twentytwenty'),
		'footer' => __('Footer Menu', 'twentytwenty'),
		'social' => __('Social Menu', 'twentytwenty'),
	);

	register_nav_menus($locations);
}

add_action('init', 'twentytwenty_menus');

/**
 * Get the information about the logo.
 *
 * @since Twenty Twenty 1.0
 *
 * @param string $html The HTML output from get_custom_logo (core function).
 * @return string
 */
function twentytwenty_get_custom_logo($html)
{

	$logo_id = get_theme_mod('custom_logo');

	if (!$logo_id) {
		return $html;
	}

	$logo = wp_get_attachment_image_src($logo_id, 'full');

	if ($logo) {
		// For clarity.
		$logo_width = esc_attr($logo[1]);
		$logo_height = esc_attr($logo[2]);

		// If the retina logo setting is active, reduce the width/height by half.
		if (get_theme_mod('retina_logo', false)) {
			$logo_width = floor($logo_width / 2);
			$logo_height = floor($logo_height / 2);

			$search = array(
				'/width=\"\d+\"/iU',
				'/height=\"\d+\"/iU',
			);

			$replace = array(
				"width=\"{$logo_width}\"",
				"height=\"{$logo_height}\"",
			);

			// Add a style attribute with the height, or append the height to the style attribute if the style attribute already exists.
			if (false === strpos($html, ' style=')) {
				$search[] = '/(src=)/';
				$replace[] = "style=\"height: {$logo_height}px;\" src=";
			} else {
				$search[] = '/(style="[^"]*)/';
				$replace[] = "$1 height: {$logo_height}px;";
			}

			$html = preg_replace($search, $replace, $html);

		}
	}

	return $html;
}

add_filter('get_custom_logo', 'twentytwenty_get_custom_logo');

if (!function_exists('wp_body_open')) {

	/**
	 * Shim for wp_body_open, ensuring backward compatibility with versions of WordPress older than 5.2.
	 *
	 * @since Twenty Twenty 1.0
	 */
	function wp_body_open()
	{
		/** This action is documented in wp-includes/general-template.php */
		do_action('wp_body_open');
	}
}

/**
 * Include a skip to content link at the top of the page so that users can bypass the menu.
 *
 * @since Twenty Twenty 1.0
 */
function twentytwenty_skip_link()
{
	echo '<a class="skip-link screen-reader-text" href="#site-content">' .
		/* translators: Hidden accessibility text. */
		__('Skip to the content', 'twentytwenty') .
		'</a>';
}

add_action('wp_body_open', 'twentytwenty_skip_link', 5);

/**
 * Register widget areas.
 *
 * @since Twenty Twenty 1.0
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function twentytwenty_sidebar_registration()
{

	// Arguments used in all register_sidebar() calls.
	$shared_args = array(
		'before_title' => '<h2 class="widget-title subheading heading-size-3">',
		'after_title' => '</h2>',
		'before_widget' => '<div class="widget %2$s"><div class="widget-content">',
		'after_widget' => '</div></div>',
	);

	// Footer #1.
	register_sidebar(
		array_merge(
			$shared_args,
			array(
				'name' => __('Footer #1', 'twentytwenty'),
				'id' => 'sidebar-1',
				'description' => __('Widgets in this area will be displayed in the first column in the footer.', 'twentytwenty'),
			)
		)
	);

	// Footer #2.
	register_sidebar(
		array_merge(
			$shared_args,
			array(
				'name' => __('Footer #2', 'twentytwenty'),
				'id' => 'sidebar-2',
				'description' => __('Widgets in this area will be displayed in the second column in the footer.', 'twentytwenty'),
			)
		)
	);
}

add_action('widgets_init', 'twentytwenty_sidebar_registration');

/**
 * Enqueue supplemental block editor styles.
 *
 * @since Twenty Twenty 1.0
 * @since Twenty Twenty 2.4 Removed a script related to the obsolete Squared style of Button blocks.
 * @since Twenty Twenty 2.6 Enqueue the CSS file for the variable font.
 */
function twentytwenty_block_editor_styles()
{

	// Enqueue the editor styles.
	wp_enqueue_style('twentytwenty-block-editor-styles', get_theme_file_uri('/assets/css/editor-style-block.css'), array(), wp_get_theme()->get('Version'), 'all');
	wp_style_add_data('twentytwenty-block-editor-styles', 'rtl', 'replace');

	// Add inline style from the Customizer.
	$customizer_css = twentytwenty_get_customizer_css('block-editor');
	if ($customizer_css) {
		wp_add_inline_style('twentytwenty-block-editor-styles', $customizer_css);
	}

	// Enqueue the CSS file for the variable font, Inter.
	wp_enqueue_style('twentytwenty-fonts', get_theme_file_uri('/assets/css/font-inter.css'), array(), wp_get_theme()->get('Version'), 'all');

	// Add inline style for non-latin fonts.
	$custom_css = TwentyTwenty_Non_Latin_Languages::get_non_latin_css('block-editor');
	if ($custom_css) {
		wp_add_inline_style('twentytwenty-block-editor-styles', $custom_css);
	}
}

if (is_admin() && version_compare($GLOBALS['wp_version'], '6.3', '>=')) {
	add_action('enqueue_block_assets', 'twentytwenty_block_editor_styles', 1, 1);
} else {
	add_action('enqueue_block_editor_assets', 'twentytwenty_block_editor_styles', 1, 1);
}

/**
 * Enqueue classic editor styles.
 *
 * @since Twenty Twenty 1.0
 * @since Twenty Twenty 2.6 Enqueue the CSS file for the variable font.
 */
function twentytwenty_classic_editor_styles()
{

	$classic_editor_styles = array(
		'/assets/css/editor-style-classic.css',
		'/assets/css/font-inter.css',
	);

	add_editor_style($classic_editor_styles);
}

add_action('init', 'twentytwenty_classic_editor_styles');

/**
 * Output Customizer settings in the classic editor.
 * Adds styles to the head of the TinyMCE iframe. Kudos to @Otto42 for the original solution.
 *
 * @since Twenty Twenty 1.0
 *
 * @param array $mce_init TinyMCE styles.
 * @return array TinyMCE styles.
 */
function twentytwenty_add_classic_editor_customizer_styles($mce_init)
{

	$styles = twentytwenty_get_customizer_css('classic-editor');

	if (!$styles) {
		return $mce_init;
	}

	if (!isset($mce_init['content_style'])) {
		$mce_init['content_style'] = $styles . ' ';
	} else {
		$mce_init['content_style'] .= ' ' . $styles . ' ';
	}

	return $mce_init;
}

add_filter('tiny_mce_before_init', 'twentytwenty_add_classic_editor_customizer_styles');

/**
 * Output non-latin font styles in the classic editor.
 * Adds styles to the head of the TinyMCE iframe. Kudos to @Otto42 for the original solution.
 *
 * @param array $mce_init TinyMCE styles.
 * @return array TinyMCE styles.
 */
function twentytwenty_add_classic_editor_non_latin_styles($mce_init)
{

	$styles = TwentyTwenty_Non_Latin_Languages::get_non_latin_css('classic-editor');

	// Return if there are no styles to add.
	if (!$styles) {
		return $mce_init;
	}

	if (!isset($mce_init['content_style'])) {
		$mce_init['content_style'] = $styles . ' ';
	} else {
		$mce_init['content_style'] .= ' ' . $styles . ' ';
	}

	return $mce_init;
}

add_filter('tiny_mce_before_init', 'twentytwenty_add_classic_editor_non_latin_styles');

/**
 * Block Editor Settings.
 * Add custom colors and font sizes to the block editor.
 *
 * @since Twenty Twenty 1.0
 */
function twentytwenty_block_editor_settings()
{

	// Block Editor Palette.
	$editor_color_palette = array(
		array(
			'name' => __('Accent Color', 'twentytwenty'),
			'slug' => 'accent',
			'color' => twentytwenty_get_color_for_area('content', 'accent'),
		),
		array(
			'name' => _x('Primary', 'color', 'twentytwenty'),
			'slug' => 'primary',
			'color' => twentytwenty_get_color_for_area('content', 'text'),
		),
		array(
			'name' => _x('Secondary', 'color', 'twentytwenty'),
			'slug' => 'secondary',
			'color' => twentytwenty_get_color_for_area('content', 'secondary'),
		),
		array(
			'name' => __('Subtle Background', 'twentytwenty'),
			'slug' => 'subtle-background',
			'color' => twentytwenty_get_color_for_area('content', 'borders'),
		),
	);

	// Add the background option.
	$background_color = get_theme_mod('background_color');
	if (!$background_color) {
		$background_color_arr = get_theme_support('custom-background');
		$background_color = $background_color_arr[0]['default-color'];
	}
	$editor_color_palette[] = array(
		'name' => __('Background Color', 'twentytwenty'),
		'slug' => 'background',
		'color' => '#' . $background_color,
	);

	// If we have accent colors, add them to the block editor palette.
	if ($editor_color_palette) {
		add_theme_support('editor-color-palette', $editor_color_palette);
	}

	// Block Editor Font Sizes.
	add_theme_support(
		'editor-font-sizes',
		array(
			array(
				'name' => _x('Small', 'Name of the small font size in the block editor', 'twentytwenty'),
				'shortName' => _x('S', 'Short name of the small font size in the block editor.', 'twentytwenty'),
				'size' => 18,
				'slug' => 'small',
			),
			array(
				'name' => _x('Regular', 'Name of the regular font size in the block editor', 'twentytwenty'),
				'shortName' => _x('M', 'Short name of the regular font size in the block editor.', 'twentytwenty'),
				'size' => 21,
				'slug' => 'normal',
			),
			array(
				'name' => _x('Large', 'Name of the large font size in the block editor', 'twentytwenty'),
				'shortName' => _x('L', 'Short name of the large font size in the block editor.', 'twentytwenty'),
				'size' => 26.25,
				'slug' => 'large',
			),
			array(
				'name' => _x('Larger', 'Name of the larger font size in the block editor', 'twentytwenty'),
				'shortName' => _x('XL', 'Short name of the larger font size in the block editor.', 'twentytwenty'),
				'size' => 32,
				'slug' => 'larger',
			),
		)
	);

	add_theme_support('editor-styles');

	// If we have a dark background color then add support for dark editor style.
	// We can determine if the background color is dark by checking if the text-color is white.
	if ('#ffffff' === strtolower(twentytwenty_get_color_for_area('content', 'text'))) {
		add_theme_support('dark-editor-style');
	}
}

add_action('after_setup_theme', 'twentytwenty_block_editor_settings');

/**
 * Overwrite default more tag with styling and screen reader markup.
 *
 * @param string $html The default output HTML for the more tag.
 * @return string
 */
function twentytwenty_read_more_tag($html)
{
	return preg_replace('/<a(.*)>(.*)<\/a>/iU', sprintf('<div class="read-more-button-wrap"><a$1><span class="faux-button">$2</span> <span class="screen-reader-text">"%1$s"</span></a></div>', get_the_title(get_the_ID())), $html);
}

add_filter('the_content_more_link', 'twentytwenty_read_more_tag');

/**
 * Enqueues scripts for customizer controls & settings.
 *
 * @since Twenty Twenty 1.0
 *
 * @return void
 */
function twentytwenty_customize_controls_enqueue_scripts()
{
	$theme_version = wp_get_theme()->get('Version');

	// Add main customizer js file.
	wp_enqueue_script('twentytwenty-customize', get_template_directory_uri() . '/assets/js/customize.js', array('jquery'), $theme_version);

	// Add script for color calculations.
	wp_enqueue_script('twentytwenty-color-calculations', get_template_directory_uri() . '/assets/js/color-calculations.js', array('wp-color-picker'), $theme_version);

	// Add script for controls.
	wp_enqueue_script('twentytwenty-customize-controls', get_template_directory_uri() . '/assets/js/customize-controls.js', array('twentytwenty-color-calculations', 'customize-controls', 'underscore', 'jquery'), $theme_version);
	wp_localize_script('twentytwenty-customize-controls', 'twentyTwentyBgColors', twentytwenty_get_customizer_color_vars());
}

add_action('customize_controls_enqueue_scripts', 'twentytwenty_customize_controls_enqueue_scripts');

/**
 * Enqueue scripts for the customizer preview.
 *
 * @since Twenty Twenty 1.0
 *
 * @return void
 */
function twentytwenty_customize_preview_init()
{
	$theme_version = wp_get_theme()->get('Version');

	wp_enqueue_script('twentytwenty-customize-preview', get_theme_file_uri('/assets/js/customize-preview.js'), array('customize-preview', 'customize-selective-refresh', 'jquery'), $theme_version, array('in_footer' => true));
	wp_localize_script('twentytwenty-customize-preview', 'twentyTwentyBgColors', twentytwenty_get_customizer_color_vars());
	wp_localize_script('twentytwenty-customize-preview', 'twentyTwentyPreviewEls', twentytwenty_get_elements_array());

	wp_add_inline_script(
		'twentytwenty-customize-preview',
		sprintf(
			'wp.customize.selectiveRefresh.partialConstructor[ %1$s ].prototype.attrs = %2$s;',
			wp_json_encode('cover_opacity'),
			wp_json_encode(twentytwenty_customize_opacity_range())
		)
	);
}

add_action('customize_preview_init', 'twentytwenty_customize_preview_init');

/**
 * Get accessible color for an area.
 *
 * @since Twenty Twenty 1.0
 *
 * @param string $area    The area we want to get the colors for.
 * @param string $context Can be 'text' or 'accent'.
 * @return string Returns a HEX color.
 */
function twentytwenty_get_color_for_area($area = 'content', $context = 'text')
{

	// Get the value from the theme-mod.
	$settings = get_theme_mod(
		'accent_accessible_colors',
		array(
			'content' => array(
				'text' => '#000000',
				'accent' => '#cd2653',
				'secondary' => '#6d6d6d',
				'borders' => '#dcd7ca',
			),
			'header-footer' => array(
				'text' => '#000000',
				'accent' => '#cd2653',
				'secondary' => '#6d6d6d',
				'borders' => '#dcd7ca',
			),
		)
	);

	// If we have a value return it.
	if (isset($settings[$area]) && isset($settings[$area][$context])) {
		return $settings[$area][$context];
	}

	// Return false if the option doesn't exist.
	return false;
}

/**
 * Returns an array of variables for the customizer preview.
 *
 * @since Twenty Twenty 1.0
 *
 * @return array
 */
function twentytwenty_get_customizer_color_vars()
{
	$colors = array(
		'content' => array(
			'setting' => 'background_color',
		),
		'header-footer' => array(
			'setting' => 'header_footer_background_color',
		),
	);
	return $colors;
}

/**
 * Get an array of elements.
 *
 * @since Twenty Twenty 1.0
 *
 * @return array
 */
function twentytwenty_get_elements_array()
{

	// The array is formatted like this:
	// [key-in-saved-setting][sub-key-in-setting][css-property] = [elements].
	$elements = array(
		'content' => array(
			'accent' => array(
				'color' => array('.color-accent', '.color-accent-hover:hover', '.color-accent-hover:focus', ':root .has-accent-color', '.has-drop-cap:not(:focus):first-letter', '.wp-block-button.is-style-outline', 'a'),
				'border-color' => array('blockquote', '.border-color-accent', '.border-color-accent-hover:hover', '.border-color-accent-hover:focus'),
				'background-color' => array('button', '.button', '.faux-button', '.wp-block-button__link', '.wp-block-file .wp-block-file__button', 'input[type="button"]', 'input[type="reset"]', 'input[type="submit"]', '.bg-accent', '.bg-accent-hover:hover', '.bg-accent-hover:focus', ':root .has-accent-background-color', '.comment-reply-link'),
				'fill' => array('.fill-children-accent', '.fill-children-accent *'),
			),
			'background' => array(
				'color' => array(':root .has-background-color', 'button', '.button', '.faux-button', '.wp-block-button__link', '.wp-block-file__button', 'input[type="button"]', 'input[type="reset"]', 'input[type="submit"]', '.wp-block-button', '.comment-reply-link', '.has-background.has-primary-background-color:not(.has-text-color)', '.has-background.has-primary-background-color *:not(.has-text-color)', '.has-background.has-accent-background-color:not(.has-text-color)', '.has-background.has-accent-background-color *:not(.has-text-color)'),
				'background-color' => array(':root .has-background-background-color'),
			),
			'text' => array(
				'color' => array('body', '.entry-title a', ':root .has-primary-color'),
				'background-color' => array(':root .has-primary-background-color'),
			),
			'secondary' => array(
				'color' => array('cite', 'figcaption', '.wp-caption-text', '.post-meta', '.entry-content .wp-block-archives li', '.entry-content .wp-block-categories li', '.entry-content .wp-block-latest-posts li', '.wp-block-latest-comments__comment-date', '.wp-block-latest-posts__post-date', '.wp-block-embed figcaption', '.wp-block-image figcaption', '.wp-block-pullquote cite', '.comment-metadata', '.comment-respond .comment-notes', '.comment-respond .logged-in-as', '.pagination .dots', '.entry-content hr:not(.has-background)', 'hr.styled-separator', ':root .has-secondary-color'),
				'background-color' => array(':root .has-secondary-background-color'),
			),
			'borders' => array(
				'border-color' => array('pre', 'fieldset', 'input', 'textarea', 'table', 'table *', 'hr'),
				'background-color' => array('caption', 'code', 'code', 'kbd', 'samp', '.wp-block-table.is-style-stripes tbody tr:nth-child(odd)', ':root .has-subtle-background-background-color'),
				'border-bottom-color' => array('.wp-block-table.is-style-stripes'),
				'border-top-color' => array('.wp-block-latest-posts.is-grid li'),
				'color' => array(':root .has-subtle-background-color'),
			),
		),
		'header-footer' => array(
			'accent' => array(
				'color' => array('body:not(.overlay-header) .primary-menu > li > a', 'body:not(.overlay-header) .primary-menu > li > .icon', '.modal-menu a', '.footer-menu a, .footer-widgets a:where(:not(.wp-block-button__link))', '#site-footer .wp-block-button.is-style-outline', '.wp-block-pullquote:before', '.singular:not(.overlay-header) .entry-header a', '.archive-header a', '.header-footer-group .color-accent', '.header-footer-group .color-accent-hover:hover'),
				'background-color' => array('.social-icons a', '#site-footer button:not(.toggle)', '#site-footer .button', '#site-footer .faux-button', '#site-footer .wp-block-button__link', '#site-footer .wp-block-file__button', '#site-footer input[type="button"]', '#site-footer input[type="reset"]', '#site-footer input[type="submit"]'),
			),
			'background' => array(
				'color' => array('.social-icons a', 'body:not(.overlay-header) .primary-menu ul', '.header-footer-group button', '.header-footer-group .button', '.header-footer-group .faux-button', '.header-footer-group .wp-block-button:not(.is-style-outline) .wp-block-button__link', '.header-footer-group .wp-block-file__button', '.header-footer-group input[type="button"]', '.header-footer-group input[type="reset"]', '.header-footer-group input[type="submit"]'),
				'background-color' => array('#site-header', '.footer-nav-widgets-wrapper', '#site-footer', '.menu-modal', '.menu-modal-inner', '.search-modal-inner', '.archive-header', '.singular .entry-header', '.singular .featured-media:before', '.wp-block-pullquote:before'),
			),
			'text' => array(
				'color' => array('.header-footer-group', 'body:not(.overlay-header) #site-header .toggle', '.menu-modal .toggle'),
				'background-color' => array('body:not(.overlay-header) .primary-menu ul'),
				'border-bottom-color' => array('body:not(.overlay-header) .primary-menu > li > ul:after'),
				'border-left-color' => array('body:not(.overlay-header) .primary-menu ul ul:after'),
			),
			'secondary' => array(
				'color' => array('.site-description', 'body:not(.overlay-header) .toggle-inner .toggle-text', '.widget .post-date', '.widget .rss-date', '.widget_archive li', '.widget_categories li', '.widget cite', '.widget_pages li', '.widget_meta li', '.widget_nav_menu li', '.powered-by-wordpress', '.footer-credits .privacy-policy', '.to-the-top', '.singular .entry-header .post-meta', '.singular:not(.overlay-header) .entry-header .post-meta a'),
			),
			'borders' => array(
				'border-color' => array('.header-footer-group pre', '.header-footer-group fieldset', '.header-footer-group input', '.header-footer-group textarea', '.header-footer-group table', '.header-footer-group table *', '.footer-nav-widgets-wrapper', '#site-footer', '.menu-modal nav *', '.footer-widgets-outer-wrapper', '.footer-top'),
				'background-color' => array('.header-footer-group table caption', 'body:not(.overlay-header) .header-inner .toggle-wrapper::before'),
			),
		),
	);

	/**
	 * Filters Twenty Twenty theme elements.
	 *
	 * @since Twenty Twenty 1.0
	 *
	 * @param array Array of elements.
	 */
	return apply_filters('twentytwenty_get_elements_array', $elements);
}


//Api Methods
class AdduserApi
{
	public function init()
	{
		add_action('rest_api_init', array($this, 'adduser_api_init'));
	}

	public function adduser_api_init()
	{
		register_rest_route(
			'adduser/v1',
			'get_user',
			array(
				'method' => 'GET',
				'callback' => array($this, 'get_user')
			)
		);

		register_rest_route(
			'adduser/v1',
			'get_users',
			array(
				'method' => 'GET',
				'callback' => array($this, 'get_users')
			)
		);

		register_rest_route(
			'adduser/v1',
			'get_posts',
			array(
				'method' => 'GET',
				'callback' => array($this, 'get_posts')
			)
		);

		register_rest_route(
			'adduser/v1',
			'createUser',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'createUser')
			)
		);
	}

	public function get_user(WP_REST_REQUEST $request)
	{
		$user = get_user_by('id', $request->get_param('user_id'));

		if (!$user) {
			return new WP_Error('user_not_found', 'User Not Found', array('status' => 404));
		}
		return $user;
	}

	public function get_users(WP_REST_Request $request)
	{
		$users = get_users();

		if (!$users) {
			return new WP_Error('users_not_found', 'Users not found', array('status' => 404));
		}
		return $users;
	}

	public function get_posts(WP_REST_Request $request)
	{

		$posts = get_posts();

		if (!$posts) {
			return new WP_Error('no_posts_found', 'No Posts Found', array('status' => 404));
		}


		// if(is_user_logged_in()) {
		// 	return $posts;
		// } else
		// return new WP_Error('user_not_logged_in', 'User not logged in', array('status'=> 404));

		if (is_user_logged_in()) {
			echo "You are logged in.";
		} else {
			echo "Log in to access content.";
		}
	}

	public function createUser(WP_REST_Request $request)
	{
		$username = sanitize_user($_POST['username']);
		$email = sanitize_email($_POST['email']);
		$password = $_POST['password'];
		$role = $_POST['role'];

		$user_id = wp_create_user($username, $password, $email);

		if (is_wp_error($user_id)) {
			wp_redirect(home_url('/register?registration_failed=true'));
			exit;
		}

		$user = new WP_User($user_id);
		$user->set_role($role);

		wp_redirect(home_url('/register?registration_success=true'));
		exit;
	}
}

$adduserApi = new AdduserApi();
$adduserApi->init();

function generate_custom_token_on_login($user_login, $user)
{
	$token = wp_generate_password(64, false);

	update_user_meta($user->ID, 'custom_token', $token);

	setcookie('custom_token', $token, time() + 3600, '/');
}
add_action('wp_login', 'generate_custom_token_on_login', 10, 2);


// function ourAssets() {
//     wp_enqueue_script('my-main-js', get_theme_file_uri('/index.js'), array(), null, true);
//     wp_localize_script('my-main-js', 'myData', array(
//         'sky' => 'blue',
//         'nonce' => wp_create_nonce('wp_rest')
//     ));
// }

// add_action('wp_enqueue_scripts', 'ourAssets');

add_filter('wp_is_application_passwords_available', '__return_true');
// rw2u PB1S X4hW JfdX BXCf BKmn



function custom_add_user_endpoint()
{
	register_rest_route('custom-api/v1', '/add-user/', array(
		'methods' => 'POST',
		'callback' => 'custom_add_user_callback',

	));
}

add_action('rest_api_init', 'custom_add_user_endpoint');
function custom_add_user_callback($request)
{
	$parameters = $request->get_params();

	$username = sanitize_text_field($parameters['username']);
	$email = sanitize_email($parameters['email']);
	$password = sanitize_text_field($parameters['password']);

	// Create the user
	$user_id = wp_create_user($username, $password, $email);

	if (is_wp_error($user_id)) {
		return new WP_Error('registration_error', $user_id->get_error_message(), array('status' => 400));
	}

	// Generate a token (you can use a secure method to generate a token)
	$token = wp_generate_password(32, false);

	// Store the token in user meta
	update_user_meta($user_id, 'custom_api_token', $token);

	$user = new WP_User($user_id);
	$user->set_role('subscriber');

	return array('message' => 'User created successfully', 'user_id' => $user_id, 'token' => $token);
}


function custom_user_login_endpoint()
{
	register_rest_route('custom-api/v1', '/login/', array(
		'methods' => 'POST',
		'callback' => 'custom_user_login_callback',
	));
}
add_action('rest_api_init', 'custom_user_login_endpoint');

function custom_user_login_callback($request)
{
	$parameters = $request->get_params();

	$username = sanitize_text_field($parameters['username']);
	$password = sanitize_text_field($parameters['password']);


	if (empty($username) || empty($password)) {
		return new WP_Error('login_error', 'Username and password are required.', array('status' => 400));
	}

	$user = wp_authenticate($username, $password);

	if (is_wp_error($user)) {
		return new WP_Error('login_error', $user->get_error_message(), array('status' => 401));
	}

	// Get the stored token from user meta
	$stored_token = get_user_meta($user->ID, 'custom_api_token', true);

	// Check if the user has a valid token
	if (empty($stored_token)) {
		return new WP_Error('token_error', 'Token not found for the user.', array('status' => 401));
	}

	return array(
		'message' => 'User logged in successfully',
		'user_id' => $user->ID,
		'token' => $stored_token,
	);
}


function custom_api_shortcode()
{
	// Replace with your API endpoint URL
	$api_url = 'http://localhost/apiTesing/wp-json/custom/v1/demo';

	// Fetch data from the API
	$response = wp_remote_get($api_url);

	if (!is_wp_error($response)) {
		$data = wp_remote_retrieve_body($response);
		return '<div class="api-data">' . wp_kses_post($data) . '</div>'; // Return API content
	} else {
		return '<p>Error fetching API data.</p>';
	}
}
add_shortcode('custom_api', 'custom_api_shortcode');


// function to generate jwt token
function generate_jwt_token($user)
{
	$secret_key = defined('JWT_AUTH_SECRET_KEY') ? JWT_AUTH_SECRET_KEY : '';

	// Create the token payload
	$payload = array(
		'user_id' => $user->ID,
		'user_email' => $user->data->user_email,
		'user_nicename' => $user->data->user_nicename,
		'user_display_name' => $user->data->display_name,
		'first_name' => get_user_meta($user->ID, 'first_name', true),
		'iat' => time(),
		'exp' => time() + (7 * 24 * 60 * 60),
	);

	// Check if the Firebase JWT library is loaded, if not, load it
	if (!class_exists('Firebase\JWT\JWT')) {
		require 'vendor/firebase/php-jwt/src/JWT.php';
	}

	// Attempt to encode the payload into a JWT token
	try {
		$token = \Firebase\JWT\JWT::encode($payload, $secret_key, 'HS256');
	} catch (Exception $e) {
		// If an error occurs during encoding, return a WP_Error with status 500
		return new WP_Error('jwt_encode_error', 'Error encoding token: ' . $e->getMessage(), array('status' => 500));
	}

	return $token;

}

// function to varify authentication
function verify_jwt_token($request)
{
	// Get the token from the request headers
	$token = $request->get_header('Authorization');
	if (!$token) {
		return new WP_Error('jwt_missing', 'JWT token is missing', array('status' => 401));
	}

	// Extract JWT token from the Authorization header
	list($jwt) = sscanf($token, 'Bearer %s');
	if (!$jwt) {
		return new WP_Error('jwt_invalid', 'Invalid JWT token', array('status' => 401));
	}

	// Check if the Firebase JWT library is loaded, if not, load it
	if (!class_exists('Firebase\JWT\JWT')) {
		require 'vendor/autoload.php';
	}

	// Verify JWT token
	$secret_key = defined('JWT_AUTH_SECRET_KEY') ? JWT_AUTH_SECRET_KEY : '';
	try {
		$decoded = \Firebase\JWT\JWT::decode($jwt, new \Firebase\JWT\Key($secret_key, 'HS256'));
		$user_id = $decoded->user_id;

		// Attach user ID to the request object
		$request->set_param('user_id', $user_id);
	} catch (Exception $e) {
		return new WP_Error('jwt_invalid', 'Invalid JWT token: ' . $e->getMessage(), array('status' => 401));
	}

	// Check token expiry
	if (isset($decoded->exp) && $decoded->exp < time()) {
		return new WP_Error('jwt_expired', 'JWT token has expired', array('status' => 401));
	}

	// Token is valid
	return true;
}


// Api for sign-in user
add_action('rest_api_init', function () {
	register_rest_route(
		'custom/v1',
		'/sign-in',
		array(
			'methods' => 'POST',
			'callback' => 'custom_login_endpoint',
		)
	);
});

function custom_login_endpoint($request)
{

	$parameters = $request->get_params();

	$username = $parameters['username'];
	$password = $parameters['password'];


	$user = wp_authenticate($username, $password);

	if (is_wp_error($user)) {
		return new WP_REST_Response(array('error' => 'Invalid username or password'), 401);
	} else {
		wp_set_current_user($user->ID);
		wp_set_auth_cookie($user->ID);
	}

	$token = generate_jwt_token($user);

	$response = array(
		'success' => true,
		'token' => $token,
		'data' => array(
			'user_id' => $user->ID,
			'user_email' => $user->user_email,
			'user_nicename' => $user->user_nicename,
			'user_display_name' => $user->display_name,
			'user_first_name' => $user->first_name,
		),
	);
	return new WP_REST_Response($response, 200);

}
	

// Api for sign up
add_action('rest_api_init', function () {
	register_rest_route(
		'custom/v1',
		'/create-user',
		array(
			'methods' => 'POST',
			'callback' => 'rest_create_user',
		)
	);
});

function rest_create_user($request)
{
    $parameters = $request->get_params();
    $username = sanitize_text_field($parameters['username']);
    $email = sanitize_email($parameters['email']);
    $password = sanitize_text_field($parameters['password']);

    if (empty($username) || empty($email) || empty($password)) {
        return new WP_REST_Response('Missing required parameters.', 400);
    }

    if (username_exists($username) || email_exists($email)) {
        return new WP_REST_Response('Username or email already exists.', 400);
    }

    $user_id = wp_insert_user(array(
        'user_login' => $username,
        'user_email' => $email,
        'user_pass' => $password,
    ));

    if (is_wp_error($user_id)) {
        return new WP_REST_Response('Failed to create user.', 500);
    }

    $user = get_user_by('id', $user_id);

    if (!$user) {
        return new WP_REST_Response('Failed to retrieve user data.', 500);
    }

    $token = generate_jwt_token($user);

    $response = array(
        'success' => true,
        'token' => $token,
        'data' => array(
            'user_id' => $user->ID,
            'user_email' => $user->user_email,
            'user_nicename' => $user->user_nicename,
            'user_display_name' => $user->display_name,
            'first_name' => get_user_meta($user->ID, 'first_name', true)
        ),
    );

    return new WP_REST_Response($response, 200);
}


//api to fetch all posts
add_action('rest_api_init', function () {
	register_rest_route(
		'custom/v1',
		'/fetch-posts',
		array(
			'methods' => 'GET',
			'callback' => 'get_all_posts',
			//'permission_callback' => 'verify_jwt_token',
		)
	);
});

function get_all_posts(WP_REST_Request $request)
{

	$args = array(
		'post_type' => 'post',
		'post_status' => 'publish',
		'posts_per_page' => -1,
	);

	$posts = get_posts($args);

	$response = array(
		'success' => true,
		'data' => $posts,
	);

	return new WP_REST_Response($response, 200);
}


//api to fetch all books
add_action('rest_api_init', function () {
	register_rest_route(
		'custom/v1',
		'/fetch-books',
		array(
			'methods' => 'GET',
			'callback' => 'get_all_books',
			'permission_callback' => 'verify_jwt_token',
		)
	);
});

function get_all_books(WP_REST_Request $request)
{

	$args = array(
		'post_type' => 'book',
		'post_status' => 'publish',
		'posts_per_page' => -1,
	);

	$posts = get_posts($args);

	$response = array(
		'success' => true,
		'data' => $posts,
	);

	return new WP_REST_Response($response, 200);
}


//api to fetch all playlists
add_action('rest_api_init', function () {
	register_rest_route(
		'custom/v1',
		'/fetch-playlist',
		array(
			'methods' => 'GET',
			'callback' => 'get_all_playlist',
			'permission_callback' => 'verify_jwt_token',
		)
	);
});

function get_all_playlist(WP_REST_Request $request)
{

	$args = array(
		'post_type' => 'bb_playlist_player',
		'post_status' => 'publish',
		'posts_per_page' => -1,
	);

	$posts = get_posts($args);

	$response = array(
		'success' => true,
		'data' => $posts,
	);

	return new WP_REST_Response($response, 200);
}


//api to fetch all category and their related posts
add_action('rest_api_init', function () {
	register_rest_route(
		'custom/v1',
		'fetch-categories',
		array(
			'methods' => 'GET',
			'callback' => 'get_all_categories_with_posts',
			'permission_callback' => 'verify_jwt_token',
		)
	);
});

function get_all_categories_with_posts()
{
	$categories = get_categories(
		array(
			'taxonomy' => 'category',
			'hide_empty' => false,
		)
	);

	$formatted_categories = array();

	foreach ($categories as $category) {
		$posts = get_posts(
			array(
				'category' => $category->term_id,
				'posts_per_page' => -1,
			)
		);

		$formatted_posts = array();
		foreach ($posts as $post) {
			$formatted_posts[] = array(
				'id' => $post->ID,
				'title' => $post->post_title,
				'content' => $post->post_content,

			);
		}

		$formatted_categories[] = array(
			'id' => $category->term_id,
			'name' => $category->name,
			'slug' => $category->slug,
			'description' => $category->description,
			'posts' => $formatted_posts,
		);
	}


	$response = rest_ensure_response($formatted_categories);

	$response->set_status(200);
	$response->header('Content-Type', 'application/json');

	return $response;
}



//api to create new post
add_action('rest_api_init', function () {
	register_rest_route(
		'custom/v1',
		'/create-post',
		array(
			'methods' => 'POST',
			'callback' => 'create_post_callback',
			'permission_callback' => 'verify_jwt_token',
		)
	);
});

function create_post_callback($request)
{
	$user_id = $request->get_param('user_id');

	$post_title = sanitize_text_field($request->get_param('title'));
	$post_content = wp_kses_post($request->get_param('content'));
	$post_category = sanitize_text_field($request->get_param('category'));
	
	$new_post = array(
		'post_title' => $post_title,
		'post_content' => $post_content,
		'post_status' => 'publish',
		'post_author' => $user_id,
		'post_type' => 'post',
	);

	$post_id = wp_insert_post($new_post);
	
	if($post_id){
		if(!$post_category){
			wp_set_object_terms($post_id, 'shopping', 'category', false);
		} else{
			wp_set_object_terms($post_id, $post_category, 'category', false);
		}
	}
	if (is_wp_error($post_id)) {
		error_log('Error creating post: ' . $post_id->get_error_message());
		return new WP_Error('error_creating_post', $post_id->get_error_message(), array('status' => 500));
	} elseif ($post_id === 0) {
		error_log('Unknown error occurred while creating post');
		return new WP_Error('error_creating_post', 'Unknown error occurred while creating post', array('status' => 500));
	} else {
		return new WP_REST_Response('Post created successfully', 200);
	}
}


//api to update posts
add_action('rest_api_init', function () {
	register_rest_route(
		'custom/v1',
		'/update-post',
		array(
			'methods' => 'POST',
			'callback' => 'update_posts',
			'permission_callback' => 'verify_jwt_token',
		)
	);
});

function update_posts($request) {

    $post_id = $request->get_param('post_id');
    $post_title = $request->get_param('post_title');
	$post_content = $request->get_param('post_content');
	$post_category = sanitize_text_field($request->get_param('category'));

    $updated_post = array(
        'ID' => $post_id,
        'post_title' => $post_title,
		'post_content' => $post_content
    );
    
    wp_update_post($updated_post);

	if($post_id){
		wp_set_object_terms($post_id, $post_category, 'category', false);
	}	
    
    $response = array(
        'message' => 'Post updated successfully',
        'post_id' => $post_id,
	    );
    
    return new WP_REST_Response($response, 200);
}


//api to delete a post
add_action('rest_api_init', function() {
	register_rest_route(
		'custom/v1',
		'delete-post',
		array(
			'methods' => 'POST',
			'callback' => 'delete_posts',
			'permission' => 'varify_jwt_token',
		)
	);
});

function delete_posts($request) {
    $post_id = $request->get_param('post_id');

    if (!$post_id) {
        return new WP_REST_Response('Post ID is required', 400);
    }

    if (!get_post($post_id)) {
        return new WP_REST_Response('No post found with this ID', 404);
    }

    $post_delete = wp_delete_post($post_id);

    if (!$post_delete || is_wp_error($post_delete)) {
        $error_message = $post_delete->get_error_message();
        return new WP_REST_Response('Failed to delete the post: ' . $error_message, 500);
    } else {
        return new WP_REST_Response('Post deleted successfully', 200);
    }
}




//enqueue for the registration
function enqueue_custom_script() {
    if ( is_page('registration') ) {
        wp_enqueue_script('custom-script', get_template_directory_uri() . '/api.js', array('jquery'), null, true);

        wp_localize_script('custom-script', 'apiVars', array(
            'signUpUrl' => esc_url(rest_url('custom/v1/create-user'))
        ));
    }
}
add_action('wp_enqueue_scripts', 'enqueue_custom_script');


//hide Registration menu if user login
function exclude_menu_item_when_logged_in( $items, $menu, $args ) {
    if ( is_user_logged_in() ) {
        foreach ( $items as $key => $item ) {
            if ( $item->title === 'Registration' || $item->title === 'login') {
                unset( $items[$key] );
            }
        }
    }
    return $items;
}
add_filter( 'wp_get_nav_menu_items', 'exclude_menu_item_when_logged_in', 10, 3 );

function redirect_from_login_registration_pages() {
    if ( is_user_logged_in() ) {
        $registration_page_slug = 'registration'; 
        $login_page_slug = 'login'; 

        if ( is_page($registration_page_slug) || is_page($login_page_slug) ) {
            wp_redirect( home_url('/') );
            exit();
        }
    }
}
add_action( 'template_redirect', 'redirect_from_login_registration_pages' );



//create custom post type
function create_custom_post_type()
{
	$labels = array(
		'name' => _x('Books', 'Post Type General Name', 'text_domain'),
		'singular_name' => _x('Book', 'Post Type Singular Name', 'text_domain'),
		'menu_name' => __('Books', 'text_domain'),
		'name_admin_bar' => __('Book', 'text_domain'),
		'archives' => __('Book Archives', 'text_domain'),
		'attributes' => __('Book Attributes', 'text_domain'),
		'parent_item_colon' => __('Parent Book:', 'text_domain'),
		'all_items' => __('All Books', 'text_domain'),
		'add_new_item' => __('Add New Book', 'text_domain'),
		'add_new' => __('Add New', 'text_domain'),
		'new_item' => __('New Book', 'text_domain'),
		'edit_item' => __('Edit Book', 'text_domain'),
		'update_item' => __('Update Book', 'text_domain'),
		'view_item' => __('View Book', 'text_domain'),
		'view_items' => __('View Books', 'text_domain'),
		'search_items' => __('Search Book', 'text_domain'),
		'not_found' => __('Not found', 'text_domain'),
		'not_found_in_trash' => __('Not found in Trash', 'text_domain'),
		'featured_image' => __('Featured Image', 'text_domain'),
		'set_featured_image' => __('Set featured image', 'text_domain'),
		'remove_featured_image' => __('Remove featured image', 'text_domain'),
		'use_featured_image' => __('Use as featured image', 'text_domain'),
		'insert_into_item' => __('Insert into book', 'text_domain'),
		'uploaded_to_this_item' => __('Uploaded to this book', 'text_domain'),
		'items_list' => __('Books list', 'text_domain'),
		'items_list_navigation' => __('Books list navigation', 'text_domain'),
		'filter_items_list' => __('Filter books list', 'text_domain'),
	);
	$args = array(
		'label' => __('Book', 'text_domain'),
		'description' => __('Post Type Description', 'text_domain'),
		'labels' => $labels,
		'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
		'taxonomies' => array('genre', 'post_tag'),
		'hierarchical' => false,
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 5,
		'show_in_admin_bar' => true,
		'show_in_nav_menus' => true,
		'can_export' => true,
		'has_archive' => true,
		'exclude_from_search' => false,
		'publicly_queryable' => true,
		'capability_type' => 'post',
	);
	register_post_type('book', $args);

}
add_action('init', 'create_custom_post_type', 0);


// function delete_book_attachments() {
//     // Arguments for fetching posts of custom post type 'books'
//     $args = array(
//         'post_type'      => 'book',
//         'posts_per_page' => -1,
//         'post_status'    => 'any',
//     );

//     $book_posts = new WP_Query($args);

//     if ($book_posts->have_posts()) {
//         while ($book_posts->have_posts()) {
//             $book_posts->the_post();
//             $post_id = get_the_ID();

//             $attachments = get_attached_media('', $post_id);

//             foreach ($attachments as $attachment) {
//                 wp_delete_attachment($attachment->ID, true);
//             }
// 			if ( has_post_thumbnail($post_id) ) {
//                 $featured_image_id = get_post_thumbnail_id($post_id);

//                 delete_post_thumbnail($post_id);
//             }

//         }
//     }

//     wp_reset_postdata();
// }


// function remove_images_from_custom_posts() {
//     $args = array(
//         'post_type' => 'book',
//         'posts_per_page' => -1, 
//     );

//     $books_query = new WP_Query($args);

//     if ($books_query->have_posts()) {
//         while ($books_query->have_posts()) {
//             $books_query->the_post();

//             $post_id = get_the_ID();

//             $post_content = get_post_field('post_content', $post_id);

//             $updated_content = remove_images_from_content($post_content);

//             wp_update_post(array(
//                 'ID'           => $post_id,
//                 'post_content' => $updated_content,
//             ));
//         }
//     }

//     wp_reset_postdata();
// }

// function remove_images_from_content($content) {
//     $updated_content = preg_replace('/<img[^>]+>/', '', $content);
//     return $updated_content;
// }


// function get_url() {
// 	if(isset($_GET['url'])) {
// 		delete_book_attachments();
//         remove_images_from_custom_posts();
// 	}
// }
// get_url();



add_action('init', function () {
	add_rewrite_rule('^custom-action/?$', 'index.php?custom_action=1', 'top');
	flush_rewrite_rules();
});

add_filter('query_vars', function ($query_vars) {
	$query_vars[] = 'custom_action';
	return $query_vars;
});

add_action('template_redirect', function () {
	if ($action = get_query_var('custom_action')) {
		if ($action === 'delete_book_attachments') {
			require_once (get_template_directory() . '/delete-images.php');
			if (function_exists('delete_book_attachments')) {
				delete_book_attachments();
				remove_images_from_custom_posts();
			}
		}
		exit;
	}
});











