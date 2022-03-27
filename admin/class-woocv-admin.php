<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Woocv
 * @subpackage Woocv/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocv
 * @subpackage Woocv/admin
 * @author     junaidzx90 <admin@easeare.com>
 */
class Woocv_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( 'fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'jquery-ui', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocv-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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
		
		wp_enqueue_script( 'jquery-ui', plugin_dir_url( __FILE__ ) . 'js/jquery-ui.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'wcvVue', plugin_dir_url( __FILE__ ) . 'js/vue.min.js', array(), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocv-admin.js', array( 'jquery', 'wcvVue', 'jquery-ui' ), $this->version, true );

		$variation_id = null;
		if(isset($_REQUEST['variation']) && !empty($_REQUEST['variation'])){
			$variation_id = intval($_REQUEST['variation']);
		}

		wp_localize_script($this->plugin_name, "admin_ajax", array(
			'ajaxurl' 		=> admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce( 'woocvnonce' ),
			'variation_id' => $variation_id
		));

	}


	function woocv_admin_footer(){
		?>
		<script>
			jQuery(document).ready(function($) {
				
				
				
			});
			
		</script>
		<?php
	}

	function woocv_admin_menus(){
		add_menu_page( 'Custom Variations', 'Custom Variations', 'manage_options', 'variations', [$this, 'custom_variaton_html'], 'dashicons-editor-table', 45 );
		add_submenu_page( 'variations', 'Variations', 'Variations', 'manage_options', 'variations', [$this, 'custom_variaton_html'], null );
		add_submenu_page( 'variations', 'Add new', 'Add new', 'manage_options', 'new-variation', [$this, 'new_variation_page'], null );
		add_submenu_page( 'variations', 'Settings', 'Settings', 'manage_options', 'variation-settings', [$this, 'fn_variation_settings_page'], null );

		// options
		add_settings_section( 'custom_variation_settings_section', '', '', 'custom_variation_settings_page' );

		// Font size of title
		add_settings_field( 'font_size_of_title', 'Font size of title', [$this,'fn_font_size_of_title'], 'custom_variation_settings_page', 'custom_variation_settings_section');
		register_setting( 'custom_variation_settings_section', 'font_size_of_title');
		// Color of title
		add_settings_field( 'color_of_title', 'Color of title', [$this,'fn_color_of_title'], 'custom_variation_settings_page', 'custom_variation_settings_section');
		register_setting( 'custom_variation_settings_section', 'color_of_title');
		// Fields name font size
		add_settings_field( 'fields_name_font_size', 'Fields name font size', [$this,'fn_fields_name_font_size'], 'custom_variation_settings_page', 'custom_variation_settings_section');
		register_setting( 'custom_variation_settings_section', 'fields_name_font_size');
		// Fields name font size
		add_settings_field( 'fields_name_font_color', 'Fields name font color', [$this,'fn_fields_name_font_color'], 'custom_variation_settings_page', 'custom_variation_settings_section');
		register_setting( 'custom_variation_settings_section', 'fields_name_font_color');
		// Fields name font weight
		add_settings_field( 'fields_name_font_weight', 'Fields name font weight', [$this,'fn_fields_name_font_weight'], 'custom_variation_settings_page', 'custom_variation_settings_section');
		register_setting( 'custom_variation_settings_section', 'fields_name_font_weight');
		// Fields background
		add_settings_field( 'fields_background', 'Fields background', [$this,'fn_fields_background'], 'custom_variation_settings_page', 'custom_variation_settings_section');
		register_setting( 'custom_variation_settings_section', 'fields_background');
		// Decoration Color
		add_settings_field( 'field_decoration_color', 'Decoration Color', [$this,'fn_field_decoration_color'], 'custom_variation_settings_page', 'custom_variation_settings_section');
		register_setting( 'custom_variation_settings_section', 'field_decoration_color');
		// Calculate button color
		add_settings_field( 'calc_btn_color', 'Calculate button color', [$this,'fn_calc_btn_color'], 'custom_variation_settings_page', 'custom_variation_settings_section');
		register_setting( 'custom_variation_settings_section', 'calc_btn_color');
		// Calculate button bg color
		add_settings_field( 'calc_btn_bg', 'Calculate button background', [$this,'fn_calc_btn_bg'], 'custom_variation_settings_page', 'custom_variation_settings_section');
		register_setting( 'custom_variation_settings_section', 'calc_btn_bg');
	}

	function fn_font_size_of_title(){
		echo '<input type="number" name="font_size_of_title" value="'.get_option('font_size_of_title').'" placeholder="18px" id="font_size_of_title">';
	}

	function fn_color_of_title(){
		echo '<input type="color" name="color_of_title" value="'.((get_option('color_of_title')) ? get_option('color_of_title') : '#424242').'" id="color_of_title">';
	}

	function fn_fields_name_font_size(){
		echo '<input type="number" name="fields_name_font_size" placeholder="14" value="'.get_option('fields_name_font_size').'" id="fields_name_font_size">';
	}

	function fn_fields_name_font_color(){
		echo '<input type="color" name="fields_name_font_color" value="'.((get_option('fields_name_font_color')) ? get_option('fields_name_font_color') : '#6f6f6e').'" id="fields_name_font_color">';
	}
	
	function fn_fields_name_font_weight(){
		echo '<input type="number" name="fields_name_font_weight" placeholder="600" value="'.get_option('fields_name_font_weight').'" id="fields_name_font_weight">';
	}

	function fn_fields_background(){
		echo '<input type="color" name="fields_background" value="'.((get_option('fields_background')) ? get_option('fields_background') : '#f6f6f6').'" id="fields_background">';
	}

	function fn_field_decoration_color(){
		echo '<input type="color" name="field_decoration_color" value="'.((get_option('field_decoration_color')) ? get_option('field_decoration_color') : '#7ea565').'" id="field_decoration_color">';
	}

	function fn_calc_btn_color(){
		echo '<input type="color" name="calc_btn_color" value="'.((get_option('calc_btn_color')) ? get_option('calc_btn_color') : '#6f6f72').'" id="calc_btn_color">';
	}

	function fn_calc_btn_bg(){
		echo '<input type="color" name="calc_btn_bg" value="'.((get_option('calc_btn_bg')) ? get_option('calc_btn_bg') : '#f6f6f6').'" id="calc_btn_bg">';
	}

	function custom_variaton_html(){
		require_once plugin_dir_path( __FILE__ ).'partials/class-variations-table.php';
		$variation_list = new Variations_List();
		if(isset($_GET['page']) && $_GET['page'] === 'variations' && isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['variation'])){
			require_once plugin_dir_path( __FILE__ ).'partials/woocv-admin-display.php';
		}else{
			?>
			<div id="variation_list">
				<div class="woocv_edittop">
					<h3>Variations</h3>
					<a href="?page=new-variation" class="button-secondary">Add new</a>
				</div>
				<hr>
				<form action="" method="post">
					<?php
					$variation_list->prepare_items();
					$variation_list->display();
					?>
				</form>
			</div>
        	<?php
		}
	}

	function new_variation_page(){
		require_once plugin_dir_path( __FILE__ ).'partials/woocv-admin-display.php';
	}

	function fn_variation_settings_page(){
		?>
		<div class="variation-settings">
			<div class="setting_form">
				<h3>Variation Customize</h3>
				<hr>
				<form action="options.php" method="post">
					<?php
					settings_fields( 'custom_variation_settings_section' );
					do_settings_sections( 'custom_variation_settings_page' );
					?>
					<p>
						<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
						<a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=variation-settings&action=woocv-reset' ) ?>">Reset</a>
					</p>
				</form>
			</div>
		</div>
		<?php

		if(isset($_GET['page']) && $_GET['page'] === 'variation-settings' && isset($_GET['action']) && $_GET['action'] === 'woocv-reset'){
			delete_option( 'font_size_of_title' );
			delete_option( 'color_of_title' );
			delete_option( 'fields_name_font_size' );
			delete_option( 'fields_name_font_color' );
			delete_option( 'fields_name_font_weight' );
			delete_option( 'fields_background' );
			delete_option( 'field_decoration_color' );
			delete_option( 'calc_btn_color' );
			delete_option( 'calc_btn_bg' );

			wp_safe_redirect( admin_url( 'admin.php?page=variation-settings' ) );
		}
	}

	function get_woocv_form_data(){
		if(!wp_verify_nonce( $_GET['nonce'], 'woocvnonce' )){
			die("Invalid request!");
		}

		if(isset($_GET['variation_id']) && !empty($_GET['variation_id'])){
			$variation_id = intval($_GET['variation_id']);
			global $wpdb;
			$results = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}woocv_variations WHERE variation_id = $variation_id");
			if($results){
				$variation_title = $results->variation_title;
				$products = null;
				if($results->products){
					$products = unserialize(base64_decode($results->products));
				}
				$switch = $results->show_infront;
				switch ($switch) {
					case 'true':
						$switch = true;
						break;
					case 'false':
						$switch = false;
						break;
					default:
						$switch = false;
						break;
				}
				$fields_data = null;
				if($results->fields_data){
					$fields_data = unserialize(base64_decode($results->fields_data));
				}

				$data = [
					'variation_title' => $variation_title,
					'products' => $products,
					'switch' => $switch,
					'fields_data' => $fields_data
				];
				echo json_encode(array("success" => $data));
				die;
			}

			echo json_encode(array("error" => "No variation"));
			die;
		}

		echo json_encode(array("error" => "No variation"));
		die;
	}

	function save_woocv_data(){
		if(!wp_verify_nonce( $_POST['nonce'], 'woocvnonce' )){
			die("Invalid request!");
		}
		
		global $wpdb;
		if(isset($_POST['data'])){
			$data = $_POST['data'];

			$field_title = stripcslashes(sanitize_text_field( $data['title'] ));
			$switch = $data['switch'];

			$products = $data['products'];
			$product_ids = [];
			$category = $products['category'];

			if(is_array($products)){
				if(array_key_exists('products', $products)){
					foreach($products['products'] as $product_id){
						$product_ids[] = intval($product_id);
					} 
				}

				$products = serialize($products);
				$products = base64_encode($products);
			}

			$fields = $data['fields'];
			if(is_array($fields)){
				$fields = serialize($fields);
				$fields = base64_encode($fields);
			}
			
			if(isset($_POST['variation_id']) && !empty($_POST['variation_id'])){
				// Update
				$variation_id = intval($_POST['variation_id']);
				$wpdb->update($wpdb->prefix.'woocv_variations', array(
					'variation_title' => $field_title,
					'products' => $products,
					'product_ids' => serialize($product_ids),
					'category' => $category,
					'show_infront' => $switch,
					'fields_data' => $fields,
				), array('variation_id' => $variation_id), array('%s', '%s', '%s', '%s', '%s', '%s'), array('%d'));

				echo json_encode(array("reload" => 'Success' ));
				die;
				
			}else{
				// Insert
				$wpdb->insert($wpdb->prefix.'woocv_variations', array(
					'variation_title' => $field_title,
					'products' => $products,
					'product_ids' => serialize($product_ids),
					'category' => $category,
					'show_infront' => $switch,
					'fields_data' => $fields,
					'create_date' => date("d/m/y h:i A")
				), array('%s', '%s', '%s', '%s', '%s', '%s','%s'));

				if(!is_wp_error( $wpdb )){
					$lastid = $wpdb->insert_id;

					echo json_encode(array("redirect" => admin_url( "admin.php?page=variations&action=edit&variation=$lastid" )));
					die;
				}
			}
		}

		echo json_encode(array("error" => "Eroor"));
		die;
	}
}
