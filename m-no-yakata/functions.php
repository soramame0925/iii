<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * CSS読み込み
 */
add_action('wp_enqueue_scripts', function(){

  // 基本スタイル
  wp_enqueue_style('mno-base', get_stylesheet_uri(), [], '1.0');

  // トップページ
  if ( is_page_template('page-top.php') ) {
    wp_enqueue_style(
      'mno-top',
      get_template_directory_uri() . '/assets/css/top-page.css',
      [],
      filemtime(get_template_directory() . '/assets/css/top-page.css')
    );
  }

  // Discoverページ
  if ( is_page_template('page-discover.php') ) {
  wp_enqueue_style(
    'mno-discover',
    get_template_directory_uri() . '/assets/css/discover.css',
    [],
    filemtime(get_template_directory() . '/assets/css/discover.css')
  );
}

  // 投稿ページ
  if ( is_single() ) {
    wp_enqueue_style(
      'mno-single',
      get_template_directory_uri() . '/assets/css/single.css',
      [],
      filemtime(get_template_directory() . '/assets/css/single.css')
    );
  }

  // 固定下部ナビ
  wp_enqueue_style(
    'mno-fix-nav',
    get_template_directory_uri() . '/assets/css/components/fix-nav.css',
    [],
    filemtime(get_template_directory() . '/assets/css/components/fix-nav.css')
  );

}, 20);

//ボトムシートJS
add_action('wp_enqueue_scripts', function() {
	wp_enqueue_script(
    'mno-no-zoom',
    get_template_directory_uri() . '/assets/js/no-zoom.js',
    [],
    filemtime(get_template_directory() . '/assets/js/no-zoom.js'),
    true
  );
  if (is_page_template('page-discover.php')) {
    wp_enqueue_script(
      'mno-discover',
      get_template_directory_uri() . '/assets/js/discover.js',
      [],
      filemtime(get_template_directory() . '/assets/js/discover.js'),
      true // フッターで読み込み
    );
  }
});



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


