<?php
class Variations_List extends WP_List_Table
{
    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $action = $this->current_action();

        $data = $this->table_data();
        usort($data, array(&$this, 'sort_data'));

        $perPage = 10;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args(array(
            'total_items' => $totalItems,
            'per_page' => $perPage,
        ));

        $data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);
        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->items = $data;

    }

    public function get_cv_results()
    {
        try {
            global $wpdb;
            $variations = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}custom_variations", ARRAY_A);
            if (sizeof($variations) > 0) {
                return $variations;
            } else {
                return array();
            }
        } catch (\Throwable$th) {
            //throw $th;
        }
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'variation_title' => 'Variation Title',
            'product_category' => 'Category Name',
            'product_counts' => 'Number of products',
            'show_infront' => 'Show in front',
            'size_of_fields' => 'Number of fields',
            'create_date' => 'Create Date',
        );

        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        return array(
            'variation_title' => array('variation_title', true),
            'product_category' => array('product_category', true),
            'create_date' => array('create_date', true),
        );
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data()
    {
        $data = array();
        global $wpdb;

		$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}woocv_variations");
		if($results){
            foreach($results as $result){
                $variation_id = $result->variation_id;
                $show_infront = $result->show_infront;
                switch ($show_infront) {
                    case 'true':
                        $show_infront = "Enabled";
                        break;
                    case true:
                        $show_infront = "Enabled";
                        break;
                    default:
                        $show_infront = "Disabled";
                        break;
                }
                $variation_title = $result->variation_title;
                $category = $result->category;
                $products = unserialize($result->product_ids);
                $fields_data = $result->fields_data;
                $create_date = $result->create_date;

                $fields = [];
                if($fields_data){
                    $fields = unserialize(base64_decode($fields_data));
                }

                $fieldArr = array(
                    'variation_id' => $variation_id,
                    'variation_title' => $variation_title,
                    'product_category' => (($category) ? get_the_category_by_ID($category) : 'Specified Products'),
                    'product_counts' => ((is_array($products)) ? sizeof($products) : 0),
                    'show_infront' => $show_infront,
                    'size_of_fields' => ((is_array($fields)) ? sizeof($fields) : 0),
                    'create_date' => date("d/m/Y h:i A", strtotime($create_date)),
                );
                
                $data[] = $fieldArr;
            }
		}
        return $data;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'variation_title':
                return $item['variation_title'];
            case 'product_category':
                return $item['product_category'];
            case 'product_counts':
                return $item['product_counts'];
            case 'show_infront':
                return $item['show_infront'];
            case 'size_of_fields':
                return $item['size_of_fields'];
            case 'create_date':
                return $item['create_date'];
            default:
                return print_r($item, true);
        }
    }

    public function column_variation_title($item)
    {
        $actions = array(
            'edit' => sprintf('<a href="?page=%s&action=%s&variation=%s">Edit</a>', $_REQUEST['page'], 'edit', $item['variation_id']),
            'delete' => sprintf('<a href="?page=%s&action=%s&variation=%s">Delete</a>', $_REQUEST['page'], 'delete', $item['variation_id']),
        );

        return sprintf('%1$s %2$s', $item['variation_title'], $this->row_actions($actions));
    }

    public function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete',
        );
        return $actions;
    }

    public function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="variation[]" value="%s" />', $item['variation_id']
        );
    }

    // All form actions
    public function current_action()
    {
        global $wpdb;
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete') {
            if (isset($_REQUEST['variation'])) {
                if(is_array($_REQUEST['variation'])){
                    $ids = $_REQUEST['variation'];
                    foreach($ids as $variation_id){
                        $wpdb->query("DELETE FROM {$wpdb->prefix}woocv_variations WHERE variation_id = $variation_id");
                    }
                }else{
                    $variation_id = intval($_REQUEST['variation']);
                    $wpdb->query("DELETE FROM {$wpdb->prefix}woocv_variations WHERE variation_id = $variation_id");
                }
            }
        }
    }

    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data($a, $b)
    {
        // Set defaults
        $orderby = 'variation_id';
        $order = 'desc';

        // If orderby is set, use this as the sort column
        if (!empty($_GET['orderby'])) {
            $orderby = $_GET['orderby'];
        }

        // If order is set use this as the order
        if (!empty($_GET['order'])) {
            $order = $_GET['order'];
        }

        $result = strcmp($a[$orderby], $b[$orderby]);

        if ($order === 'asc') {
            return $result;
        }

        return -$result;
    }

} //class
