<?php

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'wp_enqueue_scripts', function() {
  $parent_style_handle = 'swell-style';
  $parent_theme        = wp_get_theme()->parent();
  $child_theme         = wp_get_theme();
  $child_style_handle  = 'mno-child-style';

  $assets_uri  = get_stylesheet_directory_uri() . '/assets';
  $assets_path = get_stylesheet_directory() . '/assets';

  // 親テーマのスタイル（SWELL）を先に読み込む
  wp_enqueue_style(
    $parent_style_handle,
    get_template_directory_uri() . '/style.css',
    [],
    $parent_theme ? $parent_theme->get( 'Version' ) : $child_theme->get( 'Version' )
  );

  // 子テーマの基本スタイル
  wp_enqueue_style(
    $child_style_handle,
    get_stylesheet_uri(),
    [ $parent_style_handle ],
    $child_theme->get( 'Version' )
  );

  // 共有スタイル
  wp_enqueue_style(
    'mno-fix-nav',
    $assets_uri . '/css/components/fix-nav.css',
    [ $child_style_handle ],
    filemtime( $assets_path . '/css/components/fix-nav.css' )
  );

  // ページ毎のスタイル
  if ( is_page_template( 'page-top.php' ) ) {
    wp_enqueue_style(
      'mno-top',
      $assets_uri . '/css/top-page.css',
      [ $child_style_handle ],
      filemtime( $assets_path . '/css/top-page.css' )
    );
  }

  if ( is_page_template( 'page-discover.php' ) ) {
    wp_enqueue_style(
      'mno-discover',
      $assets_uri . '/css/discover.css',
      [ $child_style_handle ],
      filemtime( $assets_path . '/css/discover.css' )
    );
  }

  if ( is_single() ) {
    wp_enqueue_style(
      'mno-single',
      $assets_uri . '/css/single.css',
      [ $child_style_handle ],
      filemtime( $assets_path . '/css/single.css' )
    );
  }

  // 共通スクリプト
  wp_enqueue_script(
    'mno-no-zoom',
    $assets_uri . '/js/no-zoom.js',
    [],
    filemtime( $assets_path . '/js/no-zoom.js' ),
    true
  );

  // Discoverページ用スクリプト
  if ( is_page_template( 'page-discover.php' ) ) {
    wp_enqueue_script(
      'mno-discover',
      $assets_uri . '/js/discover.js',
      [],
      filemtime( $assets_path . '/js/discover.js' ),
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
