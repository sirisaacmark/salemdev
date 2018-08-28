<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2012-2018 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for...' );
}

if ( ! class_exists( 'WpssoCheck' ) ) {

	class WpssoCheck {

		private $p;
		private static $c = array();
		private static $extend_lib_checks = array(
			'seo' => array(
				'jetpack-seo' => 'Jetpack SEO Tools',
				'seou'        => 'SEO Ultimate',
				'sq'          => 'Squirrly SEO',
			),
		);

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
		}

		/**
		 * Please note that get_avail() is executed *before* the debug class object is defined,
		 * so do not log any debugging messages using $this->p->debug, for example.
		 *
		 * Most PHP library files have already been loaded, even if the class objects have not
		 * yet been defined, so you can safely use static methods, like SucomUtil::get_const(),
		 * for example.
		 */
		public function get_avail() {

			$avail = array();
			$is_admin = is_admin();
			$jetpack_modules = method_exists( 'Jetpack', 'get_active_modules' ) ? Jetpack::get_active_modules() : array();

			foreach ( array( 'featured', 'amp', 'p_dir', 'head_html', 'vary_ua' ) as $key ) {
				$avail['*'][$key] = $this->is_avail( $key );
			}

			$lib_checks = SucomUtil::array_merge_recursive_distinct( $this->p->cf['*']['lib']['pro'], self::$extend_lib_checks );

			foreach ( $lib_checks as $sub => $lib ) {

				$avail[$sub] = array();
				$avail[$sub]['*'] = false;

				foreach ( $lib as $id => $name ) {

					$chk = array();
					$avail[$sub][$id] = false;	// default value

					switch ( $sub.'-'.$id ) {

						/**
						 * 3rd Party Plugins
						 *
						 * Prefer to check for class names than plugin slugs for 
						 * compatibility with free / premium / pro versions.
						 */
						case 'ecom-edd':

							$chk['class'] = 'Easy_Digital_Downloads';

							break;

						case 'ecom-marketpress':

							$chk['class'] = 'Marketpress';

							break;

						case 'ecom-woocommerce':

							$chk['class'] = 'WooCommerce';

							break;

						case 'ecom-wpecommerce':

							$chk['class'] = 'WP_eCommerce';

							break;

						case 'event-tribe_events':

							$chk['class'] = 'Tribe__Events__Main';

							break;

						case 'form-gravityforms':

							$chk['class'] = 'GFForms';

							break;

						case 'form-gravityview':

							$chk['class'] = 'GravityView_Plugin';

							break;

						case 'forum-bbpress':

							$chk['plugin'] = 'bbpress/bbpress.php';

							break;

						case 'lang-polylang':

							$chk['class'] = 'Polylang';

							break;

						case 'media-ngg':	// NextGEN Gallery and NextCellent Gallery

							$chk['class'] = 'nggdb';

							break;

						case 'media-rtmedia':

							$chk['plugin'] = 'buddypress-media/index.php';

							break;

						case 'rating-wppostratings':			// wp-postratings

							$chk['constant'] = 'WP_POSTRATINGS_VERSION';

							break;

						case 'rating-yotpowc':				// yotpo-social-reviews-for-woocommerce

							$chk['function'] = 'wc_yotpo_init';

							break;

						case 'seo-aioseop':

							$chk['function'] = 'aioseop_init_class'; // Free and pro versions.

							break;

						case 'seo-autodescription':

							$chk['plugin'] = 'autodescription/autodescription.php';

							break;

						case 'seo-headspace2':

							$chk['class'] = 'HeadSpace_Plugin';

							break;

						case 'seo-jetpack-seo':

							if ( ! empty( $jetpack_modules ) ) {
								if ( in_array( 'seo-tools', $jetpack_modules ) ) {
									$avail[$sub]['*'] = $avail[$sub][$id] = true;
								}
							}

							break;

						case 'seo-seou':

							$chk['plugin'] = 'seo-ultimate/seo-ultimate.php';

							break;

						case 'seo-sq':

							$chk['plugin'] = 'squirrly-seo/squirrly.php';

							break;

						case 'seo-wpmetaseo':

							$chk['class'] = 'WpMetaSeo';

							break;

						case 'seo-wpseo':

							$chk['function'] = 'wpseo_init'; // Free and premium versions.

							break;

						case 'social-buddypress':

							$chk['plugin'] = 'buddypress/bp-loader.php';

							break;

						/**
						 * Pro Version Features / Options
						 */
						case 'media-facebook':
						case 'media-gravatar':
						case 'media-slideshare':
						case 'media-soundcloud':
						case 'media-vimeo':
						case 'media-wistia':
						case 'media-wpvideo':
						case 'media-youtube':

							$chk['optval'] = 'plugin_'.$id.'_api';

							break;

						case 'media-upscale':

							$chk['optval'] = 'plugin_upscale_images';

							break;

						case 'admin-general':
						case 'admin-advanced':

							// only load on the settings pages
							if ( $is_admin ) {
								$page = basename( $_SERVER['PHP_SELF'] );
								if ( $page === 'admin.php' || $page === 'options-general.php' ) {
									$avail[$sub]['*'] = $avail[$sub][$id] = true;
								}
							}

							break;

						case 'admin-post':
						case 'admin-meta':

							if ( $is_admin ) {
								$avail[$sub]['*'] = $avail[$sub][$id] = true;
							}

							break;

						case 'util-checkimgdims':

							$chk['optval'] = 'plugin_check_img_dims';

							break;

						case 'util-coauthors':

							$chk['plugin'] = 'co-authors-plus/co-authors-plus.php';

							break;

						case 'util-post':
						case 'util-term':
						case 'util-user':

							$avail[$sub]['*'] = $avail[$sub][$id] = true;

							break;

						case 'util-language':

							$chk['optval'] = 'plugin_filter_lang';

							break;

						case 'util-shorten':

							$chk['optval'] = 'plugin_shortener';

							break;

						case 'util-wpseo_meta':

							$chk['optval'] = 'plugin_wpseo_social_meta';

							break;
					}

					/**
					 * Check classes / functions first to include both free and pro / premium plugins,
					 * which have different plugin slugs, but use the same class / function names.
					 */
					if ( ! empty( $chk ) ) {

						if ( isset( $chk['class'] ) || isset( $chk['function'] ) || isset( $chk['plugin'] ) ) {

							if ( ( ! empty( $chk['class'] ) && class_exists( $chk['class'] ) ) ||
								( ! empty( $chk['function'] ) && function_exists( $chk['function'] ) ) ||
								( ! empty( $chk['plugin'] ) && SucomUtil::active_plugins( $chk['plugin'] ) ) ) {

								/**
								 * Check if an option value is also required.
								 */
								if ( isset( $chk['optval'] ) ) {
									if ( $this->has_optval( $chk['optval'] ) ) {
										$avail[$sub]['*'] = $avail[$sub][$id] = true;
									}
								} else {
									$avail[$sub]['*'] = $avail[$sub][$id] = true;
								}
							}

						} elseif ( isset( $chk['optval'] ) ) {

							if ( $this->has_optval( $chk['optval'] ) ) {
								$avail[$sub]['*'] = $avail[$sub][$id] = true;
							}

						} elseif ( isset( $chk['constant'] ) ) {

							if ( defined( $chk['constant'] ) ) {
								$avail[$sub]['*'] = $avail[$sub][$id] = true;
							}
						}
					}
				}
			}

			/**
			 * Define WPSSO_UNKNOWN_SEO_PLUGIN_ACTIVE as true to disable WPSSO's SEO related meta tags and features.
			 */
			if ( SucomUtil::get_const( 'WPSSO_UNKNOWN_SEO_PLUGIN_ACTIVE' ) ) {
				$avail['seo']['*'] = true;
			}

			return apply_filters( $this->p->lca.'_get_avail', $avail );
		}

		/**
		 * Private method to check for availability of specific features by keyword.
		 */
		private function is_avail( $key ) {

			$is_avail = false;

			switch ( $key ) {

				case 'featured':

					$is_avail = function_exists( 'has_post_thumbnail' ) ? true : false;

					break;

				case 'amp':

					$is_avail = function_exists( 'is_amp_endpoint' ) ? true : false;

					break;

				case 'p_dir':

					$is_avail = ! SucomUtil::get_const( 'WPSSO_PRO_MODULE_DISABLE' ) &&
						is_dir( WPSSO_PLUGINDIR.'lib/pro/' ) ? true : false;

					break;

				case 'head_html':

					$is_avail = ! SucomUtil::get_const( 'WPSSO_HEAD_HTML_DISABLE' ) &&
						empty( $_SERVER['WPSSO_HEAD_HTML_DISABLE'] ) &&
							empty( $_GET['WPSSO_HEAD_HTML_DISABLE'] ) ?
								true : false;

					break;

				case 'vary_ua':

					/**
					 * The WPSSO_VARY_USER_AGENT_DISABLE constant can be defined as true to disable mobile
					 * browser detection and the creation of Pinterest-specific meta tag values.
					 *
					 * Mobile browser and Pinterest crawler detection does NOT create additional transient
					 * cache objects or cached files on disk. Transient cache objects are indexed arrays,
					 * and an additional index element - within the same transient cache object - will be
					 * added, so browser and crawler detection does not increase the number of cache objects.
					 */
					$is_avail = ! SucomUtil::get_const( 'WPSSO_VARY_USER_AGENT_DISABLE' ) ? true : false;

					break;
			}

			return $is_avail;
		}

		public function is_aop( $lca = '', $uc = true ) {
			return $this->aop( $lca, true, ( isset( $this->p->avail['*']['p_dir'] ) ?
				$this->p->avail['*']['p_dir'] : $this->is_avail( 'p_dir' ) ), $uc );
		}

		public function aop( $lca = '', $lic = true, $rv = true, $uc = true ) {
			$lca = empty( $lca ) ? $this->p->lca : $lca;
			$kn = $lca.'-'.$lic.'-'.$rv;
			if ( $uc && isset( self::$c[$kn] ) )
				return self::$c[$kn];
			$uca = strtoupper( $lca );
			if ( defined( $uca.'_PLUGINDIR' ) ) {
				$pdir = constant( $uca.'_PLUGINDIR' );
			} elseif ( isset( $this->p->cf['plugin'][$lca]['slug'] ) ) {
				$slug = $this->p->cf['plugin'][$lca]['slug'];
				if ( ! defined ( 'WPMU_PLUGIN_DIR' ) ||
					! is_dir( $pdir = WPMU_PLUGIN_DIR.'/'.$slug.'/' ) ) {
					if ( ! defined ( 'WP_PLUGIN_DIR' ) ||
						! is_dir( $pdir = WP_PLUGIN_DIR.'/'.$slug.'/' ) )
							return self::$c[$kn] = false;
				}
			} else return self::$c[$kn] = false;
			$on = 'plugin_'.$lca.'_tid';
			$ins = is_dir( $pdir.'lib/pro/' ) ? $rv : false;
			return self::$c[$kn] = true === $lic ?
				( ( ! empty( $this->p->options[$on] ) &&
					$ins && class_exists( 'SucomUpdate' ) &&
						( $uerr = SucomUpdate::get_umsg( $lca ) ?
							false : $ins ) ) ? $uerr : false ) : $ins;
		}

		public function get_ext_list() {
			$ext_list = array();
			foreach ( $this->p->cf['plugin'] as $ext => $info ) {
				if ( empty( $info['version'] ) ) {	// only active add-ons
					continue;
				}
				$ins = $this->aop( $ext, false );
				$ext_list[] = $info['short'].' '.$info['version'].'/'.( $this->is_aop( $ext ) ? 'L' : ( $ins ? 'U' : 'F' ) );
			}
			return $ext_list;
		}

		private function has_optval( $opt_name ) {
			if ( ! empty( $opt_name ) &&
				! empty( $this->p->options[$opt_name] ) &&
					$this->p->options[$opt_name] !== 'none' )
						return true;
		}
	}
}
