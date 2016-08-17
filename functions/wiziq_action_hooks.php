<?php

/*
 * Action hooks file 
 * @since 1.0
 */ 
 
 
/*
 * Action hook for joining class
 * @since 1.0
 */
add_action( 'init', 'wiziq_join_class' );
function wiziq_join_class () {
	if ( isset ($_REQUEST['subact']) && 'join_class' == $_REQUEST['subact'] && isset ($_REQUEST['action']) && "class_detail" == $_REQUEST['action'] && isset ($_REQUEST['class_id'] ) ) {
		$wiziq_class= new Wiziq_Classes;
		$wiziq_class->wiziq_add_attendees( $_GET['course_id'] , $_GET['class_id'] );
	}
}


/*
 * Function to join class for front end
 * @since 1.0
 */ 
add_action( 'wp', 'wiziq_frontend_join_class' );
function wiziq_frontend_join_class () {

if ( isset ($_REQUEST['subact']) && 'join_class' == $_REQUEST['subact'] && isset ($_REQUEST['caction']) && "view_front_class" == $_REQUEST['caction'] && isset ($_REQUEST['class_id'] ) && isset ($_REQUEST['course_id'] ) ) {
	
       
    global $wpdb;
    
    $user_ID = get_current_user_id();
    $courseid=$_REQUEST['course_id'];
    $tbname1 = $wpdb->prefix.'wiziq_enroluser';
$sqry1 = "select * from $tbname1 where user_id=$user_ID AND course_id=$courseid";
$result_enroll_info = $wpdb->get_results($sqry1);
    if(!empty($result_enroll_info)){
    $wiziq_class= new Wiziq_Classes;
	$wiziq_class->wiziq_add_attendees( $_GET['course_id'] , $_GET['class_id'] );
	}
        else{
            
            ?>

			<script>
				window.location = "<?php echo get_permalink(); ?>&nopermission";
			</script>
       <?php }
        
 }
}


/*
 * Function to delete users 
 * @since 1.0
 */ 
function wiziq_delete_enrolled_users( $user_id ) {
	global $wpdb;
	$wiziq_enroluser = $wpdb->prefix."wiziq_enroluser";
	$wpdb->query("delete from $wiziq_enroluser  where user_id = '$user_id' ");
	/*
	 * To do-----  delete classes and content 
	 */ 
}
add_action( 'delete_user', 'wiziq_delete_enrolled_users' );
