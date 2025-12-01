<?php
/** @var array $values */
/** @var array $voice_types */
/** @var array $level_labels */

$data_bars  = isset( $values['data_bars'] ) && is_array( $values['data_bars'] ) ? $values['data_bars'] : [];
$data_voice = isset( $values['data_voice'] ) && is_array( $values['data_voice'] ) ? $values['data_voice'] : [];
$data_level = isset( $values['data_level'] ) ? $values['data_level'] : '';
?>
<div class="mno-pm-meta mno-pm-meta--data">
    <section class="mno-pm-meta__section">
        <h3><?php esc_html_e( '演出データ（棒グラフ）', 'mno-post-manager' ); ?></h3>
        <p class="description"><?php esc_html_e( '演出内容ごとの回数を入力してください。', 'mno-post-manager' ); ?></p>
        <div
            class="mno-pm-repeater mno-pm-repeater--data"
            data-name="mno_pm_data_bars"
            data-next-index="<?php echo esc_attr( count( $data_bars ) ); ?>"
        >
            <div class="mno-pm-repeater__rows">
                <?php foreach ( $data_bars as $index => $entry ) :
                    $label = isset( $entry['label'] ) ? $entry['label'] : '';
                    $track = isset( $entry['track'] ) ? $entry['track'] : '';
                    $count = isset( $entry['count'] ) ? $entry['count'] : '';
                    ?>
                    <div class="mno-pm-repeater__row mno-pm-repeater__row--data">
                        <span class="dashicons dashicons-move mno-pm-repeater__handle" aria-hidden="true"></span>
                        <div class="mno-data-fields">
                            <label>
                                <span><?php esc_html_e( 'ラベル名', 'mno-post-manager' ); ?></span>
                                <input type="text" name="mno_pm_data_bars[<?php echo esc_attr( $index ); ?>][label]" class="widefat" value="<?php echo esc_attr( $label ); ?>" />
                            </label>
                            <label>
                                <span><?php esc_html_e( '該当トラック番号', 'mno-post-manager' ); ?></span>
                                <input type="text" name="mno_pm_data_bars[<?php echo esc_attr( $index ); ?>][track]" class="widefat" value="<?php echo esc_attr( $track ); ?>" placeholder="<?php esc_attr_e( '例: 03, 04', 'mno-post-manager' ); ?>" />
                            </label>
                            <label>
                                <span><?php esc_html_e( '回数', 'mno-post-manager' ); ?></span>
                                <input type="number" name="mno_pm_data_bars[<?php echo esc_attr( $index ); ?>][count]" value="<?php echo '' !== $count ? esc_attr( $count ) : ''; ?>" min="0" step="1" class="small-text" />
                            </label>
                        </div>
                        <button type="button" class="button mno-pm-repeater__remove" aria-label="<?php esc_attr_e( '削除', 'mno-post-manager' ); ?>">&minus;</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <script type="text/template" class="mno-pm-repeater__template">
                <div class="mno-pm-repeater__row mno-pm-repeater__row--data">
                    <span class="dashicons dashicons-move mno-pm-repeater__handle" aria-hidden="true"></span>
                    <div class="mno-data-fields">
                        <label>
                            <span><?php esc_html_e( 'ラベル名', 'mno-post-manager' ); ?></span>
                            <input type="text" name="mno_pm_data_bars[__index__][label]" class="widefat" value="" />
                        </label>
                        <label>
                            <span><?php esc_html_e( '該当トラック番号', 'mno-post-manager' ); ?></span>
                            <input type="text" name="mno_pm_data_bars[__index__][track]" class="widefat" value="" placeholder="<?php esc_attr_e( '例: 03, 04', 'mno-post-manager' ); ?>" />
                        </label>
                        <label>
                            <span><?php esc_html_e( '回数', 'mno-post-manager' ); ?></span>
                            <input type="number" name="mno_pm_data_bars[__index__][count]" value="" min="0" step="1" class="small-text" />
                        </label>
                    </div>
                    <button type="button" class="button mno-pm-repeater__remove" aria-label="<?php esc_attr_e( '削除', 'mno-post-manager' ); ?>">&minus;</button>
                </div>
            </script>
            <button type="button" class="button mno-pm-repeater__add"><?php esc_html_e( '演出データを追加', 'mno-post-manager' ); ?></button>
        </div>
    </section>

    <section class="mno-pm-meta__section">
        <h3><?php esc_html_e( '声の高さ', 'mno-post-manager' ); ?></h3>
        <div
            class="mno-pm-repeater mno-pm-repeater--data"
            data-name="mno_pm_data_voice"
            data-next-index="<?php echo esc_attr( count( $data_voice ) ); ?>"
        >
            <div class="mno-pm-repeater__rows">
                <?php foreach ( $data_voice as $index => $entry ) :
                    $name  = isset( $entry['name'] ) ? $entry['name'] : '';
                    $types = isset( $entry['types'] ) && is_array( $entry['types'] ) ? $entry['types'] : [];
                    ?>
                    <div class="mno-pm-repeater__row mno-pm-repeater__row--voice">
                        <span class="dashicons dashicons-move mno-pm-repeater__handle" aria-hidden="true"></span>
                        <div class="mno-voice-fields">
                            <label>
                                <span><?php esc_html_e( '声優名', 'mno-post-manager' ); ?></span>
                                <input type="text" name="mno_pm_data_voice[<?php echo esc_attr( $index ); ?>][name]" class="widefat" value="<?php echo esc_attr( $name ); ?>" />
                            </label>
                            <div class="mno-voice-checkboxes">
                                <?php foreach ( $voice_types as $type_key => $type_label ) :
                                    $field_id = 'mno-pm-voice-' . $index . '-' . $type_key;
                                    ?>
                                    <label for="<?php echo esc_attr( $field_id ); ?>">
                                        <input
                                            type="checkbox"
                                            id="<?php echo esc_attr( $field_id ); ?>"
                                            name="mno_pm_data_voice[<?php echo esc_attr( $index ); ?>][types][]"
                                            value="<?php echo esc_attr( $type_key ); ?>"
                                            <?php checked( in_array( $type_key, $types, true ) ); ?>
                                        />
                                        <span><?php echo esc_html( $type_label ); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <button type="button" class="button mno-pm-repeater__remove" aria-label="<?php esc_attr_e( '削除', 'mno-post-manager' ); ?>">&minus;</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <script type="text/template" class="mno-pm-repeater__template">
                <div class="mno-pm-repeater__row mno-pm-repeater__row--voice">
                    <span class="dashicons dashicons-move mno-pm-repeater__handle" aria-hidden="true"></span>
                    <div class="mno-voice-fields">
                        <label>
                            <span><?php esc_html_e( '声優名', 'mno-post-manager' ); ?></span>
                            <input type="text" name="mno_pm_data_voice[__index__][name]" class="widefat" value="" />
                        </label>
                        <div class="mno-voice-checkboxes">
                            <?php foreach ( $voice_types as $type_key => $type_label ) :
                                $field_id = 'mno-pm-voice-__index__-' . $type_key;
                                ?>
                                <label for="<?php echo esc_attr( $field_id ); ?>">
                                    <input
                                        type="checkbox"
                                        id="<?php echo esc_attr( $field_id ); ?>"
                                        name="mno_pm_data_voice[__index__][types][]"
                                        value="<?php echo esc_attr( $type_key ); ?>"
                                    />
                                    <span><?php echo esc_html( $type_label ); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <button type="button" class="button mno-pm-repeater__remove" aria-label="<?php esc_attr_e( '削除', 'mno-post-manager' ); ?>">&minus;</button>
                </div>
            </script>
            <button type="button" class="button mno-pm-repeater__add"><?php esc_html_e( '声の高さを追加', 'mno-post-manager' ); ?></button>
        </div>
    </section>

    <section class="mno-pm-meta__section">
        <h3><?php esc_html_e( 'Mレベル', 'mno-post-manager' ); ?></h3>
        <fieldset class="mno-level-fields">
            <legend class="screen-reader-text"><?php esc_html_e( 'Mレベル', 'mno-post-manager' ); ?></legend>
            <?php foreach ( $level_labels as $level_key => $label ) :
                $field_id = 'mno-pm-level-' . $level_key;
                ?>
                <label for="<?php echo esc_attr( $field_id ); ?>" class="mno-level-option">
                    <input
                        type="radio"
                        id="<?php echo esc_attr( $field_id ); ?>"
                        name="mno_pm_data_level"
                        value="<?php echo esc_attr( $level_key ); ?>"
                        <?php checked( $data_level === $level_key ); ?>
                    />
                    <span><?php echo esc_html( $label ); ?></span>
                </label>
            <?php endforeach; ?>
        </fieldset>
    </section>
</div>