<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Woocv
 * @subpackage Woocv/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div id="woocv">
    <h2>Custom variation</h2>
    <hr>

    <form action="" method="post">
        <div class="wcv_contents">
            <div class="wcv_title__input">
                <input autocomplete="off" v-model="variation_title" required type="text" id="wcv_title" placeholder="Title">
            </div>

            <div class="woocv_enable">
                <h3>Disable/Enable</h3>
                <label class="switch">
                    <input v-model="variation_switch" type="checkbox">
                    <span class="woocv_slider round"></span>
                </label>
            </div>

            <div class="wcv_input">
                <h3>Products</h3>
                <div class="prduct_selection">

                    <select v-model="productSelection.type" @change="changeProduct()" id="wcv_product_selection">
                        <option value="single">Single Product</option>
                        <option value="multiple">Multiple Product</option>
                    </select>

                    <div  v-show="productSelection.type === 'single'" class="products_list">
                        <select required v-model="productSelection.data[0]" class="wcv_products">
                            <option value="">Select Product</option>
                            <?php
                                $product_ids = wc_get_products( array( 'return' => 'ids', 'limit' => -1 ) );
                                if(sizeof($product_ids) > 0){
                                    foreach($product_ids as $product_id){
                                        _e('<option value="'.$product_id.'">'.ucfirst(get_the_title($product_id)).'</option>', 'woocv');
                                    }
                                }
                            ?>
                        </select>
                    </div>

                    <div v-show="productSelection.type === 'multiple'" class="products_list">
                        <select required v-show="productSelection.type === 'multiple'" multiple v-model="productSelection.data" class="wcv_products">
                            <?php
                                $product_ids = wc_get_products( array( 'return' => 'ids', 'limit' => -1 ) );
                                if(sizeof($product_ids) > 0){
                                    foreach($product_ids as $product_id){
                                        _e('<option value="'.$product_id.'">'.ucfirst(get_the_title($product_id)).'</option>', 'woocv');
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <div id="variationFields">
                <h3 class="fieldsheading">Variation Fields</h3>

                <p>Current fields ( {{woocvFields.length}} )</p>
                <div id="vf_contents" class="vf_contents">
                    <!-- Field -->
                    <div v-for="field in woocvFields" :key="field.id" v-bind:data-id="field.id" class="wcv_filed datanone">
                        <div class="fieldHead">
                            <h4>{{field.title}} ({{field.fieldsData.length}})</h4>
                            <div class="field_action">
                                <div @click="collapsable_elements('field', event)" class="collapsable_btn">
                                    <span><i class="fa-solid fa-sort"></i></span>
                                </div>
                                <div @click="remove_field(field.id)" class="closefieldbtn">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                            </div>
                        </div>

                        <div class="fieldData">

                            <div class="__file_title">
                                <input v-model="field.title" type="text" placeholder="Field title">
                            </div>

                            <ul class="woocv_filed_items">

                                <li v-for="fitem in field.fieldsData" :key="fitem.id" v-bind:data-id="fitem.id" class="woocv_filed_item datanone">
                                    <div class="f__data">
                                        <div class="field_item_head">
                                            <h3 class="item_name">{{get_itemTypeFullText(fitem.type)}}</h3>
                                            
                                            <div class="fitemActions">
                                                <div @click="collapsable_elements('fieldItem', event)" class="collapsable_btn">
                                                    <span><i class="fa-solid fa-sort"></i></span>
                                                </div>
                                                <div @click="remove_field_item(field.id, fitem.id)" class="closefieldItembtn">
                                                    <i class="fas fa-times-circle"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="item_contents">
                                            <div class="item_row">
                                                <div class="item_data">
                                                    <label for="inputTypeSelect">Input type</label><br>
                                                    <select @change="changeFieldType(field.id, fitem.id)" v-model="fitem.type" id="inputTypeSelect">
                                                        <option value="unselected">Select type</option>
                                                        <option value="empty_input">Empty Input</option>
                                                        <option value="color_input">Color Input</option>
                                                        <option value="button_show">Button Show</option>
                                                        <option value="color_show">Color Show</option>
                                                        <option value="longtext_field">Long Text</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="item_row">
                                                <div class="item_data">
                                                    <div v-if="fitem.type === 'empty_input'" class="woocv_inputData">
                                                        <input class="item_value" type="text" v-model="fitem.label" placeholder="Label text">
                                                        <input class="item_value" type="text" v-model="fitem.placeholder" placeholder="Input Placeholder">
                                                        <input type="text" placeholder="Price" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" v-model="fitem.price" class="item_price">
                                                    </div>
                                                    <div v-if="fitem.type === 'color_input'" class="woocv_inputData">
                                                        <input class="item_value" type="text" v-model="fitem.label" placeholder="Label text">
                                                        <input type="text" placeholder="Price" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" v-model="fitem.price" class="item_price">
                                                    </div>
                                                    <div v-if="fitem.type === 'button_show'" class="woocv_inputData">
                                                        <input class="item_value" type="text" v-model="fitem.label" placeholder="Button text">
                                                        <input type="text" placeholder="Price" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" v-model="fitem.price" class="item_price">
                                                    </div>
                                                    <div v-if="fitem.type === 'color_show'" class="woocv_inputData">
                                                        <input type="color" v-model="fitem.color" id="inputColorshow" value="#000000">
                                                        <input class="item_value" type="text" v-model="fitem.label" placeholder="Color name">
                                                        <input type="text" placeholder="Price" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" v-model="fitem.price" class="item_price">
                                                    </div>
                                                    <div v-if="fitem.type === 'longtext_field'" class="woocv_inputData">
                                                        <input class="item_value" type="text" v-model="fitem.label" placeholder="Title">
                                                        <textarea v-model="fitem.longtxt" id="_item_long_txt"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                            </ul>

                            <div style="text-align: right;">
                                <button @click="add_woocv_field_item(field.id, event)" class="add_item_btn">Add Item</button>
                            </div>
                        </div>
                    </div>
                    <div class="nofield" v-if="woocvFields.length === 0">No Field found!</div>
                </div>
                <div style="text-align: right;">
                    <button @click="add_woocv_field(event)" class="add_field_btn">Add Field</button>
                </div>
            </div>
        </div>
        
        <button @click="save_woocv_form_data(event)" class="button-primary">Save variation</button>
    </form>

    <div v-if="isDisabled" id="lr-loader-loading">
        <svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="50px" height="50px" viewBox="0 0 40 40" enable-background="new 0 0 40 40" xml:space="preserve">
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