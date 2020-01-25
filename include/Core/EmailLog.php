<?php namespace EmailLog\Core;

use EmailLog\Core\DB\TableManager;

/**
 * The main plugin class.
 *
 * @since Genesis
 */
class EmailLog {

	/**
	 * Plugin Version number.
	 *
	 * @since Genesis
	 *
	 * @var string
	 */
	const VERSION = '1.0.0';

	/**
	 * Flag to track if the plugin is loaded.
	 *
	 * @since 2.0
	 * @access private
	 *
	 * @var bool
	 */
	private $loaded = false;

	/**
	 * Plugin file path.
	 *
	 * @since 2.0
	 * @access private
	 *
	 * @var string
	 */
	private $plugin_file;

	/**
	 * Filesystem directory path where translations are stored.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $translations_path;

	/**
	 * Database Table Manager.
	 *
	 * @since 2.0
	 *
	 * @var \EmailLog\Core\DB\TableManager
	 */
	public $table_manager;

	/**
	 * Initialize the plugin.
	 *
	 * @param string             $file          Plugin file.
	 * @param EmailLogAutoloader $loader        EmailLog Autoloader.
	 * @param TableManager       $table_manager Table Manager.
	 */
	public function __construct( $file, $table_manager ) {
		$this->plugin_file   = $file;
		$this->table_manager = $table_manager;

		$this->translations_path = dirname( plugin_basename( $this->plugin_file ) ) . '/languages/';
	}

	/**
	 * Load the plugin.
	 */
	public function load() {
		if ( $this->loaded ) {
			return;
		}

		load_plugin_textdomain( 'email-log', false, $this->translations_path );

		$this->table_manager->load();

		$this->loaded = true;

		/**
		 * Email Log plugin loaded.
		 *
		 * @since 2.0
		 */
		do_action( 'el_loaded' );
	}

	/**
	 * Return Email Log version.
	 *
	 * @return string Email Log Version.
	 */
	public function get_version() {
		return self::VERSION;
	}

	/**
	 * Return the Email Log plugin directory path.
	 *
	 * @return string Plugin directory path.
	 */
	public function get_plugin_path() {
		return plugin_dir_path( $this->plugin_file );
	}

	/**
	 * Return the Email Log plugin file.
	 *
	 * @since 2.0.0
	 *
	 * @return string Plugin directory path.
	 */
	public function get_plugin_file() {
		return $this->plugin_file;
	}
}
