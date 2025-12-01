<?php
/** @var array $values */
?>
<div class="mno-pm-meta">
     <section class="mno-pm-meta__section">
        <h3><?php esc_html_e( 'タイトル', 'mno-post-manager' ); ?></h3>
        <p>
            <label>
                <input
                    type="text"
                    name="mno_pm_custom_title"
                    class="widefat"
                    value="<?php echo esc_attr( '' !== $values['custom_title'] ? $values['custom_title'] : $post->post_title ); ?>"
                    placeholder="<?php esc_attr_e( 'タイトルを入力してください', 'mno-post-manager' ); ?>"
                />
            </label>
        </p>
        <p class="description"><?php esc_html_e( 'このタイトルは公開ページやSEOメタで使用されます。', 'mno-post-manager' ); ?></p>
    </section>
    <section class="mno-pm-meta__section">
        <h3><?php esc_html_e( 'ギャラリー画像', 'mno-post-manager' ); ?></h3>
        <p class="description"><?php esc_html_e( '複数の画像を追加し、ドラッグで並べ替えできます。', 'mno-post-manager' ); ?></p>
        <div id="mno-pm-gallery-list" class="mno-pm-gallery">
            <?php if ( ! empty( $values['gallery'] ) ) : ?>
                <?php foreach ( $values['gallery'] as $attachment_id ) :
                    $thumb = wp_get_attachment_image( $attachment_id, 'thumbnail' );
                    ?>
                    <div class="mno-pm-gallery__item">
                        <span class="mno-pm-gallery__handle dashicons dashicons-move" aria-hidden="true"></span>
                        <div class="mno-pm-gallery__preview">
                            <?php echo $thumb ? $thumb : esc_html__( '画像が見つかりません', 'mno-post-manager' ); ?>
                        </div>
                        <input type="hidden" name="mno_pm_gallery[]" value="<?php echo esc_attr( $attachment_id ); ?>" />
                        <button type="button" class="button mno-pm-gallery__remove" aria-label="<?php esc_attr_e( '画像を削除', 'mno-post-manager' ); ?>">&times;</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button type="button" class="button button-secondary" id="mno-pm-add-gallery"><?php esc_html_e( '画像を追加', 'mno-post-manager' ); ?></button>
        <script type="text/template" id="mno-pm-gallery-template">
            <div class="mno-pm-gallery__item">
                <span class="mno-pm-gallery__handle dashicons dashicons-move" aria-hidden="true"></span>
                <div class="mno-pm-gallery__preview">{{image}}</div>
                <input type="hidden" name="mno_pm_gallery[]" value="{{id}}" />
                <button type="button" class="button mno-pm-gallery__remove" aria-label="<?php esc_attr_e( '画像を削除', 'mno-post-manager' ); ?>">&times;</button>
            </div>
        </script>
    </section>

    <section class="mno-pm-meta__section">
        <h3><?php esc_html_e( '音声サンプル', 'mno-post-manager' ); ?></h3>
        <textarea name="mno_pm_voice_sample" rows="3" class="widefat" placeholder="<?php esc_attr_e( 'URL または埋め込みコードを入力してください', 'mno-post-manager' ); ?>"><?php echo esc_textarea( $values['voice_sample'] ); ?></textarea>
    </section>

    <section class="mno-pm-meta__section">
        <h3><?php esc_html_e( 'サークル情報', 'mno-post-manager' ); ?></h3>
        <p>
            <label>
                <?php esc_html_e( 'サークル名', 'mno-post-manager' ); ?><br />
                <input type="text" name="mno_pm_circle_name" class="widefat" value="<?php echo esc_attr( $values['circle_name'] ); ?>" />
            </label>
        </p>
        <p>
            <label>
                <?php esc_html_e( '発売日', 'mno-post-manager' ); ?><br />
                <input type="date" name="mno_pm_release_date" value="<?php echo esc_attr( $values['release_date'] ); ?>" />
            </label>
        </p>
        <p>
            <label>
                <?php esc_html_e( 'ジャンル', 'mno-post-manager' ); ?><br />
                <input type="text" name="mno_pm_genre" class="widefat" value="<?php echo esc_attr( $values['genre'] ); ?>" />
            </label>
        </p>
    </section>

    <section class="mno-pm-meta__section">
        <h3><?php esc_html_e( '出演声優', 'mno-post-manager' ); ?></h3>
        <div class="mno-pm-repeater" data-name="mno_pm_voice_actors">
            <div class="mno-pm-repeater__rows">
                <?php if ( ! empty( $values['voice_actors'] ) ) : ?>
                    <?php foreach ( $values['voice_actors'] as $voice_actor ) : ?>
                        <div class="mno-pm-repeater__row">
                            <span class="dashicons dashicons-move mno-pm-repeater__handle" aria-hidden="true"></span>
                            <input type="text" name="mno_pm_voice_actors[]" class="widefat" value="<?php echo esc_attr( $voice_actor ); ?>" />
                            <button type="button" class="button mno-pm-repeater__remove" aria-label="<?php esc_attr_e( '削除', 'mno-post-manager' ); ?>">&minus;</button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <script type="text/template" class="mno-pm-repeater__template">
                <div class="mno-pm-repeater__row">
                    <span class="dashicons dashicons-move mno-pm-repeater__handle" aria-hidden="true"></span>
                    <input type="text" name="mno_pm_voice_actors[]" class="widefat" value="" />
                    <button type="button" class="button mno-pm-repeater__remove" aria-label="<?php esc_attr_e( '削除', 'mno-post-manager' ); ?>">&minus;</button>
                </div>
            </script>
            <button type="button" class="button mno-pm-repeater__add"><?php esc_html_e( '声優を追加', 'mno-post-manager' ); ?></button>
        </div>
    </section>

    <section class="mno-pm-meta__section">
        <h3><?php esc_html_e( 'イラストレーター', 'mno-post-manager' ); ?></h3>
        <div class="mno-pm-repeater" data-name="mno_pm_illustrators">
            <div class="mno-pm-repeater__rows">
                <?php if ( ! empty( $values['illustrators'] ) ) : ?>
                    <?php foreach ( $values['illustrators'] as $illustrator ) : ?>
                        <div class="mno-pm-repeater__row">
                            <span class="dashicons dashicons-move mno-pm-repeater__handle" aria-hidden="true"></span>
                            <input type="text" name="mno_pm_illustrators[]" class="widefat" value="<?php echo esc_attr( $illustrator ); ?>" />
                            <button type="button" class="button mno-pm-repeater__remove" aria-label="<?php esc_attr_e( '削除', 'mno-post-manager' ); ?>">&minus;</button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <script type="text/template" class="mno-pm-repeater__template">
                <div class="mno-pm-repeater__row">
                    <span class="dashicons dashicons-move mno-pm-repeater__handle" aria-hidden="true"></span>
                    <input type="text" name="mno_pm_illustrators[]" class="widefat" value="" />
                    <button type="button" class="button mno-pm-repeater__remove" aria-label="<?php esc_attr_e( '削除', 'mno-post-manager' ); ?>">&minus;</button>
                </div>
            </script>
            <button type="button" class="button mno-pm-repeater__add"><?php esc_html_e( 'イラストレーターを追加', 'mno-post-manager' ); ?></button>
        </div>
    </section>

    <section class="mno-pm-meta__section mno-pm-meta__section--grid">
        <div>
            <h3><?php esc_html_e( '通常価格', 'mno-post-manager' ); ?></h3>
            <input type="text" name="mno_pm_normal_price" class="widefat" value="<?php echo esc_attr( $values['normal_price'] ); ?>" placeholder="<?php esc_attr_e( '例：1,320円', 'mno-post-manager' ); ?>" />
        </div>
        <div>
            <h3><?php esc_html_e( 'セール価格', 'mno-post-manager' ); ?></h3>
            <input type="text" name="mno_pm_sale_price" class="widefat" value="<?php echo esc_attr( $values['sale_price'] ); ?>" placeholder="<?php esc_attr_e( 'セールがない場合は空欄のままにしてください', 'mno-post-manager' ); ?>" />
        </div>
        <div>
            <h3><?php esc_html_e( 'セール終了日', 'mno-post-manager' ); ?></h3>
            <input type="date" name="mno_pm_sale_end_date" value="<?php echo esc_attr( $values['sale_end_date'] ); ?>" />
            <p class="description"><?php esc_html_e( 'この日付以降は自動的に通常価格に戻ります。', 'mno-post-manager' ); ?></p>
        </div>
    </section>

    <section class="mno-pm-meta__section">
        <h3><?php esc_html_e( '作品のみどころ', 'mno-post-manager' ); ?></h3>
        <div class="mno-pm-repeater mno-pm-repeater--textarea" data-name="mno_pm_highlights">
            <div class="mno-pm-repeater__rows">
                <?php if ( ! empty( $values['highlights'] ) ) : ?>
                    <?php foreach ( $values['highlights'] as $highlight ) : ?>
                        <div class="mno-pm-repeater__row">
                            <span class="dashicons dashicons-move mno-pm-repeater__handle" aria-hidden="true"></span>
                            <textarea name="mno_pm_highlights[]" class="widefat" rows="3"><?php echo esc_textarea( $highlight ); ?></textarea>
                            <button type="button" class="button mno-pm-repeater__remove" aria-label="<?php esc_attr_e( '削除', 'mno-post-manager' ); ?>">&minus;</button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <script type="text/template" class="mno-pm-repeater__template">
                <div class="mno-pm-repeater__row">
                    <span class="dashicons dashicons-move mno-pm-repeater__handle" aria-hidden="true"></span>
                    <textarea name="mno_pm_highlights[]" class="widefat" rows="3"></textarea>
                    <button type="button" class="button mno-pm-repeater__remove" aria-label="<?php esc_attr_e( '削除', 'mno-post-manager' ); ?>">&minus;</button>
                </div>
            </script>
            <button type="button" class="button mno-pm-repeater__add"><?php esc_html_e( 'みどころを追加', 'mno-post-manager' ); ?></button>
        </div>
    </section>

    <section class="mno-pm-meta__section">
        <h3><?php esc_html_e( 'トラックリスト', 'mno-post-manager' ); ?></h3>
        <?php $track_list = isset( $values['track_list'] ) ? $values['track_list'] : []; ?>
        <div
            class="mno-pm-repeater mno-pm-repeater--tracks"
            data-name="mno_pm_track_list"
            data-next-index="<?php echo esc_attr( is_array( $track_list ) ? count( $track_list ) : 0 ); ?>"
        >
            <div class="mno-pm-repeater__rows">
                <?php if ( ! empty( $track_list ) ) : ?>
                    <?php foreach ( $track_list as $index => $track ) :
                        $track_name = isset( $track['track_name'] ) ? $track['track_name'] : '';
                        $count      = isset( $track['ejaculation_count'] ) ? $track['ejaculation_count'] : '';
                        $duration   = isset( $track['track_duration'] ) ? $track['track_duration'] : '';
                        $genres     = isset( $track['genres'] ) && is_array( $track['genres'] ) ? $track['genres'] : [];
                        $genres_val = $genres ? implode( '、', array_map( 'sanitize_text_field', $genres ) ) : '';
                        ?>
                        <div class="mno-pm-repeater__row mno-pm-repeater__row--track">
                            <span class="dashicons dashicons-move mno-pm-repeater__handle" aria-hidden="true"></span>
                            <div class="mno-pm-track-fields">
                                <p>
                                    <label class="mno-pm-track-fields__label">
                                        <span class="mno-pm-track-fields__label-text"><?php esc_html_e( 'トラック名', 'mno-post-manager' ); ?></span>
                                        <textarea
                                            class="widefat"
                                            name="mno_pm_track_list[<?php echo esc_attr( $index ); ?>][track_name]"
                                            rows="2"
                                        ><?php echo esc_textarea( $track_name ); ?></textarea>
                                    </label>
                                </p>
                                 <p class="mno-pm-track-fields__duration">
                                    <label class="mno-pm-track-fields__label">
                                        <span class="mno-pm-track-fields__label-text"><?php esc_html_e( 'Duration (mm:ss)', 'mno-post-manager' ); ?></span>
                                        <input
                                            type="text"
                                            class="small-text"
                                            name="mno_pm_track_list[<?php echo esc_attr( $index ); ?>][track_duration]"
                                            value="<?php echo esc_attr( $duration ); ?>"
                                            placeholder="<?php esc_attr_e( '例：03:45', 'mno-post-manager' ); ?>"
                                        />
                                    </label>
                                </p>
                                <p class="mno-pm-track-fields__count">
                                    <label class="mno-pm-track-fields__label">
                                        <span class="mno-pm-track-fields__label-text"><?php esc_html_e( '射精回数', 'mno-post-manager' ); ?></span>
                                        <input
                                            type="number"
                                            min="0"
                                            step="1"
                                            class="small-text"
                                            name="mno_pm_track_list[<?php echo esc_attr( $index ); ?>][ejaculation_count]"
                                            value="<?php echo '' !== $count ? esc_attr( $count ) : ''; ?>"
                                        />
                                        <span class="mno-pm-track-fields__count-suffix"><?php esc_html_e( '回', 'mno-post-manager' ); ?></span>
                                    </label>
                                </p>
                                <p class="mno-pm-track-fields__genres">
                                    <label class="mno-pm-track-fields__label">
                                        <span class="mno-pm-track-fields__label-text"><?php esc_html_e( 'ジャンル', 'mno-post-manager' ); ?></span>
                                        <input
                                            type="text"
                                            class="widefat"
                                            name="mno_pm_track_list[<?php echo esc_attr( $index ); ?>][genres]"
                                            value="<?php echo esc_attr( $genres_val ); ?>"
                                            placeholder="<?php esc_attr_e( '例：癒し、耳かき', 'mno-post-manager' ); ?>"
                                        />
                                    </label>
                                </p>
                            </div>
                            <button type="button" class="button mno-pm-repeater__remove" aria-label="<?php esc_attr_e( '削除', 'mno-post-manager' ); ?>">&minus;</button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <script type="text/template" class="mno-pm-repeater__template">
                <div class="mno-pm-repeater__row mno-pm-repeater__row--track">
                    <span class="dashicons dashicons-move mno-pm-repeater__handle" aria-hidden="true"></span>
                    <div class="mno-pm-track-fields">
                        <p>
                            <label class="mno-pm-track-fields__label">
                                <span class="mno-pm-track-fields__label-text"><?php esc_html_e( 'トラック名', 'mno-post-manager' ); ?></span>
                                <textarea class="widefat" name="mno_pm_track_list[__index__][track_name]" rows="2"></textarea>
                            </label>
                        </p>
                         <p class="mno-pm-track-fields__duration">
                            <label class="mno-pm-track-fields__label">
                                <span class="mno-pm-track-fields__label-text"><?php esc_html_e( 'Duration (mm:ss)', 'mno-post-manager' ); ?></span>
                                <input type="text" class="small-text" name="mno_pm_track_list[__index__][track_duration]" value="" placeholder="<?php esc_attr_e( '例：03:45', 'mno-post-manager' ); ?>" />
                            </label>
                        </p>
                        <p class="mno-pm-track-fields__count">
                            <label class="mno-pm-track-fields__label">
                                <span class="mno-pm-track-fields__label-text"><?php esc_html_e( '射精回数', 'mno-post-manager' ); ?></span>
                                <input type="number" min="0" step="1" class="small-text" name="mno_pm_track_list[__index__][ejaculation_count]" value="" />
                                <span class="mno-pm-track-fields__count-suffix"><?php esc_html_e( '回', 'mno-post-manager' ); ?></span>
                            </label>
                        </p>
                        <p class="mno-pm-track-fields__genres">
                            <label class="mno-pm-track-fields__label">
                                <span class="mno-pm-track-fields__label-text"><?php esc_html_e( 'ジャンル', 'mno-post-manager' ); ?></span>
                                <input type="text" class="widefat" name="mno_pm_track_list[__index__][genres]" value="" placeholder="<?php esc_attr_e( '例：癒し、耳かき', 'mno-post-manager' ); ?>" />
                            </label>
                        </p>
                    </div>
                    <button type="button" class="button mno-pm-repeater__remove" aria-label="<?php esc_attr_e( '削除', 'mno-post-manager' ); ?>">&minus;</button>
                </div>
            </script>
            <button type="button" class="button mno-pm-repeater__add"><?php esc_html_e( 'トラックを追加', 'mno-post-manager' ); ?></button>
        </div>
    </section>

    <section class="mno-pm-meta__section">
        <?php
        $dialogue_block   = isset( $values['dialogue_block'] ) && is_array( $values['dialogue_block'] ) ? $values['dialogue_block'] : [];
        $dialogue_title   = isset( $dialogue_block['main_title'] ) ? $dialogue_block['main_title'] : '';
        $dialogue_image   = isset( $dialogue_block['image_id'] ) ? absint( $dialogue_block['image_id'] ) : 0;
        $track_desc       = isset( $dialogue_block['track_description'] ) ? $dialogue_block['track_description'] : '';
        $dialogue_tracks  = isset( $dialogue_block['track_list'] ) && is_array( $dialogue_block['track_list'] ) ? $dialogue_block['track_list'] : [];
        $dialogue_heads   = isset( $dialogue_block['subheadings'] ) && is_array( $dialogue_block['subheadings'] ) ? $dialogue_block['subheadings'] : [];
        $dialogue_content = isset( $dialogue_block['dialogue_body'] ) ? $dialogue_block['dialogue_body'] : '';
        $dialogue_gallery = isset( $values['gallery'] ) && is_array( $values['gallery'] ) ? array_filter( array_map( 'absint', $values['gallery'] ) ) : [];
        ?>
        <h3><?php esc_html_e( 'セリフブロック', 'mno-post-manager' ); ?></h3>
        <div class="mno-pm-dialogue-block">
            <p>
                <label>
                    <?php esc_html_e( 'メインタイトル', 'mno-post-manager' ); ?><br />
                    <textarea
                        name="mno_pm_dialogue_block[main_title]"
                        class="widefat"
                        rows="3"
                    ><?php echo esc_textarea( $dialogue_title ); ?></textarea>
                </label>
            </p>

             <div class="mno-pm-dialogue-block__image-select">
                <span class="mno-pm-dialogue-block__label"><?php esc_html_e( 'ギャラリーから画像を選択', 'mno-post-manager' ); ?></span>
                <div class="mno-pm-dialogue-block__image-options">
                    <label class="mno-pm-dialogue-block__image-option">
                        <input
                            type="radio"
                            name="mno_pm_dialogue_block[image_id]"
                            value=""
                            <?php checked( '', $dialogue_image ); ?>
                        />
                        <span><?php esc_html_e( '画像を選択しない', 'mno-post-manager' ); ?></span>
                    </label>

                    <?php if ( $dialogue_gallery ) : ?>
                        <?php foreach ( $dialogue_gallery as $attachment_id ) : ?>
                            <?php $preview = wp_get_attachment_image( $attachment_id, 'thumbnail' ); ?>
                            <?php if ( ! $preview ) : ?>
                                <?php continue; ?>
                            <?php endif; ?>
                            <label class="mno-pm-dialogue-block__image-option">
                                <input
                                    type="radio"
                                    name="mno_pm_dialogue_block[image_id]"
                                    value="<?php echo esc_attr( $attachment_id ); ?>"
                                    <?php checked( $dialogue_image, $attachment_id ); ?>
                                />
                                <span class="mno-pm-dialogue-block__image-thumb"><?php echo $preview; ?></span>
                            </label>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <?php if ( empty( $dialogue_gallery ) ) : ?>
                    <p class="description"><?php esc_html_e( 'ギャラリーブロックに画像を追加するとここで選択できます。', 'mno-post-manager' ); ?></p>
                <?php endif; ?>
            </div>


            <p>
                <label>
                    <?php esc_html_e( 'トラック説明', 'mno-post-manager' ); ?><br />
                    <textarea
                        name="mno_pm_dialogue_block[track_description]"
                        class="widefat"
                        rows="3"
                    ><?php echo esc_textarea( $track_desc ); ?></textarea>
                </label>
            </p>

            <div class="mno-pm-dialogue-block__repeater">
                <span class="mno-pm-dialogue-block__label"><?php esc_html_e( 'トラックリスト', 'mno-post-manager' ); ?></span>
                <div
                    class="mno-pm-repeater mno-pm-repeater--dialogue"
                    data-name="mno_pm_dialogue_block[track_list]"
                    data-next-index="<?php echo esc_attr( count( $dialogue_tracks ) ); ?>"
                >
                    <div class="mno-pm-repeater__rows">
                        <?php foreach ( $dialogue_tracks as $index => $track_item ) : ?>
                            <div class="mno-pm-repeater__row mno-pm-repeater__row--dialogue">
                                <span class="dashicons dashicons-move mno-pm-repeater__handle" aria-hidden="true"></span>
                                <textarea name="mno_pm_dialogue_block[track_list][<?php echo esc_attr( $index ); ?>]" class="widefat" rows="2"><?php echo esc_textarea( $track_item ); ?></textarea>
                                <button type="button" class="button mno-pm-repeater__remove" aria-label="<?php esc_attr_e( '削除', 'mno-post-manager' ); ?>">&minus;</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <script type="text/template" class="mno-pm-repeater__template">
                        <div class="mno-pm-repeater__row mno-pm-repeater__row--dialogue">
                            <span class="dashicons dashicons-move mno-pm-repeater__handle" aria-hidden="true"></span>
                            <textarea name="mno_pm_dialogue_block[track_list][__index__]" class="widefat" rows="2"></textarea>
                            <button type="button" class="button mno-pm-repeater__remove" aria-label="<?php esc_attr_e( '削除', 'mno-post-manager' ); ?>">&minus;</button>
                        </div>
                    </script>
                    <button type="button" class="button mno-pm-repeater__add"><?php esc_html_e( 'トラックを追加', 'mno-post-manager' ); ?></button>
                </div>
            </div>

            <div class="mno-pm-dialogue-block__repeater">
                <span class="mno-pm-dialogue-block__label"><?php esc_html_e( 'サブ見出し', 'mno-post-manager' ); ?></span>
                <div
                    class="mno-pm-repeater mno-pm-repeater--dialogue"
                    data-name="mno_pm_dialogue_block[subheadings]"
                    data-next-index="<?php echo esc_attr( count( $dialogue_heads ) ); ?>"
                >
                    <div class="mno-pm-repeater__rows">
                        <?php foreach ( $dialogue_heads as $index => $heading ) : ?>
                            <div class="mno-pm-repeater__row mno-pm-repeater__row--dialogue">
                                <span class="dashicons dashicons-move mno-pm-repeater__handle" aria-hidden="true"></span>
                                <textarea name="mno_pm_dialogue_block[subheadings][<?php echo esc_attr( $index ); ?>]" class="widefat" rows="2"><?php echo esc_textarea( $heading ); ?></textarea>
                                <button type="button" class="button mno-pm-repeater__remove" aria-label="<?php esc_attr_e( '削除', 'mno-post-manager' ); ?>">&minus;</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <script type="text/template" class="mno-pm-repeater__template">
                        <div class="mno-pm-repeater__row mno-pm-repeater__row--dialogue">
                            <span class="dashicons dashicons-move mno-pm-repeater__handle" aria-hidden="true"></span>
                            <textarea name="mno_pm_dialogue_block[subheadings][__index__]" class="widefat" rows="2"></textarea>
                            <button type="button" class="button mno-pm-repeater__remove" aria-label="<?php esc_attr_e( '削除', 'mno-post-manager' ); ?>">&minus;</button>
                        </div>
                    </script>
                    <button type="button" class="button mno-pm-repeater__add"><?php esc_html_e( 'サブ見出しを追加', 'mno-post-manager' ); ?></button>
                </div>
            </div>

            <p>
                <label>
                    <?php esc_html_e( 'セリフ本文', 'mno-post-manager' ); ?><br />
                    <textarea
                        name="mno_pm_dialogue_block[dialogue_body]"
                        class="widefat"
                        rows="6"
                    ><?php echo esc_textarea( $dialogue_content ); ?></textarea>
                </label>
            </p>
        </div>
    </section>

    <section class="mno-pm-meta__section">
        <h3><?php esc_html_e( 'トラック概要', 'mno-post-manager' ); ?></h3>
        <p>
            <label>
                <?php esc_html_e( 'トラック総時間', 'mno-post-manager' ); ?><br />
                <input type="text" name="mno_pm_track_duration" class="widefat" value="<?php echo esc_attr( $values['track_duration'] ); ?>" placeholder="<?php esc_attr_e( '例：45分20秒', 'mno-post-manager' ); ?>" />
            </label>
        </p>
    </section>

    <section class="mno-pm-meta__section">
        <h3><?php esc_html_e( '購入ボタンのURL', 'mno-post-manager' ); ?></h3>
        <input type="url" name="mno_pm_buy_url" class="widefat" value="<?php echo esc_attr( $values['buy_url'] ); ?>" placeholder="<?php esc_attr_e( 'https://www.dlsite.com/...', 'mno-post-manager' ); ?>" />
    </section>
</div>