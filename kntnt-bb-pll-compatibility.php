<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Beaver Builder and Polylang Compatibility plugin
 * Plugin URI:        https://www.kntnt.com/
 * Description:       Makes Beaver Builder Page Builder and Beaver Builder Themer compatible with Polylang.
 * Version:           1.0.0
 * Author:            Thomas Barregren
 * Author URI:        https://www.kntnt.com/
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 */


namespace Kntnt\BB_PLL_Compatibility;

defined( 'ABSPATH' ) && new Plugin;

final class Plugin {

    public function __construct() {
        add_action( 'init', [ $this, 'run' ] );
    }

    public function run() {
        if ( function_exists( 'pll_current_language' ) ) {
            add_filter( 'fl_builder_loop_query_args', [ $this, 'fl_builder_loop_query_args' ] );
            add_filter( 'fl_theme_builder_current_page_layouts', [ $this, 'fl_theme_builder_current_page_layouts' ] );
        };
    }

    public function fl_builder_loop_query_args( $args ) {
        $args['lang'] = pll_current_language();
        return $args;
    }

    public function fl_theme_builder_current_page_layouts( $layouts ) {
        $lang = pll_current_language();
        foreach ( $layouts as $layout => $posts ) {
            $layouts[ $layout ] = array_filter( $posts, function ( $post ) use ( $lang ) {
                return pll_get_post_language( $post['id'] ) == $lang;
            } );
        }
        return $layouts;
    }

}
