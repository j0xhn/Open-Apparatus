<?php


//  rating code start

add_action( 'add_meta_boxes', 'cd_meta_box_add' );
function cd_meta_box_add()
{
add_meta_box( 'my-meta-box-id', 'Review Info', 'cd_meta_box_cb', 'post', 'normal', 'high' );
}

function cd_meta_box_cb( $post )
{
$values = get_post_custom( $post->ID );

$selected = isset( $values['my_meta_box_select'] ) ? esc_attr( $values['my_meta_box_select'][0] ) : '';

$creteria_1_text = isset( $values['creteria_1_text'] ) ? esc_attr( $values['creteria_1_text'][0] ) : '';
$creteria_1 = isset( $values['creteria_1'] ) ? esc_attr( $values['creteria_1'][0] ) : '';

$creteria_2_text = isset( $values['creteria_2_text'] ) ? esc_attr( $values['creteria_2_text'][0] ) : '';
$creteria_2 = isset( $values['creteria_2'] ) ? esc_attr( $values['creteria_2'][0] ) : '';

$creteria_3_text = isset( $values['creteria_3_text'] ) ? esc_attr( $values['creteria_3_text'][0] ) : '';
$creteria_3 = isset( $values['creteria_3'] ) ? esc_attr( $values['creteria_3'][0] ) : '';

$creteria_4_text = isset( $values['creteria_4_text'] ) ? esc_attr( $values['creteria_4_text'][0] ) : '';
$creteria_4 = isset( $values['creteria_4'] ) ? esc_attr( $values['creteria_4'][0] ) : '';

$creteria_5_text = isset( $values['creteria_5_text'] ) ? esc_attr( $values['creteria_5_text'][0] ) : '';
$creteria_5 = isset( $values['creteria_5'] ) ? esc_attr( $values['creteria_5'][0] ) : '';



$check = isset( $values['my_meta_box_check'] ) ? esc_attr( $values['my_meta_box_check'][0] ) : '';
wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
?>
<p>
<label for="my_meta_box_text"><b><?php _e("Over All Score", 'funding'); ?></b></label>
<select name="my_meta_box_select" id="my_meta_box_select">
<option value="0" <?php selected( $selected, '0' ); ?>>0</option>
<option value="0.5" <?php selected( $selected, '0.5' ); ?>>0.5</option>
<option value="1" <?php selected( $selected, '1' ); ?>>1</option>
<option value="1.5" <?php selected( $selected, '1.5' ); ?>>1.5</option>
<option value="2" <?php selected( $selected, '2' ); ?>>2</option>
<option value="2.5" <?php selected( $selected, '2.5' ); ?>>2.5</option>
<option value="3" <?php selected( $selected, '3' ); ?>>3</option>
<option value="3.5" <?php selected( $selected, '3.5' ); ?>>3.5</option>
<option value="4" <?php selected( $selected, '4' ); ?>>4</option>
<option value="4.5" <?php selected( $selected, '4.5' ); ?>>4.5</option>
<option value="5" <?php selected( $selected, '5' ); ?>>5</option>
</select>

</p>


<p>
<label for="creteria_1"><b><?php _e("Critera 1", 'funding'); ?></b></label>
<input type="text" name="creteria_1_text" id="creteria_1_text" value="<?php echo $creteria_1_text; ?>" />
</p>
<p>
<label for="creteria_1"><b><?php _e("Critera 1 Score", 'funding'); ?></b></label>

<select name="creteria_1" id="creteria_1">
<option value="0" <?php selected( $creteria_1, '0' ); ?>>0</option>
<option value="0.5" <?php selected( $creteria_1, '0.5' ); ?>>0.5</option>
<option value="1" <?php selected( $creteria_1, '1' ); ?>>1</option>
<option value="1.5" <?php selected( $creteria_1, '1.5' ); ?>>1.5</option>
<option value="2" <?php selected( $creteria_1, '2' ); ?>>2</option>
<option value="2.5" <?php selected( $creteria_1, '2.5' ); ?>>2.5</option>
<option value="3" <?php selected( $creteria_1, '3' ); ?>>3</option>
<option value="3.5" <?php selected( $creteria_1, '3.5' ); ?>>3.5</option>
<option value="4" <?php selected( $creteria_1, '4' ); ?>>4</option>
<option value="4.5" <?php selected( $creteria_1, '4.5' ); ?>>4.5</option>
<option value="5" <?php selected( $creteria_1, '5' ); ?>>5</option>
</select>
</p>


<p>
<label for="creteria_2"><b><?php _e("Critera 2", 'funding'); ?></b></label>
<input type="text" name="creteria_2_text" id="creteria_2_text" value="<?php echo $creteria_2_text; ?>" />
</p>
<p>
<label for="creteria_2"><b><?php _e("Critera 2 Score", 'funding'); ?></b></label>

<select name="creteria_2" id="creteria_2">
<option value="0" <?php selected( $creteria_2, '0' ); ?>>0</option>
<option value="0.5" <?php selected( $creteria_2, '0.5' ); ?>>0.5</option>
<option value="1" <?php selected( $creteria_2, '1' ); ?>>1</option>
<option value="1.5" <?php selected( $creteria_2, '1.5' ); ?>>1.5</option>
<option value="2" <?php selected( $creteria_2, '2' ); ?>>2</option>
<option value="2.5" <?php selected( $creteria_2, '2.5' ); ?>>2.5</option>
<option value="3" <?php selected( $creteria_2, '3' ); ?>>3</option>
<option value="3.5" <?php selected( $creteria_2, '3.5' ); ?>>3.5</option>
<option value="4" <?php selected( $creteria_2, '4' ); ?>>4</option>
<option value="4.5" <?php selected( $creteria_2, '4.5' ); ?>>4.5</option>
<option value="5" <?php selected( $creteria_2, '5' ); ?>>5</option>
</select>

</p>


<p>
<label for="Critera_1"><b><?php _e("Critera 3", 'funding'); ?></b></label>
<input type="text" name="creteria_3_text" id="creteria_3_text" value="<?php echo $creteria_3_text; ?>" />
</p>
<p>
<label for="creteria_3"><b><?php _e("Critera 3 Score", 'funding'); ?></b></label>

<select name="creteria_3" id="creteria_3">
<option value="0" <?php selected( $creteria_3, '0' ); ?>>0</option>
<option value="0.5" <?php selected( $creteria_3, '0.5' ); ?>>0.5</option>
<option value="1" <?php selected( $creteria_3, '1' ); ?>>1</option>
<option value="1.5" <?php selected( $creteria_3, '1.5' ); ?>>1.5</option>
<option value="2" <?php selected( $creteria_3, '2' ); ?>>2</option>
<option value="2.5" <?php selected( $creteria_3, '2.5' ); ?>>2.5</option>
<option value="3" <?php selected( $creteria_3, '3' ); ?>>3</option>
<option value="3.5" <?php selected( $creteria_3, '3.5' ); ?>>3.5</option>
<option value="4" <?php selected( $creteria_3, '4' ); ?>>4</option>
<option value="4.5" <?php selected( $creteria_3, '4.5' ); ?>>4.5</option>
<option value="5" <?php selected( $creteria_3, '5' ); ?>>5</option>
</select>

</p>


<p>
<label for="creteria_4_text"><b><?php _e("Critera 4", 'funding'); ?></b></label>
<input type="text" name="creteria_4_text" id="creteria_4_text" value="<?php echo $creteria_4_text; ?>" />
</p>
<p>
<label for="creteria_4"><b><?php _e("Critera 4 Score", 'funding'); ?></b></label>

<select name="creteria_4" id="creteria_4">
<option value="0" <?php selected( $creteria_4, '0' ); ?>>0</option>
<option value="0.5" <?php selected( $creteria_4, '0.5' ); ?>>0.5</option>
<option value="1" <?php selected( $creteria_4, '1' ); ?>>1</option>
<option value="1.5" <?php selected( $creteria_4, '1.5' ); ?>>1.5</option>
<option value="2" <?php selected( $creteria_4, '2' ); ?>>2</option>
<option value="2.5" <?php selected( $creteria_4, '2.5' ); ?>>2.5</option>
<option value="3" <?php selected( $creteria_4, '3' ); ?>>3</option>
<option value="3.5" <?php selected( $creteria_4, '3.5' ); ?>>3.5</option>
<option value="4" <?php selected( $creteria_4, '4' ); ?>>4</option>
<option value="4.5" <?php selected( $creteria_4, '4.5' ); ?>>4.5</option>
<option value="5" <?php selected( $creteria_4, '5' ); ?>>5</option>
</select>

</p>


<p>
<label for="creteria_5_text"><b><?php _e("Critera 5", 'funding'); ?></b></label>
<input type="text" name="creteria_5_text" id="creteria_5_text" value="<?php echo $creteria_5_text; ?>" />
</p>
<p>
<label for="creteria_5"><b><?php _e("Critera 5 Score", 'funding'); ?></b></label>

<select name="creteria_5" id="creteria_5">
<option value="0" <?php selected( $creteria_5, '0' ); ?>>0</option>
<option value="0.5" <?php selected( $creteria_5, '0.5' ); ?>>0.5</option>
<option value="1" <?php selected( $creteria_5, '1' ); ?>>1</option>
<option value="1.5" <?php selected( $creteria_5, '1.5' ); ?>>1.5</option>
<option value="2" <?php selected( $creteria_5, '2' ); ?>>2</option>
<option value="2.5" <?php selected( $creteria_5, '2.5' ); ?>>2.5</option>
<option value="3" <?php selected( $creteria_5, '3' ); ?>>3</option>
<option value="3.5" <?php selected( $creteria_5, '3.5' ); ?>>3.5</option>
<option value="4" <?php selected( $creteria_5, '4' ); ?>>4</option>
<option value="4.5" <?php selected( $creteria_5, '4.5' ); ?>>4.5</option>
<option value="5" <?php selected( $creteria_5, '5' ); ?>>5</option>
</select>

</p>



<?php
}





