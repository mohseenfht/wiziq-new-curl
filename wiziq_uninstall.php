<?php
/*
 * WiziQ uninstallation options
 */ 


/*
 * Function to delete the wiziq options on de-activation
 */  
function wiziq_uninstall_options() {
	delete_option('api_url');
	delete_option('recurring_api_url');
	delete_option('content_url');
	delete_option('access_key');
	delete_option('secret_key');
	delete_option('content_language');
	delete_option('timezone_api_url');
        delete_option('email_notification');
        
        
   global $wpdb;
/* code for course listing page start   */ 
    $the_page_title = get_option( "my_plugin_page_title" );
    $the_page_name = get_option( "my_plugin_page_name" );

    //  the id of our page...
    $the_page_id = get_option( 'my_plugin_page_id' );
    if( $the_page_id ) {

        //wp_delete_post( $the_page_id ); // this will trash, not delete
       wp_trash_post( $the_page_id );

    }

    delete_option("my_plugin_page_title");
    delete_option("my_plugin_page_name");
    delete_option("my_plugin_page_id");   
    
    /* code for course listing page end   */ 
    
    
    
    
    /* code for sign in page start   */ 
    $the_page_title = get_option( "my_plugin_page_title1" );
    $the_page_name = get_option( "my_plugin_page_name1" );

    //  the id of our page...
    $the_page_id = get_option( 'my_plugin_page_id1' );
    if( $the_page_id ) {

       // wp_delete_post( $the_page_id ); // this will trash, not delete
        wp_trash_post( $the_page_id );

    }

    delete_option("my_plugin_page_title1");
    delete_option("my_plugin_page_name1");
    delete_option("my_plugin_page_id1");   
    
    /* code for signin page end   */ 
    
    
    
    /* code for sign up page start   */ 
    $the_page_title = get_option( "my_plugin_page_title2" );
    $the_page_name = get_option( "my_plugin_page_name2" );

    //  the id of our page...
    $the_page_id = get_option( 'my_plugin_page_id2' );
    if( $the_page_id ) {

       // wp_delete_post( $the_page_id ); // this will trash, not delete
        wp_trash_post( $the_page_id );

    }

    delete_option("my_plugin_page_title2");
    delete_option("my_plugin_page_name2");
    delete_option("my_plugin_page_id2");   
    
    /* code for signup page end   */     
 
    
    
     /* code for calendar page start   */ 
    $the_page_title = get_option( "my_plugin_page_title3" );
    $the_page_name = get_option( "my_plugin_page_name3" );

    //  the id of our page...
    $the_page_id = get_option( 'my_plugin_page_id3' );
    if( $the_page_id ) {

       // wp_delete_post( $the_page_id ); // this will trash, not delete
        wp_trash_post( $the_page_id );

    }

    delete_option("my_plugin_page_title3");
    delete_option("my_plugin_page_name3");
    delete_option("my_plugin_page_id3");   
    
    /* code for calendar page end   */    
        
}

/*
 * Function to delete tables on plugin de-activation
 */ 
function wiziq_delete_tables() {
	global $wpdb;
	$wiziq_courses = $wpdb->prefix."wiziq_courses";
	$wiziq_wclasses = $wpdb->prefix."wiziq_wclasses";
	$wiziq_enroluser = $wpdb->prefix."wiziq_enroluser";
	$wiziq_contents = $wpdb->prefix."wiziq_contents";
	$wpdb->query("Drop TABLE $wiziq_courses");
	$wpdb->query("Drop TABLE $wiziq_wclasses");
	$wpdb->query("Drop TABLE $wiziq_enroluser");
	$wpdb->query("Drop TABLE $wiziq_contents");
}
