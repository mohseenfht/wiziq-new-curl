<?php
/*
 * Add WiziQ Menu in wordpress
 */ 
 
 

 
function wiziq_menu() {
	add_menu_page( __('WizIQ','wiziq'),__('WizIQ','wiziq'),'manage_options','wiziq','wiziq_admin', plugins_url( 'images/w.png' , dirname(__FILE__) )  ,'21');
	add_submenu_page( 'wiziq', __( 'Courses' , 'wiziq' ), __( 'Courses' , 'wiziq' ), 'manage_options', 'wiziq', 'wiziq_admin' );  
	add_submenu_page( 'wiziq', __( 'Content' , 'wiziq' ), __( 'Content' , 'wiziq' ) , 'manage_options', 'wiziq_content', 'wiziq_content' ); 
	add_submenu_page( 'wiziq', __( 'Settings' , 'wiziq' ), __( 'Settings' , 'wiziq' ) , 'manage_options', 'wiziq_settings', 'wiziq_settings' ); 
        add_submenu_page( '', '', __('Create Pages', 'wiziq'), 'manage_options', 'wiziq_create_pages', 'wiziq_create_pages' ); 
        
        add_submenu_page( 'wiziq', __( 'Calendar' , 'wiziq' ), __( 'Calendar' , 'wiziq' ) , 'manage_options', 'wiziq_calendar', 'wiziq_calendar' );
	add_submenu_page( '', '', __('Enroll Users', 'wiziq'), 'manage_options', 'wiziq_enroll', 'wiziq_enroll_users' ); 
	add_submenu_page( '', '', __('WizIQ Class', 'wiziq'), 'manage_options', 'wiziq_class', 'wiziq_class' ); 
}
add_action('admin_menu','wiziq_menu');

//Display content on Courses page
function wiziq_admin(){
	require_once( 'wiziq_courses.php' );
}


/*
 * Function to display content menu page
 */ 
function wiziq_content () {
	require_once( 'wiziq_content.php');
}


/*
 * Function to display classes
 */ 

function  wiziq_class() {
	require_once( 'wiziq_class.php' );
}


/*
 * Function to enroll users in a course
 */ 
function wiziq_enroll_users () {
	require_once( 'wiziq_courses_enroll_users.php' );
}




//function to work on settings
function wiziq_settings() {
    $siteurl=get_site_url();
      echo'<h2 class="nav-tab-wrapper">';
            
   echo '<a href="?page=wiziq_settings&tab=display_options" class=" active nav-tab <?php echo $active_tab == "display_options" ? "nav-tab-active" : ""; ?> '.__('Settings','wiziq').'</a>'; 
 echo '<a href="?page=wiziq_create_pages&tab=display_options" class="nav-tab <?php echo $active_tab == "display_options" ? "nav-tab-active" : ""; ?> '.__('Create Pages','wiziq').'</a>'; 
 echo'</h2>';
    $the_page_id = get_option( 'my_plugin_page_id' );
    $course_pageurl=get_permalink($the_page_id);
    $coursepagetag="<a href='$course_pageurl'>Go Here > </a>";
	echo '<h2>'.__('WizIQ Credentials','wiziq').'</h2>';
        
	echo '<div class = "wrap" >';
                echo "<br>";

	echo '</div>';
	//Check For Valid Nonce and access key and secret key and then update
	if( isset ( $_POST['wiziq_settings'] )) {
		if ( ! empty( $_POST['access_key'] ) && ! empty( $_POST['secret_key'] ) && check_admin_referer( 'update_settings', 'setting_nonce' ) ) {
			$access_key = trim($_POST['access_key']);
			$secret_key = trim($_POST['secret_key']);
			$obj = new CheckapicredentailsClass();
                       // echo "sss";die;
                               if (isset($_POST['email_notification'] )) {
                                  
 				update_option( 'email_notification', 'YES' );
                                }
                                else{
                                   
                                update_option( 'email_notification', 'NO' );               
                                    
                                }
			$apiresponse = $obj->Checkapicredentails($secret_key,$access_key,get_option('recurring_api_url'));
			if($apiresponse == 1){
				update_option( 'access_key', $access_key );
				update_option( 'secret_key', $secret_key );

				echo '<div class = "updated" ><p><strong>'.__('Settings Updated','wiziq').'</strong></p></div>';
			} else {
				echo '<div class="error"><p><strong>'.__('ERROR','wiziq').'</strong>: '.$apiresponse['code'].' , '.$apiresponse['msg'].'</p></div>';
				echo '<div class="error"><p><strong>'.__('ERROR','wiziq').'</strong>: '.__('Please Enter Correct Access Key & Secret Key','wiziq').'</p></div>';
			}
		} 
	}
	wiziq_settings_form();
        
                echo '<h2 style="color:red;">'.__('Important Note *','wiziq').'</h2>';
        echo '<h5>'.__('1.  Turn on allow_url_fopen in PHP.ini file.','wiziq').'</h5>';
        echo '<h5>'.__('2.  Set value of max_input_vars to  50000 in PHP.ini file.','wiziq').'</h5>';

        echo "<br>";

}


