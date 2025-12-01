<?php
/**
 * Plugin Name: MNO Post Manager
 * Description: Provides structured meta fields and front-end rendering for posts.
 * Version: 1.3.0
 * Author: OpenAI Assistant
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class MNO_Post_Manager {
    const META_PREFIX = '_mpm_';

    const VOICE_TYPE_OPTIONS = [
        'very_low'      => '非常に低い',
        'low'           => '低い',
        'mid'           => '中間（ナチュラル）',
        'high'          => '高い',
        'very_high'     => '非常に高い',
        'multiple'      => '複数人',
        'command_shift' => '命令口調に変化',
    ];

    const VOICE_TYPE_LEGACY_MAP = [
        'calm'     => 'low',
        'sweet'    => 'very_high',
        'mechanic' => 'mid',
        'sister'   => 'command_shift',
        'loli'     => 'high',
        'tsundere' => 'high',
        'boyish'   => 'mid',
        'mature'   => 'multiple',
        'sadistic' => 'high',
    ];

    const LEVEL_OPTIONS = [
        'soft'   => 'ソフト',
        'medium' => '中間',
        'hard'   => 'ハード',
    ];

    const PARENT_CATEGORY_SLUGS = [
        'circle'       => 'circle',
        'voice_actor'  => 'voice-actor',
        'illustration' => 'illustration',
    ];

    public static function init() {
        add_action( 'init', [ __CLASS__, 'remove_default_title_field' ], 11 );
        add_action( 'add_meta_boxes', [ __CLASS__, 'register_meta_boxes' ] );
        add_action( 'save_post', [ __CLASS__, 'save_post' ], 10, 2 );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_admin_assets' ] );
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_frontend_assets' ] );
    }

    public static function remove_default_title_field() {
        if ( ! is_admin() ) {
            return;
        }

        if ( post_type_exists( 'post' ) ) {
            remove_post_type_support( 'post', 'title' );
        }
    }

    public static function register_meta_boxes() {
        add_meta_box(
            'mno-post-manager',
            __( '投稿管理', 'mno-post-manager' ),
            [ __CLASS__, 'render_meta_box' ],
            'post',
            'normal',
            'high'
        );

        add_meta_box(
            'mno-post-manager-data',
            __( 'データ', 'mno-post-manager' ),
            [ __CLASS__, 'render_data_meta_box' ],
            'post',
            'normal',
            'high'
        );
    }

    public static function render_meta_box( $post ) {
        wp_nonce_field( 'mno_pm_save_post', 'mno_pm_nonce' );

        $values = self::get_post_values( $post->ID );

        include __DIR__ . '/partials/meta-box.php';
    }

    public static function render_data_meta_box( $post ) {
        $values       = self::get_post_values( $post->ID );
        $voice_types  = self::get_voice_type_options();
        $level_labels = self::get_level_options();

        include __DIR__ . '/partials/meta-box-data.php';
    }

    private static function get_post_values( $post_id ) {
        $defaults = [
            'custom_title'   => '',
            'gallery'        => [],
            'voice_sample'   => '',
            'circle_name'    => '',
            'voice_actors'   => [],
            'illustrators'   => [],
            'normal_price'   => '',
            'sale_price'     => '',
            'sale_end_date'  => '',
            'highlights'     => [],
            'track_list'     => [],
            'dialogue_block' => [
                'main_title'        => '',
                'image_id'          => '',
                'track_description' => '',
                'track_list'        => [],
                'subheadings'       => [],
                'dialogue_body'     => '',
            ],
            'release_date'   => '',
            'genre'          => '',
            'track_duration' => '',
            'buy_url'        => '',
            'data_bars'      => [],
            'data_voice'     => [],
            'data_level'     => '',
        ];

        $data = [];
        foreach ( $defaults as $key => $default ) {
            $meta_key = self::META_PREFIX . $key;
            $value    = get_post_meta( $post_id, $meta_key, true );
            if ( '' === $value || null === $value ) {
                $value = $default;
            }
            $data[ $key ] = $value;
        }

        $data['dialogue_block'] = is_array( $data['dialogue_block'] )
            ? wp_parse_args(
                $data['dialogue_block'],
                [
                    'main_title'        => '',
                    'image_id'          => '',
                    'track_description' => '',
                    'track_list'        => [],
                    'subheadings'       => [],
                    'dialogue_body'     => '',
                ]
            )
            : $defaults['dialogue_block'];
        $data['dialogue_block']['image_id'] = $data['dialogue_block']['image_id'] ? absint( $data['dialogue_block']['image_id'] ) : '';

        $data['gallery']      = is_array( $data['gallery'] ) ? array_map( 'intval', $data['gallery'] ) : [];
        $data['voice_actors'] = is_array( $data['voice_actors'] ) ? array_map( 'sanitize_text_field', $data['voice_actors'] ) : [];
        $data['illustrators'] = is_array( $data['illustrators'] ) ? array_map( 'sanitize_text_field', $data['illustrators'] ) : [];
        $data['highlights']   = is_array( $data['highlights'] ) ? array_map( 'sanitize_textarea_field', $data['highlights'] ) : [];

        $track_list = [];
        if ( is_array( $data['track_list'] ) ) {
            foreach ( $data['track_list'] as $track ) {
                if ( is_array( $track ) ) {
                    $track_name = isset( $track['track_name'] ) ? sanitize_textarea_field( $track['track_name'] ) : '';

                    $count = '';
                    if ( isset( $track['ejaculation_count'] ) && '' !== $track['ejaculation_count'] && null !== $track['ejaculation_count'] ) {
                        $count = (string) absint( $track['ejaculation_count'] );
                    }

                    $duration = isset( $track['track_duration'] ) ? sanitize_text_field( $track['track_duration'] ) : '';

                    $genres      = [];
                    $genres_raw  = isset( $track['genres'] ) ? $track['genres'] : [];
                    if ( is_array( $genres_raw ) ) {
                        foreach ( $genres_raw as $genre ) {
                            $genre = sanitize_text_field( $genre );
                            if ( '' !== $genre ) {
                                $genres[] = $genre;
                            }
                        }
                    } elseif ( is_string( $genres_raw ) && '' !== $genres_raw ) {
                        $split_genres = preg_split( '/[,、\n]+/u', $genres_raw );
                        if ( $split_genres ) {
                            foreach ( $split_genres as $genre ) {
                                $genre = sanitize_text_field( $genre );
                                if ( '' !== $genre ) {
                                    $genres[] = $genre;
                                }
                            }
                        }
                    }

                    if ( '' === $track_name && '' === $count && '' === $duration && empty( $genres ) ) {
                        continue;
                    }

                    $track_list[] = [
                        'track_name'         => $track_name,
                        'ejaculation_count'  => $count,
                        'track_duration'     => $duration,
                        'genres'             => array_values( $genres ),
                    ];
                    continue;
                }

                if ( is_string( $track ) ) {
                    $track_name = sanitize_textarea_field( $track );
                    if ( '' === $track_name ) {
                        continue;
                    }

                    $track_list[] = [
                        'track_name'         => $track_name,
                        'ejaculation_count'  => '',
                        'track_duration'     => '',
                        'genres'             => [],
                    ];
                }
            }
        }

        $data['track_list'] = $track_list;

        $data_bars = [];
        if ( is_array( $data['data_bars'] ) ) {
            foreach ( $data['data_bars'] as $entry ) {
                if ( ! is_array( $entry ) ) {
                    continue;
                }

                $label = isset( $entry['label'] ) ? sanitize_text_field( $entry['label'] ) : '';
                $track = isset( $entry['track'] ) ? sanitize_text_field( $entry['track'] ) : '';

                $count = '';
                if ( isset( $entry['count'] ) && '' !== $entry['count'] && null !== $entry['count'] ) {
                    $count = (string) absint( $entry['count'] );
                }

                if ( '' === $label && '' === $track && '' === $count ) {
                    continue;
                }

                $data_bars[] = [
                    'label' => $label,
                    'track' => $track,
                    'count' => $count,
                ];
            }
        }
        $data['data_bars'] = $data_bars;

        $voice_entries = [];
        if ( is_array( $data['data_voice'] ) ) {
            $options    = array_keys( self::get_voice_type_options() );
            $legacy_map = self::VOICE_TYPE_LEGACY_MAP;
            foreach ( $data['data_voice'] as $entry ) {
                if ( ! is_array( $entry ) ) {
                    continue;
                }

                $name  = isset( $entry['name'] ) ? sanitize_text_field( $entry['name'] ) : '';
                $types = [];
                if ( isset( $entry['types'] ) && is_array( $entry['types'] ) ) {
                    foreach ( $entry['types'] as $type_key ) {
                        $type_key = sanitize_key( $type_key );
                        if ( isset( $legacy_map[ $type_key ] ) ) {
                            $type_key = $legacy_map[ $type_key ];
                        }
                        if ( in_array( $type_key, $options, true ) ) {
                            $types[] = $type_key;
                        }
                    }
                }

                if ( '' === $name && empty( $types ) ) {
                    continue;
                }

                $voice_entries[] = [
                    'name'  => $name,
                    'types' => array_values( array_unique( $types ) ),
                ];
            }
        }
        $data['data_voice'] = $voice_entries;

        $level = isset( $data['data_level'] ) ? sanitize_key( $data['data_level'] ) : '';
        $data['data_level'] = array_key_exists( $level, self::get_level_options() ) ? $level : '';

        $data['dialogue_block'] = self::sanitize_dialogue_block( $data['dialogue_block'] );

        $data['post_id'] = $post_id;

        $parent_ids   = [];
        $category_map = [
            'circle_terms'       => self::PARENT_CATEGORY_SLUGS['circle'],
            'voice_actor_terms'  => self::PARENT_CATEGORY_SLUGS['voice_actor'],
            'illustrator_terms'  => self::PARENT_CATEGORY_SLUGS['illustration'],
        ];

        foreach ( $category_map as $key => $slug ) {
            $parent_ids[ $key ] = self::ensure_parent_category( $slug );
            $data[ $key ]       = [];
        }

        $assigned_categories = wp_get_post_terms( $post_id, 'category' );
        foreach ( $assigned_categories as $term ) {
            foreach ( $category_map as $key => $slug ) {
                if ( $parent_ids[ $key ] && (int) $term->parent === (int) $parent_ids[ $key ] ) {
                    $data[ $key ][] = $term;
                }
            }
        }

        $data['genre_terms'] = wp_get_post_terms( $post_id, 'post_tag', [ 'orderby' => 'name', 'order' => 'ASC' ] );

        return wp_parse_args( $data, $defaults );
    }

    public static function save_post( $post_id, $post ) {
        if ( ! isset( $_POST['mno_pm_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mno_pm_nonce'] ) ), 'mno_pm_save_post' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( 'post' !== $post->post_type ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $custom_title = '';
        if ( isset( $_POST['mno_pm_custom_title'] ) ) {
            $custom_title = sanitize_text_field( wp_unslash( $_POST['mno_pm_custom_title'] ) );
        }
        update_post_meta( $post_id, self::META_PREFIX . 'custom_title', $custom_title );

        if ( '' !== $custom_title && $custom_title !== $post->post_title ) {
            remove_action( 'save_post', [ __CLASS__, 'save_post' ], 10 );
            wp_update_post(
                [
                    'ID'         => $post_id,
                    'post_title' => $custom_title,
                ]
            );
            add_action( 'save_post', [ __CLASS__, 'save_post' ], 10, 2 );
        }

        $fields = [
            'voice_sample'   => [ __CLASS__, 'sanitize_voice_sample' ],
            'circle_name'    => 'sanitize_text_field',
            'normal_price'   => 'sanitize_text_field',
            'sale_price'     => 'sanitize_text_field',
            'sale_end_date'  => 'sanitize_text_field',
            'release_date'   => 'sanitize_text_field',
            'genre'          => 'sanitize_text_field',
            'track_duration' => 'sanitize_text_field',
            'buy_url'        => 'esc_url_raw',
        ];

        foreach ( $fields as $key => $sanitize_callback ) {
            $raw = isset( $_POST[ 'mno_pm_' . $key ] ) ? wp_unslash( $_POST[ 'mno_pm_' . $key ] ) : '';
            $value = '';
            if ( '' !== $raw ) {
                $value = call_user_func( $sanitize_callback, $raw );
            }
            update_post_meta( $post_id, self::META_PREFIX . $key, $value );
        }

        $gallery_ids = [];
        if ( isset( $_POST['mno_pm_gallery'] ) && is_array( $_POST['mno_pm_gallery'] ) ) {
            $gallery_ids = array_filter( array_map( 'intval', wp_unslash( $_POST['mno_pm_gallery'] ) ) );
        }
        update_post_meta( $post_id, self::META_PREFIX . 'gallery', $gallery_ids );

        $voice_actors = [];
        if ( isset( $_POST['mno_pm_voice_actors'] ) && is_array( $_POST['mno_pm_voice_actors'] ) ) {
            $voice_actors = array_values( array_filter( array_map( 'sanitize_text_field', wp_unslash( $_POST['mno_pm_voice_actors'] ) ) ) );
        }
        update_post_meta( $post_id, self::META_PREFIX . 'voice_actors', $voice_actors );

        $illustrators = [];
        if ( isset( $_POST['mno_pm_illustrators'] ) && is_array( $_POST['mno_pm_illustrators'] ) ) {
            $illustrators = array_values( array_filter( array_map( 'sanitize_text_field', wp_unslash( $_POST['mno_pm_illustrators'] ) ) ) );
        }
        update_post_meta( $post_id, self::META_PREFIX . 'illustrators', $illustrators );

        $highlights = [];
        if ( isset( $_POST['mno_pm_highlights'] ) && is_array( $_POST['mno_pm_highlights'] ) ) {
            $highlights = array_values( array_filter( array_map( 'sanitize_textarea_field', wp_unslash( $_POST['mno_pm_highlights'] ) ) ) );
        }
        update_post_meta( $post_id, self::META_PREFIX . 'highlights', $highlights );

        $track_list = [];
        if ( isset( $_POST['mno_pm_track_list'] ) && is_array( $_POST['mno_pm_track_list'] ) ) {
            foreach ( wp_unslash( $_POST['mno_pm_track_list'] ) as $track ) {
                if ( is_array( $track ) ) {
                    $track_name = isset( $track['track_name'] ) ? sanitize_textarea_field( $track['track_name'] ) : '';

                    $count = '';
                    if ( isset( $track['ejaculation_count'] ) && '' !== $track['ejaculation_count'] && null !== $track['ejaculation_count'] ) {
                        $count = absint( $track['ejaculation_count'] );
                    }

                    $duration = isset( $track['track_duration'] ) ? sanitize_text_field( $track['track_duration'] ) : '';

                    $genres     = [];
                    $genres_raw = isset( $track['genres'] ) ? $track['genres'] : '';
                    if ( is_array( $genres_raw ) ) {
                        foreach ( $genres_raw as $genre ) {
                            $genre = sanitize_text_field( $genre );
                            if ( '' !== $genre ) {
                                $genres[] = $genre;
                            }
                        }
                    } elseif ( is_string( $genres_raw ) && '' !== $genres_raw ) {
                        $split_genres = preg_split( '/[,、\n]+/u', $genres_raw );
                        if ( $split_genres ) {
                            foreach ( $split_genres as $genre ) {
                                $genre = sanitize_text_field( $genre );
                                if ( '' !== $genre ) {
                                    $genres[] = $genre;
                                }
                            }
                        }
                    }

                    if ( '' === $track_name && '' === $count && '' === $duration && empty( $genres ) ) {
                        continue;
                    }

                    $track_list[] = [
                        'track_name'        => $track_name,
                        'ejaculation_count' => '' === $count ? '' : $count,
                        'track_duration'    => $duration,
                        'genres'            => array_values( $genres ),
                    ];
                    continue;
                }

                if ( is_string( $track ) ) {
                    $track_name = sanitize_textarea_field( $track );
                    if ( '' === $track_name ) {
                        continue;
                    }

                    $track_list[] = [
                        'track_name'        => $track_name,
                        'ejaculation_count' => '',
                        'track_duration'    => '',
                        'genres'            => [],
                    ];
                }
            }
        }
        update_post_meta( $post_id, self::META_PREFIX . 'track_list', $track_list );

        $dialogue_block = [];
        if ( isset( $_POST['mno_pm_dialogue_block'] ) && is_array( $_POST['mno_pm_dialogue_block'] ) ) {
            $dialogue_block = wp_unslash( $_POST['mno_pm_dialogue_block'] );
        }
        update_post_meta(
            $post_id,
            self::META_PREFIX . 'dialogue_block',
            self::sanitize_dialogue_block( $dialogue_block, $gallery_ids )
        );
        delete_post_meta( $post_id, self::META_PREFIX . 'quote_blocks' );
        delete_post_meta( $post_id, self::META_PREFIX . 'sample_lines' );

        $data_bars = [];
        if ( isset( $_POST['mno_pm_data_bars'] ) && is_array( $_POST['mno_pm_data_bars'] ) ) {
            foreach ( wp_unslash( $_POST['mno_pm_data_bars'] ) as $entry ) {
                if ( ! is_array( $entry ) ) {
                    continue;
                }

                $label = isset( $entry['label'] ) ? sanitize_text_field( $entry['label'] ) : '';
                $track = isset( $entry['track'] ) ? sanitize_text_field( $entry['track'] ) : '';

                $count = '';
                if ( isset( $entry['count'] ) && '' !== $entry['count'] && null !== $entry['count'] ) {
                    $count = absint( $entry['count'] );
                }

                if ( '' === $label && '' === $track && '' === $count ) {
                    continue;
                }

                $data_bars[] = [
                    'label' => $label,
                    'track' => $track,
                    'count' => '' === $count ? '' : $count,
                ];
            }
        }
        update_post_meta( $post_id, self::META_PREFIX . 'data_bars', $data_bars );

        $voice_entries = [];
        if ( isset( $_POST['mno_pm_data_voice'] ) && is_array( $_POST['mno_pm_data_voice'] ) ) {
            $allowed    = array_keys( self::get_voice_type_options() );
            $legacy_map = self::VOICE_TYPE_LEGACY_MAP;
            foreach ( wp_unslash( $_POST['mno_pm_data_voice'] ) as $entry ) {
                if ( ! is_array( $entry ) ) {
                    continue;
                }

                $name  = isset( $entry['name'] ) ? sanitize_text_field( $entry['name'] ) : '';
                $types = [];
                if ( isset( $entry['types'] ) && is_array( $entry['types'] ) ) {
                    foreach ( $entry['types'] as $type_key ) {
                        $type_key = sanitize_key( $type_key );
                        if ( isset( $legacy_map[ $type_key ] ) ) {
                            $type_key = $legacy_map[ $type_key ];
                        }
                        if ( in_array( $type_key, $allowed, true ) ) {
                            $types[] = $type_key;
                        }
                    }
                }

                if ( '' === $name && empty( $types ) ) {
                    continue;
                }

                $voice_entries[] = [
                    'name'  => $name,
                    'types' => array_values( array_unique( $types ) ),
                ];
            }
        }
        update_post_meta( $post_id, self::META_PREFIX . 'data_voice', $voice_entries );

        $level = '';
        if ( isset( $_POST['mno_pm_data_level'] ) ) {
            $level = sanitize_key( wp_unslash( $_POST['mno_pm_data_level'] ) );
        }
        $level_options = array_keys( self::get_level_options() );
        update_post_meta(
            $post_id,
            self::META_PREFIX . 'data_level',
            in_array( $level, $level_options, true ) ? $level : ''
        );

        $sale_price    = get_post_meta( $post_id, self::META_PREFIX . 'sale_price', true );
        $sale_end_date = get_post_meta( $post_id, self::META_PREFIX . 'sale_end_date', true );

        if ( $sale_price && $sale_end_date ) {
            $today       = current_time( 'Y-m-d' );
            $today_ts    = strtotime( $today );
            $sale_end_ts = strtotime( $sale_end_date );

            if ( $today_ts && $sale_end_ts && $today_ts > $sale_end_ts ) {
                update_post_meta( $post_id, self::META_PREFIX . 'sale_price', '' );
            }
        }

         self::sync_taxonomies( $post_id );
    }

    public static function get_voice_sample_allowed_tags() {
        $allowed = wp_kses_allowed_html( 'post' );

        $allowed['iframe'] = [
            'src'             => true,
            'width'           => true,
            'height'          => true,
            'frameborder'     => true,
            'allow'           => true,
            'allowfullscreen' => true,
            'loading'         => true,
            'title'           => true,
            'referrerpolicy'  => true,
        ];

        return $allowed;
    }

    private static function sanitize_voice_sample( $value ) {
        return wp_kses( $value, self::get_voice_sample_allowed_tags() );
    }

    private static function sanitize_dialogue_block( $value, $allowed_gallery_ids = [] ) {
        $block  = is_array( $value ) ? $value : [];
        $allowed_gallery_ids = array_filter( array_map( 'absint', (array) $allowed_gallery_ids ) );

        $main_title        = isset( $block['main_title'] ) ? sanitize_textarea_field( $block['main_title'] ) : '';
        $image_id          = isset( $block['image_id'] ) ? absint( $block['image_id'] ) : 0;
        $track_description = isset( $block['track_description'] ) ? sanitize_textarea_field( $block['track_description'] ) : '';

        if ( $image_id && $allowed_gallery_ids && ! in_array( $image_id, $allowed_gallery_ids, true ) ) {
            $image_id = 0;
        }

        $track_list = [];
        if ( isset( $block['track_list'] ) && is_array( $block['track_list'] ) ) {
            foreach ( $block['track_list'] as $entry ) {
                $entry = sanitize_textarea_field( $entry );
                if ( '' !== $entry ) {
                    $track_list[] = $entry;
                }
            }
        }

        $subheadings = [];
        if ( isset( $block['subheadings'] ) && is_array( $block['subheadings'] ) ) {
            foreach ( $block['subheadings'] as $entry ) {
                $entry = sanitize_textarea_field( $entry );
                if ( '' !== $entry ) {
                    $subheadings[] = $entry;
                }
            }
        }

        $dialogue_body = isset( $block['dialogue_body'] ) ? sanitize_textarea_field( $block['dialogue_body'] ) : '';

        return [
            'main_title'        => $main_title,
            'image_id'          => $image_id ? $image_id : '',
            'track_description' => $track_description,
            'track_list'        => $track_list,
            'subheadings'       => $subheadings,
            'dialogue_body'     => $dialogue_body,
        ];
    }

    public static function get_voice_type_options() {
        return self::VOICE_TYPE_OPTIONS;
    }

    public static function get_level_options() {
        return self::LEVEL_OPTIONS;
    }

    private static function normalize_list_values( $value ) {
        $results = [];

        $maybe_add = static function ( $entry ) use ( &$results ) {
            if ( ! is_string( $entry ) ) {
                return;
            }

            $pieces = preg_split( '/[,、\n\r\/]+/u', $entry );
            if ( ! $pieces ) {
                return;
            }

            foreach ( $pieces as $piece ) {
                $piece = trim( wp_strip_all_tags( $piece ) );
                if ( '' !== $piece ) {
                    $results[] = $piece;
                }
            }
        };

        if ( is_array( $value ) ) {
            foreach ( $value as $entry ) {
                if ( is_array( $entry ) ) {
                    foreach ( $entry as $inner ) {
                        $maybe_add( $inner );
                    }
                    continue;
                }

                $maybe_add( $entry );
            }
        } elseif ( is_string( $value ) ) {
            $maybe_add( $value );
        }

        return array_values( array_unique( $results ) );
    }

    private static function ensure_parent_category( $slug ) {
        $parent = get_term_by( 'slug', $slug, 'category' );
        if ( $parent && ! is_wp_error( $parent ) ) {
            return (int) $parent->term_id;
        }

        $created = wp_insert_term( ucwords( str_replace( '-', ' ', $slug ) ), 'category', [ 'slug' => $slug ] );
        if ( is_wp_error( $created ) ) {
            return 0;
        }

        return (int) $created['term_id'];
    }

    private static function ensure_child_term( $name, $parent_slug ) {
        $name = trim( wp_strip_all_tags( (string) $name ) );
        if ( '' === $name ) {
            return 0;
        }

        $parent_id = self::ensure_parent_category( $parent_slug );
        if ( ! $parent_id ) {
            return 0;
        }

        $existing = get_terms(
            [
                'taxonomy'   => 'category',
                'hide_empty' => false,
                'name'       => $name,
                'parent'     => $parent_id,
                'number'     => 1,
            ]
        );

        if ( $existing && ! is_wp_error( $existing ) ) {
            return (int) $existing[0]->term_id;
        }

        $created = wp_insert_term(
            $name,
            'category',
            [
                'parent' => $parent_id,
            ]
        );

        if ( is_wp_error( $created ) ) {
            return 0;
        }

        return (int) $created['term_id'];
    }

    private static function sync_category_terms( $post_id, $circle_name, $voice_actors, $illustrators ) {
        $circle_term_id  = $circle_name ? self::ensure_child_term( $circle_name, self::PARENT_CATEGORY_SLUGS['circle'] ) : 0;
        $voice_term_ids  = [];
        $image_term_ids  = [];

        foreach ( $voice_actors as $actor ) {
            $term_id = self::ensure_child_term( $actor, self::PARENT_CATEGORY_SLUGS['voice_actor'] );
            if ( $term_id ) {
                $voice_term_ids[] = $term_id;
            }
        }

        foreach ( $illustrators as $illustrator ) {
            $term_id = self::ensure_child_term( $illustrator, self::PARENT_CATEGORY_SLUGS['illustration'] );
            if ( $term_id ) {
                $image_term_ids[] = $term_id;
            }
        }

        $target_parents = array_map( [ __CLASS__, 'ensure_parent_category' ], self::PARENT_CATEGORY_SLUGS );
        $target_parents = array_filter( $target_parents );

        $existing_terms = wp_get_post_terms( $post_id, 'category' );
        $preserve_ids   = [];

        foreach ( $existing_terms as $term ) {
            if ( in_array( (int) $term->term_id, $target_parents, true ) ) {
                continue;
            }

            if ( in_array( (int) $term->parent, $target_parents, true ) ) {
                continue;
            }

            $preserve_ids[] = (int) $term->term_id;
        }

        $new_ids = array_filter( array_merge( [ $circle_term_id ], $voice_term_ids, $image_term_ids ) );

        if ( empty( $new_ids ) && empty( $preserve_ids ) ) {
            return;
        }

        wp_set_post_terms( $post_id, array_values( array_unique( array_merge( $preserve_ids, $new_ids ) ) ), 'category' );
    }

    private static function sync_genre_tags( $post_id, $genres ) {
        $tag_ids = [];

        foreach ( $genres as $genre ) {
            $genre = trim( wp_strip_all_tags( (string) $genre ) );
            if ( '' === $genre ) {
                continue;
            }

            $existing = get_term_by( 'name', $genre, 'post_tag' );
            if ( $existing && ! is_wp_error( $existing ) ) {
                $tag_ids[] = (int) $existing->term_id;
                continue;
            }

            $created = wp_insert_term( $genre, 'post_tag' );
            if ( is_wp_error( $created ) ) {
                continue;
            }

            $tag_ids[] = (int) $created['term_id'];
        }

        wp_set_post_terms( $post_id, array_values( array_unique( $tag_ids ) ), 'post_tag' );
    }

    private static function sync_taxonomies( $post_id ) {
        $circle_name    = get_post_meta( $post_id, self::META_PREFIX . 'circle_name', true );
        $voice_actors   = get_post_meta( $post_id, self::META_PREFIX . 'voice_actors', true );
        $illustrators   = get_post_meta( $post_id, self::META_PREFIX . 'illustrators', true );
        $genre_field    = get_post_meta( $post_id, self::META_PREFIX . 'genre', true );

        if ( function_exists( 'get_field' ) ) {
            $acf_circle      = get_field( 'circle_name', $post_id );
            $acf_voice       = get_field( 'voice_actors', $post_id );
            $acf_illustrator = get_field( 'illustrators', $post_id );
            $acf_genres      = get_field( 'genres', $post_id );

            if ( $acf_circle ) {
                $circle_name = $acf_circle;
            }

            if ( $acf_voice ) {
                $voice_actors = $acf_voice;
            }

            if ( $acf_illustrator ) {
                $illustrators = $acf_illustrator;
            }

            if ( $acf_genres ) {
                $genre_field = $acf_genres;
            }
        }

        $voice_list        = self::normalize_list_values( $voice_actors );
        $illustrator_list  = self::normalize_list_values( $illustrators );
        $genre_list        = self::normalize_list_values( $genre_field );
        $circle_name_clean = '';

        if ( is_array( $circle_name ) ) {
            $circle_candidates = self::normalize_list_values( $circle_name );
            $circle_name_clean = $circle_candidates ? $circle_candidates[0] : '';
        } else {
            $circle_name_clean = trim( wp_strip_all_tags( (string) $circle_name ) );
        }

        self::sync_category_terms( $post_id, $circle_name_clean, $voice_list, $illustrator_list );
        self::sync_genre_tags( $post_id, $genre_list );
    }

    public static function enqueue_admin_assets( $hook ) {
        if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_style( 'mno-pm-admin', plugin_dir_url( __FILE__ ) . 'assets/admin.css', [], '1.2.0' );
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_script( 'mno-pm-admin', plugin_dir_url( __FILE__ ) . 'assets/admin.js', [ 'jquery', 'jquery-ui-sortable' ], '1.2.0', true );
    }

    public static function enqueue_frontend_assets() {
        if ( ! is_single() ) {
            return;
        }

        $script_version = file_exists( __DIR__ . '/assets/frontend.js' ) ? filemtime( __DIR__ . '/assets/frontend.js' ) : '1.3.0';
        wp_enqueue_script( 'mno-pm-frontend', plugin_dir_url( __FILE__ ) . 'assets/frontend.js', [], $script_version, true );
        wp_localize_script(
            'mno-pm-frontend',
            'mnoPmSlider',
            [
                'i18n' => [
                    'next'  => __( 'Next', 'mno-post-manager' ),
                    'prev'  => __( 'Previous', 'mno-post-manager' ),
                    'slide' => __( 'Go to slide %d', 'mno-post-manager' ),
                ],
            ]
        );
    }

    public static function get_post_data( $post_id = null ) {
        $post_id = $post_id ?: get_the_ID();
        if ( ! $post_id ) {
            return [];
        }

        return self::get_post_values( $post_id );
    }
}

MNO_Post_Manager::init();

function mno_pm_render_single_template( $post_id = null ) {
    $post_id = $post_id ?: get_the_ID();
    if ( ! $post_id ) {
        return '';
    }

    $data = MNO_Post_Manager::get_post_data( $post_id );

    ob_start();
    include __DIR__ . '/partials/frontend-template.php';
    return ob_get_clean();
}