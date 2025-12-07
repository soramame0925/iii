<?php

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'wp_enqueue_scripts', function() {
  $parent_style_handle = 'swell-style';
  $parent_theme        = wp_get_theme()->parent();

  wp_enqueue_style(
    $parent_style_handle,
    get_template_directory_uri() . '/style.css',
    [],
    $parent_theme ? $parent_theme->get( 'Version' ) : wp_get_theme()->get( 'Version' )
  );

  wp_enqueue_style(
    'mno-child-style',
    get_stylesheet_uri(),
    [ $parent_style_handle ],
    wp_get_theme()->get( 'Version' )
  );

  // トップページ
  if ( is_page_template( 'page-top.php' ) ) {
    wp_enqueue_style(
      'mno-top',
      get_template_directory_uri() . '/assets/css/top-page.css',
      [ $parent_style_handle ],
      filemtime( get_template_directory() . '/assets/css/top-page.css' )
    );
  }

  // Discoverページ
  if ( is_page_template( 'page-discover.php' ) ) {
    wp_enqueue_style(
      'mno-discover',
      get_template_directory_uri() . '/assets/css/discover.css',
      [ $parent_style_handle ],
      filemtime( get_template_directory() . '/assets/css/discover.css' )
    );
  }

  // 投稿ページ
  if ( is_single() ) {
    wp_enqueue_style(
      'mno-single',
      get_template_directory_uri() . '/assets/css/single.css',
      [ $parent_style_handle ],
      filemtime( get_template_directory() . '/assets/css/single.css' )
    );
  }

  // 固定下部ナビ
  wp_enqueue_style(
    'mno-fix-nav',
    get_template_directory_uri() . '/assets/css/components/fix-nav.css',
    [ $parent_style_handle ],
    filemtime( get_template_directory() . '/assets/css/components/fix-nav.css' )
  );

  wp_enqueue_script(
    'mno-no-zoom',
    get_template_directory_uri() . '/assets/js/no-zoom.js',
    [],
    filemtime( get_template_directory() . '/assets/js/no-zoom.js' ),
    true
  );

  if ( is_page_template( 'page-discover.php' ) ) {
    wp_enqueue_script(
      'mno-discover',
      get_template_directory_uri() . '/assets/js/discover.js',
      [],
      filemtime( get_template_directory() . '/assets/js/discover.js' ),
      true // フッターで読み込み
    );
  }
}, 20 );

/**
 * ショート動画専用のカスタム投稿タイプを追加
 */
add_action('init', function() {
  register_post_type('short_videos', [
    'label' => 'ショート動画',
    'public' => true,
    'menu_position' => 5,
    'menu_icon' => 'dashicons-video-alt3',
    'supports' => ['title', 'thumbnail'],
    'has_archive' => true,
    'show_in_rest' => true,
    'rewrite' => ['slug' => 'shorts'],
  ]);
});