function wiziq_create_pages() {
    $siteurl=get_site_url();
    if(isset($_GET['page'])){
        $active_tab='wiziq_create_pages';
        
        
    }
      echo'<h2 class="nav-tab-wrapper">';
            
   echo '<a href="?page=wiziq_settings&tab=display_options" class="nav-tab <?php echo $active_tab == "display_options" ? "nav-tab-active" : ""; ?> '.__('Settings','wiziq').'</a>'; 
 echo '<a href="?page=wiziq_create_pages&tab=display_options" class="active nav-tab <?php echo $active_tab == "wiziq_create_pages" ? "nav-tab-active" : ""; ?> '.__('Create Pages','wiziq').'</a>'; 
 echo'</h2>';    
    wiziq_create_pages_form();
}


function wiziq_create_pages_form() { 
      global $wpdb;
    /* code for course listing page start  */
    if(isset($_POST['wiziq_course_page'])){
        if($_POST['course_page_title']!=""){
          $the_page_idd = get_option( 'my_plugin_page_id' ); 
   
 $paged_id_exist= get_page($the_page_idd);
          $the_page_title = $_POST['course_page_title'];
       
if(!$the_page_idd){
//echo "if"; die;
    $the_page_title = $_POST['course_page_title'];
    $the_page_name = $_POST['course_page_title'];

    // the menu entry...
    delete_option("my_plugin_page_title");
    add_option("my_plugin_page_title", $the_page_title, '', 'yes');
    // the slug...
    delete_option("my_plugin_page_name");
    add_option("my_plugin_page_name", $the_page_name, '', 'yes');
    // the id...
    delete_option("my_plugin_page_id");
    add_option("my_plugin_page_id", '0', '', 'yes');

    $the_page = get_page_by_title( $the_page_title );
    $the_page_idd = get_option( 'my_plugin_page_id' );

    if ( ! $the_page_idd ) {

        // Create post object
        $_p = array();
        $_p['post_title'] = $the_page_title;
        $_p['post_content'] = "[WizIQ]";
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        $_p['post_category'] = array(1); // the default 'Uncatrgorised'

        // Insert the post into the database
        $the_page_id = wp_insert_post( $_p );
echo "<p style='color:green'>Page has been created successfully</p>";
    }
    else {
        // the plugin may have been previously active and the page may just be trashed...

        $the_page_id = $the_page_idd;

        //make sure the page is not trashed...
        $the_page->post_title=$the_page_title;
        $the_page->post_status = 'publish';
        $the_page_id = wp_update_post( $the_page );
echo "<p style='color:green'>Page has been created successfully</p>";
    }

    delete_option( 'my_plugin_page_id' );
    add_option( 'my_plugin_page_id', $the_page_id );
    }
elseif(!empty($the_page_idd) && empty($paged_id_exist) ) {
       //echo "elseif";die;
            //delete_option("my_plugin_page_title");
    //delete_option("my_plugin_page_name");
    //delete_option("my_plugin_page_id");
    
        $the_page_title = $_POST['course_page_title'];
    $the_page_name = $_POST['course_page_title'];

    // the menu entry...
    delete_option("my_plugin_page_title");
    add_option("my_plugin_page_title", $the_page_title, '', 'yes');
    // the slug...
    delete_option("my_plugin_page_name");
    add_option("my_plugin_page_name", $the_page_name, '', 'yes');
    // the id...
    delete_option("my_plugin_page_id");
    add_option("my_plugin_page_id", '0', '', 'yes');

    $the_page = get_page_by_title( $the_page_title );
    $the_page_idd = get_option( 'my_plugin_page_id' );

    if ( ! $the_page_idd ) {

        // Create post object
        $_p = array();
        $_p['post_title'] = $the_page_title;
        $_p['post_content'] = "[WizIQ]";
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        $_p['post_category'] = array(1); // the default 'Uncatrgorised'

        // Insert the post into the database
        $the_page_id = wp_insert_post( $_p );
echo "<p style='color:green'>Page has been created successfully</p>";
    }
    else {
        // the plugin may have been previously active and the page may just be trashed...

        $the_page_id = $the_page_idd;

        //make sure the page is not trashed...
        $the_page->post_title=$the_page_title;
        $the_page->post_status = 'publish';
        $the_page_id = wp_update_post( $the_page );
        
        echo "<p style='color:green'>Page has been created successfully</p>";

    }

    delete_option( 'my_plugin_page_id' );
    add_option( 'my_plugin_page_id', $the_page_id );
    
    
        
    } 
    else{
  //echo "else"; die;
          $my_post = array(
      'ID'           => $the_page_idd,
      'post_title'   => $the_page_title,
       'post_status' => 'publish',       
  );

// Update the post into the database
  wp_update_post( $my_post );
  update_option('my_plugin_page_title', $the_page_title );
  update_option('my_plugin_page_name', $the_page_title );
   echo "<p style='color:green'>Page has been updated successfully</p>";      
    }
    
    }
else{
      echo "<p style='color:red'>Page title required</p>";   
}    

    }

    
    /* code for course listing page end  */    
    
    
    
        /* code for signin page start  */
    if(isset($_POST['wiziq_signin_page'])){
     if($_POST['signin_page_title']!=""){  
          $the_page_idd = get_option( 'my_plugin_page_id1' );

          $the_page_title = $_POST['signin_page_title'];
          $paged_id_exist= get_page($the_page_idd);
       
if(!$the_page_idd){
//echo "if"; die;
    $the_page_title = $_POST['signin_page_title'];
    $the_page_name = $_POST['signin_page_title'];

    // the menu entry...
    delete_option("my_plugin_page_title1");
    add_option("my_plugin_page_title1", $the_page_title, '', 'yes');
    // the slug...
    delete_option("my_plugin_page_name1");
    add_option("my_plugin_page_name1", $the_page_name, '', 'yes');
    // the id...
    delete_option("my_plugin_page_id1");
    add_option("my_plugin_page_id1", '0', '', 'yes');

    $the_page = get_page_by_title( $the_page_title );
    $the_page_idd = get_option( 'my_plugin_page_id1' );

    if ( ! $the_page_idd ) {

        // Create post object
        $_p = array();
        $_p['post_title'] = $the_page_title;
        $_p['post_content'] = "[login_form]";
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        $_p['post_category'] = array(1); // the default 'Uncatrgorised'

        // Insert the post into the database
        $the_page_id = wp_insert_post( $_p );
echo "<p style='color:green'>Page has been created successfully</p>";
    }
    else {
        // the plugin may have been previously active and the page may just be trashed...

        $the_page_id = $the_page_idd;

        //make sure the page is not trashed...
        $the_page->post_title=$the_page_title;
        $the_page->post_status = 'publish';
        $the_page_id = wp_update_post( $the_page );
echo "<p style='color:green'>Page has been created successfully</p>";
    }

    delete_option( 'my_plugin_page_id1' );
    add_option( 'my_plugin_page_id1', $the_page_id );
    }
    
    elseif(!empty($the_page_idd) && empty($paged_id_exist) ) {
       //echo "elseif";die;
            //delete_option("my_plugin_page_title");
    //delete_option("my_plugin_page_name");
    //delete_option("my_plugin_page_id");
    

    $the_page_title = $_POST['signin_page_title'];
    $the_page_name = $_POST['signin_page_title'];
    // the menu entry...
    delete_option("my_plugin_page_title1");
    add_option("my_plugin_page_title1", $the_page_title, '', 'yes');
    // the slug...
    delete_option("my_plugin_page_name1");
    add_option("my_plugin_page_name1", $the_page_name, '', 'yes');
    // the id...
    delete_option("my_plugin_page_id1");
    add_option("my_plugin_page_id1", '0', '', 'yes');

    $the_page = get_page_by_title( $the_page_title );
    $the_page_idd = get_option( 'my_plugin_page_id1' );

    if ( ! $the_page_idd ) {

        // Create post object
        $_p = array();
        $_p['post_title'] = $the_page_title;
        $_p['post_content'] = "[login_form]";
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        $_p['post_category'] = array(1); // the default 'Uncatrgorised'

        // Insert the post into the database
        $the_page_id = wp_insert_post( $_p );
echo "<p style='color:green'>Page has been created successfully</p>";
    }
    else {
        // the plugin may have been previously active and the page may just be trashed...

        $the_page_id = $the_page_idd;

        //make sure the page is not trashed...
        $the_page->post_title=$the_page_title;
        $the_page->post_status = 'publish';
        $the_page_id = wp_update_post( $the_page );
echo "<p style='color:green'>Page has been created successfully</p>";
    }

    delete_option( 'my_plugin_page_id1' );
    add_option( 'my_plugin_page_id1', $the_page_id );
    
    
        
    }     
    
    else{
  //echo "else"; die;
          $my_post = array(
      'ID'           => $the_page_idd,
      'post_title'   => $the_page_title,
      'post_status' => 'publish',        
  );

// Update the post into the database
  wp_update_post( $my_post );
    update_option('my_plugin_page_title1', $the_page_title );
  update_option('my_plugin_page_name1', $the_page_title );
      echo "<p style='color:green'>Page has been updated successfully</p>";  
    }
    } 
else{
      echo "<p style='color:red'>Page title required</p>";   
}
    }
    /* code for signin page end  */    
    
    
    
            /* code for signup page start  */
    if(isset($_POST['wiziq_signup_page'])){
     if($_POST['signup_page_title']!=""){   
          $the_page_idd = get_option( 'my_plugin_page_id2' );

          $the_page_title = $_POST['signup_page_title'];
          $paged_id_exist= get_page($the_page_idd);
       
if(!$the_page_idd){
//echo "if"; die;
    $the_page_title = $_POST['signup_page_title'];
    $the_page_name = $_POST['signup_page_title'];

    // the menu entry...
    delete_option("my_plugin_page_title2");
    add_option("my_plugin_page_title2", $the_page_title, '', 'yes');
    // the slug...
    delete_option("my_plugin_page_name2");
    add_option("my_plugin_page_name2", $the_page_name, '', 'yes');
    // the id...
    delete_option("my_plugin_page_id2");
    add_option("my_plugin_page_id2", '0', '', 'yes');

    $the_page = get_page_by_title( $the_page_title );
    $the_page_idd = get_option( 'my_plugin_page_id2' );

    if ( ! $the_page_idd ) {

        // Create post object
        $_p = array();
        $_p['post_title'] = $the_page_title;
        $_p['post_content'] = "[cr_custom_registration]";
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        $_p['post_category'] = array(1); // the default 'Uncatrgorised'

        // Insert the post into the database
        $the_page_id = wp_insert_post( $_p );
echo "<p style='color:green'>Page has been created successfully</p>";
    }
    else {
        // the plugin may have been previously active and the page may just be trashed...

        $the_page_id = $the_page_idd;

        //make sure the page is not trashed...
        $the_page->post_title=$the_page_title;
        $the_page->post_status = 'publish';
        $the_page_id = wp_update_post( $the_page );
echo "<p style='color:green'>Page has been created successfully</p>";
    }

    delete_option( 'my_plugin_page_id2' );
    add_option( 'my_plugin_page_id2', $the_page_id );
    }
    
 elseif(!empty($the_page_idd) && empty($paged_id_exist) ) {
       //echo "elseif";die;
            //delete_option("my_plugin_page_title");
    //delete_option("my_plugin_page_name");
    //delete_option("my_plugin_page_id");
    

    $the_page_title = $_POST['signup_page_title'];
    $the_page_name = $_POST['signup_page_title'];
    // the menu entry...
    delete_option("my_plugin_page_title2");
    add_option("my_plugin_page_title2", $the_page_title, '', 'yes');
    // the slug...
    delete_option("my_plugin_page_name2");
    add_option("my_plugin_page_name2", $the_page_name, '', 'yes');
    // the id...
    delete_option("my_plugin_page_id2");
    add_option("my_plugin_page_id2", '0', '', 'yes');

    $the_page = get_page_by_title( $the_page_title );
    $the_page_idd = get_option( 'my_plugin_page_id2' );

    if ( ! $the_page_idd ) {

        // Create post object
        $_p = array();
        $_p['post_title'] = $the_page_title;
        $_p['post_content'] = "[cr_custom_registration]";
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        $_p['post_category'] = array(1); // the default 'Uncatrgorised'

        // Insert the post into the database
        $the_page_id = wp_insert_post( $_p );
echo "<p style='color:green'>Page has been created successfully</p>";
    }
    else {
        // the plugin may have been previously active and the page may just be trashed...

        $the_page_id = $the_page_idd;

        //make sure the page is not trashed...
        $the_page->post_title=$the_page_title;
        $the_page->post_status = 'publish';
        $the_page_id = wp_update_post( $the_page );
echo "<p style='color:green'>Page has been created successfully</p>";
    }

    delete_option( 'my_plugin_page_id2' );
    add_option( 'my_plugin_page_id2', $the_page_id );
    
    
        
    }     
    
    else{
  //echo "else"; die;
          $my_post = array(
      'ID'           => $the_page_idd,
      'post_title'   => $the_page_title,
       'post_status' => 'publish',        
  );

// Update the post into the database
  wp_update_post( $my_post );
      update_option('my_plugin_page_title2', $the_page_title );
  update_option('my_plugin_page_name2', $the_page_title );
     echo "<p style='color:green'>Page has been updated successfully</p>";   
    }
    
    } 
else{
      echo "<p style='color:red'>Page title required</p>";   
}
    }
    /* code for signup page end  */    
 
    
        /* code for calendar page start  */
    if(isset($_POST['wiziq_calendar_page'])){
       if($_POST['calendar_page_title']!=""){
        
          $the_page_idd = get_option( 'my_plugin_page_id3' );

          $the_page_title = $_POST['calendar_page_title'];
          $paged_id_exist= get_page($the_page_idd);
       
if(!$the_page_idd){
//echo "if"; die;
    $the_page_title = $_POST['calendar_page_title'];
    $the_page_name = $_POST['calendar_page_title'];

    // the menu entry...
    delete_option("my_plugin_page_title3");
    add_option("my_plugin_page_title3", $the_page_title, '', 'yes');
    // the slug...
    delete_option("my_plugin_page_name3");
    add_option("my_plugin_page_name3", $the_page_name, '', 'yes');
    // the id...
    delete_option("my_plugin_page_id3");
    add_option("my_plugin_page_id3", '0', '', 'yes');

    $the_page = get_page_by_title( $the_page_title );
    $the_page_idd = get_option( 'my_plugin_page_id3' );

    if ( ! $the_page_idd ) {

        // Create post object
        $_p = array();
        $_p['post_title'] = $the_page_title;
        $_p['post_content'] = "[fc_agenda_view]";
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        $_p['post_category'] = array(1); // the default 'Uncatrgorised'

        // Insert the post into the database
        $the_page_id = wp_insert_post( $_p );
echo "<p style='color:green'>Page has been created successfully</p>";
    }
    else {
        // the plugin may have been previously active and the page may just be trashed...

        $the_page_id = $the_page_idd;

        //make sure the page is not trashed...
        $the_page->post_title=$the_page_title;
        $the_page->post_status = 'publish';
        $the_page_id = wp_update_post( $the_page );
echo "<p style='color:green'>Page has been created successfully</p>";
    }

    delete_option( 'my_plugin_page_id3' );
    add_option( 'my_plugin_page_id3', $the_page_id );
    }
 elseif(!empty($the_page_idd) && empty($paged_id_exist) ) {
       //echo "elseif";die;
            //delete_option("my_plugin_page_title");
    //delete_option("my_plugin_page_name");
    //delete_option("my_plugin_page_id");
    

    $the_page_title = $_POST['calendar_page_title'];
    $the_page_name = $_POST['calendar_page_title'];
    // the menu entry...
    delete_option("my_plugin_page_title3");
    add_option("my_plugin_page_title3", $the_page_title, '', 'yes');
    // the slug...
    delete_option("my_plugin_page_name3");
    add_option("my_plugin_page_name3", $the_page_name, '', 'yes');
    // the id...
    delete_option("my_plugin_page_id3");
    add_option("my_plugin_page_id3", '0', '', 'yes');

    $the_page = get_page_by_title( $the_page_title );
    $the_page_idd = get_option( 'my_plugin_page_id3' );

    if ( ! $the_page_idd ) {

        // Create post object
        $_p = array();
        $_p['post_title'] = $the_page_title;
        $_p['post_content'] = "[fc_agenda_view]";
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        $_p['post_category'] = array(1); // the default 'Uncatrgorised'

        // Insert the post into the database
        $the_page_id = wp_insert_post( $_p );
echo "<p style='color:green'>Page has been created successfully</p>";
    }
    else {
        // the plugin may have been previously active and the page may just be trashed...

        $the_page_id = $the_page_idd;

        //make sure the page is not trashed...
        $the_page->post_title=$the_page_title;
        $the_page->post_status = 'publish';
        $the_page_id = wp_update_post( $the_page );
echo "<p style='color:green'>Page has been created successfully</p>";
    }

    delete_option( 'my_plugin_page_id3' );
    add_option( 'my_plugin_page_id3', $the_page_id );
    
    
        
    }    
    
    
    else{
  //echo "else"; die;
          $my_post = array(
      'ID'           => $the_page_idd,
      'post_title'   => $the_page_title,
         'post_status' => 'publish',           
  );

// Update the post into the database
  wp_update_post( $my_post );
        update_option('my_plugin_page_title3', $the_page_title );
  update_option('my_plugin_page_name3', $the_page_title );
       echo "<p style='color:green'>Page has been updated successfully</p>"; 
    }
    
     } 
else{
      echo "<p style='color:red'>Page title required</p>";   
}
    }
    
      /* code for calendar page end  */ 
    
    
    ?>
 <form method = "post" id= "course_page_form" >
			<table class = "form-table" >
				<tbody>
					<tr>
						<th scope = "row" ><label for = "course_page_title" ><?php _e('Create Course Listing Page', 'wiziq'); ?></label></th>
						<td><input class = "regular-text " type = "text"  name= "course_page_title" Placeholder="Page Title"   value ="<?php   $the_page_idd = get_option( 'my_plugin_page_id' ); echo get_the_title( $the_page_idd ) ?>"/> <input class= "button button-primary" type = "Submit" name = "wiziq_course_page" value="<?php _e('Save ','wiziq'); ?>" />  </td>
					</tr>
                                        
                        </table>
     
         </form>

 <form method = "post" id= "signin_page_form" >
			<table class = "form-table" >
				<tbody>
					<tr>
						<th scope = "row" ><label for = "signin_page_title" ><?php _e('Create Sign In Page', 'wiziq'); ?></label></th>
						<td><input class = "regular-text " type = "text"  name= "signin_page_title" Placeholder="Page Title"   value ="<?php   $the_page_idd = get_option( 'my_plugin_page_id1' ); echo get_the_title( $the_page_idd ) ?>"/> <input class= "button button-primary" type = "Submit" name = "wiziq_signin_page" value="<?php _e('Save ','wiziq'); ?>" />  </td>
					</tr>
                                        
                        </table>
     
         </form>



 <form method = "post" id= "signup_page_form" >
			<table class = "form-table" >
				<tbody>
					<tr>
						<th scope = "row" ><label for = "signup_page_title" ><?php _e('Create Sign Up Page', 'wiziq'); ?></label></th>
						<td><input class = "regular-text " type = "text"  name= "signup_page_title" Placeholder="Page Title"   value ="<?php   $the_page_idd = get_option( 'my_plugin_page_id2' ); echo get_the_title( $the_page_idd ) ?>"/> <input class= "button button-primary" type = "Submit" name = "wiziq_signup_page" value="<?php _e('Save ','wiziq'); ?>" />  </td>
					</tr>
                                        
                        </table>
     
  </form>


 <form method = "post" id= "calendar_page_form" >
			<table class = "form-table" >
				<tbody>
					<tr>
						<th scope = "row" ><label for = "calendar_page_title" ><?php _e('Create Calendar Page', 'wiziq'); ?></label></th>
						<td><input class = "regular-text " type = "text"   name= "calendar_page_title" Placeholder="Page Title"   value ="<?php   $the_page_idd = get_option( 'my_plugin_page_id3' ); echo get_the_title( $the_page_idd ) ?>"/> <input class= "button button-primary" type = "Submit" name = "wiziq_calendar_page" value="<?php _e('Save ','wiziq'); ?>" />  </td>
					</tr>
                                        
                        </table>
     
  </form>

<?php 

}


