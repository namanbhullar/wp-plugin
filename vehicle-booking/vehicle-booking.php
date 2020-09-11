<?php
/**
 * Plugin Name: Vehicle Booking
 * Description: Simple vehicle booking system
 * Version: 1.0
 * Author: Gamdur Singh
 */




define( 'VEHICLE__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );



register_activation_hook( __FILE__, array( 'Vehicle', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'Vehicle', 'plugin_deactivation' ) );

require_once( VEHICLE__PLUGIN_DIR . 'class-vehicle-booking.php' );


add_action( 'init', array( 'Vehicle_booking', 'init' ) );
