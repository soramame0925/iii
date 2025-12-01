<article class="mno-card">
  <a href="<?php the_permalink(); ?>" class="mno-card__thumb">
    <?php if (has_post_thumbnail()) {
      the_post_thumbnail('medium');
    } else {
      echo '<img src="' . esc_url(get_template_directory_uri() . '/noimage.png') . '" alt="">';
    } ?>
  </a>
  <div class="mno-card__body">
    <h2 class="mno-card__title"><?php the_title(); ?></h2>
    <?php if($circle = get_field('circle_name')): ?>
      <p class="mno-card__meta">サークル: <?php echo esc_html($circle); ?></p>
    <?php endif; ?>
    <?php if($voice = get_field('voice_actor')): ?>
      <p class="mno-card__meta">声優: <?php echo esc_html($voice); ?></p>
    <?php endif; ?>
    <?php if($price = get_field('price')): ?>
      <p class="mno-card__price">¥<?php echo number_format((float)$price); ?></p>
    <?php endif; ?>
    <?php if($dl = get_field('dlsite_url')): ?>
      <a href="<?php echo esc_url($dl); ?>" target="_blank" rel="noopener" class="mno-card__btn">DLsiteで見る</a>
    <?php endif; ?>
  </div>
</article>