<?php
class Cf7_ajaxzip3 {

	/**
	 * contact-form-7にajaxzip3.jsを追加
	 * 自動的に郵便番号から住所が入力
	 * @return bool
	 */
	function cf7_ajaxzip3() {
		global $post;

		/**
		 * cf7 がインストールされていない
		 * もしくは、ページの本文中にショートコードがない場合は
		 * 何もしない
		 */
		if ( ! defined( 'WPCF7_VERSION' )
		     || ( isset( $post->post_content )  && ! strpos( $post->post_content, 'contact-form-7' ) )
			 || is_admin()
		) {
			return false;
		}

		wp_enqueue_script( 'cf7_ajaxzip3', plugins_url( '/', __FILE__ ) . '/assets/js/ajaxzip3.js', array( 'jquery' ), null, false );

		add_action( 'wp_head', function () {
			?>
			<script type="text/javascript">
				(function ($) {
					$(function () {
						AjaxZip3.JSONDATA = "http://ajaxzip3.googlecode.com/svn/trunk/ajaxzip3/zipdata";
						$('#zip').keyup(function (event) {
							AjaxZip3.zip2addr('zip', '', 'pref', 'address');
							return false;
						})
					})
				})(jQuery);
			</script>
		<?
		}, 99 );
	}
}