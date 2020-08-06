<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Polylang and Beaver Builder Page Builder compatibility plugin
 * Plugin URI:        https://www.kntnt.com/
 * Description:       Makes Polylang compatible with Beaver Builder Page Builder.
 * Version:           1.1.1
 * Author:            Thomas Barregren
 * Author URI:        https://www.kntnt.com/
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 */


namespace Kntnt\PLL_BB_Plugin_Compatibility;

defined( 'ABSPATH' ) && new Plugin;

final class Plugin {

    public function __construct() {
        add_action( 'plugins_loaded', [ $this, 'run' ] );
    }

    public function run() {
        if ( class_exists( 'FLBuilderLoader' ) && class_exists( 'PLL_FLBuilder' ) ) {
            add_filter( 'fl_builder_loop_query_args', [ $this, 'fl_builder_loop_query_args' ] );
            add_filter( 'rewrite_rules_array', [ $this, 'rewrite_rules_array' ] );
        };
    }

    public function fl_builder_loop_query_args( $args ) {
        $args['lang'] = pll_current_language();
        return $args;
    }

    // Beaver Builder add its rewrite rules to $wp_rewrite->extra_rules_top
    // which isn't processed by PLL_Links_Directory->rewrite_rules(). This
    // function extracts the  extra rules added to the top and process them
    // the same way as Polylang does.
    public function rewrite_rules_array( $rules ) {

        global $wp_rewrite;
        $newrules = [];

        $languages = PLL()->model->get_languages_list( [ 'fields' => 'slug' ] );
        if ( PLL()->options['hide_default'] ) {
            $languages = array_diff( $languages, [ PLL()->options['default_lang'] ] );
        }

        if ( ! empty( $languages ) ) {
            $slug = $wp_rewrite->root . ( PLL()->options['rewrite'] ? '' : 'language/' ) . '(' . implode( '|', $languages ) . ')/';
        }

        foreach ( $rules as $key => $rule ) {
            $newrules[ $slug . str_replace( $wp_rewrite->root, '', ltrim( $key, '^' ) ) ] = str_replace(
                [ '[8]', '[7]', '[6]', '[5]', '[4]', '[3]', '[2]', '[1]', '?' ],
                [ '[9]', '[8]', '[7]', '[6]', '[5]', '[4]', '[3]', '[2]', '?lang=$matches[1]&' ],
                $rule
            );
            $newrules[ $key ] = $rule;
        }

        return $newrules;

    }

}
