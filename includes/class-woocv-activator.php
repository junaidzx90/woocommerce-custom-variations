<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Woocv
 * @subpackage Woocv/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Woocv
 * @subpackage Woocv/includes
 * @author     junaidzx90 <admin@easeare.com>
 */
class Woocv_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$woocv_variations = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}woocv_variations` (
			`variation_id` INT NOT NULL AUTO_INCREMENT,
			`variation_title` VARCHAR(255) NOT NULL,
			`product_ids` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
			`show_infront` VARCHAR(20) NOT NULL,
			`fields_data` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
			`create_date` DATETIME NOT NULL,
			PRIMARY KEY (`variation_id`)) ENGINE = InnoDB";
			dbDelta($woocv_variations);
	}

}
