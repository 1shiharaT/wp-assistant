<?php
/**
 * =====================================================
 * エディターをカスタマイズ
 * @package   siteSupports
 * @author    Grow Group
 * @license   gpl v2 or later
 * @link      http://grow-group.jp
 * =====================================================
 */

namespace siteSupports\modules\aceEditor;

use siteSupports\config;
use siteSupports\inc\helper;
/**
 * Class Ace_editor
 */
class aceEditor {

	/**
	 * 初期化
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * ace を登録
	 */
	public function enqueue_scripts() {

		/** テーマの編集、プラグインの編集画面以外はリターン **/
		if (
			! strpos( $_SERVER['SCRIPT_NAME'], 'theme-editor.php' )
			&&
			! strpos( $_SERVER['SCRIPT_NAME'], 'plugin-editor.php' )
		) {
			return;
		}

		$ace_version = '1.1.8';
		wp_enqueue_script( 'ace-editor', '//cdnjs.cloudflare.com/ajax/libs/ace/' . $ace_version . '/ace.js' , array( 'jquery' ), null );
		wp_enqueue_script( 'emmet', '//nightwing.github.io/emmet-core/emmet.js', array( 'ace-editor' ), null );
		wp_enqueue_script( 'ace-editor-emmet', '//cdnjs.cloudflare.com/ajax/libs/ace/' . $ace_version . '/ext-emmet.js', array( 'ace-editor' ), null );
		wp_enqueue_script( 'ace-editor-launguage', '//cdnjs.cloudflare.com/ajax/libs/ace/' . $ace_version . '/ext-language_tools.js', array( 'ace-editor' ), null );
		wp_enqueue_script( 'ace-editor-init', config::get( 'plugin_url' ) . 'modules/aceEditor/assets/aceinit.js', array( 'ace-editor' ), null );

		// ファイルの拡張子を取得
		$file_name = isset( $_REQUEST['file'] ) ? esc_html( $_REQUEST['file'] ) : 'style.css';
		$pattern = "/(.*)(?:\.([^.]+$))/";
		preg_match( $pattern, $file_name, $mode );

		/** js に値を渡す */
		wp_localize_script( 'ace-editor-init', 'Ace', array(
			'filename' => $file_name,
			'mode'=> $mode[2]
		) );

		wp_enqueue_style( 'ace-edior-style', plugins_url( '/assets/ace-editor-style.css', __FILE__ ) );

	}

}