add_action( 'save_post', 'cd_meta_box_save' );
function cd_meta_box_save( $post_id )
{
// Bail if we're doing an auto save
if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

// if our nonce isn't there, or we can't verify it, bail
if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return;

// if our current user can't edit this post, bail
if( !current_user_can( 'edit_post' ) ) return;

// now we can actually save the data
$allowed = array(
'a' => array( // on allow a tags
'href' => array() // and those anchords can only have href attribute
)
);

// Probably a good idea to make sure your data is set
if( isset( $_POST['creteria_1_text'] ) )
update_post_meta( $post_id, 'creteria_1_text', wp_kses( $_POST['creteria_1_text'], $allowed ) );

if( isset( $_POST['my_meta_box_select'] ) )
update_post_meta( $post_id, 'my_meta_box_select', esc_attr( $_POST['my_meta_box_select'] ) );

if( isset( $_POST['creteria_1'] ) )
update_post_meta( $post_id, 'creteria_1', esc_attr( $_POST['creteria_1'] ) );



if( isset( $_POST['creteria_2_text'] ) )
update_post_meta( $post_id, 'creteria_2_text', wp_kses( $_POST['creteria_2_text'], $allowed ) );

if( isset( $_POST['creteria_2'] ) )
update_post_meta( $post_id, 'creteria_2', esc_attr( $_POST['creteria_2'] ) );


if( isset( $_POST['creteria_3_text'] ) )
update_post_meta( $post_id, 'creteria_3_text', wp_kses( $_POST['creteria_3_text'], $allowed ) );

if( isset( $_POST['creteria_3'] ) )
update_post_meta( $post_id, 'creteria_3', esc_attr( $_POST['creteria_3'] ) );


if( isset( $_POST['creteria_4_text'] ) )
update_post_meta( $post_id, 'creteria_4_text', wp_kses( $_POST['creteria_4_text'], $allowed ) );

if( isset( $_POST['creteria_4'] ) )
update_post_meta( $post_id, 'creteria_4', esc_attr( $_POST['creteria_4'] ) );

if( isset( $_POST['creteria_5_text'] ) )
update_post_meta( $post_id, 'creteria_5_text', wp_kses( $_POST['creteria_5_text'], $allowed ) );

if( isset( $_POST['creteria_5'] ) )
update_post_meta( $post_id, 'creteria_5', esc_attr( $_POST['creteria_5'] ) );

}

// function for show rating content
//$key_1_value = get_post_meta($post->ID, 'my_meta_box_text', true);
?>