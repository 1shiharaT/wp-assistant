<?php
if ( ! class_exists( 'Ggs_Cleanup' ) ) {
	class Ggs_Cleanup {

		/**
		 * 初期化
		 */
		public function __construct() {
			add_action( 'wp_head', array( $this, 'head_cleaner' ), 10 );
			add_action( 'wp_head', array( $this, 'link_tag_cleaner' ), 10 );
			add_action( 'init', array( $this, 'init' ), 10 );
		}

		public function init() {
			$options = $this->get_options();

			foreach ( $options as $option_key => $option ) {

				/**
				 * サイトの設定なおかつ、対応したメソッドが存在する場合発動
				 */
				if ( strpos( $option_key, 'general' )
				     && method_exists( $this, $option_key )
				) {

					$this->{$option_key}( $option );
				}
			}
		}

		public function get_options() {
			$options = Ggs_Helper::get_ggs_options();

			return $options;
		}

		/**
		 * wp_headから余計な記述を削除
		 *
		 * @return void
		 */
		public function head_cleaner() {
			remove_action( 'wp_head', 'wlwmanifest_link' );
			remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
			remove_action( 'wp_head', 'wp_generator' );
			remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
			global $wp_widget_factory;
			remove_action( 'wp_head',
				array(
					$wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
					'recent_comments_style',
				)
			);
		}

		/**
		 * link タグの余計な記述を削除
		 *
		 * @param  string $input
		 *
		 * @return string <link> tag
		 */
		public function link_tag_cleaner( $input ) {
			preg_match_all( "!<link rel='stylesheet'\s?(id='[^']+')?\s+href='(.*)' type='text/css' media='(.*)' />!", $input, $matches );
			// Only display media if it is meaningful
			$media = $matches[3][0] !== '' && $matches[3][0] !== 'all' ? ' media="' . $matches[3][0] . '"' : '';

			return '<link rel="stylesheet" href="' . $matches[2][0] . '"' . $media . '>' . "\n";
		}

		/**
		 * フィードリンクの出力
		 *
		 * @param $option
		 */
		public function ggsupports_general_feed_links( $option ) {
			if ( ! intval( $option ) ) {
				remove_action( 'wp_head', 'feed_links', 2 );
				remove_action( 'wp_head', 'feed_links_extra', 3 );
				remove_action( 'wp_head', 'rsd_link' );
			}
		}

		/**
		 * WordPressバージョン情報の出力
		 *
		 * @param $option
		 */
		public function ggsupports_general_wp_generator( $option ) {
			if ( ! intval( $option ) ) {
				remove_action( 'wp_head', 'wp_generator' );

				return false;
			}
		}

		/**
		 * ショートリンクの出力
		 *
		 * @param $option
		 */
		public function ggsupports_general_wp_shortlink_wp_head( $option ) {
			if ( ! intval( $option ) ) {
				remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );

				return false;
			}
		}

		/**
		 * 自動整形の停止
		 *
		 * @param $option
		 */
		public function ggsupports_general_wpautop( $option ) {
			if ( ! intval( $option ) ) {
				/**
				 * 通常コンテンツ
				 */
				remove_filter( 'the_excerpt', 'wpautop' );
				remove_filter( 'the_content', 'wpautop' );

				/**
				 * contact form 7
				 */
				if ( ! defined( 'WPCF7_AUTOP' ) ) {
					define( 'WPCF7_AUTOP', false );
				}

				/**
				 * Advanced Custom Field
				 */
				if ( function_exists( 'get_field' ) ) {
					remove_filter( 'acf_the_content', 'wpautop' );
				}

				return false;
			}
		}

		public function ggsupports_general_revision( $option ) {
			if ( ! intval( $option ) ) {
				// リビジョンの停止
				if ( ! defined( 'WP_POST_REVISIONS' ) ) {
					define( 'WP_POST_REVISIONS', false );
				}
				// 自動保存の停止
				add_action( 'wp_print_scripts', function () {
					wp_deregister_script( 'autosave' );
				} );

				return false;
			}
		}

		/**
		 * jQueryの読み込み
		 *
		 * @param $option
		 *
		 * @return bool
		 */
		public function ggsupports_general_jquery( $option ) {
			if ( intval( $option ) ) {
				if ( ! is_admin() ) {
					add_action( 'wp_enqueue_scripts', function () {
						wp_deregister_script( 'jquery' );
						wp_register_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js', array(), null, false );
						wp_enqueue_script( 'jquery' );
					}, 10 );
				}

				return true;
			}
		}

		/**
		 * xmlrpc の停止
		 *
		 * @param $option
		 */
		public function ggsupports_general_xmlrpc( $option ) {
			if ( intval( $option ) ) {
				add_filter(
					'xmlrpc_methods',
					function ( $methods ) {
						unset( $methods['pingback.ping'] );
						unset( $methods['pingback.extensions.getPingbacks'] );

						return $methods;
					}, 10, 1 );

				add_filter(
					'wp_headers',
					function ( $headers ) {
						unset( $headers['X-Pingback'] );

						return $headers;
					}, 10, 1 );
			}
		}
	}
}