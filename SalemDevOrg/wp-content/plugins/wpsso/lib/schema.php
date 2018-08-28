<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2012-2018 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for...' );
}

if ( ! class_exists( 'WpssoSchema' ) ) {

	class WpssoSchema {

		protected $p;
		protected $types_cache = null;			// schema types array cache
		protected static $mod_cache_exp_secs = null;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$this->p->util->add_plugin_filters( $this, array( 
				'plugin_image_sizes' => 3,
			), 5 );

			add_action( 'add_head_attributes', array( $this, 'add_head_attributes' ), -1000 );

			if ( ! empty( $this->p->options['plugin_head_attr_filter_name'] ) ) {

				$filter_name = $this->p->options['plugin_head_attr_filter_name'];
				$filter_prio = $this->p->options['plugin_head_attr_filter_prio'];

				add_filter( $filter_name, array( $this, 'filter_head_attributes' ), $filter_prio, 1 );
			}

			if ( ! empty( $this->p->options['p_add_img_html'] ) ) {
				add_filter( 'the_content', array( $this, 'get_pinterest_img_html' ) );
			}
		}

		public function get_pinterest_img_html( $content = '' ) {

			/**
			 * Do not add the pinterest image if the current webpage is amp or rss feed.
			 */
			if ( SucomUtil::is_amp() || is_feed() ) {
				return $content;
			}

			/**
			 * Check if the content filter is being applied to create a description text.
			 */
			if ( ! empty( $GLOBALS[$this->p->lca . '_doing_filter_the_content'] ) ) {
				return $content;
			}

			static $do_once = array();			// prevent recursion

			$mod        = $this->p->util->get_page_mod( true );	// $use_post is true.
			$cache_salt = SucomUtil::get_mod_salt( $mod );

			if ( ! empty( $do_once[$cache_salt] ) ) {	// check for recursion
				return $content;
			} else {
				$do_once[$cache_salt] = true;
			}

			$size_name = $this->p->lca . '-schema';
			$og_images = $this->p->og->get_all_images( 1, $size_name, $mod, false, 'schema' );	// $md_pre is 'schema'.
			$image_url = SucomUtil::get_mt_media_url( $og_images );

			if ( ! empty( $image_url ) ) {

				$desc_len  = $this->p->options['schema_desc_len'];
				$desc_idx  = array( 'schema_desc', 'seo_desc', 'og_desc' );
				$desc_text = $this->p->page->get_description( $desc_len, '...', $mod, true, false, true, $desc_idx );

				$img_html = "\n" . '<!-- ' . $this->p->lca . ' schema image for pinterest pin it button -->' . "\n" . 
					'<div class="' . $this->p->lca . '-schema-image-for-pinterest" style="display:none;">' . "\n" . 
					'<img src="' . $image_url . '" width="0" height="0" style="width:0;height:0;" ' . 
					'data-pin-description="' . $desc_text . '" alt=""/>' . "\n" . 	// empty alt required for w3c validation
					'</div><!-- .' . $this->p->lca . '-schema-image-for-pinterest -->' . "\n\n";

				$content = $img_html . $content;
			}

			return $content;
		}

		public function filter_plugin_image_sizes( $sizes, $mod, $crawler_name ) {

			$sizes['schema_img'] = array(		// options prefix
				'name' => 'schema',		// wpsso-schema
				'label' => _x( 'Google / Schema Image', 'image size label', 'wpsso' ),
			);

			$sizes['schema_article_img'] = array(		// options prefix
				'name' => 'schema-article',		// wpsso-schema-article
				'label' => _x( 'Google / Schema Image', 'image size label', 'wpsso' ),
				'prefix' => 'schema_img',
			);

			return $sizes;
		}

		public function add_head_attributes() {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			if ( ! $this->is_head_attributes_enabled() ) {
				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'exiting early: head attributes disabled' );
				}
				return;
			}

			if ( ! empty( $this->p->options['plugin_head_attr_filter_name'] ) ) {	// Just in case.

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'calling filter ' . $this->p->options['plugin_head_attr_filter_name'] );
				}

				echo apply_filters( $this->p->options['plugin_head_attr_filter_name'], '' );

			} elseif ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'plugin_head_attr_filter_name is empty' );
			}
		}

		public function filter_head_attributes( $head_attr = '' ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			if ( ! $this->is_head_attributes_enabled() ) {
				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'exiting early: head attributes disabled' );
				}
				return $head_attr;
			}

			$use_post = apply_filters( $this->p->lca . '_use_post', false );	// Used by woocommerce with is_shop().

			if ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'required call to get_page_mod()' );
			}

			$mod = $this->p->util->get_page_mod( $use_post );

			$page_type_id  = $this->get_mod_schema_type( $mod, true );	// $get_schema_id is true.
			$page_type_url = $this->get_schema_type_url( $page_type_id );

			if ( empty( $page_type_url ) ) {
				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'exiting early: schema head type value is empty' );
				}
				return $head_attr;
			}

			/**
			 * Fix incorrect itemscope values
			 */
			if ( strpos( $head_attr, 'itemscope="itemscope"' ) !== false ) {
				$head_attr = preg_replace( '/ *itemscope="itemscope"/', ' itemscope', $head_attr );
			} elseif ( strpos( $head_attr, 'itemscope' ) === false ) {
				$head_attr .= ' itemscope';
			}

			/**
			 * Replace existing itemtype values.
			 */
			if ( strpos( $head_attr, 'itemtype="' ) !== false ) {
				$head_attr = preg_replace( '/ *itemtype="[^"]+"/', ' itemtype="' . $page_type_url . '"', $head_attr );
			} else {
				$head_attr .= ' itemtype="' . $page_type_url . '"';
			}

			$head_attr = trim( $head_attr );

			if ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'returning head attributes "' . $head_attr . '"' );
			}

			return $head_attr;
		}

		public function is_head_attributes_enabled() {

			if ( empty( $this->p->options['plugin_head_attr_filter_name'] ) ||
				$this->p->options['plugin_head_attr_filter_name'] === 'none' ) {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'head attributes disabled for empty option name' );
				}
				return false;
			}

			if ( SucomUtil::is_amp() ) {
				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'head attributes disabled for amp endpoint' );
				}
				return false;
			}

			if ( ! apply_filters( $this->p->lca . '_add_schema_head_attributes', true ) ) {
				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'head attributes disabled by filters' );
				}
				return false;
			}

			return true;
		}

		/**
		 * Return the schema type URL by default. Use $get_schema_id = true to return the schema type ID instead.
		 */
		public function get_mod_schema_type( array $mod, $get_schema_id = false, $use_mod_opts = true ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			static $local_cache = array();

			/**
			 * Optimize and cache post/term/user schema type values.
			 */
			if ( ! empty( $mod['name'] ) && ! empty( $mod['id'] ) ) {

				if ( isset( $local_cache[$mod['name']][$mod['id']][$get_schema_id][$use_mod_opts] ) ) {

					$value =& $local_cache[$mod['name']][$mod['id']][$get_schema_id][$use_mod_opts];

					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'returning local cache value "' . $value . '"' );
					}

					return $value;

				} elseif ( is_object( $mod['obj'] ) && $use_mod_opts ) {	// Check for a column schema_type value in wp_cache.

					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'checking for value from column wp_cache' );
					}

					$value = $mod['obj']->get_column_wp_cache( $mod, $this->p->lca . '_schema_type' );	// Returns empty string if no value found.

					if ( ! empty( $value ) ) {

						if ( ! $get_schema_id && $value !== 'none' ) {	// Return the schema type url instead.

							$schema_types = $this->get_schema_types_array( true );	// $flatten is true.

							if ( ! empty( $schema_types[$value] ) ) {

								$value = $schema_types[$value];

							} else {

								if ( $this->p->debug->enabled ) {
									$this->p->debug->log( 'columns wp_cache value "' . $value . '" not in schema types' );
								}

								$value = '';
							}
						}

						if ( $this->p->debug->enabled ) {
							$this->p->debug->log( 'returning column wp_cache value "' . $value . '"' );
						}

						return $local_cache[$mod['name']][$mod['id']][$get_schema_id][$use_mod_opts] = $value;
					}
				}

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'no value found in local cache or column wp_cache' );
				}

			} elseif ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'skipping cache check: mod name and/or id value is empty' );
			}

			$default_key  = apply_filters( $this->p->lca . '_schema_type_for_default', 'webpage', $mod );
			$schema_types = $this->get_schema_types_array( true );	// $flatten is true.
			$type_id      = null;

			/**
			 * Get custom schema type from post, term, or user meta.
			 */
			if ( $use_mod_opts ) {

				if ( ! empty( $mod['obj'] ) ) {	// Just in case.

					$type_id = $mod['obj']->get_options( $mod['id'], 'schema_type' );	// Returns null if an index key is not found.

					if ( empty( $type_id ) ) {	// Must be a non-empty string.
						if ( $this->p->debug->enabled ) {
							$this->p->debug->log( 'custom type id from meta is empty' );
						}
					} elseif ( $type_id === 'none' ) {
						if ( $this->p->debug->enabled ) {
							$this->p->debug->log( 'custom type id is disabled with value none' );
						}
					} elseif ( empty( $schema_types[$type_id] ) ) {
						if ( $this->p->debug->enabled ) {
							$this->p->debug->log( 'custom type id "' . $type_id . '" not in schema types' );
						}
						$type_id = null;
					} elseif ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'custom type id "' . $type_id . '" from ' . $mod['name'] . ' meta' );
					}

				} elseif ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'skipping custom type id - mod object is empty' );
				}

			} elseif ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'skipping custom type id - use_mod_opts is false' );
			}

			if ( empty( $type_id ) ) {
				$is_custom = false;
			} else {
				$is_custom = true;
			}

			if ( empty( $type_id ) ) {	// If no custom schema type, then use the default settings.

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'using plugin settings to determine schema type' );
				}

				if ( $mod['is_home'] ) {	// Static or index page.

					if ( $mod['is_home_page'] ) {

						$type_id = apply_filters( $this->p->lca . '_schema_type_for_home_page',
							$this->get_schema_type_id_for_name( 'home_page' ), $mod );

						if ( $this->p->debug->enabled ) {
							$this->p->debug->log( 'using schema type id "' . $type_id . '" for home page' );
						}

					} else {

						$type_id = apply_filters( $this->p->lca . '_schema_type_for_home_index',
							$this->get_schema_type_id_for_name( 'home_index' ), $mod );

						if ( $this->p->debug->enabled ) {
							$this->p->debug->log( 'using schema type id "' . $type_id . '" for home index' );
						}
					}

				} elseif ( $mod['is_post'] ) {

					if ( ! empty( $mod['post_type'] ) ) {

						if ( empty( $mod['id'] ) && is_post_type_archive() ) {

							$type_id = apply_filters( $this->p->lca . '_schema_type_for_post_type_archive_page',
								$this->get_schema_type_id_for_name( 'post_archive' ), $mod );

							if ( $this->p->debug->enabled ) {
								$this->p->debug->log( 'using schema type id "' . $type_id . '" for post type archive page' );
							}

						} elseif ( isset( $this->p->options['schema_type_for_' . $mod['post_type']] ) ) {

							$type_id = $this->get_schema_type_id_for_name( $mod['post_type'] );

							if ( $this->p->debug->enabled ) {
								$this->p->debug->log( 'using schema type id "' . $type_id . '" from post type option value' );
							}

						} elseif ( ! empty( $schema_types[$mod['post_type']] ) ) {

							$type_id = $mod['post_type'];

							if ( $this->p->debug->enabled ) {
								$this->p->debug->log( 'using schema type id "' . $type_id . '" from post type name' );
							}

						} else {	// Unknown post type.

							$type_id = apply_filters( $this->p->lca . '_schema_type_for_post_type_unknown_type', 
								$this->get_schema_type_id_for_name( 'page' ), $mod );

							if ( $this->p->debug->enabled ) {
								$this->p->debug->log( 'using "page" schema type for unknown post type ' . $mod['post_type'] );
							}
						}

					} else {	// Post objects without a post_type property.

						$type_id = apply_filters( $this->p->lca . '_schema_type_for_post_type_empty_type', 
							$this->get_schema_type_id_for_name( 'page' ), $mod );

						if ( $this->p->debug->enabled ) {
							$this->p->debug->log( 'using "page" schema type for empty post type' );
						}
					}

				} elseif ( $mod['is_term'] ) {

					if ( ! empty( $mod['tax_slug'] ) ) {

						$type_id = $this->get_schema_type_id_for_name( 'tax_' . $mod['tax_slug'] );

						if ( $this->p->debug->enabled ) {
							$this->p->debug->log( 'using schema type id "' . $type_id . '" from term taxonomy option value' );
						}
					}

					if ( empty( $type_id ) ) {	// Just in case.
						$type_id = $this->get_schema_type_id_for_name( 'archive_page' );
					}

				} elseif ( $mod['is_user'] ) {

					$type_id = $this->get_schema_type_id_for_name( 'user_page' );

				} elseif ( SucomUtil::is_archive_page() ) {	// Just in case.

					$type_id = $this->get_schema_type_id_for_name( 'archive_page' );

				} elseif ( is_search() ) {

					$type_id = $this->get_schema_type_id_for_name( 'search_page' );

				} else {	// Everything else.

					$type_id = $default_key;

					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'using default schema type id "' . $default_key . '"' );
					}
				}
			}

			if ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'schema type id before filter is "' . $type_id . '"' );
			}

			$type_id = apply_filters( $this->p->lca . '_schema_type_id', $type_id, $mod, $is_custom );

			if ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'schema type id after filter is "' . $type_id . '"' );
			}

			$get_value = false;

			if ( empty( $type_id ) ) {
				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'returning false: schema type id is empty' );
				}
			} elseif ( $type_id === 'none' ) {
				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'returning false: schema type id is disabled' );
				}
			} elseif ( ! isset( $schema_types[$type_id] ) ) {
				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'returning false: schema type id "' . $type_id . '" is unknown' );
				}
			} elseif ( ! $get_schema_id ) {	// False by default.
				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'returning schema type url "' . $schema_types[$type_id] . '"' );
				}
				$get_value = $schema_types[$type_id];
			} else {
				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'returning schema type id "' . $type_id . '"' );
				}
				$get_value = $type_id;
			}

			/**
			 * Optimize and cache post/term/user schema type values.
			 */
			if ( ! empty( $mod['name'] ) && ! empty( $mod['id'] ) ) {
				$local_cache[$mod['name']][$mod['id']][$get_schema_id][$use_mod_opts] = $get_value;
			}

			return $get_value;
		}

		public function get_types_cache_exp() {

			static $cache_exp_secs = null;

			if ( ! isset( $cache_exp_secs ) ) {
				$cache_md5_pre    = $this->p->lca . '_t_';
				$cache_exp_filter = $this->p->cf['wp']['transient'][$cache_md5_pre]['filter'];
				$cache_opt_key    = $this->p->cf['wp']['transient'][$cache_md5_pre]['opt_key'];
				$cache_exp_secs   = isset( $this->p->options[$cache_opt_key] ) ? $this->p->options[$cache_opt_key] : MONTH_IN_SECONDS;
				$cache_exp_secs   = (int) apply_filters( $cache_exp_filter, $cache_exp_secs );
			}

			return $cache_exp_secs;
		}

		/**
		 * By default, returns a one-dimensional (flat) array of schema types, otherwise returns a 
		 * multi-dimensional array of all schema types, including cross-references for sub-types with 
		 * multiple parent types.
		 */
		public function get_schema_types_array( $flatten = true ) {

			if ( ! isset( $this->types_cache['filtered'] ) ) {	// check class property cache

				$cache_md5_pre  = $this->p->lca . '_t_';
				$cache_exp_secs = $this->get_types_cache_exp();

				if ( $cache_exp_secs > 0 ) {

					$cache_salt = __METHOD__;
					$cache_id   = $cache_md5_pre . md5( $cache_salt );

					$this->types_cache = get_transient( $cache_id );	// returns false when not found

					if ( ! empty( $this->types_cache ) ) {
						if ( $this->p->debug->enabled ) {
							$this->p->debug->log( 'using schema types array from transient ' . $cache_id );
						}
					}
				}

				if ( ! isset( $this->types_cache['filtered'] ) ) {	// from transient cache or not, check if filtered

					if ( $this->p->debug->enabled ) {
						$this->p->debug->mark( 'create schema types array' );	// begin timer
					}

					if ( $this->p->debug->enabled ) {
						$this->p->debug->mark( 'filtering schema type array' );
					}

					$this->types_cache['filtered'] = (array) apply_filters( $this->p->lca . '_schema_types',
						$this->p->cf['head']['schema_type'] );

					if ( $this->p->debug->enabled ) {
						$this->p->debug->mark( 'creating tangible flat array' );
					}

					$this->types_cache['flattened'] = SucomUtil::array_flatten( $this->types_cache['filtered'] );
					ksort( $this->types_cache['flattened'] );

					if ( $this->p->debug->enabled ) {
						$this->p->debug->mark( 'creating parent index array' );
					}

					$this->types_cache['parents'] = SucomUtil::array_parent_index( $this->types_cache['filtered'] );
					ksort( $this->types_cache['parents'] );

					// add cross-references at the end to avoid duplicate parent index key errors
					if ( $this->p->debug->enabled ) {
						$this->p->debug->mark( 'adding cross-references' );
					}

					$this->add_schema_type_xrefs( $this->types_cache['filtered'] );

					if ( $cache_exp_secs > 0 ) {
						set_transient( $cache_id, $this->types_cache, $cache_exp_secs );
						if ( $this->p->debug->enabled ) {
							$this->p->debug->log( 'schema types array saved to transient cache for ' . $cache_exp_secs . ' seconds' );
						}
					}

					if ( $this->p->debug->enabled ) {
						$this->p->debug->mark( 'create schema types array' );	// end timer
					}
				} elseif ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'schema types array already filtered' );
				}
			}

			if ( $flatten ) {
				return $this->types_cache['flattened'];
			} else {
				return $this->types_cache['filtered'];
			}
		}

		/**
		 * Add array cross-references for schema sub-types that exist under more than one type.
		 * For example, Thing > Place > LocalBusiness also exists under Thing > Organization > LocalBusiness.
		 */
		protected function add_schema_type_xrefs( &$schema_types ) {

			$thing =& $schema_types['thing'];	// quick ref variable for the 'thing' array

			/**
			 * Intangible > Enumeration
			 */
			$thing['intangible']['enumeration']['medical.enumeration']['medical.specialty'] =&
				$thing['intangible']['enumeration']['specialty']['medical.specialty'];

			/**
			 * Organization > Local Business
			 */
			$thing['organization']['local.business'] =& 
				$thing['place']['local.business'];

			/**
			 * Organization > Medical Organization
			 */
			$thing['organization']['medical.organization']['hospital'] =& 
				$thing['place']['local.business']['emergency.service']['hospital'];

			/**
			 * Place > Accommodation
			 */
			$thing['place']['accommodation']['house']['house.single.family'] =&
				$thing['place']['accommodation']['house']['residence.single.family'];

			/**
			 * Place > Civic Structure
			 */
			$thing['place']['civic.structure']['campground'] =&
				$thing['place']['local.business']['lodging.business']['campground'];

			$thing['place']['civic.structure']['fire.station'] =&
				$thing['place']['local.business']['emergency.service']['fire.station'];

			$thing['place']['civic.structure']['hospital'] =&
				$thing['place']['local.business']['emergency.service']['hospital'];

			$thing['place']['civic.structure']['movie.theatre'] =&
				$thing['place']['local.business']['entertainment.business']['movie.theatre'];

			$thing['place']['civic.structure']['police.station'] =&
				$thing['place']['local.business']['emergency.service']['police.station'];

			$thing['place']['civic.structure']['stadium.or.arena'] =&
				$thing['place']['local.business']['sports.activity.location']['stadium.or.arena'];

			/**
			 * Place > Local Business
			 */
			$thing['place']['local.business']['dentist.organization'] =&
				$thing['organization']['medical.organization']['dentist.organization'];

			$thing['place']['local.business']['store']['auto.parts.store'] =& 
				$thing['place']['local.business']['automotive.business']['auto.parts.store'];

		}

		public function get_schema_types_select( $schema_types = null, $add_none = true ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			if ( ! is_array( $schema_types ) ) {
				$schema_types = $this->get_schema_types_array( false );	// $flatten is false.
			}

			$schema_types = SucomUtil::array_flatten( $schema_types );

			$select = array();

			foreach ( $schema_types as $type_id => $type_url ) {
				$type_url = preg_replace( '/^.*\/\//', '', $type_url );
				$select[ $type_id ] = $type_id . ' | ' . $type_url;
			}

			if ( defined( 'SORT_STRING' ) ) {	// Just in case.
				asort( $select, SORT_STRING );
			} else {
				asort( $select );
			}

			if ( $add_none ) {
				return array_merge( array( 'none' => '[None]' ), $select );
			} else {
				return $select;
			}
		}

		/**
		 * Get the full schema type url from the array key.
		 */
		public function get_schema_type_url( $type_id, $default_id = false ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$schema_types = $this->get_schema_types_array( true );	// $flatten is true.

			if ( isset( $schema_types[$type_id] ) ) {
				return $schema_types[$type_id];
			} elseif ( $default_id !== false && isset( $schema_types[$default_id] ) ) {
				return $schema_types[$default_id];
			} else {
				return false;
			}
		}

		/**
		 * Returns an array of schema type ids with gparent, parent, child (in that order).
		 */
		public function get_schema_type_child_family( $child_id, &$child_family = array(), $use_cache = true ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			if ( $use_cache ) {

				$cache_md5_pre  = $this->p->lca . '_t_';
				$cache_exp_secs = $this->get_types_cache_exp();

				if ( $cache_exp_secs > 0 ) {

					$cache_salt   = __METHOD__ . '(child_id:' . $child_id . ')';
					$cache_id     = $cache_md5_pre . md5( $cache_salt );
					$child_family = get_transient( $cache_id );	// Returns false when not found.

					if ( ! empty( $child_family ) ) {
						return $child_family;
					}
				}
			}

			$schema_types = $this->get_schema_types_array( true );	// $flatten is true.

			if ( isset( $this->types_cache['parents'][$child_id] ) ) {
				$parent_id = $this->types_cache['parents'][$child_id];
				if ( isset( $schema_types[$parent_id] ) ) {
					if ( $parent_id !== $child_id )	{	// Prevent infinite loops.
						$this->get_schema_type_child_family( $parent_id, $child_family, false );
					}
				}
			}

			$child_family[] = $child_id;	// add child after parents

			if ( $use_cache ) {
				if ( $cache_exp_secs > 0 ) {
					set_transient( $cache_id, $child_family, $cache_exp_secs );
				}
			}

			return $child_family;
		}

		/**
		 * Returns an array of schema type ids with child, parent, gparent (in that order).
		 */
		public function get_schema_type_children( $type_id, &$children = array(), $use_cache = true ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'getting children for type id ' . $type_id );
			}

			if ( $use_cache ) {

				$cache_md5_pre  = $this->p->lca . '_t_';
				$cache_exp_secs = $this->get_types_cache_exp();

				if ( $cache_exp_secs > 0 ) {

					$cache_salt = __METHOD__ . '(type_id:' . $type_id . ')';
					$cache_id   = $cache_md5_pre . md5( $cache_salt );
					$children   = get_transient( $cache_id );	// Returns false when not found.

					if ( ! empty( $children ) ) {
						if ( $this->p->debug->enabled ) {
							$this->p->debug->log( 'returning children from transient cache' );
						}
						return $children;
					}
				}
			}

			$children[]   = $type_id;	// add children before parents
			$schema_types = $this->get_schema_types_array( true );	// $flatten is true.

			foreach ( $this->types_cache['parents'] as $child_id => $parent_id ) {
				if ( $parent_id === $type_id ) {
					$this->get_schema_type_children( $child_id, $children, false );
				}
			}

			if ( $use_cache ) {
				if ( $cache_exp_secs > 0 ) {
					set_transient( $cache_id, $children, $cache_exp_secs );
					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'children saved to transient cache for ' . $cache_exp_secs . ' seconds' );
					}
				}
			}

			return $children;
		}

		public static function get_schema_type_context( $type_url, array $json_data = array() ) {

			if ( preg_match( '/^(.+:\/\/.+)\/([^\/]+)$/', $type_url, $match ) ) {

				$context_value = $match[1];
				$type_value    = $match[2];

				/**
				 * Check for schema extension (example: https://health-lifesci.schema.org).
				 *
				 * $context_value = array(
				 *	"https://schema.org",
				 *	array(
				 *		"health-lifesci" => "https://health-lifesci.schema.org",
				 *	)
				 * )
				 *
				 */
				if ( preg_match( '/^(.+:\/\/)([^\.]+)\.([^\.]+\.[^\.]+)$/', $context_value, $ext ) ) {
					$context_value = array( $ext[1] . $ext[3], array( $ext[2] => $ext[0] ) );
				}

				// keep the @id property top-most
				if ( empty( $json_data['@id'] ) ) {
					$json_head = array( '@context' => null, '@type' => null );
				} else {
					$json_head = array( '@id' => null, '@context' => null, '@type' => null );
				}

				$json_data = array_merge( $json_head, $json_data, array( '@context' => $context_value, '@type' => $type_value ) );
			}

			return $json_data;
		}

		public static function get_schema_type_parts( $type_url ) {
			if ( preg_match( '/^(.+:\/\/.+)\/([^\/]+)$/', $type_url, $match ) ) {
				return array( $match[1], $match[2] );
			} else {
				return array( null, null );	// return two elements
			}
		}

		public function get_schema_type_id_for_name( $type_name, $default_id = null ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->log_args( array( 
					'type_name'  => $type_name,
					'default_id' => $default_id,
				) );
			}

			if ( empty( $type_name ) ) {
				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'exiting early: schema type name is empty' );
				}
				return $default_id;	// Just in case.
			}

			$schema_types = $this->get_schema_types_array( true );	// $flatten is true.

			$type_id = isset( $this->p->options['schema_type_for_' . $type_name] ) ?	// Just in case.
				$this->p->options['schema_type_for_' . $type_name] : $default_id;

			if ( empty( $type_id ) || $type_id === 'none' ) {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'schema type id for ' . $type_name . ' is empty or disabled' );
				}

				$type_id = $default_id;

			} elseif ( empty( $schema_types[$type_id] ) ) {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'schema type id "' . $type_id . '" for ' . $type_name . ' not in schema types' );
				}

				$type_id = $default_id;

			} elseif ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'schema type id for ' . $type_name . ' is ' . $type_id );
			}

			return $type_id;
		}

		public function get_children_css_class( $type_id, $class_names = 'hide_schema_type', $exclude_match = '' ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$class_prefix = empty( $class_names ) ? '' : SucomUtil::sanitize_hookname( $class_names ) . '_';

			foreach ( $this->get_schema_type_children( $type_id ) as $child ) {
				if ( ! empty( $exclude_match ) ) {
					if ( preg_match( $exclude_match, $child ) ) {
						continue;
					}
				}
				$class_names .= ' ' . $class_prefix . SucomUtil::sanitize_hookname( $child );
			}

			return trim( $class_names );
		}

		public function is_schema_type_child( $child_id, $member_id ) {

			static $local_cache = array();

			if ( isset( $local_cache[$child_id][$member_id] ) ) {
				return $local_cache[$child_id][$member_id];
			}

			if ( $child_id === $member_id ) {	// optimize and check for obvious
				$is_child = true;
			} else {
				$child_family = $this->get_schema_type_child_family( $child_id );
				$is_child     = in_array( $member_id, $child_family ) ? true : false;
			}

			return $local_cache[$child_id][$member_id] = $is_child;
		}

		public function count_schema_type_children( $type_id ) {
			return count( $this->get_schema_type_children( $type_id ) );
		}

		public function has_json_data_filter( array &$mod, $type_url = '' ) {
			$filter_name = $this->get_json_data_filter( $mod, $type_url );
			return ! empty( $filter_name ) && has_filter( $filter_name ) ? true : false;
		}

		public function get_json_data_filter( array &$mod, $type_url = '' ) {
			if ( empty( $type_url ) ) {
				$type_url = $this->get_mod_schema_type( $mod );
			}
			return $this->p->lca . '_json_data_' . SucomUtil::sanitize_hookname( $type_url );
		}

		public static function get_data_context( $json_data ) {
			if ( ( $type_url = self::get_data_type_url( $json_data ) ) !== false ) {
				return self::get_schema_type_context( $type_url );
			}
			return array();
		}

		/**
		 * json_data can be null, so don't cast an array on the input argument. 
		 *
		 * The @context value can be an array if the schema type is an extension.
		 *
		 * @context = array(
		 *	"https://schema.org",
		 *	array(
		 *		"health-lifesci" => "https://health-lifesci.schema.org",
		 *	)
		 * )
		 */
		public static function get_data_type_url( $json_data ) {

			$type_url = false;

			if ( empty( $json_data['@type'] ) ) {
				return $type_url;	// stop here
			}

			if ( is_array( $json_data['@type'] ) ) {

				$json_data['@type'] = reset( $json_data['@type'] );	// use first @type element

				return self::get_data_type_url( $json_data );
			}

			if ( strpos( $json_data['@type'], '://' ) ) {	// @type is a complete url

				$type_url = $json_data['@type'];

			} elseif ( ! empty(  $json_data['@context'] ) ) {	// Just in case.

				if ( is_array( $json_data['@context'] ) ) {	// get the extension url

					$context_url = self::get_context_extension_url( $json_data['@context'] );

					if ( ! empty( $context_url ) ) {	// Just in case.
						$type_url = trailingslashit( $context_url ) . $json_data['@type'];
					}

				} elseif ( is_string( $json_data['@context'] ) ) {
					$type_url = trailingslashit( $json_data['@context'] ) . $json_data['@type'];
				}
			}

			return $type_url;
		}

		public static function get_context_extension_url( array $json_data ) {

			$type_url = false;
			$ext_data = array_reverse( $json_data );	// read the array bottom-up

			foreach ( $ext_data as $val ) {
				if ( is_array( $val ) ) {		// if it's an extension array, drill down and return that value
					return self::get_context_extension_url( $val );
				} elseif ( is_string( $val ) ) {	// set a backup value in case there is no extension array
					$type_url = $val;
				}
			}

			return false;
		}

		/**
		 * JSON-LD Script Array
		 *
		 * $mt_og must be passed by reference to assign the schema:type internal meta tags.
		 */
		public function get_json_array( array &$mod, array &$mt_og, $crawler_name ) {

			if ( ! empty( $this->p->avail['*']['vary_ua'] ) ) {
				switch ( $crawler_name ) {
					case 'pinterest':
						return array();
				}
			}

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark( 'build json array' );	// begin timer for json array
			}

			$ret = array();

			$page_type_id  = $mt_og['schema:type:id']  = $this->get_mod_schema_type( $mod, true );		// example: article.tech
			$page_type_url = $mt_og['schema:type:url'] = $this->get_schema_type_url( $page_type_id );	// example: https://schema.org/TechArticle

			list(
				$mt_og['schema:type:context'],
				$mt_og['schema:type:name'],
			) = self::get_schema_type_parts( $page_type_url );		// example: https://schema.org, TechArticle

			$page_type_ids    = array();
			$page_type_added  = array();	// Prevent duplicate schema types.
			$site_org_type_id = false;	// Just in case.

			if ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'head schema type id is ' . $page_type_id . ' (' . $page_type_url . ')' );
			}

			/**
			 * Include WebSite, Organization and/or Person markup on the home page.
			 * Note that the custom 'site_org_type' may be a sub-type of organization, 
			 * and may be filtered as a local.business.
			 */
			if ( $mod['is_home'] ) {	// static or index home page

				$site_org_type_id = $this->p->options['site_org_type'];	// organization or a sub-type of organization

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'organization schema type id is ' . $site_org_type_id );
				}

				$page_type_ids['website'] = isset( $this->p->options['schema_add_home_website'] ) ?
					$this->p->options['schema_add_home_website'] : 1;

				$page_type_ids[$site_org_type_id] = isset( $this->p->options['schema_add_home_organization'] ) ?
					$this->p->options['schema_add_home_organization'] : 1;

				$page_type_ids['person'] = isset( $this->p->options['schema_add_home_person'] ) ?
					$this->p->options['schema_add_home_person'] : 0;
			}

			/**
			 * Could be an organization, website, or person, so include last to 
			 * re-enable (if disabled by default).
			 */
			if ( ! empty( $page_type_url ) ) {
				$page_type_ids[$page_type_id] = true;
			}

			/**
			 * Array (
			 *	[product] => true
			 *	[website] => true
			 *	[organization] => true
			 *	[person] => false
			 * )
			 */
			$page_type_ids = apply_filters( $this->p->lca . '_json_array_schema_page_type_ids', $page_type_ids, $mod );

			if ( $this->p->debug->enabled ) {
				$this->p->debug->log_arr( 'page_type_ids', $page_type_ids );
			}

			foreach ( $page_type_ids as $type_id => $is_enabled ) {

				if ( ! $is_enabled ) {
					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'skipping schema type id "' . $type_id . '" (disabled)' );
					}
					continue;
				} elseif ( ! empty( $page_type_added[$type_id] ) ) {	// prevent duplicate schema types
					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'skipping schema type id "' . $type_id . '" (previously added)' );
					}
					continue;
				} else {
					$page_type_added[$type_id] = true;	// prevent adding duplicate schema types
				}

				if ( $this->p->debug->enabled ) {
					$this->p->debug->mark( 'schema type id ' . $type_id );	// begin timer
				}

				if ( $type_id === $page_type_id ) {	// this is the main entity
					$is_main = true;
				} else {
					$is_main = false;	// default for all other types
				}

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'schema main entity is ' . ( $is_main ? 'true' : 'false' ) . ' for ' . $type_id );
				}

				$json_data = $this->get_json_data( $mod, $mt_og, $type_id, $is_main );

				/**
				 * The $json_data array will almost always be a single associative array,
				 * but the breadcrumblist filter may return an array of $json_data arrays.
				 */
				if ( isset( $json_data[0] ) && ! SucomUtil::is_assoc( $json_data ) ) {	// multiple json scripts returned
					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'multiple json data arrays returned' );
					}
					$scripts_data = $json_data;
				} else {
					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'single json data array returned' );
					}
					$scripts_data = array( $json_data );	// single json script returned
				}

				/**
				 * Sanitize the @id and @type properties and encode the json data in an HTML script block.
				 */
				foreach ( $scripts_data as $json_data ) {

					if ( empty( $json_data ) || ! is_array( $json_data ) ) {
						continue;
					}
	
					/**
					 * The combined url and schema type create a unique @id string.
					 */
					if ( empty( $json_data['@id'] ) ) {

						if ( ! empty( $json_data['url'] ) ) {

							$json_data = array( '@id' => $json_data['url'] . '#id/' . $type_id ) + $json_data;

							if ( $this->p->debug->enabled ) {
								$this->p->debug->log( 'added @id property is ' . $json_data['@id'] );
							}

						} elseif ( $this->p->debug->enabled ) {
							$this->p->debug->log( 'missing url property to add an @id property' );
						}

					/**
					 * Filters may return an @id as a way to signal a change to the schema type.
					 */
					} else {

						if ( $this->p->debug->enabled ) {
							$this->p->debug->log( 'existing @id property is ' . $json_data['@id'] );
						}

						if ( ( $id_pos = strpos( $json_data['@id'], '#id/' ) ) !== false ) {

							$id_str = substr( $json_data['@id'], $id_pos + 4 );	// Add strlen of #id/.

							if ( preg_match_all( '/([^\/]+)/', $id_str, $all_matches, PREG_SET_ORDER ) ) {

								$has_type_id = false;

								foreach ( $all_matches as $match ) {

									if ( $match[1] === $type_id ) {
										$has_type_id = true;		// Found the original type id.
									}

									$page_type_added[$match[1]] = true;	// Prevent duplicate schema types.
								}

								if ( ! $has_type_id ) {

									$json_data['@id'] .= '/' . $type_id;	// Append the original type id.

									if ( $this->p->debug->enabled ) {
										$this->p->debug->log( 'modified @id is ' . $json_data['@id'] );
									}
								}
							}
						}
					}

					/**
					 * Check for missing @context / @type and add them if required.
					 */
					if ( empty( $json_data['@type'] ) ) {

						$type_url  = $this->get_schema_type_url( $type_id );
						$json_data = self::get_schema_type_context( $type_url, $json_data );

						if ( $this->p->debug->enabled ) {
							$this->p->debug->log( 'added @type property is ' . $json_data['@type'] );
						}

					} elseif ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'existing @type property is ' . print_r( $json_data['@type'], true ) );	// @type can be an array.
					}
	
					/**
					 * Encode the json data in an HTML script block.
					 */
					$ret[] = '<script type="application/ld+json">' . $this->p->util->json_format( $json_data ) . '</script>' . "\n";
				}

				if ( $this->p->debug->enabled ) {
					$this->p->debug->mark( 'schema type id ' . $type_id );	// end timer
				}
			}

			$ret = SucomUtil::a2aa( $ret );	// Convert to array of arrays.

			if ( $this->p->debug->enabled ) {
				$this->p->debug->log( $ret );
				$this->p->debug->mark( 'build json array' );	// End timer for json array.
			}

			return $ret;
		}

		/**
		 * JSON-LD Data Array
		 */
		public function get_json_data( array &$mod, array &$mt_og, $page_type_id = false, $is_main = false ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$cache_index = false;
			$cache_data  = false;

			/**
			 * $page_type_id is false when called by get_single_mod_data().
			 *
			 * Optimize and use $page_type_id (when not false) as a signal to check if we 
			 * have single mod data in the transient cache.
			 *
			 * If we're called by get_single_mod_data() ($page_type_id is false), then don't
			 * bother checking because we wouldn't be called if the cached data existed. ;-)
			 */
			if ( false === $page_type_id ) {

				$page_type_id = $this->get_mod_schema_type( $mod, true );	// $get_schema_id is true.

			} elseif ( $is_main && $mod['is_post'] && $mod['id'] ) {

				$cache_index = self::get_mod_cache_index( $mod, $page_type_id );
				$cache_data  = self::get_mod_cache_data( $mod, $cache_index );

				if ( isset( $cache_data[$cache_index] ) ) {
					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'exiting early: returning single post cache data' );
					}
					return $cache_data[$cache_index];	// stop here
				}
			}

			$json_data         = null;
			$page_type_url     = $this->get_schema_type_url( $page_type_id );
			$filter_name       = SucomUtil::sanitize_hookname( $page_type_url );
			$child_family_urls = array();

			// returns an array of type ids with gparents, parents, child (in that order)
			foreach ( $this->get_schema_type_child_family( $page_type_id ) as $type_id ) {
				$child_family_urls[] = $this->get_schema_type_url( $type_id );
			}

			if ( $this->p->debug->enabled ) {
				$this->p->debug->log_arr( 'page_type_id ' . $page_type_id . ' child_family_urls', $child_family_urls );
			}

			foreach ( $child_family_urls as $type_url ) {

				$type_filter_name = SucomUtil::sanitize_hookname( $type_url );
				$has_type_filter  = has_filter( $this->p->lca . '_json_data_' . $type_filter_name );	// check only once

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'type filter name is ' . $type_filter_name . ' and has filter is ' . 
						( $has_type_filter ? 'true' : 'false' ) );
				}

				// add website, organization, and person markup to home page
				if ( $mod['is_home'] && ! $has_type_filter && method_exists( __CLASS__, 'filter_json_data_' . $type_filter_name ) ) {

					$json_data = call_user_func( array( __CLASS__, 'filter_json_data_' . $type_filter_name ),
						$json_data, $mod, $mt_og, $page_type_id, false );	// $is_main is always false for method.

				} elseif ( $has_type_filter ) {

					$json_data = apply_filters( $this->p->lca . '_json_data_' . $type_filter_name,
						$json_data, $mod, $mt_og, $page_type_id, $is_main );
				} else {
					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'no filters registered for ' . $type_filter_name );
					}
				}
			}

			/**
			 * If this is a single post, save the json data to the transient cache to optimize the 
			 * creation of Schema JSON-LD for archive type pages like Blog, CollectionPage, ProfilePage,
			 * and SearchResultsPage.
			 *
			 * If $cache_index is not set, then we were called by get_single_mod_data() and the cache 
			 * data will be saved by that method instead.
			 */
			if ( ! empty( $cache_index ) ) {

				if ( $is_main && $mod['is_post'] && $mod['id'] ) {

					if ( empty( $cache_data ) ) {	// Just in case.
						$cache_data = array();
					}

					$cache_data[$cache_index] = $json_data;

					self::save_mod_cache_data( $mod, $cache_data );
				}
			}

			return $json_data;
		}

		/**
		 * Adds a $json_data array element and returns a reference to 0 or 1. If the cache does not contain an existing entry,
		 * a new cache entry is created (as false) and a reference to that cache entry is returned.
		 */
		public static function &set_single_data_from_cache( &$json_data, $mod, $single_name, $single_id, $list_element = false ) {

			$wpsso        =& Wpsso::get_instance();
			$single_added = 0;
			$action_name  = 'creating';

			if ( $single_id === 'none' ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'exiting early: ' . $single_name . ' id is ' . $single_id );
				}
				return $single_added;
			}

			static $local_cache = array();

			if ( isset( $local_cache[$mod['name']][$mod['id']][$single_name][$single_id] ) ) {

				$action_name = 'using';
				$single_data =& $local_cache[$mod['name']][$mod['id']][$single_name][$single_id];

				if ( false === $single_data ) {
					$single_added = 0;
				} else {
					if ( empty( $list_element ) ) {
						$json_data = $single_data;
					} else {
						$json_data[] = $single_data;
					}
					$single_added = 1;
				}

			} else {
				$local_cache[$mod['name']][$mod['id']][$single_name][$single_id] = false;
				$single_added =& $local_cache[$mod['name']][$mod['id']][$single_name][$single_id];	// return reference to false
			}

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( $action_name . ' ' . $single_name . ' cache data for mod id ' . $mod['id'] . 
					' / ' . $single_name . ' id ' . ( false === $single_id ? 'is false' : $single_id ) );
			}

			return $single_added;	// 0, 1, or false
		}

		public static function get_single_type_id_url( $json_data, $type_opts, $opt_key, $default_id, $list_element = false ) {

			$wpsso =& Wpsso::get_instance();

			/**
			 * If not adding a list element, then inherit the existing schema type url (if one exists).
			 */
			$single_type_id   = false;
			$single_type_url  = $list_element ? false : self::get_data_type_url( $json_data );
			$single_type_from = 'inherited';

			if ( false === $single_type_url ) {

				/**
				 * $type_opts may be false, null, or an array.
				 */
				if ( empty( $type_opts[$opt_key] ) || $type_opts[$opt_key] === 'none' ) {

					$single_type_id   = $default_id;
					$single_type_url  = $wpsso->schema->get_schema_type_url( $default_id );
					$single_type_from = 'default';

				} else {

					$single_type_id   = $type_opts[$opt_key];
					$single_type_url  = $wpsso->schema->get_schema_type_url( $single_type_id, $default_id );
					$single_type_from = 'options';
				}
			}

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'using ' . $single_type_from . ' single type url: ' . $single_type_url );
			}

			return array( $single_type_id, $single_type_url );
		}

		/**
		 * Sanitation used by filters to return their data.
		 */
		public static function return_data_from_filter( $json_data, $merge_data, $is_main = false ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->mark();
			}

			if ( ! isset( $merge_data['mainEntityOfPage'] ) ) {
				if ( $is_main && ! empty( $merge_data['url'] ) ) {
					$merge_data['mainEntityOfPage'] = $merge_data['url'];
				}
			}

			if ( empty( $merge_data ) ) {	// Just in case - nothing to merge.

				return $json_data;

			} elseif ( null === $json_data ) {	// Just in case - nothing to merge.

				return $merge_data;

			} elseif ( is_array( $json_data ) ) {

				$json_head = array( '@id' => null, '@context' => null, '@type' => null, 'mainEntityOfPage' => null );
				$json_data = array_merge( $json_head, $json_data, $merge_data );

				foreach ( array( '@id', '@context', '@type', 'mainEntityOfPage' ) as $prop_name ) {
					if ( empty( $json_data[$prop_name] ) ) {
						unset( $json_data[$prop_name] );
					}
				}

				return $json_data;

			} else {
				return $json_data;
			}
		}

		/**
		 * https://schema.org/WebSite for Google
		 */
		public function filter_json_data_https_schema_org_website( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$ret = self::get_schema_type_context( 'https://schema.org/WebSite', array( 'url' => $mt_og['og:url'] ) );

			foreach ( array(
				'name' => SucomUtil::get_site_name( $this->p->options, $mod ),
				'alternateName' => SucomUtil::get_site_name_alt( $this->p->options, $mod ),
				'description' => SucomUtil::get_site_description( $this->p->options, $mod ),
			) as $key => $value ) {
				if ( ! empty( $value ) ) {
					$ret[$key] = $value;
				}
			}

			/**
			 * Potential Action (SearchAction, OrderAction, etc.)
			 *
			 * The 'wpsso_json_prop_https_schema_org_potentialaction' filter may already
			 * be applied by the WPSSO JSON add-on, so do not re-apply it here.
			 *
			 * Hook the 'wpsso_json_ld_search_url' filter and return false if you wish to
			 * disable / skip the Potential Action property.
			 */
			if ( $search_url = apply_filters( $this->p->lca . '_json_ld_search_url', get_bloginfo( 'url' ) . '?s={search_term_string}' ) ) {

				if ( ! empty( $search_url ) ) {

					/**
					 * Potential Action may already be defined by the WPSSO JSON
					 * 'wpsso_json_prop_https_schema_org_potentialaction' filter.
					 * Make sure it's an array - just in case. ;-)
					 */
					if ( ! isset( $ret['potentialAction'] ) || ! is_array( $ret['potentialAction'] ) ) {
						$ret['potentialAction'] = array();
					}

					$ret['potentialAction'][] = array(
						'@context'    => 'https://schema.org',
						'@type'       => 'SearchAction',
						'target'      => $search_url,
						'query-input' => 'required name=search_term_string',
					);

				} elseif ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'skipping search action: search url is empty' );
				}
			}

			return self::return_data_from_filter( $json_data, $ret, $is_main );
		}

		/**
		 * https://schema.org/Organization social markup for Google
		 */
		public function filter_json_data_https_schema_org_organization( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			if ( ! empty( $mod['obj'] ) ) {	// Just in case.
				$org_id = $mod['obj']->get_options( $mod['id'], 'schema_org_org_id' );	// Returns null if an index key is not found.
			} else {
				$org_id = null;
			}

			if ( null === $org_id ) {
				if ( $mod['is_home'] ) {	// static or index page
					$org_id = 'site';
				} else {
					$org_id = 'none';
				}
			}

			if ( $org_id === 'none' ) {
				$this->p->debug->log( 'exiting early: organization id is none' );
				return $json_data;
			}

			/**
			 * Possibly inherit the schema type.
			 */
			$ret = self::get_data_context( $json_data );	// Returns array() if no schema type found.

		 	/**
			 * $org_id can be 'none', 'site', or a number (including 0).
		 	 * $logo_key can be 'org_logo_url' or 'org_banner_url' (600x60px image) for Articles.
			 * do not provide localized option names - the method will fetch the localized values.
			 */
			self::add_single_organization_data( $ret, $mod, $org_id, 'org_logo_url', false );	// $list_element is false.

			return self::return_data_from_filter( $json_data, $ret, $is_main );
		}

		/**
		 * https://schema.org/LocalBusiness social markup for Google
		 */
		public function filter_json_data_https_schema_org_localbusiness( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'page_type_id = ' . $page_type_id );
				$this->p->debug->log( 'is_main = ' . $is_main );
				$this->p->debug->mark( 'organization filter for local business' );	// begin timer
			}

			/**
			 * All local businesses are also organizations.
			 */
			$ret = $this->filter_json_data_https_schema_org_organization( $json_data, $mod, $mt_og, $page_type_id, $is_main );

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark( 'organization filter for local business' );	// end timer
			}

			$this->organization_to_localbusiness( $ret );

			return self::return_data_from_filter( $json_data, $ret, $is_main );
		}

		/**
		 * https://schema.org/Person social markup for Google
		 */
		public function filter_json_data_https_schema_org_person( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			if ( ! empty( $mod['obj'] ) ) {	// Just in case.
				$user_id = $mod['obj']->get_options( $mod['id'], 'schema_person_id' );	// Returns null if an index key is not found.
			} else {
				$user_id = null;
			}

			if ( null === $user_id ) {
				if ( $mod['is_home'] ) {	// Static or index page.
					if ( empty( $this->p->options['schema_home_person_id'] ) ) {
						if ( $this->p->debug->enabled ) {
							$this->p->debug->log( 'exiting early: schema_home_person_id disabled for home page' );
						}
						return $json_data;	// Exit early.
					} else {
						$user_id = $this->p->options['schema_home_person_id'];
						if ( $this->p->debug->enabled ) {
							$this->p->debug->log( 'person / user_id for home page is ' . $user_id );
						}
					}
				} elseif ( $mod['is_user'] ) {
					$user_id = $mod['id'];
				} else {
					$user_id = false;
				}
			}

			if ( empty( $user_id ) || $user_id === 'none' ) {
				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'exiting early: empty user_id' );
				}
				return $json_data;
			} else {
				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'user id is "' . $user_id . '"' );
				}
			}

			/**
			 * Possibly inherit the schema type.
			 */
			$ret = self::get_data_context( $json_data );	// Returns array() if no schema type found.

			self::add_single_person_data( $ret, $mod, $user_id, false );	// $list_element is false.

			/**
			 * Override author's website url and use the open graph url instead.
			 */
			if ( $mod['is_home'] ) {
				$ret['url'] = $mt_og['og:url'];
			}

			return self::return_data_from_filter( $json_data, $ret, $is_main );
		}

		/**
		 * Get the site organization array.
		 *
		 * $mixed = 'default' | 'current' | post ID | $mod array
		 */
		public static function get_site_organization( $mixed = 'current' ) {

			$wpsso           =& Wpsso::get_instance();
			$social_accounts = apply_filters( $wpsso->lca . '_social_accounts', $wpsso->cf['form']['social_accounts'] );
			$org_sameas      = array();

			foreach ( $social_accounts as $social_key => $social_label ) {

				$url = SucomUtil::get_key_value( $social_key, $wpsso->options, $mixed );	// localized value

				if ( empty( $url ) ) {
					continue;
				} elseif ( $social_key === 'tc_site' ) {	// convert twitter name to url
					$url = 'https://twitter.com/' . preg_replace( '/^@/', '', $url );
				}

				if ( filter_var( $url, FILTER_VALIDATE_URL ) !== false ) {
					$org_sameas[] = $url;
				}
			}

			/**
			 * Logo and banner image dimensions are localized as well.
			 *
			 * Example: 'schema_logo_url:width#fr_FR'.
			 */
			return array(
				'org_type'              => $wpsso->options['site_org_type'],
				'org_url'               => SucomUtil::get_site_url( $wpsso->options, $mixed ),
				'org_name'              => SucomUtil::get_site_name( $wpsso->options, $mixed ),
				'org_name_alt'          => SucomUtil::get_site_name_alt( $wpsso->options, $mixed ),
				'org_desc'              => SucomUtil::get_site_description( $wpsso->options, $mixed ),
				'org_logo_url'          => SucomUtil::get_key_value( 'schema_logo_url', $wpsso->options, $mixed ),
				'org_logo_url:width'    => SucomUtil::get_key_value( 'schema_logo_url:width', $wpsso->options, $mixed ),
				'org_logo_url:height'   => SucomUtil::get_key_value( 'schema_logo_url:height', $wpsso->options, $mixed ),
				'org_banner_url'        => SucomUtil::get_key_value( 'schema_banner_url', $wpsso->options, $mixed ),
				'org_banner_url:width'  => SucomUtil::get_key_value( 'schema_banner_url:width', $wpsso->options, $mixed ),
				'org_banner_url:height' => SucomUtil::get_key_value( 'schema_banner_url:height', $wpsso->options, $mixed ),
				'org_place_id'          => $wpsso->options['site_place_id'],
				'org_sameas'            => $org_sameas,
			);
		}

		/**
		 * $user_id is optional and takes precedence over the $mod post_author value.
		 */
		public static function add_author_coauthor_data( &$json_data, $mod, $user_id = false ) {

			$wpsso =& Wpsso::get_instance();
			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->mark();
			}

			$authors_added = 0;
			$coauthors_added = 0;

			if ( empty( $user_id ) && isset( $mod['post_author'] ) ) {
				$user_id = $mod['post_author'];
			}

			if ( empty( $user_id ) || $user_id === 'none' ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'exiting early: empty user_id / post_author' );
				}
				return 0;
			}

			/**
			 * Single author.
			 */
			$authors_added += self::add_single_person_data( $json_data['author'], $mod, $user_id, false );	// $list_element is false.

			/**
			 * List of contributors / co-authors.
			 */
			if ( ! empty( $mod['post_coauthors'] ) ) {
				foreach ( $mod['post_coauthors'] as $author_id ) {
					$coauthors_added += self::add_single_person_data( $json_data['contributor'], $mod, $author_id, true );	// $list_element is true.
				}
			}

			foreach ( array( 'author', 'contributor' ) as $itemprop ) {
				if ( empty( $json_data[$itemprop] ) ) {
					unset( $json_data[$itemprop] );	// prevent null assignment
				}
			}

			return $authors_added + $coauthors_added;	// return count of authors and coauthors added
		}

		/**
		 * Pass a single or two dimension image array in $og_images.
		 */
		public static function add_og_image_list_data( &$json_data, &$og_images, $mt_image_pre = 'og:image' ) {

			$images_added = 0;

			if ( isset( $og_images[0] ) && is_array( $og_images[0] ) ) {						// 2 dimensional array.

				foreach ( $og_images as $og_single_image ) {
					$images_added += self::add_og_single_image_data( $json_data, $og_single_image, $mt_image_pre, true );	// $list_element is true.
				}

			} elseif ( is_array( $og_images ) ) {

				$images_added += self::add_og_single_image_data( $json_data, $og_images, $mt_image_pre, true );	// $list_element is true.
			}

			return $images_added;	// return count of images added
		}

		/**
		 * Pass a single dimension image array in $opts.
		 */
		public static function add_og_single_image_data( &$json_data, $opts, $mt_image_pre = 'og:image', $list_element = true ) {

			$wpsso =& Wpsso::get_instance();

			if ( empty( $opts ) || ! is_array( $opts ) ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'exiting early: options array is empty or not an array' );
				}
				return 0;	// return count of images added
			}

			$image_url = SucomUtil::get_mt_media_url( $opts, $mt_image_pre );

			if ( empty( $image_url ) ) {

				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'exiting early: ' . $mt_image_pre . ' URL values are empty' );
				}

				return 0;	// return count of images added
			}

			/**
			 * If not adding a list element, inherit the existing schema type url (if one exists).
			 */
			list( $image_type_id, $image_type_url ) = self::get_single_type_id_url( $json_data, false, 'image_type', 'image.object', $list_element );

			$ret = self::get_schema_type_context( $image_type_url, array(
				'url' => esc_url_raw( $image_url ),
			) );

			/**
			 * If we have an ID, and it's numeric (so exclude NGG v1 image IDs), 
			 * check the WordPress Media Library for a title and description.
			 */
			if ( ! empty( $opts[$mt_image_pre . ':id'] ) && is_numeric( $opts[$mt_image_pre . ':id'] ) ) {

				$wpsso   = Wpsso::get_instance();
				$post_id = $opts[$mt_image_pre . ':id'];
				$mod     = $wpsso->m['util']['post']->get_mod( $post_id );

				/**
				 * Get the image title.
				 */
				$ret['name'] = $wpsso->page->get_title( 0, '', $mod, true, false, true, 'schema_title', false );

				if ( empty( $ret['name'] ) ) {
					unset( $ret['name'] );
				}

				/**
				 * Get the image alternate title, if one has been defined in the custom post meta.
				 */
				$title_len = $wpsso->options['og_title_len'];

				$ret['alternateName'] = $wpsso->page->get_title( $title_len, '...', $mod, true, false, true, 'schema_title_alt' );

				if ( empty( $ret['alternateName'] ) || $ret['name'] === $ret['alternateName'] ) {
					unset( $ret['alternateName'] );
				}

				/**
				 * Use the image "Alternative Text" for the 'alternativeHeadline' property.
				 */
				$ret['alternativeHeadline'] = get_post_meta( $mod['id'], '_wp_attachment_image_alt', true );

				if ( empty( $ret['alternativeHeadline'] ) || $ret['name'] === $ret['alternativeHeadline'] ) {
					unset( $ret['alternativeHeadline'] );
				}

				/**
				 * Get the image caption (aka excerpt of the post object).
				 */
				$ret['caption'] = $wpsso->page->get_the_excerpt( $mod );

				if ( empty( $ret['caption'] ) ) {
					unset( $ret['caption'] );
				}

				/**
				 * If we don't have a caption, then provide a short description.
				 * If we have a caption, then add the complete image description.
				 */
				$desc_len = $wpsso->options['schema_desc_len'];
				$desc_idx = array( 'schema_desc', 'seo_desc', 'og_desc' );

				if ( empty( $ret['caption'] ) ) {
					$ret['description'] = $wpsso->page->get_description( $desc_len, '...', $mod, true, false, true, $desc_idx );
				} else {
					$ret['description'] = $wpsso->page->get_the_content( $mod, true, $desc_idx );
					$ret['description'] = $wpsso->util->cleanup_html_tags( $ret['description'] );
				}

				if ( empty( $ret['description'] ) ) {
					unset( $ret['description'] );
				}

				/**
				 * Set the 'fileFormat' property to the image mime type.
				 */
				$ret['fileFormat'] = get_post_mime_type( $mod['id'] );

				if ( empty( $ret['fileFormat'] ) ) {
					unset( $ret['fileFormat'] );
				}
			}

			foreach ( array( 'width', 'height' ) as $prop_name ) {
				if ( isset( $opts[$mt_image_pre . ':' . $prop_name] ) && $opts[$mt_image_pre . ':' . $prop_name] > 0 ) {	// Just in case.
					$ret[$prop_name] = $opts[$mt_image_pre . ':' . $prop_name];
				}
			}

			if ( ! empty( $opts[$mt_image_pre . ':tag'] ) ) {
				if ( is_array( $opts[$mt_image_pre . ':tag'] ) ) {
					$ret['keywords'] = implode( ', ', $opts[$mt_image_pre . ':tag'] );
				} else {
					$ret['keywords'] = $opts[$mt_image_pre . ':tag'];
				}
			}

			if ( empty( $list_element ) ) {
				$json_data = $ret;
			} else {
				$json_data[] = $ret;	// add an item to the list
			}

			return 1;	// return count of images added
		}

		public static function add_data_itemprop_from_assoc( array &$json_data, array $assoc, array $names, $overwrite = true ) {

			$wpsso      =& Wpsso::get_instance();
			$is_assoc   = SucomUtil::is_assoc( $names );
			$prop_added = 0;

			foreach ( $names as $itemprop_name => $key_name ) {

				if ( ! $is_assoc ) {
					$itemprop_name = $key_name;
				}

				if ( isset( $assoc[$key_name] ) && $assoc[$key_name] !== '' ) {	// Exclude empty strings.

					if ( isset( $json_data[$itemprop_name] ) && empty( $overwrite ) ) {

						if ( $wpsso->debug->enabled ) {
							$wpsso->debug->log( 'skipping ' . $itemprop_name . ': itemprop exists and overwrite is false' );
						}

					} else {

						if ( is_string( $assoc[$key_name] ) && filter_var( $assoc[$key_name], FILTER_VALIDATE_URL ) !== false ) {
							$json_data[$itemprop_name] = esc_url_raw( $assoc[$key_name] );
						} else {
							$json_data[$itemprop_name] = $assoc[$key_name];
						}

						if ( $wpsso->debug->enabled ) {
							$wpsso->debug->log( 'assigned ' . $key_name . ' value to itemprop ' . $itemprop_name . ' = ' . 
								print_r( $json_data[$itemprop_name], true ) );
						}

						$prop_added++;
					}
				}
			}

			return $prop_added;
		}

		public static function get_data_itemprop_from_assoc( array $assoc, array $names, $exclude = array( '' ) ) {

			$wpsso     =& Wpsso::get_instance();
			$json_data = array();

			foreach ( $names as $itemprop_name => $key_name ) {

				if ( isset( $assoc[$key_name] ) && ! in_array( $assoc[$key_name], $exclude, true ) ) {	// $strict is true.

					$json_data[$itemprop_name] = $assoc[$key_name];

					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'assigned ' . $key_name . ' value to itemprop ' . 
							$itemprop_name . ' = ' . print_r( $json_data[$itemprop_name], true ) );
					}
				}
			}
			return empty( $json_data ) ? false : $json_data;
		}

		/**
		 * QuantitativeValue (width, height, length, depth, weight).
		 *
		 * unitCodes from http://wiki.goodrelations-vocabulary.org/Documentation/UN/CEFACT_Common_Codes.
		 */
		public static function add_data_quant_from_assoc( array &$json_data, array $assoc, array $names ) {
			foreach ( $names as $itemprop_name => $key_name ) {
				if ( isset( $assoc[$key_name] ) && $assoc[$key_name] !== '' ) {	// exclude empty strings
					switch ( $itemprop_name ) {
						case 'length':	// QuantitativeValue does not have a length itemprop
							$json_data['additionalProperty'][] = array(
								'@context' => 'https://schema.org',
								'@type' => 'PropertyValue',
								'propertyID' => $itemprop_name,
								'value' => $assoc[$key_name],
								'unitCode' => 'CMT',
							);
							break;
						default:
							$json_data[$itemprop_name] = array(
								'@context' => 'https://schema.org',
								'@type' => 'QuantitativeValue',
								'value' => $assoc[$key_name],
								'unitCode' => ( $itemprop_name === 'weight' ? 'KGM' : 'CMT' ),
							);
							break;
					}
				}
			}
		}

		/**
		 * Return any 3rd party and custom post options for a given option type.
		 * 
		 * function wpsso_get_post_event_options( $post_id, $event_id = false ) {
		 * 	WpssoSchema::get_post_md_type_opts( $post_id, 'event', $event_id );
		 * }
		 */
		public static function get_post_md_type_opts( $post_id, $md_type, $type_id = false ) {

			$wpsso =& Wpsso::get_instance();

			if ( empty( $post_id ) ) {	// Just in case.
				return false;
			} elseif ( empty( $md_type ) ) {	// Just in case.
				return false;
			} elseif ( isset( $wpsso->m['util']['post'] ) ) {
				$mod = $wpsso->m['util']['post']->get_mod( $post_id );
			} else {
				return false;
			}

			$md_opts = apply_filters( $wpsso->lca . '_get_' . $md_type . '_options', false, $mod, $type_id );

			if ( ! empty( $md_opts ) ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log_arr( 'get_' . $md_type . '_options filters returned', $md_opts );
				}
			}

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'merging default, filter, and custom option values' );
			}

			WpssoSchema::merge_custom_mod_opts( $mod, $md_opts, array( $md_type => 'schema_' . $md_type ) );

			if ( has_filter( $wpsso->lca . '_get_' . $md_type . '_place_id' ) ) {	// skip if no filters

				if ( ! isset( $md_opts[$md_type . '_place_id'] ) ) {
					$md_opts[$md_type . '_place_id'] = null;		// return null by default
				}

				$md_opts[$md_type . '_place_id'] = apply_filters( $wpsso->lca . '_get_' . $md_type . '_place_id',
					$md_opts[$md_type . '_place_id'], $mod, $type_id );

				if ( $md_opts[$md_type . '_place_id'] === null ) {	// unset if still null
					unset( $md_opts[$md_type . '_place_id'] );
				}
			}

			return $md_opts;
		}

		public static function merge_custom_mod_opts( array $mod, &$opts, array $opts_md_pre ) {

			if ( is_object( $mod['obj'] ) ) {	// Just in case.

				$md_defs = (array) $mod['obj']->get_defaults( $mod['id'] );
				$md_opts = (array) $mod['obj']->get_options( $mod['id'] );

				foreach ( $opts_md_pre as $opt_key => $md_pre ) {

					$md_defs = SucomUtil::preg_grep_keys( '/^' . $md_pre . '_/', $md_defs, false, $opt_key . '_' );
					$md_opts = SucomUtil::preg_grep_keys( '/^' . $md_pre . '_/', $md_opts, false, $opt_key . '_' );
	
					/**
					 * Merge defaults, values from filters, and custom values (in that order).
					 */
					if ( is_array( $opts ) ) {
						$opts = array_merge( $md_defs, $opts, $md_opts );
					} else {
						$opts = array_merge( $md_defs, $md_opts );
					}
				}
			}
		}

		/**
		 * Create and add ISO formatted date options.
		 *
		 * $opts_md_pre = array( 
		 *	'event_start_date'        => 'schema_event_start',        // Prefix for date, time, timezone, iso.
		 *	'event_end_date'          => 'schema_event_end',          // Prefix for date, time, timezone, iso.
		 *	'event_offers_start_date' => 'schema_event_offers_start', // Prefix for date, time, timezone, iso.
		 *	'event_offers_end_date'   => 'schema_event_offers_end',   // Prefix for date, time, timezone, iso.
		 * );
		 */
		public static function add_mod_opts_date_iso( array $mod, &$opts, array $opts_md_pre ) {

			$wpsso =& Wpsso::get_instance();
			
			foreach ( $opts_md_pre as $opt_pre => $md_pre ) {

				$md_date = $mod['obj']->get_options( $mod['id'], $md_pre . '_date' );
				
				if ( ( $md_time = $mod['obj']->get_options( $mod['id'], $md_pre . '_time' ) ) === 'none' ) {
					$md_time = '';
				}

				if ( empty( $md_date ) && empty( $md_time ) ) {

					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'skipping ' . $md_pre . ': date and time are empty' );
					}

					continue;	// nothing to do

				} elseif ( ! empty( $md_date ) && empty( $md_time ) ) {	// date with no time

					$md_time = '00:00';

					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( $md_pre . ' time is empty - using time ' . $md_time );
					}

				} elseif ( empty( $md_date ) && ! empty( $md_time ) ) {	// time with no date

					$md_date = gmdate( 'Y-m-d', time() );

					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( $md_pre . ' date is empty - using date ' . $md_date );
					}
				}

				if ( ! $md_timezone = $mod['obj']->get_options( $mod['id'], $md_pre . '_timezone' ) ) {
					$md_timezone = get_option( 'timezone_string' );
				}

				if ( ! is_array( $opts ) ) {	// Just in case.
					$opts = array();
				}

				$opts[$opt_pre . '_iso'] = date_format( date_create( $md_date . ' ' . $md_time . ' ' . $md_timezone ), 'c' );
			}
		}

		/**
		 * Get single mod data and its related methods:
		 *
		 * 	get_mod_cache_index()
		 *	get_mod_cache_data()
		 *	save_mod_cache_data()
		 */
		public static function get_single_mod_data( array $mod, $mt_og, $page_type_id ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->mark();
			}

			if ( ! is_object( $mod['obj'] ) || ! $mod['id'] ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'exiting early: $mod has no object or id is empty' );
				}
				return false;
			}

			$cache_index = self::get_mod_cache_index( $mod, $page_type_id );
			$cache_data  = self::get_mod_cache_data( $mod, $cache_index );

			if ( isset( $cache_data[$cache_index] ) ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'exiting early: returning single "' . $mod['name'] . '" cache data' );
				}
				return $cache_data[$cache_index];	// stop here
			}

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->mark( 'get single ' . $mod['name'] . ' id ' . $mod['id'] . ' data' );	// end timer
			}

			// set reference values for admin notices
			if ( is_admin() ) {
				$sharing_url = $wpsso->util->get_sharing_url( $mod );
				$wpsso->notice->set_ref( $sharing_url, $mod, sprintf( __( 'adding schema for %s object', 'wpsso' ), $mod['name'] ) );
			}

			if ( ! is_array( $mt_og ) ) {
				$mt_og = $wpsso->og->get_array( $mod, $mt_og = array() );
			}

			$cache_data[$cache_index] = $wpsso->schema->get_json_data( $mod, $mt_og, false, true );	// $page_type_id is false.

			// restore previous reference values for admin notices
			if ( is_admin() ) {
				$wpsso->notice->unset_ref( $sharing_url );
			}

			self::save_mod_cache_data( $mod, $cache_data );

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->mark( 'get single ' . $mod['name'] . ' id ' . $mod['id'] . ' data' );	// end timer
			}

			return $cache_data[$cache_index];
		}

		public static function get_mod_cache_index( $mixed, $page_type_id ) {

			$cache_index = 'page_type_id:' . $page_type_id;

			if ( $mixed !== false ) {
				$cache_index .= '_locale:' . SucomUtil::get_locale( $mixed );
			}

			if ( SucomUtil::is_amp() ) {
				$cache_index .= '_amp:true';
			}

			return $cache_index;
		}

		/**
		 * Returns an associative array of json data. The $cache_index argument is used for 
		 * quality control - making sure the $cache_index json data is an array (if it exists).
		 */
		public static function get_mod_cache_data( $mod, $cache_index ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->mark();
			}

			$cache_md5_pre = $wpsso->lca . '_j_';

			if ( ! isset( self::$mod_cache_exp_secs ) ) {	// Filter cache expiration if not already set.

				$cache_exp_filter = $wpsso->cf['wp']['transient'][$cache_md5_pre]['filter'];
				$cache_opt_key    = $wpsso->cf['wp']['transient'][$cache_md5_pre]['opt_key'];

				self::$mod_cache_exp_secs = (int) apply_filters( $cache_exp_filter, $wpsso->options[$cache_opt_key] );
			}

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'cache expire = ' . self::$mod_cache_exp_secs );
			}

			if ( self::$mod_cache_exp_secs > 0 ) {

				$cache_salt = 'WpssoSchema::get_mod_cache_data(' . SucomUtil::get_mod_salt( $mod ) . ')';
				$cache_id   = $cache_md5_pre . md5( $cache_salt );

				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'cache salt = ' . $cache_salt );
					$wpsso->debug->log( 'cache id = ' . $cache_id );
					$wpsso->debug->log( 'cache index = ' . $cache_index );
				}

				$cache_data = get_transient( $cache_id );

				if ( isset( $cache_data[$cache_index] ) ) {
					if ( is_array( $cache_data[$cache_index] ) ) {	// Just in case.
						if ( $wpsso->debug->enabled ) {
							$wpsso->debug->log( 'cache index data found in array from transient' );
						}
						return $cache_data;	// stop here
					} else {
						if ( $wpsso->debug->enabled ) {
							$wpsso->debug->log( 'cache index data not an array (unsetting index)' );
						}
						unset( $cache_data[$cache_index] );	// Just in case.
						return $cache_data;	// stop here
					}
				} else {
					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'cache index not in transient' );
					}
					return $cache_data;	// stop here
				}

			} elseif ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'transient cache is disabled' );
			}

			return false;
		}

		public static function save_mod_cache_data( $mod, $cache_data ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->mark();
			}

			$cache_md5_pre = $wpsso->lca . '_j_';

			if ( ! isset( self::$mod_cache_exp_secs ) ) {	// Filter cache expiration if not already set.

				$cache_exp_filter = $wpsso->cf['wp']['transient'][$cache_md5_pre]['filter'];
				$cache_opt_key    = $wpsso->cf['wp']['transient'][$cache_md5_pre]['opt_key'];

				self::$mod_cache_exp_secs = (int) apply_filters( $cache_exp_filter, $wpsso->options[$cache_opt_key] );
			}

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'cache expire = ' . self::$mod_cache_exp_secs );
			}

			if ( self::$mod_cache_exp_secs > 0 ) {

				$cache_salt = 'WpssoSchema::get_mod_cache_data(' . SucomUtil::get_mod_salt( $mod ) . ')';
				$cache_id   = $cache_md5_pre . md5( $cache_salt );

				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'cache salt = ' . $cache_salt );
					$wpsso->debug->log( 'cache id = ' . $cache_id );
				}

				/**
				 * Update the cached array and maintain the existing transient expiration time.
				 */
				$expires_in_secs = SucomUtil::update_transient_array( $cache_id, $cache_data, self::$mod_cache_exp_secs );

				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'cache data saved to transient cache (expires in ' . $expires_in_secs . ' secs)' );
				}

			} elseif ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'transient cache is disabled' );
			}

			return false;
		}

		public static function delete_mod_cache_data( $mod ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->mark();
			}

			$cache_md5_pre = $wpsso->lca . '_j_';
			$cache_salt    = 'WpssoSchema::get_mod_cache_data(' . SucomUtil::get_mod_salt( $mod ) . ')';
			$cache_id      = $cache_md5_pre . md5( $cache_salt );

			return delete_transient( $cache_id );
		}

		/**
		 * Add Single Methods:
		 *
		 *	add_single_event_data()
		 *	add_single_job_data()
		 *	add_single_organization_data()
		 *	add_single_person_data()
		 *	add_single_place_data()
		 */
		public static function add_single_event_data( &$json_data, array $mod, $event_id = false, $list_element = false ) {

			$ret =& self::set_single_data_from_cache( $json_data, $mod, 'event', $event_id, $list_element );

			if ( $ret !== false ) {	// 0 or 1 (data retrieved from cache)
				return $ret;
			}

			$wpsso =& Wpsso::get_instance();
			$sharing_url = $wpsso->util->get_sharing_url( $mod );
			$event_opts = apply_filters( $wpsso->lca . '_get_event_options', false, $mod, $event_id );

			if ( ! empty( $event_opts ) ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log_arr( 'get_event_options filters returned', $event_opts );
				}
			}

			/**
			 * Add Optional Place ID
			 */
			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'checking for custom event place id (null by default)' );
			}

			if ( ! isset( $event_opts['event_place_id'] ) ) {
				$event_opts['event_place_id'] = null;
			}

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'applying the \'get_event_place_id\' filter to event place id ' . 
					( $event_opts['event_place_id'] === null ? '(null)' : $event_opts['event_place_id'] ) );
			}

			$event_opts['event_place_id'] = apply_filters( $wpsso->lca . '_get_event_place_id', $event_opts['event_place_id'], $mod, $event_id );

			/**
			 * Add ISO Date Options
			 */
			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'checking for custom event start/end date and time' );
			}

			self::add_mod_opts_date_iso( $mod, $event_opts, array( 
				'event_start_date'        => 'schema_event_start',        // Prefix for date, time, timezone, iso.
				'event_end_date'          => 'schema_event_end',          // Prefix for date, time, timezone, iso.
				'event_offers_start_date' => 'schema_event_offers_start', // Prefix for date, time, timezone, iso.
				'event_offers_end_date'   => 'schema_event_offers_end',   // Prefix for date, time, timezone, iso.
			) );

			/**
			 * Add Event Offers
			 */
			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'checking for custom event offers' );
			}

			$have_offers = false;

			foreach ( range( 0, WPSSO_SCHEMA_EVENT_OFFERS_MAX - 1, 1 ) as $key_num ) {

				$offer_opts = apply_filters( $wpsso->lca . '_get_event_offer_options', false, $mod, $event_id, $key_num );

				if ( ! empty( $offer_opts ) ) {
					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log_arr( 'get_event_offer_options filters returned', $offer_opts );
					}
				}

				if ( ! is_array( $offer_opts ) ) {

					$offer_opts = array();

					foreach ( array( 
						'offer_name'           => 'schema_event_offer_name',
						'offer_url'            => 'schema_event_offer_url',
						'offer_price'          => 'schema_event_offer_price',
						'offer_price_currency' => 'schema_event_offer_currency',
						'offer_availability'   => 'schema_event_offer_avail',
					) as $opt_key => $md_pre ) {
						$offer_opts[$opt_key] = $mod['obj']->get_options( $mod['id'], $md_pre . '_' . $key_num );
					}
				}

				// must have at least an offer name and price
				if ( isset( $offer_opts['offer_name'] ) && isset( $offer_opts['offer_price'] ) ) {

					if ( ! isset( $event_opts['offer_url'] ) ) {
						if ( $wpsso->debug->enabled ) {
							$wpsso->debug->log( 'setting offer_url to ' . $sharing_url );
						}
						$offer_opts['offer_url'] = $sharing_url;
					}

					if ( ! isset( $offer_opts['offer_valid_from_date'] ) ) {
						if ( ! empty( $event_opts['event_offers_start_date_iso'] ) ) {
							if ( $wpsso->debug->enabled ) {
								$wpsso->debug->log( 'setting offer_valid_from_date to ' . $event_opts['event_offers_start_date_iso'] );
							}
							$offer_opts['offer_valid_from_date'] = $event_opts['event_offers_start_date_iso'];
						} elseif ( $wpsso->debug->enabled ) {
							$wpsso->debug->log( 'event option event_offers_start_date_iso is empty' );
						}
					}

					if ( ! isset( $offer_opts['offer_valid_to_date'] ) ) {
						if ( ! empty( $event_opts['event_offers_end_date_iso'] ) ) {
							if ( $wpsso->debug->enabled ) {
								$wpsso->debug->log( 'setting offer_valid_to_date to ' . $event_opts['event_offers_end_date_iso'] );
							}
							$offer_opts['offer_valid_to_date'] = $event_opts['event_offers_end_date_iso'];
						} elseif ( $wpsso->debug->enabled ) {
							$wpsso->debug->log( 'event option event_offers_end_date_iso is empty' );
						}
					}

					if ( false === $have_offers ) {
						$have_offers = true;
						if ( $wpsso->debug->enabled ) {
							$wpsso->debug->log( 'custom event offer found - creating new offers array' );
						}
						$event_opts['event_offers'] = array();	// clear offers returned by filter
					}

					$event_opts['event_offers'][] = $offer_opts;
				}
			}

			if ( empty( $event_opts ) ) {	// $event_opts could be false or empty array.
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'exiting early: empty event options' );
				}
				return 0;
			}

			/**
			 * If not adding a list element, inherit the existing schema type url (if one exists).
			 */
			list( $event_type_id, $event_type_url ) = self::get_single_type_id_url( $json_data, $event_opts, 'event_type', 'event', $list_element );

			$ret = self::get_schema_type_context( $event_type_url );

			if ( isset( $event_opts['event_organizer_person_id'] ) && SucomUtil::is_opt_id( $event_opts['event_organizer_person_id'] ) ) {
				if ( ! self::add_single_person_data( $ret['organizer'], $mod, $event_opts['event_organizer_person_id'], false ) ) {
					unset( $ret['organizer'] );
				}
			}

			if ( isset( $event_opts['event_place_id'] ) && SucomUtil::is_opt_id( $event_opts['event_place_id'] ) ) {
				if ( ! self::add_single_place_data( $ret['location'], $mod, $event_opts['event_place_id'], false ) ) {
					unset( $ret['location'] );
				}
			}

			self::add_data_itemprop_from_assoc( $ret, $event_opts, array(
				'startDate' => 'event_start_date_iso',
				'endDate'   => 'event_end_date_iso',
			) );

			if ( ! empty( $event_opts['event_offers'] ) && is_array( $event_opts['event_offers'] ) ) {

				foreach ( $event_opts['event_offers'] as $event_offer ) {

					// setup the offer with basic itemprops
					if ( is_array( $event_offer ) &&	// Just in case.
						( $offer = self::get_data_itemprop_from_assoc( $event_offer, array( 
							'name'          => 'offer_name',
							'url'           => 'offer_url',
							'price'         => 'offer_price',
							'priceCurrency' => 'offer_price_currency',
							'availability'  => 'offer_availability',	// In stock, Out of stock, Pre-order, etc.
							'validFrom'     => 'offer_valid_from_date',
							'validThrough'  => 'offer_valid_to_date',
					) ) ) !== false ) {
						// add the complete offer
						$ret['offers'][] = self::get_schema_type_context( 'https://schema.org/Offer', $offer );
					}
				}
			}

			$ret = apply_filters( $wpsso->lca . '_json_data_single_event', $ret, $mod, $event_id );

			if ( empty( $list_element ) ) {
				$json_data = $ret;
			} else {
				$json_data[] = $ret;
			}

			return 1;
		}

		public static function add_single_job_data( &$json_data, array $mod, $job_id = false, $list_element = false ) {

			$ret =& self::set_single_data_from_cache( $json_data, $mod, 'job', $job_id, $list_element );

			if ( $ret !== false ) {	// 0 or 1 (data retrieved from cache)
				return $ret;
			}

			$wpsso =& Wpsso::get_instance();

			/**
			 * Get job options from Pro modules and/or custom filters.
			 */
			$job_opts = apply_filters( $wpsso->lca . '_get_job_options', false, $mod, $job_id );

			if ( ! empty( $job_opts ) ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log_arr( 'get_job_options filters returned', $job_opts );
				}
			}

			/**
			 * Override job options from filters with custom meta values (if any).
			 */
			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'merging default, filter, and custom option values' );
			}

			self::merge_custom_mod_opts( $mod, $job_opts, array( 'job' => 'schema_job' ) );

			/**
			 * If not adding a list element, inherit the existing schema type url (if one exists).
			 */
			list( $job_type_id, $job_type_url ) = self::get_single_type_id_url( $json_data, $job_opts, 'job_type', 'job.posting', $list_element );

			$ret = self::get_schema_type_context( $job_type_url );

			if ( empty( $job_opts['job_title'] ) ) {
				$job_opts['job_title'] = $wpsso->page->get_title( 0, '', $mod, true, false, true, 'schema_title', false );
			}

			/**
			 * Create and add ISO formatted date options.
			 */
			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'checking for custom job expire date and time' );
			}
			self::add_mod_opts_date_iso( $mod, $job_opts, array( 'job_expire' => 'schema_job_expire' ) );

			// add schema properties from the job options
			self::add_data_itemprop_from_assoc( $ret, $job_opts, array(
				'title'        => 'job_title',
				'validThrough' => 'job_expire_iso',
			) );

			if ( isset( $job_opts['job_salary'] ) && is_numeric( $job_opts['job_salary'] ) ) {	// allow for 0

				$ret['baseSalary'] = self::get_schema_type_context( 'https://schema.org/MonetaryAmount' );

				self::add_data_itemprop_from_assoc( $ret['baseSalary'], $job_opts, array(
					'currency' => 'job_salary_currency',
				) );

				$ret['baseSalary']['value'] = self::get_schema_type_context( 'https://schema.org/QuantitativeValue' );

				self::add_data_itemprop_from_assoc( $ret['baseSalary']['value'], $job_opts, array(
					'value' => 'job_salary',
					'unitText' => 'job_salary_period',
				) );
			}

			/**
			 * Allow for a preformatted employment types array.
			 */
			if ( ! empty( $job_opts['job_empl_types'] ) && is_array( $job_opts['job_empl_types'] ) ) {
				$ret['employmentType'] = $job_opts['job_empl_types'];
			}

			/**
			 * Add single employment type options (value must be non-empty).
			 */
			foreach ( SucomUtil::preg_grep_keys( '/^job_empl_type_/', $job_opts, false, '' ) as $empl_type => $checked ) {
				if ( ! empty( $checked ) ) {
					$ret['employmentType'][] = $empl_type;
				}
			}

			if ( isset( $job_opts['job_org_id'] ) && SucomUtil::is_opt_id( $job_opts['job_org_id'] ) ) {	// allow for 0
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'adding organization data for job_org_id ' . $job_opts['job_org_id'] );
				}
				if ( ! self::add_single_organization_data( $ret['hiringOrganization'], $mod, $job_opts['job_org_id'], 'org_logo_url', false ) ) {
					unset( $ret['hiringOrganization'] );
				}
			} elseif ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'job_org_id is empty or none' );
			}

			if ( isset( $job_opts['job_location_id'] ) && SucomUtil::is_opt_id( $job_opts['job_location_id'] ) ) {	// allow for 0
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'adding place data for job_location_id ' . $job_opts['job_location_id'] );
				}
				if ( ! self::add_single_place_data( $ret['jobLocation'], $mod, $job_opts['job_location_id'], false ) ) {
					unset( $ret['jobLocation'] );
				}
			} elseif ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'job_location_id is empty or none' );
			}

			$ret = apply_filters( $wpsso->lca . '_json_data_single_job', $ret, $mod, $job_id );

			if ( empty( $list_element ) ) {
				$json_data = $ret;
			} else {
				$json_data[] = $ret;
			}

			return 1;
		}

		/**
		 * $org_id can be 'none', 'site', or a number (including 0).
		 * $logo_key can be 'org_logo_url' or 'org_banner_url' (600x60px image) for Articles.
		 * Do not provide localized option names - the method will fetch the localized values.
		 */
		public static function add_single_organization_data( &$json_data, $mod, $org_id = 'site', $logo_key = 'org_logo_url', $list_element = false ) {

			if ( ! SucomUtil::is_opt_id( $org_id ) ) {	// allow for 0 but not false or null
				return 0;
			}

			$ret =& self::set_single_data_from_cache( $json_data, $mod, 'organization', $org_id, $list_element );

			if ( $ret !== false ) {	// 0 or 1 (data retrieved from cache)
				return $ret;
			}

			$wpsso =& Wpsso::get_instance();

			/**
			 * Returned organization option values can change depending on the locale, but the option key names should NOT be localized.
			 *
			 * Example: 'org_banner_url' is a valid option key, but 'org_banner_url#fr_FR' is not.
			 */
			$org_opts = apply_filters( $wpsso->lca . '_get_organization_options', false, $mod, $org_id );

			if ( ! empty( $org_opts ) ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log_arr( 'get_organization_options filters returned', $org_opts );
				}
			} else {
				if ( $org_id === 'site' ) {
					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'getting site organization options array' );
					}
					$org_opts = self::get_site_organization( $mod ); // returns localized values (not the key names)
				} else {
					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'exiting early: unknown org_id ' . $org_id );
					}
					return 0;
				}
			}

			// if not adding a list element, inherit the existing schema type url (if one exists)
			list( $org_type_id, $org_type_url ) = self::get_single_type_id_url( $json_data, $org_opts, 'org_type', 'organization', $list_element );

			$ret = self::get_schema_type_context( $org_type_url );

			// set reference values for admin notices
			if ( is_admin() ) {
				$sharing_url = $wpsso->util->get_sharing_url( $mod );
				$wpsso->notice->set_ref( $sharing_url, $mod, __( 'adding schema for organization', 'wpsso' ) );
			}

			/**
			 * Add schema properties from the organization options.
			 */
			self::add_data_itemprop_from_assoc( $ret, $org_opts, array(
				'url'           => 'org_url',
				'name'          => 'org_name',
				'alternateName' => 'org_name_alt',
				'description'   => 'org_desc',
				'email'         => 'org_email',
				'telephone'     => 'org_phone',
			) );

			/**
			 * Organization Logo
			 *
			 * $logo_key can be false, 'org_logo_url' (default), or 'org_banner_url' (600x60px image) for Articles
			 */
			if ( ! empty( $logo_key ) ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'adding image from ' . $logo_key . ' option' );
				}
				if ( ! empty( $org_opts[$logo_key] ) ) {
					if ( ! self::add_og_single_image_data( $ret['logo'], $org_opts, $logo_key, false ) ) {	// $list_element is false.
						unset( $ret['logo'] );	// Prevent null assignment.
					}
				}
				if ( empty( $ret['logo'] ) ) {
					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'organization ' . $logo_key . ' image is missing and required' );
					}
					if ( $wpsso->notice->is_admin_pre_notices() && ( ! $mod['is_post'] || $mod['post_status'] === 'publish' ) ) {
						if ( $logo_key === 'org_logo_url' ) {
							$wpsso->notice->err( sprintf( __( 'The "%1$s" Organization Logo image is missing and required for the Schema %2$s markup.', 'wpsso' ), $ret['name'], $org_type_url ) );
						} elseif ( $logo_key === 'org_banner_url' ) {
							$wpsso->notice->err( sprintf( __( 'The "%1$s" Organization Banner (600x60px) image is missing and required for the Schema %2$s markup.', 'wpsso' ), $ret['name'], $org_type_url ) );
						}
					}
				}
			}

			/**
			 * Place / Location Properties
			 */
			if ( isset( $org_opts['org_place_id'] ) && SucomUtil::is_opt_id( $org_opts['org_place_id'] ) ) {

				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'adding place / location properties' );
				}

				/**
				 * Check for a custom place id that might have precedence.
				 *
				 * 'plm_addr_id' can be 'none', 'custom', or numeric (including 0).
				 */
				if ( ! empty( $mod['obj'] ) ) {
					$place_id = $mod['obj']->get_options( $mod['id'], 'plm_addr_id' );
				} else {
					$place_id = null;
				}

				if ( null === $place_id ) {
					$place_id = $org_opts['org_place_id'];
				} else {
					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'overriding org_place_id ' . $org_opts['org_place_id'] . ' with plm_addr_id ' . $place_id );
					}
				}

				if ( ! self::add_single_place_data( $ret['location'], $mod, $place_id, false ) ) {	// $list_element is false.
					unset( $ret['location'] );	// prevent null assignment
				}
			}

			/**
			 * Google's Knowledge Graph
			 */
			$org_opts['org_sameas'] = isset( $org_opts['org_sameas'] ) ? $org_opts['org_sameas'] : array();
			$org_opts['org_sameas'] = apply_filters( $wpsso->lca . '_json_data_single_organization_sameas', $org_opts['org_sameas'], $mod, $org_id );

			if ( ! empty( $org_opts['org_sameas'] ) && is_array( $org_opts['org_sameas'] ) ) {	// Just in case.
				foreach ( $org_opts['org_sameas'] as $url ) {
					if ( ! empty( $url ) ) {	// Just in case.
						$ret['sameAs'][] = esc_url_raw( $url );
					}
				}
			}

			if ( ! empty( $org_type_id ) && $org_type_id !== 'organization' && 
				$wpsso->schema->is_schema_type_child( $org_type_id, 'local.business' ) ) {
				$wpsso->schema->organization_to_localbusiness( $ret );
			}

			$ret = apply_filters( $wpsso->lca . '_json_data_single_organization', $ret, $mod, $org_id );

			/**
			 * Restore previous reference values for admin notices.
			 */
			if ( is_admin() ) {
				$wpsso->notice->unset_ref( $sharing_url );
			}

			if ( empty( $list_element ) ) {
				$json_data = $ret;
			} else {
				$json_data[] = $ret;
			}

			return 1;
		}

		/**
		 * A $user_id argument is required.
		 */
		public static function add_single_person_data( &$json_data, $mod, $user_id, $list_element = true ) {

			$ret =& self::set_single_data_from_cache( $json_data, $mod, 'person', $user_id, $list_element );

			if ( $ret !== false ) {	// 0 or 1 (data retrieved from cache)
				return $ret;
			}

			$wpsso       =& Wpsso::get_instance();
			$size_name   = $wpsso->lca . '-schema';
			$person_opts = apply_filters( $wpsso->lca . '_get_person_options', false, $mod, $user_id );

			if ( ! empty( $person_opts ) ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log_arr( 'get_person_options filters returned', $person_opts );
				}
			} else {
				if ( empty( $user_id ) || $user_id === 'none' ) {
					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'exiting early: empty user_id' );
					}
					return 0;
				} elseif ( empty( $wpsso->m['util']['user'] ) ) {
					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'exiting early: empty user module' );
					}
					return 0;
				} else {
					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'getting user module for user_id ' . $user_id );
					}
					$user_mod = $wpsso->m['util']['user']->get_mod( $user_id );
				}

				$md_idx = array( 'schema_desc', 'seo_desc', 'og_desc' );

				$user_desc = $user_mod['obj']->get_options_multi( $user_id, $md_idx );

				if ( empty( $user_desc ) ) {
					$user_desc = $user_mod['obj']->get_author_meta( $user_id, 'description' );
				}

				/**
				 * Remove shortcodes, strip html, etc.
				 */
				$user_desc = $wpsso->util->cleanup_html_tags( $user_desc );

				$user_sameas = array();

				foreach ( WpssoUser::get_user_id_contact_methods( $user_id ) as $cm_id => $cm_label ) {

					$url = $user_mod['obj']->get_author_meta( $user_id, $cm_id );

					if ( empty( $url ) ) {
						continue;
					} elseif ( $cm_id === $wpsso->options['plugin_cm_twitter_name'] ) {	// convert twitter name to url
						$url = 'https://twitter.com/' . preg_replace( '/^@/', '', $url );
					}

					if ( filter_var( $url, FILTER_VALIDATE_URL ) !== false ) {
						$user_sameas[] = $url;
					}
				}

				$person_opts = array(
					'person_type'      => 'person',
					'person_url'       => $user_mod['obj']->get_author_website( $user_id, 'url' ),
					'person_name'      => $user_mod['obj']->get_author_meta( $user_id, $wpsso->options['schema_author_name'] ),
					'person_desc'      => $user_desc,
					'person_job_title' => $user_mod['obj']->get_options( $user_id, 'schema_person_job_title' ),
					'person_og_image'  => $user_mod['obj']->get_og_images( 1, $size_name, $user_id, false ),
					'person_sameas'    => $user_sameas,
				);
			}

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log_arr( 'person options', $person_opts );
			}

			// if not adding a list element, inherit the existing schema type url (if one exists)
			list( $person_type_id, $person_type_url ) = self::get_single_type_id_url( $json_data, $person_opts, 'person_type', 'person', $list_element );

			$ret = self::get_schema_type_context( $person_type_url );

			self::add_data_itemprop_from_assoc( $ret, $person_opts, array(
				'url'         => 'person_url',
				'name'        => 'person_name',
				'description' => 'person_desc',
				'jobTitle'    => 'person_job_title',
				'email'       => 'person_email',
				'telephone'   => 'person_phone',
			) );

			/**
			 * Images
			 */
			if ( ! empty( $person_opts['person_og_image'] ) ) {
				if ( ! self::add_og_image_list_data( $ret['image'], $person_opts['person_og_image'] ) ) {
					unset( $ret['image'] );	// prevent null assignment
				}
			}

			/**
			 * Google's Knowledge Graph
			 */
			$person_opts['person_sameas'] = isset( $person_opts['person_sameas'] ) ? $person_opts['person_sameas'] : array();
			$person_opts['person_sameas'] = apply_filters( $wpsso->lca . '_json_data_single_person_sameas', $person_opts['person_sameas'], $mod, $user_id );

			if ( ! empty( $person_opts['person_sameas'] ) && is_array( $person_opts['person_sameas'] ) ) {	// Just in case.
				foreach ( $person_opts['person_sameas'] as $url ) {
					if ( ! empty( $url ) ) {	// Just in case.
						$ret['sameAs'][] = esc_url_raw( $url );
					}
				}
			}

			$ret = apply_filters( $wpsso->lca . '_json_data_single_person', $ret, $mod, $user_id );

			if ( empty( $list_element ) ) {
				$json_data = $ret;
			} else {
				$json_data[] = $ret;
			}

			return 1;
		}

		public static function add_single_place_data( &$json_data, $mod, $place_id = false, $list_element = false ) {

			$ret =& self::set_single_data_from_cache( $json_data, $mod, 'place', $place_id, $list_element );

			if ( $ret !== false ) {	// 0 or 1 (data retrieved from cache)
				return $ret;
			}

			$wpsso      =& Wpsso::get_instance();
			$size_name  = $wpsso->lca . '-schema';
			$place_opts = apply_filters( $wpsso->lca . '_get_place_options', false, $mod, $place_id );

			if ( ! empty( $place_opts ) ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log_arr( 'get_place_options filters returned', $place_opts );
				}
			} else {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'exiting early: empty place options' );
				}
				return 0;
			}

			/**
			 * If not adding a list element, inherit the existing schema type url (if one exists).
			 */
			list( $place_type_id, $place_type_url ) = self::get_single_type_id_url( $json_data, $place_opts, 'place_business_type', 'place', $list_element );

			$ret = self::get_schema_type_context( $place_type_url );

			/**
			 * Set reference values for admin notices.
			 */
			if ( is_admin() ) {
				$sharing_url = $wpsso->util->get_sharing_url( $mod );
				$wpsso->notice->set_ref( $sharing_url, $mod, __( 'adding schema for place', 'wpsso' ) );
			}

			/**
			 * Add schema properties from the place options.
			 */
			self::add_data_itemprop_from_assoc( $ret, $place_opts, array(
				'url' => 'place_url',
				'name' => 'place_name',
				'alternateName' => 'place_name_alt',
				'description' => 'place_desc',
				'telephone' => 'place_phone',
				'currenciesAccepted' => 'place_currencies_accepted',
				'paymentAccepted' => 'place_payment_accepted',
				'priceRange' => 'place_price_range',
			) );

			/**
			 * Property:
			 *	address as https://schema.org/PostalAddress
			 */
			$address = array();

			if ( self::add_data_itemprop_from_assoc( $address, $place_opts, array(
				'name' => 'place_name', 
				'streetAddress' => 'place_streetaddr', 
				'postOfficeBoxNumber' => 'place_po_box_number', 
				'addressLocality' => 'place_city',
				'addressRegion' => 'place_state',
				'postalCode' => 'place_zipcode',
				'addressCountry' => 'place_country',	// alpha2 country code
			) ) ) {
				$ret['address'] = self::get_schema_type_context( 'https://schema.org/PostalAddress', $address );
			}

			/**
			 * Property:
			 *	geo as https://schema.org/GeoCoordinates
			 */
			$geo = array();

			if ( self::add_data_itemprop_from_assoc( $geo, $place_opts, array(
				'elevation' => 'place_altitude', 
				'latitude' => 'place_latitude',
				'longitude' => 'place_longitude',
			) ) ) {
				$ret['geo'] = self::get_schema_type_context( 'https://schema.org/GeoCoordinates', $geo );
			}

			/**
			 * Property:
			 *	openingHoursSpecification as https://schema.org/OpeningHoursSpecification
			 */
			$opening_hours = array();

			foreach ( $wpsso->cf['form']['weekdays'] as $day => $label ) {
				if ( ! empty( $place_opts['place_day_' . $day] ) ) {
					$dayofweek = array(
						'@context' => 'https://schema.org',
						'@type' => 'OpeningHoursSpecification',
						'dayOfWeek' => $label,
					);
					foreach ( array(
						'opens' => 'place_day_' . $day . '_open',
						'closes' => 'place_day_' . $day . '_close',
						'validFrom' => 'place_season_from_date',
						'validThrough' => 'place_season_to_date',
					) as $prop_name => $opt_key ) {
						if ( isset( $place_opts[$opt_key] ) && $place_opts[$opt_key] !== '' ) {
							$dayofweek[$prop_name] = $place_opts[$opt_key];
						}
					}
					$opening_hours[] = $dayofweek;
				}
			}

			if ( ! empty( $opening_hours ) ) {
				$ret['openingHoursSpecification'] = $opening_hours;
			}

			/**
			 * FoodEstablishment schema type properties
			 */
			if ( ! empty( $place_opts['place_business_type'] ) && $place_opts['place_business_type'] !== 'none' ) {
				if ( $wpsso->schema->is_schema_type_child( $place_opts['place_business_type'], 'food.establishment' ) ) {
					foreach ( array(
						'acceptsReservations' => 'place_accept_res',
						'hasMenu' => 'place_menu_url',
						'servesCuisine' => 'place_cuisine',
					) as $prop_name => $opt_key ) {
						if ( $opt_key === 'place_accept_res' ) {
							$ret[$prop_name] = empty( $place_opts[$opt_key] ) ? 'false' : 'true';
						} elseif ( isset( $place_opts[$opt_key] ) ) {
							$ret[$prop_name] = $place_opts[$opt_key];
						}
					}
				}
			}

			if ( ! empty( $place_opts['place_order_urls'] ) ) {
				foreach ( SucomUtil::explode_csv( $place_opts['place_order_urls'] ) as $order_url ) {
					if ( ! empty( $order_url ) ) {	// Just in case.
						$ret['potentialAction'][] = array(
							'@context' => 'https://schema.org',
							'@type' => 'OrderAction',
							'target' => $order_url,
						);
					}
				}
			}

			/**
			 * Image
			 */
			if ( ! empty( $place_opts['place_img_id'] ) || ! empty( $place_opts['place_img_url'] ) ) {

				$mt_image = $wpsso->media->get_opts_single_image( $place_opts, $size_name, 'place_img' );

				if ( ! self::add_og_single_image_data( $ret['image'], $mt_image, 'og:image', true ) ) {	// $list_element is true.
					unset( $ret['image'] );	// Prevent null assignment.
				}
			}

			$ret = apply_filters( $wpsso->lca . '_json_data_single_place', $ret, $mod, $place_id );

			/**
			 * Restore previous reference values for admin notices.
			 */
			if ( is_admin() ) {
				$wpsso->notice->unset_ref( $sharing_url );
			}

			if ( empty( $list_element ) ) {
				$json_data = $ret;
			} else {
				$json_data[] = $ret;
			}

			return 1;
		}

		/**
		 * Meta Name Array
		 */
		public function get_meta_array( array &$mod, array &$mt_og, $crawler_name ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			// returns false when the wpsso-schema-json-ld add-on is active
			if ( ! apply_filters( $this->p->lca . '_add_schema_meta_array', true ) ) {
				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'exiting early: schema meta array disabled' );
				}
				return array();	// empty array
			}

			$mt_schema     = array();
			$max_nums      = $this->p->util->get_max_nums( $mod, 'schema' );
			$page_type_id  = $this->get_mod_schema_type( $mod, true );	// $get_schema_id is true.
			$page_type_url = $this->get_schema_type_url( $page_type_id );
			$size_name     = $this->p->lca . '-schema';

			$this->add_mt_schema_from_og( $mt_schema, $mt_og, array(
				'url'  => 'og:url',
				'name' => 'og:title',
			) );

			if ( ! empty( $this->p->options['add_meta_itemprop_description'] ) ) {

				$desc_len = $this->p->options['schema_desc_len'];
				$desc_idx = array( 'schema_desc', 'seo_desc', 'og_desc' );

				$mt_schema['description'] = $this->p->page->get_description( $desc_len, '...', $mod, true, false, true, $desc_idx );
			}

			switch ( $page_type_url ) {
				case 'https://schema.org/BlogPosting':
					$size_name = $this->p->lca . '-schema-article';
					// no break - add date published and modified

				case 'https://schema.org/WebPage':
					$this->add_mt_schema_from_og( $mt_schema, $mt_og, array(
						'datePublished' => 'article:published_time',
						'dateModified' => 'article:modified_time',
					) );
					break;
			}

			if ( $this->is_noscript_enabled( $crawler_name ) ) {
				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'skipping images: noscript is enabled for ' . $crawler_name );
				}
			} elseif ( empty( $this->p->options['add_meta_itemprop_image'] ) ) {
				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'skipping images: meta itemprop image is disabled' );
				}
			} else {	// add single image meta tags (no width or height)
				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'getting images for ' . $page_type_url );
				}

				$og_images = $this->p->og->get_all_images( $max_nums['schema_img_max'], $size_name, $mod, true, 'schema' );	// $md_pre is 'schema'.

				if ( empty( $og_images ) && $mod['is_post'] ) {
					$og_images = $this->p->media->get_default_images( 1, $size_name, true );
				}

				foreach ( $og_images as $og_single_image ) {
					$mt_schema['image'][] = SucomUtil::get_mt_media_url( $og_single_image );
				}
			}

			return (array) apply_filters( $this->p->lca . '_schema_meta_itemprop', $mt_schema, $mod, $mt_og, $page_type_id );
		}

		public function add_mt_schema_from_og( array &$mt_schema, array &$assoc, array $names ) {
			foreach ( $names as $itemprop_name => $key_name ) {
				if ( ! empty( $assoc[$key_name] ) && $assoc[$key_name] !== WPSSO_UNDEF_INT ) {
					$mt_schema[$itemprop_name] = $assoc[$key_name];
				}
			}
		}

		/**
		 * LocalBusiness markup requires an image, and the address, priceRange, 
		 * and telephone properties are recommended.
		 */
		public function organization_to_localbusiness( array &$json_data ) {

			/**
			 * Promote all location information up.
			 */
			if ( isset( $json_data['location'] ) ) {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'promoting location property array' );
				}

				$prop_added = self::add_data_itemprop_from_assoc( $json_data, $json_data['location'], 
					array_keys( $json_data['location'] ), false );	// $overwrite is false.

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'promoted ' . $prop_added . ' location keys' );
				}

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'removing the location property' );
				}

				unset( $json_data['location'] );

			} elseif ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'no location property to promote' );
			}

			/**
			 * Google requires a local business to have an image.
			 * Check last as the location may have had an image that was promoted.
			 */
			if ( isset( $json_data['logo'] ) && empty( $json_data['image'] ) ) {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'adding logo from organization markup' );
				}

				$json_data['image'][] = $json_data['logo'];

			} elseif ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'logo is missing from organization markup' );
			}

		}

		/**
		 * NoScript Meta Name Array
		 */
		public function get_noscript_array( array &$mod, array &$mt_og, $crawler_name ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			if ( ! self::is_noscript_enabled( $crawler_name ) ) {
				return array();	// empty array
			}

			$ret           = array();
			$max_nums      = $this->p->util->get_max_nums( $mod, 'schema' );
			$page_type_id  = $this->get_mod_schema_type( $mod, true );	// $get_schema_id is true.
			$page_type_url = $this->get_schema_type_url( $page_type_id );
			$size_name     = $this->p->lca . '-schema';
			$og_type_id    = $mt_og['og:type'];

			switch ( $page_type_url ) {
				case 'https://schema.org/BlogPosting':
					$size_name = $this->p->lca . '-schema-article';
					// no break - get the webpage author list as well

				case 'https://schema.org/WebPage':
					$ret = array_merge( $ret, $this->get_author_list_noscript( $mod ) );
					break;
			}

			if ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'getting images for ' . $page_type_url );
			}

			$og_images = $this->p->og->get_all_images( $max_nums['schema_img_max'], $size_name, $mod, true, 'schema' );	// $md_pre is 'schema'.

			if ( empty( $og_images ) && $mod['is_post'] ) {
				$og_images = $this->p->media->get_default_images( 1, $size_name, true );
			}

			foreach ( $og_images as $og_single_image ) {
				$ret = array_merge( $ret, $this->get_single_image_noscript( $mod, $og_single_image ) );
			}

			if ( ! empty( $mt_og[$og_type_id . ':rating:average'] ) ) {	// Example: "product:rating:average".
				$ret = array_merge( $ret, $this->get_aggregate_rating_noscript( $mod, $og_type_id, $mt_og ) );
			}

			return (array) apply_filters( $this->p->lca . '_schema_noscript_array', $ret, $mod, $mt_og, $page_type_id );
		}

		public function is_noscript_enabled( $crawler_name = false ) {

			if ( SucomUtil::is_amp() ) {
				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'noscript disabled for amp endpoint' );
				}
				return false;
			}

			if ( false === $crawler_name ) {
				if ( is_admin() ) {
					$crawler_name = 'none';
				} else {
					$crawler_name = SucomUtil::get_crawler_name();
				}
			}

			$is_enabled = empty( $this->p->options['schema_add_noscript'] ) ? false : true;

			/**
			 * Returns false when the wpsso-schema-json-ld add-on is active.
			 */
			if ( ! apply_filters( $this->p->lca . '_add_schema_noscript_array', $is_enabled, $crawler_name ) ) {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'noscript is disabled for crawler "' . $crawler_name . '"' );
				}

				return false;
			}

			return true;
		}

		public function get_single_image_noscript( array &$mod, &$mixed, $mt_image_pre = 'og:image' ) {

			$mt_image = array();

			if ( empty( $mixed ) ) {

				return array();

			} elseif ( is_array( $mixed ) ) {

				$image_url = SucomUtil::get_mt_media_url( $mixed, $mt_image_pre );

				if ( empty( $image_url ) ) {
					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'exiting early: ' . $mt_image_pre . ' url values are empty' );
					}
					return array();
				}

				/**
				 * Defines a two-dimensional array.
				 */
				$mt_image = array_merge(
					$this->p->head->get_single_mt( 'link', 'itemprop', 'image.url', $image_url, '', $mod ),
					( empty( $mixed[$mt_image_pre . ':width'] ) ? array() : $this->p->head->get_single_mt( 'meta',
						'itemprop', 'image.width', $mixed[$mt_image_pre . ':width'], '', $mod ) ),
					( empty( $mixed[$mt_image_pre . ':height'] ) ? array() : $this->p->head->get_single_mt( 'meta',
						'itemprop', 'image.height', $mixed[$mt_image_pre . ':height'], '', $mod ) )
				);

			} else {

				/**
				 * Defines a two-dimensional array.
				 */
				$mt_image = $this->p->head->get_single_mt( 'link', 'itemprop', 'image.url', $mixed, '', $mod );
			}

			/**
			 * Make sure we have html for at least one meta tag.
			 */
			$have_image_html = false;

			foreach ( $mt_image as $num => $img ) {
				if ( ! empty( $img[0] ) ) {
					$have_image_html = true;
					break;
				}
			}

			if ( $have_image_html ) {
				return array_merge(
					array( array( '<noscript itemprop="image" itemscope itemtype="https://schema.org/ImageObject">' . "\n" ) ),
					$mt_image,
					array( array( '</noscript>' . "\n" ) )
				);
			} else {
				return array();
			}
		}

		public function get_aggregate_rating_noscript( array &$mod, $og_type_id, array $mt_og ) {

			/**
			 * Aggregate rating needs at least one rating or review count.
			 */
			if ( empty( $mt_og[$og_type_id . ':rating:average'] ) ||
				( empty( $mt_og[$og_type_id . ':rating:count'] ) && empty( $mt_og[$og_type_id . ':review:count'] ) ) ) {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'exiting early: rating average and/or counts are empty' );
				}

				return array();
			}

			return array_merge(
				array( array( '<noscript itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">' . "\n" ) ),
				( empty( $mt_og[$og_type_id . ':rating:average'] ) ? 
					array() : $this->p->head->get_single_mt( 'meta', 'itemprop',
						'aggregaterating.ratingValue', $mt_og[$og_type_id . ':rating:average'], '', $mod ) ),
				( empty( $mt_og[$og_type_id . ':rating:count'] ) ? 
					array() : $this->p->head->get_single_mt( 'meta', 'itemprop',
						'aggregaterating.ratingCount', $mt_og[$og_type_id . ':rating:count'], '', $mod ) ),
				( empty( $mt_og[$og_type_id . ':rating:worst'] ) ? 
					array() : $this->p->head->get_single_mt( 'meta', 'itemprop',
						'aggregaterating.worstRating', $mt_og[$og_type_id . ':rating:worst'], '', $mod ) ),
				( empty( $mt_og[$og_type_id . ':rating:best'] ) ? 
					array() : $this->p->head->get_single_mt( 'meta', 'itemprop',
						'aggregaterating.bestRating', $mt_og[$og_type_id . ':rating:best'], '', $mod ) ),
				( empty( $mt_og[$og_type_id . ':review:count'] ) ? 
					array() : $this->p->head->get_single_mt( 'meta', 'itemprop', 
						'aggregaterating.reviewCount', $mt_og[$og_type_id . ':review:count'], '', $mod ) ),
				array( array( '</noscript>' . "\n" ) )
			);
		}

		public function get_author_list_noscript( array &$mod ) {

			if ( empty( $mod['post_author'] ) ) {
				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'exiting early: empty post_author' );
				}
				return array();
			}

			$ret = $this->get_single_author_noscript( $mod, $mod['post_author'], 'author' );

			if ( ! empty( $mod['post_coauthors'] ) ) {
				foreach ( $mod['post_coauthors'] as $author_id ) {
					$ret = array_merge( $ret, $this->get_single_author_noscript( $mod, $author_id, 'contributor' ) );
				}
			}

			return $ret;
		}

		public function get_single_author_noscript( array &$mod, $author_id = 0, $itemprop = 'author' ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->log_args( array( 
					'author_id' => $author_id,
					'itemprop'  => $itemprop,
				) );
			}

			$og_ret = array();

			if ( empty( $author_id ) || $author_id === 'none' ) {
				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'exiting early: empty author_id' );
				}
				return array();
			} elseif ( empty( $this->p->m['util']['user'] ) ) {
				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'exiting early: empty user module' );
				}
				return array();
			} else {
				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'getting user_mod for author id '.$author_id );
				}
				$user_mod = $this->p->m['util']['user']->get_mod( $author_id );
			}

			$author_url  = $user_mod['obj']->get_author_website( $author_id, 'url' );
			$author_name = $user_mod['obj']->get_author_meta( $author_id, $this->p->options['schema_author_name'] );
			$desc_idx    = array( 'schema_desc', 'seo_desc', 'og_desc' );
			$author_desc = $user_mod['obj']->get_options_multi( $author_id, $desc_idx );

			if ( empty( $author_desc ) ) {
				$author_desc = $user_mod['obj']->get_author_meta( $author_id, 'description' );
			}

			$mt_author = array_merge(
				( empty( $author_url ) ? array() : $this->p->head->get_single_mt( 'link', 'itemprop', $itemprop . '.url', $author_url, '', $user_mod ) ),
				( empty( $author_name ) ? array() : $this->p->head->get_single_mt( 'meta', 'itemprop', $itemprop . '.name', $author_name, '', $user_mod ) ),
				( empty( $author_desc ) ? array() : $this->p->head->get_single_mt( 'meta', 'itemprop', $itemprop . '.description', $author_desc, '', $user_mod ) )
			);

			/**
			 * Optimize by first checking if the head tag is enabled.
			 */
			if ( ! empty( $this->p->options['add_link_itemprop_author.image'] ) ) {

				/**
				 * get_og_images() also provides filter hooks for additional image ids and urls.
				 */
				$size_name = $this->p->lca . '-schema';
				$og_images = $user_mod['obj']->get_og_images( 1, $size_name, $author_id, false );	// $check_dupes is false.
	
				foreach ( $og_images as $og_single_image ) {

					$image_url = SucomUtil::get_mt_media_url( $og_single_image );

					if ( ! empty( $image_url ) ) {
						$mt_author = array_merge( $mt_author, $this->p->head->get_single_mt( 'link',
							'itemprop', $itemprop . '.image', $image_url, '', $user_mod ) );
					}
				}
			}

			/**
			 * Make sure we have html for at least one meta tag.
			 */
			$have_author_html = false;

			foreach ( $mt_author as $num => $author ) {
				if ( ! empty( $author[0] ) ) {
					$have_author_html = true;
					break;
				}
			}

			if ( $have_author_html ) {
				return array_merge(
					array( array( '<noscript itemprop="' . $itemprop . '" itemscope itemtype="https://schema.org/Person">' . "\n" ) ),
					$mt_author,
					array( array( '</noscript>' . "\n" ) )
				);
			} else {
				return array();
			}
		}
	}
}
