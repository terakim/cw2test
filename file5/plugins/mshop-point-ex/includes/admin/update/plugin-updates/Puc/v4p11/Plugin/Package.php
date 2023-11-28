<?php
if ( !class_exists('Puc_v4p11_Plugin_Package', false) ):

	class Puc_v4p11_Plugin_Package extends Puc_v4p11_InstalledPackage {
		protected $updateChecker;
		protected $pluginAbsolutePath = '';
		private $pluginFile;
		private $cachedInstalledVersion = null;

		public function __construct($pluginAbsolutePath, $updateChecker) {
			$this->pluginAbsolutePath = $pluginAbsolutePath;
			$this->pluginFile = plugin_basename($this->pluginAbsolutePath);

			parent::__construct($updateChecker);

			//Clear the version number cache when something - anything - is upgraded or WP clears the update cache.
			add_filter('upgrader_post_install', array($this, 'clearCachedVersion'));
			add_action('delete_site_transient_update_plugins', array($this, 'clearCachedVersion'));
		}

		public function getInstalledVersion() {
			if ( isset($this->cachedInstalledVersion) ) {
				return $this->cachedInstalledVersion;
			}

			$pluginHeader = $this->getPluginHeader();
			if ( isset($pluginHeader['Version']) ) {
				$this->cachedInstalledVersion = $pluginHeader['Version'];
				return $pluginHeader['Version'];
			} else {
				//This can happen if the filename points to something that is not a plugin.
				$this->updateChecker->triggerError(
					sprintf(
						"Can't to read the Version header for '%s'. The filename is incorrect or is not a plugin.",
						$this->updateChecker->pluginFile
					),
					E_USER_WARNING
				);
				return null;
			}
		}
		public function clearCachedVersion($filterArgument = null) {
			$this->cachedInstalledVersion = null;
			return $filterArgument;
		}

		public function getAbsoluteDirectoryPath() {
			return dirname($this->pluginAbsolutePath);
		}
		public function getHeaderValue($headerName, $defaultValue = '') {
			$headers = $this->getPluginHeader();
			if ( isset($headers[$headerName]) && ($headers[$headerName] !== '') ) {
				return $headers[$headerName];
			}
			return $defaultValue;
		}

		protected function getHeaderNames() {
			return array(
				'Name'              => 'Plugin Name',
				'PluginURI'         => 'Plugin URI',
				'Version'           => 'Version',
				'Description'       => 'Description',
				'Author'            => 'Author',
				'AuthorURI'         => 'Author URI',
				'TextDomain'        => 'Text Domain',
				'DomainPath'        => 'Domain Path',
				'Network'           => 'Network',

				//The newest WordPress version that this plugin requires or has been tested with.
				//We support several different formats for compatibility with other libraries.
				'Tested WP'         => 'Tested WP',
				'Requires WP'       => 'Requires WP',
				'Tested up to'      => 'Tested up to',
				'Requires at least' => 'Requires at least',
			);
		}
		public function getPluginTitle() {
			$title = '';
			$header = $this->getPluginHeader();
			if ( $header && !empty($header['Name']) && isset($header['TextDomain']) ) {
				$title = translate($header['Name'], $header['TextDomain']);
			}
			return $title;
		}
		public function getPluginHeader() {
			if ( !is_file($this->pluginAbsolutePath) ) {
				//This can happen if the plugin filename is wrong.
				$this->updateChecker->triggerError(
					sprintf(
						"Can't to read the plugin header for '%s'. The file does not exist.",
						$this->updateChecker->pluginFile
					),
					E_USER_WARNING
				);
				return array();
			}

			if ( !function_exists('get_plugin_data') ) {
				require_once(ABSPATH . '/wp-admin/includes/plugin.php');
			}
			return get_plugin_data($this->pluginAbsolutePath, false, false);
		}

		public function removeHooks() {
			remove_filter('upgrader_post_install', array($this, 'clearCachedVersion'));
			remove_action('delete_site_transient_update_plugins', array($this, 'clearCachedVersion'));
		}
		public function isMuPlugin() {
			static $cachedResult = null;

			if ( $cachedResult === null ) {
				if ( !defined('WPMU_PLUGIN_DIR') || !is_string(WPMU_PLUGIN_DIR) ) {
					$cachedResult = false;
					return $cachedResult;
				}

				//Convert both paths to the canonical form before comparison.
				$muPluginDir = realpath(WPMU_PLUGIN_DIR);
				$pluginPath  = realpath($this->pluginAbsolutePath);
				//If realpath() fails, just normalize the syntax instead.
				if (($muPluginDir === false) || ($pluginPath === false)) {
					$muPluginDir = Puc_v4p11_Factory::normalizePath(WPMU_PLUGIN_DIR);
					$pluginPath  = Puc_v4p11_Factory::normalizePath($this->pluginAbsolutePath);
				}

				$cachedResult = (strpos($pluginPath, $muPluginDir) === 0);
			}

			return $cachedResult;
		}
	}

endif;
