<?php
	/*
	 * function to handle request before page is loaded
	 */
	
	add_action('admin_init', 'wiziq_admin_request_hook');
	
	function wiziq_admin_request_hook () {
		/*
		 * Make objects of classes
		 */ 
		$wiziq_courses = new Wiziq_Courses;
		$wiziq_classes = new Wiziq_Classes;
		/*
		 * Courses functions 
		 */ 
		 
		if ( isset ( $_REQUEST['add_course'] )  && (isset ( $_POST['wiziq_add_course'] )) ) {
			$wiziq_courses->wiziq_add_course( $_POST , WIZIQ_COURSES_MENU."&success" );
		}elseif ( isset ($_REQUEST['delete_course'] ) && ! isset ($_POST['multiple_actions']) && ( isset ( $_REQUEST['course_id'] )) && isset ( $_REQUEST['wp_nonce'] ) ) {
			$wiziq_courses->wiziq_delete_course( $_REQUEST['wp_nonce'] , $_REQUEST['course_id'] ,WIZIQ_COURSES_MENU  );
		}
		/*
		 * Classes functions
		 */ 
		elseif ( isset ($_REQUEST['action']) && isset ($_REQUEST['course_id']) && isset ( $_REQUEST['wp_nonce'] )  && "add_class" == $_REQUEST['action'] && isset( $_POST['add_class_wiziq']) ) {
			$wiziq_classes->wiziq_add_classes ( $_POST) ;
		}else if ( isset ($_REQUEST['action']) && "delete_class" == $_REQUEST['action'] && isset ($_REQUEST['class_id'] ) ) {
			$wiziq_classes->wiziq_delete_single_class( $_REQUEST['class_id'] , WIZIQ_CLASS_MENU, $_REQUEST['course_id'] );
		}
	}
