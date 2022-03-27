<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Woocv
 * @subpackage Woocv/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Woocv
 * @subpackage Woocv/public
 * @author     junaidzx90 <admin@easeare.com>
 */
class Woocv_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocv_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocv_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( 'colorPick', plugin_dir_url( __FILE__ ) . 'css/colorPick.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocv-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocv_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocv_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( 'colorPick', plugin_dir_url( __FILE__ ) . 'js/colorPick.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocv-public.js', array( 'jquery', 'colorPick' ), $this->version, true );
		wp_localize_script($this->plugin_name, "woocv_ajax", array(
			'ajaxurl' 		=> admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce( 'woocvnonce' )
		));

	}

	function wp_head_scripts(){
		?>
		<style>
			:root{
				--font_size_of_title: <?php echo ((get_option('font_size_of_title')) ? get_option('font_size_of_title') : '18').'px' ?>;
				--color_of_title: <?php echo ((get_option('color_of_title')) ? get_option('color_of_title') : '#424242') ?>;
				--fields_name_font_size: <?php echo ((get_option('fields_name_font_size')) ? get_option('fields_name_font_size') : '14').'px' ?>;
				--fields_name_font_color: <?php echo ((get_option('fields_name_font_color')) ? get_option('fields_name_font_color') : '#6f6f6e') ?>;
				--fields_name_font_weight: <?php echo ((get_option('fields_name_font_weight')) ? get_option('fields_name_font_weight') : '600') ?>;
				--fields_background: <?php echo ((get_option('fields_background')) ? get_option('fields_background') : '#f6f6f6') ?>;
				--field_decoration_color: <?php echo ((get_option('field_decoration_color')) ? get_option('field_decoration_color') : '#7ea565') ?>;
				--calc_btn_color: <?php echo ((get_option('calc_btn_color')) ? get_option('calc_btn_color') : '#6f6f72') ?>;
				--calc_btn_bg: <?php echo ((get_option('calc_btn_bg')) ? get_option('calc_btn_bg') : '#f6f6f6') ?>;
			}
		</style>
		<?php
	}

	function get_variation_by_product_id($product_id){
		if(!$product_id){
			return;
		}
		global $wpdb;

		$results = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}woocv_variations WHERE product_ids LIKE '%%$product_id;%%'");

		if(!$results){
			$terms = wp_list_pluck(get_the_terms( $product_id, 'product_cat', array('field' => 'ids')), 'term_id');
			$terms = implode(",",$terms);
			$results = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}woocv_variations WHERE category IN($terms)");
		}
		
		if($results){
			$switch = $results->show_infront;
			if($switch === "true" || $switch === true){
				$variation_title = $results->variation_title;
				
				$fields_data = null;
				if($results->fields_data){
					$fields_data = unserialize(base64_decode($results->fields_data));
				}

				return array(
					'title' => $variation_title,
					'data' => $fields_data
				);
			}
		}
	}

	function woocv_variations(){
		require_once plugin_dir_path( __FILE__ ).'partials/woocv-public-display.php';
	}

	function get_woo_price(){
		if(isset($_GET['product_id']) && isset($_GET['custom_price'])){
			$product = wc_get_product(intval($_GET['product_id']));
			$custom_price = intval($_GET['custom_price']);
			
			$price_html = '<div class="product-price">';
			if ( $product->get_price() > 0 ) {

				$regular_price = $product->get_regular_price();
				$sale_price = $product->get_sale_price();

				if($sale_price){
					$sale_price += $custom_price;
					$price_html .= '<bdi><del><bdi>'. ( ( is_numeric( $regular_price ) ) ? wc_price( $regular_price ) : $regular_price ) .'</bdi></del></bdi>&nbsp;';
					$price_html .= '<ins>'. ( ( is_numeric( $sale_price ) ) ? wc_price( $sale_price ) : $sale_price ) .'</ins>';
				}else{
					$regular_price += $custom_price;
					$price_html .= '<ins>' . ( ( is_numeric( $regular_price ) ) ? wc_price( $regular_price ) : $regular_price ) . '</ins>';
				}

			}else{
				$price_html .= '<div class="free">Free</div>';
			}
			$price_html .= '</div>';
			echo $price_html;;
			die;
		}
    }

	function get_field_data_by_ids($product_id, $field_id){
		if(!$product_id){
			return;
		}
		global $wpdb;

		$results = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}woocv_variations WHERE product_ids LIKE '%%$product_id;%%'");

		if(!$results){
			$terms = wp_list_pluck(get_the_terms( $product_id, 'product_cat', array('field' => 'ids')), 'term_id');
			$terms = implode(",",$terms);
			$results = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}woocv_variations WHERE category IN($terms)");
		}
		
		if($results){
			$fields_data = null;
			if($results->fields_data){
				$fields_data = unserialize(base64_decode($results->fields_data));
			}

			if(is_array($fields_data)){
				foreach($fields_data as $field){
					if(array_key_exists('id', $field)){
						if(intval($field['id']) === intval($field_id)){
							return $field;
						}
					}
				}
			}
		}
	}

	function woocommerce_add_cart_item_data_process($cart_item_data, $product_id , $variation_id){
		global $wpdb;
		
		if(isset($_REQUEST['woocv_inputs'])){
			$data = $_REQUEST['woocv_inputs'];
			$values = []; //Final data
			
			if(is_array($data) && sizeof($data) > 0){
				foreach($data as $field_id => $field){
					$fielArr = $this->get_field_data_by_ids($product_id, $field_id); // Field data
					if($fielArr){
						$fieldata = $fielArr['fieldsData'];
						$fielTitle = stripslashes($fielArr['title']);
						$fielId = $fielArr['id'];

						foreach($field as $itemId => $itemValue){ // Submitted data
							if(!empty($itemValue)){
								if($fieldata && is_array($fieldata)){ // Stored data
									foreach($fieldata as $fdata){
										if(intval($fdata['id']) === intval($itemId)){
											$type = $fdata['type'];
											switch ($type) {
												case 'empty_input':
													$values[] = array(
														'id' => $fielId,
														'label' => $fielTitle,
														'value' => stripslashes($itemValue),
														'price' => floatval($fdata['price']),
														'type' => 'empty_input'
													);
													break;
												case 'color_input':
													$values[] = array(
														'id' => $fielId,
														'label' => $fielTitle,
														'value' => stripslashes($itemValue),
														'price' => floatval($fdata['price']),
														'type' => 'color_input'
													);
													break;
												case 'button_show':
													$fieldId = intval($itemValue);
													$btnsFieldIndex = array_search($fieldId, array_column($fieldata, 'id'));
		
													if($btnsFieldIndex || $btnsFieldIndex == "0"){
														$currentField = $fieldata[$btnsFieldIndex];
														$values[] = array(
															'id' => $fielId,
															'label' => $fielTitle,
															'value' => stripcslashes($currentField['label']),
															'price' => floatval($currentField['price']),
															'type' => 'button_show'
														);
													}
													
													break;
												case 'color_show':
													$fieldId = intval($itemValue);
													$colorsFieldIndex = array_search($fieldId, array_column($fieldata, 'id'));
													
													if($colorsFieldIndex || $colorsFieldIndex == "0"){
														$currentField = $fieldata[$colorsFieldIndex];
														
														$values[] = array(
															'id' => $fielId,
															'label' => $fielTitle,
															'value' => $currentField['color'],
															'name' => $currentField['label'],
															'price' => floatval($currentField['price']),
															'type' => 'color_show'
														);
													}
													
													break;
											}
											
										}
									}
								}
							}
						}
					}
				}
			}

			// Variation data
			$woocv_costs = 0;
			foreach($values as $vdata){
				$woocv_costs += $vdata['price'];
			}

			$woo_variation_price = 0;
			if(!empty($variation_id)){
				$variable_product = wc_get_product($variation_id);
				$woo_variation_price = floatval($variable_product->get_price());
			}

			$product = wc_get_product( $product_id );
			$price = $product->get_price();
			$price = $price+$woo_variation_price;
			$price = $price+$woocv_costs;

			$cart_item_data['woocv_price'] = $price;
			$cart_item_data['woocv_fields'] = $values;

			return $cart_item_data;
		}
	}

	function woocv_get_item_data( $item_data, $cart_item_data ) {
		if( isset( $cart_item_data['woocv_fields'] ) ) {
			global $readColor;
			
			$finalFields = [];
			foreach($cart_item_data['woocv_fields'] as $field){
				$type = $field['type'];
				$value = $field['value'];

				switch ($type) {
					case 'color_input':
						$value = ucfirst($readColor->name($value)['name']);
						break;
					case 'color_show':
						$value = $field['name'];
						break;
					default:
						$value = $field['value'];
						break;
				}

				$finalFields[$field['id']]['label'] = $field['label'];
				$finalFields[$field['id']]['values'][] = $value;
			}

			foreach($finalFields as $field){
				$label = $field['label'];
				$value = $field['values'];

				$item_data[] = array(
					'key' => $label,
					'value' =>  wc_clean( implode(", ", $value) )
				);
			}
		}
		return $item_data;
	}

	function woocv_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {
		if( isset( $values['woocv_fields'] ) ) {
			global $readColor;
			
			$finalFields = [];
			foreach($values['woocv_fields'] as $field){
				$type = $field['type'];
				$value = $field['value'];

				switch ($type) {
					case 'color_input':
						$value = ucfirst($readColor->name($value)['name']);
						break;
					case 'color_show':
						$value = $field['name'];
						break;
					default:
						$value = $field['value'];
						break;
				}

				$finalFields[$field['id']]['label'] = $field['label'];
				$finalFields[$field['id']]['values'][] = $value;
			}

			foreach($finalFields as $field){
				$label = $field['label'];
				$value = $field['values'];
				$value = wc_clean( implode(", ", $value) );

				$item->add_meta_data($label, $value, true);
			}
		}
	}
	
	function woocv_order_item_name( $product_name, $item ) {
		
		if( isset( $item['woocv_fields'] ) ) {
			global $readColor;
			
			$finalFields = [];
			foreach($item['woocv_fields'] as $field){
				$type = $field['type'];
				$value = $field['value'];

				switch ($type) {
					case 'color_input':
						$value = ucfirst($readColor->name($value)['name']);
						break;
					case 'color_show':
						$value = $field['name'];
						break;
					default:
						$value = $field['value'];
						break;
				}

				$finalFields[$field['id']]['label'] = $field['label'];
				$finalFields[$field['id']]['values'][] = $value;
			}

			foreach($finalFields as $field){
				$label = $field['label'];
				$value = $field['values'];
				$value = wc_clean( implode(", ", $value) );

				$product_name .= "<li>".$label.": ".$value."</li>";
			}
		}
		return $product_name;
	}
	
	function calculate_final_woocv_prices( $cart_object ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) )
			return;

		foreach ( $cart_object->get_cart() as $hash => $value ) {
			$cv_price = 0;
			if(array_key_exists('woocv_price', $value)){
				$cv_price = floatval($value['woocv_price']);
			}else{
				return;
			}
			
			$value['data']->set_price( $cv_price );
		}
	}
}
