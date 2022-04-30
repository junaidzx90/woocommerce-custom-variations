<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Woocv
 * @subpackage Woocv/public/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php
global $product;
$product_id = $product->get_id();
if(!$product_id){
    return;
}
$variation_data = $this->get_variation_by_product_id($product_id);
if(!$variation_data){
    return;
}
$title = $variation_data['title'];
$data = $variation_data['data'];
?>

<div id="woocv_variation">
    <h3 class="wcv_variation_title"><?php echo $title; ?></h3>

    <div class="wcv_fields">
        <input type="hidden" name="woocv_product_id" value="<?php echo $product_id ?>">
        <?php
        
        if(is_array($data) && sizeof($data) > 0){
            foreach($data as $field){
                $fieldId = $field['id'];
                $fieldTitle = $field['title'];
                $fieldsData = $field['fieldsData'];
                ?>
                <div class="wcv_field woocv_close">
                    <div class="wcv_field_top">
                        <p class="wcv_field_name"><?php echo $fieldTitle ?></p>
                        <span class="collapse_icon">▲</span>
                    </div>

                    <div class="wcv_field_items">
                        <?php
                        if(is_array($fieldsData) && sizeof($fieldsData) > 0){

                            $cleanFields = [];
                            foreach($fieldsData as $fi){
                                $t = $fi['type'];
                                $cleanFields[$t][] = $fi;
                            }

                            foreach($cleanFields as $field_type => $field_items){
                                switch ($field_type) {
                                    case 'empty_input':
                                        if(is_array($field_items)){
                                            foreach($field_items as $fitem){
                                                $item_id = $fitem['id'];
                                                $item_label = $fitem['label'];
                                                $item_placeholder = $fitem['placeholder'];
                                                $item_price = $fitem['price'];
                                                ?>
                                                <div class="wcv_empty_field">
                                                    <label for="<?php echo $item_id ?>"><?php echo $item_label ?></label>
                                                    <input data-id="<?php echo $item_id ?>" data-price="<?php echo $item_price ?>" name="woocv_inputs[<?php echo $fieldId ?>][<?php echo $item_id ?>]" id="<?php echo $item_id ?>" type="text" placeholder="<?php echo $item_placeholder ?>">
                                                </div>
                                            <?php
                                            }
                                        }
                                        break;
                                    case 'available_color':
                                        if(is_array($field_items)){
                                            foreach($field_items as $fitem){
                                                $item_id = $fitem['id'];
                                                $item_label = $fitem['label'];

                                                if(array_key_exists('availableColors', $fitem)){
                                                    $item_availableColors = $fitem['availableColors'];
                                                    $item_availableColors = wp_list_pluck( $item_availableColors, 'value' );
                                                    $item_availableColors = implode(",", $item_availableColors);
                                                    $item_price = $fitem['price'];

                                                    ?>
                                                    <div class="wcv_color_field">
                                                        <label><?php echo $item_label ?></label>
                                                        <button data-id="<?php echo $item_id ?>" data-price="<?php echo $item_price ?>" data-support="<?php echo $item_availableColors ?>" class="woocv_available_color"></button>
                                                        <span class="clearColor">Clear</span>
                                                        <input type="hidden" class="available_color_v" value="" name="woocv_inputs[<?php echo $fieldId ?>][<?php echo $item_id ?>]" id="<?php echo $item_id ?>">
                                                    </div>
                                                    <?php
                                                }
                                            }
                                        }
                                        break;
                                    case 'button_show':
                                        echo '<div class="wcv_btn_fields">';
                                        if(is_array($field_items)){
                                            foreach($field_items as $fitem){
                                                $item_id = $fitem['id'];
                                                $item_label = $fitem['label'];
                                                $item_price = $fitem['price'];
                                                ?>
                                                <button data-id="<?php echo $item_id ?>" data-price="<?php echo $item_price ?>" class="woocv_btns woocv_text_btn"><?php echo $item_label ?></button>
                                                <?php
                                            }
                                        }
                                        echo '<input type="hidden" data-tempid="'.time().'" class="btnInpValue" name="woocv_inputs['.$fieldId.']['.$item_id.']" value=""></div>';
                                        break;
                                    case 'color_select':
                                        echo '<div class="wcv_color_btn_fields">';
                                        if(is_array($field_items)){
                                            foreach($field_items as $fitem){
                                                $item_id = $fitem['id'];
                                                $item_price = $fitem['price'];
                                                
                                                echo '<input type="text" style="width: 100%" data-price="'.$item_price.'" data-tempid="'.$item_id.'" class="colorInpValue" name="woocv_inputs['.$fieldId.']['.$item_id.']" value=""></div>';
                                            }
                                        }
                                        
                                        break;
                                    case 'longtext_field':
                                        if(is_array($field_items)){
                                            foreach($field_items as $fitem){
                                                $item_id = $fitem['id'];
                                                $item_label = $fitem['label'];
                                                $item_longtxt = $fitem['longtxt'];
                                                
                                                ?>
                                                <div class="wcv_texts_fields">
                                                    <h3 class="text_title"><?php echo stripcslashes($item_label) ?></h3>
                                                    <p><?php echo stripslashes($item_longtxt) ?></p>
                                                </div>
                                                <?php
                                            }
                                        }
                                        break;
                                }
                                
                            }
                        }
                        ?>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>

    <div class="woocv_actions">
        <button class="calculate_woocv_total">Calculate total price</button>
        <div id="lr-loader-loading">
            <svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="30px" height="30px" viewBox="0 0 40 40" enable-background="new 0 0 40 40" xml:space="preserve">
                <path opacity="0.2" fill="#000" d="M20.201,5.169c-8.254,0-14.946,6.692-14.946,14.946c0,8.255,6.692,14.946,14.946,14.946
                s14.946-6.691,14.946-14.946C35.146,11.861,28.455,5.169,20.201,5.169z M20.201,31.749c-6.425,0-11.634-5.208-11.634-11.634
                c0-6.425,5.209-11.634,11.634-11.634c6.425,0,11.633,5.209,11.633,11.634C31.834,26.541,26.626,31.749,20.201,31.749z"></path>
                <path fill="<?php echo ((get_option('lr_selected_color')) ? get_option('lr_selected_color') : '#00bcd4') ?>" d="M26.013,10.047l1.654-2.866c-2.198-1.272-4.743-2.012-7.466-2.012h0v3.312h0
                C22.32,8.481,24.301,9.057,26.013,10.047z">
                <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 20 20" to="360 20 20" dur="0.9s" repeatCount="indefinite"></animateTransform>
                </path>
            </svg>
        </div>
    </div>
</div>