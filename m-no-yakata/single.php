<?php get_header(); ?>

<main class="mno-container">
  <?php
  if (have_posts()) :
    while (have_posts()) : the_post(); ?>

      <article <?php post_class('mno-single'); ?>>
        <?php
        $custom_title = get_post_meta( get_the_ID(), '_mpm_custom_title', true );
        $display_title = '' !== $custom_title ? $custom_title : get_the_title();
        $title_markup  = '<section class="mno-pm-article__section mno-title-block">';
        $title_markup .= '<h1 class="mno-single-title">' . esc_html( $display_title ) . '</h1>';
        $title_markup .= '</section>';

        if ( function_exists( 'mno_pm_render_single_template' ) ) {
          $single_content = mno_pm_render_single_template( get_the_ID() );

          if ( false !== strpos( $single_content, 'mno-pm-article__gallery' ) ) {
            $original_content = $single_content;

            $single_content = preg_replace(
              '/(<section\s+class="mno-pm-article__section\s+mno-pm-article__gallery"[^>]*>.*?<\/section>)/s',
               '$1' . $title_markup,
              $single_content,
              1
            );

            if ( null === $single_content ) {
               $single_content = $original_content . $title_markup;
            }
          } else {
            $single_content = $title_markup . $single_content;
          }

          echo $single_content;
        } else {
          echo $title_markup;
          $voice_sample_value = get_post_meta( get_the_ID(), '_mpm_voice_sample', true );
          if ( $voice_sample_value ) {
            $allowed_tags = wp_kses_allowed_html( 'post' );
            $allowed_tags['iframe'] = [
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

            $voice_sample_html = wp_kses( $voice_sample_value, $allowed_tags );

            if ( $voice_sample_html ) {
              echo '<div class="mno-voice-sample">' . $voice_sample_html . '</div>';
            }
          }
          echo '<div class="mno-single__content">' . apply_filters( 'the_content', get_the_content() ) . '</div>';
        }
        ?>
      </article>

    <?php endwhile;
  else : ?>
    <p>記事が見つかりませんでした。</p>
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