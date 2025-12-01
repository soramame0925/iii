<?php get_header(); ?>

<main class="mno-single-page">
  <?php if (have_posts()) : while (have_posts()) : the_post();
    $post_id       = get_the_ID();
    $custom_title  = get_post_meta( $post_id, '_mpm_custom_title', true );
    $display_title = '' !== $custom_title ? $custom_title : get_the_title();

    $post_data   = class_exists( 'MNO_Post_Manager' ) ? MNO_Post_Manager::get_post_data( $post_id ) : [];
    $gallery     = isset( $post_data['gallery'] ) && is_array( $post_data['gallery'] ) ? $post_data['gallery'] : [];
    $voice_sample = isset( $post_data['voice_sample'] ) ? $post_data['voice_sample'] : '';
    $highlights  = isset( $post_data['highlights'] ) && is_array( $post_data['highlights'] ) ? array_filter( $post_data['highlights'] ) : [];

    $circle_terms = isset( $post_data['circle_terms'] ) ? $post_data['circle_terms'] : [];
    $voice_terms  = isset( $post_data['voice_actor_terms'] ) ? $post_data['voice_actor_terms'] : [];
    $artist_terms = isset( $post_data['illustrator_terms'] ) ? $post_data['illustrator_terms'] : [];
    $genre_terms  = isset( $post_data['genre_terms'] ) ? $post_data['genre_terms'] : [];

    $normal_price   = isset( $post_data['normal_price'] ) ? trim( (string) $post_data['normal_price'] ) : '';
    $sale_price     = isset( $post_data['sale_price'] ) ? trim( (string) $post_data['sale_price'] ) : '';
    $sale_end_date  = isset( $post_data['sale_end_date'] ) ? $post_data['sale_end_date'] : '';
    $release_date   = isset( $post_data['release_date'] ) ? $post_data['release_date'] : '';
    $track_duration = isset( $post_data['track_duration'] ) ? $post_data['track_duration'] : '';
    $buy_url        = isset( $post_data['buy_url'] ) ? $post_data['buy_url'] : '';

    $today            = current_time( 'Y-m-d' );
    $today_ts         = strtotime( $today );
    $sale_end_ts      = $sale_end_date ? strtotime( $sale_end_date ) : false;
    $sale_active      = $sale_price && $sale_end_ts && $today_ts && $today_ts <= $sale_end_ts;
    $sale_end_display = $sale_active && $sale_end_ts ? wp_date( 'Y年n月j日', $sale_end_ts ) : '';

    $price_display = '&mdash;';
    if ( $sale_active ) {
      $price_display = esc_html( $sale_price );
      if ( $normal_price ) {
        $price_display .= ' / ' . esc_html__( '通常', 'mno' ) . ' ' . esc_html( $normal_price );
      }
    } elseif ( $normal_price ) {
      $price_display = esc_html( $normal_price );
    }

    $release_date_display = '&mdash;';
    if ( $release_date ) {
      $release_date_object = DateTime::createFromFormat( 'Y-m-d', $release_date );
      if ( $release_date_object instanceof DateTime ) {
        $release_date_display = wp_date( 'Y年n月j日', $release_date_object->getTimestamp() );
      } else {
        $release_timestamp = strtotime( $release_date );
        $release_date_display = false !== $release_timestamp ? wp_date( 'Y年n月j日', $release_timestamp ) : $release_date;
      }
    }

    $render_terms = function( $terms ) {
      if ( empty( $terms ) ) {
        return '&mdash;';
      }

      $links = [];
      foreach ( $terms as $term ) {
        if ( ! $term instanceof WP_Term || 'uncategorized' === $term->slug ) {
          continue;
        }

        $link = get_term_link( $term );
        if ( ! is_wp_error( $link ) ) {
          $links[] = '<a href="' . esc_url( $link ) . '">' . esc_html( $term->name ) . '</a>';
        } else {
          $links[] = esc_html( $term->name );
        }
      }

      return $links ? implode( ' / ', $links ) : '&mdash;';
    };

    $tags_output   = $render_terms( $genre_terms );
    $circle_output = $render_terms( $circle_terms );
    $voice_output  = $render_terms( $voice_terms );
    $artist_output = $render_terms( $artist_terms );

    $voice_sample_markup = '';
    if ( $voice_sample ) {
      if ( filter_var( $voice_sample, FILTER_VALIDATE_URL ) ) {
        $embed = wp_oembed_get( $voice_sample );
        if ( ! $embed && preg_match( '/\.mp3$|\.wav$|\.m4a$/i', $voice_sample ) ) {
          $embed = '<audio controls preload="none" class="mno-pm-voice-sample__audio"><source src="' . esc_url( $voice_sample ) . '" /></audio>';
        }
        if ( ! $embed && strpos( $voice_sample, 'chobit' ) !== false ) {
          $embed = '<iframe class="mno-pm-voice-sample__iframe" src="' . esc_url( $voice_sample ) . '" loading="lazy" allow="autoplay"></iframe>';
        }
        if ( ! $embed ) {
          $embed = '<a class="mno-pm-voice-sample__link" href="' . esc_url( $voice_sample ) . '" target="_blank" rel="noopener">' . esc_html__( '音声サンプルを開く', 'mno' ) . '</a>';
        }
        $voice_sample_markup = $embed;
      } else {
        $allowed_tags             = wp_kses_allowed_html( 'post' );
        $allowed_tags['iframe']   = [
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
        $voice_sample_markup = wp_kses( $voice_sample, $allowed_tags );
      }
    }

    $button_label = __( 'DLsiteで購入', 'mno' );
    $button_price = $sale_active && $sale_price ? $sale_price : $normal_price;

    if ( $button_price ) {
      $button_label = sprintf( '%s（%s）', $button_label, $button_price );
    }

    if ( $buy_url ) {
      $buy_button = '<a class="mno-buy-button" href="' . esc_url( $buy_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $button_label ) . '</a>';
    } else {
      $buy_button = '<span class="mno-buy-button" aria-disabled="true">' . esc_html( $button_label ) . '</span>';
    }
  ?>
    <article <?php post_class( 'mno-single-article' ); ?>>
      <section class="mno-single mno-single--title">
        <p class="mno-single__section-title"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></p>
        <h1 class="mno-single-title"><?php echo esc_html( $display_title ); ?></h1>
      </section>

      <?php if ( $gallery ) : ?>
        <section class="mno-single mno-single--gallery">
          <p class="mno-single__section-title"><?php esc_html_e( 'Gallery', 'mno' ); ?></p>
          <div class="mno-single__gallery">
            <div class="mno-gallery-frame">
              <div class="mno-pm-slider mno-gallery" data-mno-pm-slider data-mno-gallery-slider>
                <div class="mno-pm-slider__track mno-gallery-track">
                  <?php foreach ( $gallery as $image_id ) :
                    $image_html = wp_get_attachment_image( $image_id, 'large', false, [
                      'class'   => 'mno-pm-slider__image',
                      'loading' => 'lazy',
                      'sizes'   => '100vw',
                    ] );

                    if ( ! $image_html ) {
                      continue;
                    }
                    ?>
                    <div class="mno-pm-slider__slide mno-gallery-slide">
                      <figure class="mno-gallery-media">
                        <?php echo $image_html; ?>
                      </figure>
                    </div>
                  <?php endforeach; ?>
                </div>

                <div class="mno-gallery-controls">
                  <button
                    type="button"
                    class="mno-pm-slider__nav mno-gallery-arrow mno-gallery-arrow--left mno-pm-slider__nav--prev"
                    aria-label="<?php esc_attr_e( 'Previous', 'mno-post-manager' ); ?>"
                  >&#10094;</button>
                  <button
                    type="button"
                    class="mno-pm-slider__nav mno-gallery-arrow mno-gallery-arrow--right mno-pm-slider__nav--next"
                    aria-label="<?php esc_attr_e( 'Next', 'mno-post-manager' ); ?>"
                  >&#10095;</button>
                </div>

                <div class="mno-pm-slider__dots mno-gallery-dots" role="tablist" aria-label="<?php esc_attr_e( 'Gallery navigation', 'mno-post-manager' ); ?>"></div>
              </div>
            </div>
          </div>
        </section>
      <?php endif; ?>

      <section class="mno-single mno-single--info">
        <p class="mno-single__section-title"><?php esc_html_e( 'Basic info', 'mno' ); ?></p>
        <ul class="mno-single__meta">
          <li class="mno-single__meta-row">
            <span class="mno-single__meta-label"><?php esc_html_e( 'サークル', 'mno' ); ?></span>
            <span class="mno-single__meta-value"><?php echo wp_kses_post( $circle_output ); ?></span>
          </li>
          <li class="mno-single__meta-row">
            <span class="mno-single__meta-label"><?php esc_html_e( '声優', 'mno' ); ?></span>
            <span class="mno-single__meta-value"><?php echo wp_kses_post( $voice_output ); ?></span>
          </li>
          <li class="mno-single__meta-row">
            <span class="mno-single__meta-label"><?php esc_html_e( 'イラスト', 'mno' ); ?></span>
            <span class="mno-single__meta-value"><?php echo wp_kses_post( $artist_output ); ?></span>
          </li>
          <li class="mno-single__meta-row">
            <span class="mno-single__meta-label"><?php esc_html_e( '発売日', 'mno' ); ?></span>
            <span class="mno-single__meta-value"><?php echo esc_html( $release_date_display ); ?></span>
          </li>
          <li class="mno-single__meta-row">
            <span class="mno-single__meta-label"><?php esc_html_e( '価格', 'mno' ); ?></span>
            <span class="mno-single__meta-value">
              <?php echo wp_kses_post( $price_display ); ?>
              <?php if ( $sale_end_display ) : ?>
                <br /><small><?php printf( esc_html__( 'セール終了 %s まで', 'mno' ), esc_html( $sale_end_display ) ); ?></small>
              <?php endif; ?>
            </span>
          </li>
          <li class="mno-single__meta-row">
            <span class="mno-single__meta-label"><?php esc_html_e( 'トラック時間', 'mno' ); ?></span>
            <span class="mno-single__meta-value"><?php echo $track_duration ? esc_html( $track_duration ) : '&mdash;'; ?></span>
          </li>
          <li class="mno-single__meta-row">
            <span class="mno-single__meta-label"><?php esc_html_e( 'タグ', 'mno' ); ?></span>
            <span class="mno-single__meta-value"><?php echo wp_kses_post( $tags_output ); ?></span>
          </li>
        </ul>

        <?php if ( $voice_sample_markup ) : ?>
          <div class="mno-voice-sample"><?php echo $voice_sample_markup; ?></div>
        <?php endif; ?>

        <?php echo $buy_button; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
      </section>

      <section class="mno-single mno-single--content">
        <p class="mno-single__section-title"><?php esc_html_e( 'Content', 'mno' ); ?></p>
        <div class="mno-single__content">
          <?php echo apply_filters( 'the_content', get_the_content() ); ?>
        </div>
      </section>

      <section class="mno-single mno-single--summary">
        <p class="mno-single__section-title"><?php esc_html_e( 'まとめ', 'mno' ); ?></p>
        <ul class="mno-summary-list">
          <?php if ( $highlights ) : ?>
            <?php foreach ( $highlights as $highlight ) : ?>
              <li>
                <span><?php esc_html_e( 'Highlight', 'mno' ); ?></span>
                <?php echo nl2br( esc_html( $highlight ) ); ?>
              </li>
            <?php endforeach; ?>
          <?php else : ?>
            <li>
              <span><?php esc_html_e( 'Overview', 'mno' ); ?></span>
              <?php echo esc_html( get_the_excerpt() ); ?>
            </li>
          <?php endif; ?>
        </ul>
      </section>

      <section class="mno-single mno-single--related">
        <p class="mno-single__section-title"><?php esc_html_e( 'Related posts', 'mno' ); ?></p>
        <?php
        $related_args = [
          'post_type'      => 'post',
          'posts_per_page' => 3,
          'post__not_in'   => [ $post_id ],
        ];

        if ( $genre_terms ) {
          $related_args['tag__in'] = array_map( 'intval', wp_list_pluck( $genre_terms, 'term_id' ) );
        }

        $related_query = new WP_Query( $related_args );

        if ( $related_query->have_posts() ) : ?>
          <ul class="mno-related-list">
            <?php while ( $related_query->have_posts() ) : $related_query->the_post(); ?>
              <li class="mno-related-list__item">
                <a class="mno-related-list__title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                <span class="mno-related-list__meta"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></span>
              </li>
            <?php endwhile; ?>
          </ul>
        <?php else : ?>
          <p class="mno-related-list__empty"><?php esc_html_e( '関連記事はまだありません。', 'mno' ); ?></p>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
      </section>
    </article>
  <?php endwhile; else : ?>
    <p><?php esc_html_e( '記事が見つかりませんでした。', 'mno' ); ?></p>
  <?php endif; ?>
</main>

<div class="mno-gallery-lightbox" data-mno-gallery-lightbox hidden aria-hidden="true" role="dialog" aria-modal="true">
  <figure class="mno-gallery-lightbox__figure">
    <img class="mno-gallery-lightbox__image" alt="" loading="lazy" />
  </figure>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var slider = document.querySelector('[data-mno-gallery-slider]');
    var lightbox = document.querySelector('[data-mno-gallery-lightbox]');

    if (!slider || !lightbox) {
      return;
    }

    var lightboxImage = lightbox.querySelector('.mno-gallery-lightbox__image');
    var lightboxFigure = lightbox.querySelector('.mno-gallery-lightbox__figure');
    var touchStartY = null;

    function setLightboxImage(image) {
      if (!image) {
        return;
      }

      var source = image.currentSrc || image.src;

      if (source) {
        lightboxImage.src = source;
      }

      if (image.srcset) {
        lightboxImage.srcset = image.srcset;
      } else {
        lightboxImage.removeAttribute('srcset');
      }

      if (image.sizes) {
        lightboxImage.sizes = image.sizes;
      } else {
        lightboxImage.removeAttribute('sizes');
      }

      lightboxImage.alt = image.alt || '';
    }

    function openLightbox(image) {
      setLightboxImage(image);

      lightbox.removeAttribute('hidden');

      requestAnimationFrame(function () {
        lightbox.classList.add('is-visible');
      });

      lightbox.setAttribute('aria-hidden', 'false');
      document.body.classList.add('mno-gallery-lightbox-open');
    }

    function hideLightbox() {
      lightbox.setAttribute('hidden', '');
      lightboxImage.removeAttribute('src');
      lightboxImage.removeAttribute('srcset');
      lightboxImage.removeAttribute('sizes');
    }

    function closeLightbox() {
      if (lightbox.hasAttribute('hidden')) {
        return;
      }

      lightbox.classList.remove('is-visible');
      lightbox.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('mno-gallery-lightbox-open');
      lightboxFigure.style.transform = '';
      lightboxFigure.style.opacity = '';

      lightbox.addEventListener(
        'transitionend',
        function handler() {
          hideLightbox();
          lightbox.removeEventListener('transitionend', handler);
        }
      );
    }

    slider.addEventListener('click', function (event) {
      var target = event.target;

      if (!(target instanceof Element)) {
        return;
      }

      var image = target.closest('img');

      if (!image) {
        return;
      }

      openLightbox(image);
    });

    lightbox.addEventListener('click', function (event) {
      if (event.target === lightbox) {
        closeLightbox();
      }
    });

    document.addEventListener('keydown', function (event) {
      if ('Escape' === event.key) {
        closeLightbox();
      }
    });

    lightboxFigure.addEventListener('click', function (event) {
      event.stopPropagation();
    });

    lightboxFigure.addEventListener('touchstart', function (event) {
      if (!event.touches || !event.touches.length) {
        return;
      }

      touchStartY = event.touches[0].clientY;
      lightboxFigure.style.transition = 'none';
    }, { passive: true });

    function resetFigureTransform() {
      lightboxFigure.style.transition = '';
      lightboxFigure.style.transform = '';
      lightboxFigure.style.opacity = '';
    }

    lightboxFigure.addEventListener('touchmove', function (event) {
      if (null === touchStartY || !event.touches || !event.touches.length) {
        return;
      }

      var currentY = event.touches[0].clientY;
      var deltaY = currentY - touchStartY;

      if (deltaY <= 0) {
        resetFigureTransform();
        return;
      }

      var translate = Math.min(deltaY, 160);
      lightboxFigure.style.transform = 'translateY(' + translate + 'px)';
      lightboxFigure.style.opacity = String(Math.max(0.35, 1 - deltaY / 180));
    }, { passive: true });

    function handleTouchEnd(event) {
      if (null === touchStartY) {
        return;
      }

      var endY = event.changedTouches && event.changedTouches.length ? event.changedTouches[0].clientY : touchStartY;
      var deltaY = endY - touchStartY;

      touchStartY = null;
      resetFigureTransform();

      if (deltaY > 100) {
        closeLightbox();
      }
    }

    lightboxFigure.addEventListener('touchend', handleTouchEnd);
    lightboxFigure.addEventListener('touchcancel', handleTouchEnd);
  });
</script>

<?php get_footer(); ?>
