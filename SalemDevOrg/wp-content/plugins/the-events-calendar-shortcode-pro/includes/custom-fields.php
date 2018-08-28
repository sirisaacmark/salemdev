<?php
/*
 * Custom fields support
 */

// Toolset Types

function ecsp_is_toolset_types_installed() {
    return defined( 'TYPES_VERSION' );
}

function ecsp_get_toolset_types_field_meta() {
    global $wpdb;
    $cf_meta = $wpdb->get_row( "SELECT option_value FROM $wpdb->options WHERE option_name = 'wpcf-fields'" );
    $cf_meta = unserialize($cf_meta->option_value);
    return $cf_meta;
}

function ecsp_get_toolset_types_custom_fields() {
    global $wpdb;
    $post_type = 'tribe_events';
    $fields = array();
    $query = "
    SELECT * 
    FROM  $wpdb->postmeta AS pm1, $wpdb->postmeta AS pm2, $wpdb->posts
    WHERE pm1.meta_key = '_wp_types_group_post_types'
    AND pm1.meta_value LIKE '%$post_type%'
    AND pm2.post_id = pm1.post_id
    AND pm2.meta_key = '_wp_types_group_fields'
    AND $wpdb->posts.ID = pm2.post_id
    ORDER BY $wpdb->posts.post_title ASC
";
    $results = $wpdb->get_results( $query );
    $cf_meta = ecsp_get_toolset_types_field_meta();
    $my_cfs['post_type'] = $post_type;
    foreach ( $results as $result ) {
        $the_fields = explode( ',', $result->meta_value );
        $the_fields = array_filter( $the_fields );
        foreach ( $the_fields as $the_field ) {
            $fields[] = $cf_meta[$the_field];
        }
    }
    return $fields;
}

function ecsp_add_toolset_types_custom_fields( $atts ) {
    if ( !ecsp_is_toolset_types_installed() ) {
        return $atts;
    }
    foreach ( (array) ecsp_get_toolset_types_custom_fields() as $field ) {
        if ( !isset( $atts[ $field['slug'] ]) ) {
            $atts[ $field['slug'] ] = '';
        }
    }
    return $atts;
}
add_filter( 'ecs_shortcode_atts', 'ecsp_add_toolset_types_custom_fields' );

function ecsp_filter_by_toolset_types_custom_fields( $args, $atts, $meta_date_date, $meta_date_compare ) {
    if ( !ecsp_is_toolset_types_installed() ) {
        return $args;
    }
    foreach ( (array) ecsp_get_toolset_types_custom_fields() as $field ) {
        if ( ! empty( $atts[ $field['slug'] ] ) ) {
            if ( ! isset( $args['meta_query']['relation'] ) )
                $args['meta_query']['relation'] = 'AND';
            $args['meta_query'][] = array(
                'key' => $field['meta_key'],
                'value' => $atts[ $field['slug'] ],
                'compare' => '=',
            );
        }
    }
    return $args;
}
add_filter( 'ecs_get_events_args', 'ecsp_filter_by_toolset_types_custom_fields', 99, 4 );


// Advanced Custom Fields

function ecsp_is_acf_installed() {
    return class_exists( 'acf_field_group' );
}

function ecsp_get_acf_custom_fields() {
    if ( !ecsp_is_acf_installed() ) {
        return array();
    }
    $groups = apply_filters( 'acf/get_field_groups', array() );
    if ( is_array( $groups ) ) {
        foreach ( $groups as $group ) {
            $fields = apply_filters( 'acf/field_group/get_fields', array(), $group['id'] );
            return $fields;
        }
    }
}

function ecsp_add_acf_custom_fields( $atts ) {
    if ( !ecsp_is_acf_installed() ) {
        return $atts;
    }
    foreach ( (array) ecsp_get_acf_custom_fields() as $field ) {
        if ( !isset( $atts[ $field['name'] ]) ) {
            $atts[ $field['name'] ] = '';
        }
    }
    return $atts;
}
add_filter( 'ecs_shortcode_atts', 'ecsp_add_acf_custom_fields' );

function ecsp_filter_by_acf_custom_fields( $args, $atts, $meta_date_date, $meta_date_compare ) {
    if ( !ecsp_is_acf_installed() ) {
        return $args;
    }
    foreach ( (array) ecsp_get_acf_custom_fields() as $field ) {
        if ( ! empty( $atts[ $field['name'] ] ) ) {
            if ( ! isset( $args['meta_query']['relation'] ) )
                $args['meta_query']['relation'] = 'AND';
            $args['meta_query'][] = array(
                'key' => $field['name'],
                'value' => $atts[ $field['name'] ],
                'compare' => '=',
            );
        }
    }
    return $args;
}
add_filter( 'ecs_get_events_args', 'ecsp_filter_by_acf_custom_fields', 99, 4 );


// Additional Fields (with The Events Calendar Pro

function ecsp_add_custom_fields( $atts ) {
    if ( function_exists( 'tribe_get_option' ) ) {
        $custom_fields = tribe_get_option( 'custom-fields', false );
        if ( is_array( $custom_fields ) ) {
            foreach ( $custom_fields as $field ) {
                $atts[ $field['name'] ] = '';
            }
        }
    }
    return $atts;
}
add_filter( 'ecs_shortcode_atts', 'ecsp_add_custom_fields' );

function ecsp_filter_by_custom_fields( $args, $atts, $meta_date_date, $meta_date_compare ) {
    if ( function_exists( 'tribe_get_option' ) ) {
        $custom_fields = tribe_get_option( 'custom-fields', false );
        if ( is_array( $custom_fields ) ) {
            foreach ( $custom_fields as $field ) {
                if ( ! empty( $atts[ $field['name'] ] ) ) {
                    if ( ! isset( $args['meta_query']['relation'] ) )
                        $args['meta_query']['relation'] = 'AND';
                    $args['meta_query'][] = array(
                        'key' => $field['name'],
                        'value' => $atts[ $field['name'] ],
                        'compare' => '=',
                    );
                }
            }
        }
    }
    return $args;
}
add_filter( 'ecs_get_events_args', 'ecsp_filter_by_custom_fields', 99, 4 );