//settings form 
function wiziq_settings_form() {
	?>
		<form method = "post" id= "api-settings-form" >
			<?php wp_nonce_field('update_settings','setting_nonce'); ?>
			<table class = "form-table" >
				<tbody>
					<tr>
						<th scope = "row" ><label for = "api_url" ><?php _e('Class API URL', 'wiziq'); ?></label></th>
						<td><input class = "regular-text wiziq-text-disable" type = "text"  name= "api_url" disabled = "disabled"  value ="<?php echo get_option( 'api_url' ); ?>"/> </td>
					</tr>
					<tr>
						<th><?php _e('Recurring API URL', 'wiziq'); ?></th>
						<td><input class = "regular-text wiziq-text-disable" type = "text"  name= "recurring_api_url" disabled value ="<?php echo get_option( 'recurring_api_url' ); ?> "/> </td>
					</tr>
					<tr>
						<th><?php _e('Content API URL', 'wiziq'); ?></th>
						<td><input class = "regular-text wiziq-text-disable" type = "text"  name= "content_url" disabled value ="<?php echo get_option( 'content_url' ); ?>"/> </td>
					</tr>
					<tr>
						<th><?php _e('Access Key', 'wiziq'); ?></th>
						<td>
                                                       
							<input class = "regular-text" type = "text" id= "access_key" name= "access_key" value ="<?php echo get_option( 'access_key' ); ?>"/>
							<div class = "wiziq_error" id = "setting_access_key_err" ></div>
							<div class = "wiziq_hide" id = "setting_access_key_msg" ><?php _e('Please Enter Access Key','wiziq');?></div>
						</td>
					</tr>
                                       
					<tr>
						<th><?php _e('Secret Key', 'wiziq'); ?></th>
						<td>
                                                    
							<input class = "regular-text" type = "text"  id= "secret_key" name= "secret_key" value ="<?php echo get_option( 'secret_key' ); ?>"/>
							<div class = "wiziq_error" id = "setting_secret_key_err" ></div>
							<div class = "wiziq_hide" id = "setting_secret_key_msg" ><?php _e('Please Enter Secret Key','wiziq');?></div>
                                                         <p style="color:red">Don’t have WizIQ API key <a target="_blank" href="http://www.wiziq.com/api">get these</a></p>
						</td>
					</tr>
					<tr>
						<th><?php _e('Virtual Classroom Language XML', 'wiziq'); ?></th>
						<td><input class = "regular-text wiziq-text-disable" type = "text"  name= "content_language" disabled value ="<?php echo get_option( 'content_language' ); ?> "/> </td>
					</tr>
					<tr>
						<th><?php _e('Time Zone API URL', 'wiziq'); ?></th>
						<td><input class = "regular-text wiziq-text-disable" type = "text"  name= "content_language" disabled value ="<?php echo get_option( 'timezone_api_url' ); ?> "/> </td>
					</tr>
                                        
  					<tr>
						<th><?php _e('Enable Email Notification', 'wiziq'); ?></th>
                                                <?php $emailnotification = get_option( 'email_notification' ); ?>
                                                <td><input type="checkbox" name="email_notification" value="NO"<?php if($emailnotification=="YES"){ echo 'checked'; } ?> > Default: No </td>
					</tr>                                      
                                        
                                        
                                        
				</tbody>
			</table>
			<input class= "button button-primary" type = "Submit" name = "wiziq_settings" value="<?php _e('Save Changes','wiziq'); ?>" /> 
		</form>
	<?php
}

/* show fullcalendar in backend for admin */
function wiziq_calendar(){
    
  echo '<center><h1>'.__('Calendar','wiziq').'</h1></center>';
  echo '<div class = "wrap" >';  
    
  echo do_shortcode('[fc_agenda_view]');
   
  echo '</div>'; 
    
}