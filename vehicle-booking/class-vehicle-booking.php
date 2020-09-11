<?php

class Vehicle_booking{


    private static $post_type =   'vehicle';
    private static $menu_tile   =   'Vehicle Booking';



    public static function init() {
        self::vehicle_booking_setup();

        add_action( 'admin_menu', array( 'Vehicle_booking', 'admin_menu' ) );
        add_action( 'add_meta_boxes', array( 'Vehicle_booking', 'vehicle_price_box' ) );
        add_action( 'save_post', array( 'Vehicle_booking', 'save_vehicle_price' ) );


        add_shortcode( 'vehicle-booking-form', array('Vehicle_booking','show_vehicle_booking_form') );

        add_action("wp_ajax_get_vehicle_list", array('Vehicle_booking','get_vehicle_list'));


        self::saveVehicleBookingData();


    }

    /**
     * Register the "vehicle" custom post type
     */
    public static  function vehicle_booking_setup() {

        register_post_type( 'vehicle', ['public' => true,'label'=>'Vehicle'] );


        $labels = [
            'name'              => _x('Vehicle Category', 'taxonomy general name'),
            'singular_name'     => _x('Vehicle Category', 'taxonomy singular name'),
            'search_items'      => __('Search Vehicle Category'),
            'all_items'         => __('All Vehicle Category'),
            'parent_item'       => __('Parent Vehicle Category'),
            'parent_item_colon' => __('Parent Vehicle Category:'),
            'edit_item'         => __('Edit Category'),
            'update_item'       => __('Update Category'),
            'add_new_item'      => __('Add New Vehicle Category'),
            'new_item_name'     => __('New Vehicle Category'),
            'menu_name'         => __('Category'),
        ];
        $args = [
            'hierarchical'      => true, // make it hierarchical (like categories)
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'vehicle-category'],
        ];
        register_taxonomy('vehicle-category', ['vehicle'], $args);





        flush_rewrite_rules();



    }



    public static function admin_menu() {

        add_submenu_page('edit.php?post_type=vehicle', 'Vehicle Bookings', 'Vehicle Bookings', 'manage_options', 'vehicle-booking',array( 'Vehicle_booking', 'Show_Vehicle_Bookings' ));

    }




    public function vehicle_price_box() {
        add_meta_box(
            'vehicle_price_box',
            __( 'Vehicle Price', 'price' ),
            array('Vehicle_booking','vehicle_price_options'),
            'vehicle',
            'side',
            'high'
        );
    }

    public function vehicle_price_options(){
        global $post;
        $price = get_post_custom($post->ID);
        $price  =   isset($price['vehicle_price'][0]) ? $price['vehicle_price'][0] : '';
        ?>
            <label>Price:</label><input name="vehicle_price" value="<?php echo $price; ?>" />
        <?php
    }

    public  function save_vehicle_price(){
        global $post;
        update_post_meta($post->ID, "vehicle_price", $_POST["vehicle_price"]);
    }



    public function show_vehicle_booking_form(){

        ob_start();
        require_once( VEHICLE__PLUGIN_DIR . 'templates/vehicle-booking-form.php' );
        return ob_get_clean();
    }

    public function get_vehicle_list(){

        if ( !wp_verify_nonce( $_REQUEST['nonce'], "vehicle_listing_nonce")) {
                exit("Invalid request ");
            }

        $termid =   $_POST['term_id'];

        $args = array(
            'post_type' => 'vehicle',
            'tax_query' => array(
                array(
                    'taxonomy' => 'vehicle-category',
                    'field'    => 'term_id',
                    'terms'    => $termid
                )
            )
        );

        $query = get_posts($args);
        $html = '<option value="">Select vehicle</option>';
        if($query){
            foreach ($query as $list){
                $price  =   get_post_meta($list->ID,'vehicle_price',true);
                $html   .= '<option value="'.$list->ID.'">'.$list->post_title.' - '.$price.'</option>';
            }
        }

        echo  json_encode(array('type'=>'success','html'=>$html));
        die();
    }


    public function saveVehicleBookingData(){

        if(isset($_POST['submit_booking']) && $_POST['submit_booking']!=''){

            if ( !wp_verify_nonce( $_REQUEST['nonce'], "vehicle_listing_nonce")) {
                exit("Invalid request ");
            }

            $arg = array(
                'post_status'=>'pending',
                'post_type'=>'vehicle_bookings',
                'post_title'=>$_POST['first_name'].' '.$_POST['last_name'],
                'post_content'=>$_POST['message'],
            );
            $last_id = wp_insert_post($arg);

            update_post_meta($last_id,'first_name',$_POST['first_name']);
            update_post_meta($last_id,'last_name',$_POST['last_name']);
            update_post_meta($last_id,'phone',$_POST['phone']);
            update_post_meta($last_id,'email',$_POST['email']);
            update_post_meta($last_id,'vehicle_type',$_POST['vehicle_type']);
            update_post_meta($last_id,'vehicle',$_POST['vehicle']);

            // send email to user
            wp_mail($_POST['email'],'Booking requested has been submitted by you','Booking requested has been submited by you');

            // send email to admin
            $namae = $_POST['first_name'].' '.$_POST['last_name'];
            wp_mail(get_option('admin_email'),'New Booking requested has been submitted','Booking requested has been submitted by '.$namae);


            wp_redirect($_POST['redirect_url']);
            exit();
        }

        if(isset($_POST['update_booking']) && $_POST['update_booking']!=''){

            if ( !wp_verify_nonce( $_REQUEST['nonce'], "vehicle_listing_nonce")) {
                exit("Invalid request ");
            }

            $oldpost    =   $post = get_post($_REQUEST['post_id']);

            $arg = array(
                'ID' => $_REQUEST['post_id'],
                'post_status'=> $_POST['post_status'],
                'post_title'=>$_POST['first_name'].' '.$_POST['last_name'],
                'post_content'=>$_POST['message'],
            );
            wp_update_post($arg);


            update_post_meta($_REQUEST['post_id'],'first_name',$_POST['first_name']);
            update_post_meta($_REQUEST['post_id'],'last_name',$_POST['last_name']);
            update_post_meta($_REQUEST['post_id'],'phone',$_POST['phone']);
            update_post_meta($_REQUEST['post_id'],'email',$_POST['email']);
            update_post_meta($_REQUEST['post_id'],'vehicle_type',$_POST['vehicle_type']);
            update_post_meta($_REQUEST['post_id'],'vehicle',$_POST['vehicle']);

            // send email to user
            wp_mail($_POST['email'],'Booking updated by admin','Booking updated by admin');

            if($oldpost->post_status!=$_POST['post_status']){
                wp_mail($_POST['email'],'Booking status updated by admin','Booking status updated by admin to '.$_POST['post_status']);
            }



            wp_redirect(admin_url('edit.php?post_type=vehicle&page=vehicle-booking'));
            exit();
        }

    }

    public function Show_Vehicle_Bookings(){

        ob_start();
        if(isset($_REQUEST['type']) && $_REQUEST['type']=='edit'){
            require_once( VEHICLE__PLUGIN_DIR . 'templates/vehicle-booking-edit.php' );
        }else{
            require_once( VEHICLE__PLUGIN_DIR . 'templates/vehicle-booking-list.php' );
        }

        echo ob_get_clean();
        exit();
    }


}

