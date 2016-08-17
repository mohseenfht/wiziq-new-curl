<?php 
/*
 * Add shortcode for frontend.
 * @since 1.0
 */ 



add_shortcode('WizIQ','wiziq_shortcode');

function wiziq_shortcode(){

include 'shortcode/shortcode_wiziq.php';

}

/*   ------------- Full Calendar  CSS, JS Files included Start----------------      */

add_action('wp_head','add_css_fullcalendar');

function add_css_fullcalendar(){
 wp_register_style( 'frontendstylesheet-fullcalendar', WIZIQ_PLUGINURL_PATH.'fullcalendar/fullcalendar.css' );
 wp_enqueue_style( 'frontendstylesheet-fullcalendar' );
 
 wp_register_style( 'customcss-fullcalendar', WIZIQ_PLUGINURL_PATH.'fullcalendar/fullcalendar_custom.css' );
 wp_enqueue_style( 'customcss-fullcalendar' );
 wp_enqueue_script( 'fullcalendar_moment_js', WIZIQ_PLUGINURL_PATH . 'fullcalendar/lib/moment.min.js');
 /*wp_enqueue_script( 'fullcalendar_jquery_js', WIZIQ_PLUGINURL_PATH . 'fullcalendar/lib/jquery.min.js');*/
 wp_enqueue_script( 'fullcalendar_min_js', WIZIQ_PLUGINURL_PATH . 'fullcalendar/fullcalendar.min.js');
 
  
 // sortable jquery
  wp_enqueue_style( 'sorttable_css', WIZIQ_PLUGINURL_PATH . 'sorttable/sorttable.css');
 wp_enqueue_script( 'sorttable_js', WIZIQ_PLUGINURL_PATH . 'sorttable/sorttable.js');
// sorttable jquery
 

 
}

/*   --------------Full Calendar  CSS, JS Files included End----------------  */

/*
 * Add css in front end 
 * @since 1.0
 */
add_action('wp_head','add_css_wiziqfront');

function add_css_wiziqfront(){
 wp_register_style( 'frontendstylesheet', WIZIQ_PLUGINURL_PATH.'stylesheet/frontend.css' );
 wp_enqueue_style( 'frontendstylesheet' );
 wp_register_style('jquery-ui-customsite-css', WIZIQ_PLUGINURL_PATH . 'stylesheet/jquery-ui-1.10.3.css');
 wp_enqueue_style( 'jquery-ui-customsite-css' ); 
 wp_enqueue_script('jquery');
 wp_enqueue_style( 'dashicons' );
 wp_enqueue_script('jquery-ui-tooltip');
 wp_enqueue_script('jquery-ui-datepicker');
 wp_enqueue_script( 'wiziq_front_js', WIZIQ_PLUGINURL_PATH . 'js/wiziq_front_custom.js'); 

}

//add_action( 'plugins_loaded', 'my_plugin_override' );
add_action( 'wp', 'my_plugin_override' );
add_action( 'wp', 'wiziq_custom_authentication' );
add_action( 'wp', 'is_user_administrator' );



function is_user_administrator(){
	if(is_user_logged_in()){
		global $current_user;
		get_currentuserinfo();
		if(in_array("administrator", $current_user->roles)){
			return true;
		}
		else {
			return false;
		}
	} else {
		return false;
	}
}


function wiziq_custom_authentication(){
	if(isset($_GET['action']) && $_GET['action'] == 'addcourse'){
		$auth = is_user_administrator();
		if( ! $auth ) {
			$rurl = $_SERVER['REDIRECT_URL'];
			?>
				<script>
				//	window.location = "<?php echo $rurl; ?>";
				</script>	 
			<?php
		}
	}
}

function remove_querystring_var($url, $key) { 
	$url = preg_replace('/(.*)(?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&'); 
	$url = substr($url, 0, -1); 
	return $url; 
}

require_once ('shortcode/wiziq_frontend_classes.php');
require_once ('shortcode/wiziq_frontend_courses.php');
require_once ('shortcode/wiziq_frontend_content.php');

/*
 * Function to check for special request which have to be completed before header loads
 * @since 1.0
 */ 
function my_plugin_override() {
	// condition for add course only for the administrator.	
	$wiziq_courses = new Wiziq_Courses;
	$wiziq_content = new Wiziq_Content;
	$wiziq_frontend_classes = new Wiziq_Frontend_Classes;
	$wiziq_frontend_content = new Wiziq_Frontend_Content;
	if(isset($_POST['wiziq_addfront_course']))	{
		if ( !is_user_administrator () ) {
			?>
			<script>
				window.location = "<?php echo get_permalink(); ?>";
			</script>
		<?php
		}
		$wiziq_courses->wiziq_add_course( $_POST , get_permalink() );
	}
	else if(isset($_GET['action']) && $_GET['action'] == 'editcourse' && $_GET['wn'] != ''){
		$course_id = $_GET['course_id'];
		if ( ! wp_verify_nonce( $_GET['wn'] , 'edit-course-'.$course_id  ) ) {
			?>
			<script>
				window.location = "<?php echo get_permalink(); ?>";
			</script>
		<?php
		}
	}
	else if ( isset ($_POST['wiziq_editfront_course']) && isset ( $_POST['course_id'] ) ) {
		$course_id = $_POST['course_id'];
		$wiziq_courses->wiziq_edit_course($course_id, $_POST , get_permalink() );
	}
	else if(isset($_GET['action']) && $_GET['action'] == 'deletecourse' && $_GET['wn'] != '' && !isset( $_GET['deleted'])){
		$course_id = $_GET['course_id'];
		if ( wp_verify_nonce( $_GET['wn'] , 'delete-course-'.$course_id  ) ) {
			$wiziq_courses->wiziq_delete_course ( $_GET['wn'], $course_id , get_permalink());
			?>
			<script>
				window.location = "<?php echo get_permalink(); ?>";
			</script>
			<?php
		} 
	}
	/*
	 * classes functionality
	 */
	 else if ( isset ($_GET['caction'] ) && isset ( $_GET['course_id'] ) && isset ( $_GET['wp_nonce'] ) && 'add_class' == isset ($_GET['caction'] ) && isset ($_POST['add_class_wiziq']) ) {
		 $returnerrormsg = $wiziq_frontend_classes->wiziq_frontend_add_class($_POST);
	 }else if ( isset ($_GET['caction']) && isset ($_GET['class_id']) && isset ( $_GET['course_id'] ) && isset ( $_GET['wp_nonce'] ) && 'edit_class' == $_GET['caction'] && isset ( $_POST['wiziq_edit_class'] ) ) {
		 $wiziq_frontend_classes->wiziq_frontend_update_class  ( $_POST, $_GET['class_id'] );
	 }
	 /*
	  * Content functionality
	  */
	else if ( isset ( $_GET['ccaction'] ) && "view_content" == $_GET['ccaction'] && isset ( $_GET['refresh_content'] ) && isset ( $_GET['parent'] ) ) {
		$wiziq_content->wiziq_refresh_content ( $_GET['parent'] );
	} else if ( isset ( $_GET['ccaction'] ) && "view_content" == $_GET['ccaction'] && isset ( $_GET [ 'wp_nonce' ] ) && isset ( $_GET ['delete_content'] )  ) {
		$wiziq_frontend_content->wiziq_frontend_delete_content ( $_GET ['delete_content'], $_GET['parent'] , $_GET [ 'wp_nonce' ] );
	} else if ( isset ($_POST['wiziq_front_add_content'] ) && isset ( $_GET ['ccaction'] ) && "add_content" == $_GET ['ccaction'] &&  isset ( $_GET ['parent'] ) && isset ( $_GET ['course_id'] ) ) {
		$wiziq_frontend_content->wiziq_frontend_add_content ( $_GET ['parent'] , $_POST );
	}
	
}





function custom_registration_function() {
    if (isset($_POST['submit'])) {
        registration_validation(
        $_POST['username'],
        $_POST['password'],
        $_POST['email'],
        $_POST['website'],
        $_POST['fname'],
        $_POST['lname'],
        $_POST['nickname'],
        $_POST['bio'],
        $_POST['role']               
		);
		
		// sanitize user form input
        global $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio;
        $username	= 	sanitize_user($_POST['username']);
        $password 	= 	esc_attr($_POST['password']);
        $email 		= 	sanitize_email($_POST['email']);
        $website 	= 	esc_url($_POST['website']);
        $first_name = 	sanitize_text_field($_POST['fname']);
        $last_name 	= 	sanitize_text_field($_POST['lname']);
        $nickname 	= 	sanitize_text_field($_POST['nickname']);
        $bio 		= 	esc_textarea($_POST['bio']);
        $role 	= 	sanitize_text_field($_POST['role']);

		// call @function complete_registration to create the user
		// only when no WP_error is found
        complete_registration(
        $username,
        $password,
        $email,
        $website,
        $first_name,
        $last_name,
        $nickname,
        $bio,
        $role        
		);
    }

    registration_form(
    	$username,
        $password,
        $email,
        $website,
        $first_name,
        $last_name,
        $nickname,
        $bio,
        $role    
		);
}



/* Registration Form  */

function registration_form( $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio, $role ) {
     global $wp_roles;

    //echo '<select name="role" class="input">';
    //foreach ( $wp_roles->roles as $key=>$value ):
      //echo '<option value="'.$key.'">'.$value['name'].'</option>';
    //endforeach;
   //echo '</select>';
   
    
    echo '
    <style>
	div {
		margin-bottom:2px;
	}
	
	input{
		margin-bottom:4px;
	}
        
       /* select {
            background: #f7f7f7 none repeat scroll 0 0;
            border: 1px solid #d1d1d1;
            border-radius: 2px;
            color: #686868;
            padding: 0.625em 0.4375em;
            width: 100%;
        } */
        
.pippin_form label { display: block; float: left; width: 130px; }
.pippin_form input[type="text"], .pippin_form input[type="password"],
.pippin_form input[type="email"] {
	padding: 4px 8px;
	background: #f0f0f0;
	border: 1px solid #ccc;
}
.pippin_form input[type="text"]:focus, .pippin_form input[type="password"]:focus,
.pippin_form input[type="email"]:focus {
	border-color: #aaa;
}
 
.pippin_errors { padding: 8px; border: 1px solid #f50; margin: 0 0 15px; }
	</style>
	';
 if (!is_user_logged_in()) { // show form if user is not already logged in
    echo '
    <form action="' . $_SERVER['REQUEST_URI'] . '" class="pippin_form" method="post">
	<div>
	<label class="label_reg" for="username">Username <strong>*</strong></label>
	<input type="text" name="username" value="' . (isset($_POST['username']) ? $username : null) . '">
	</div>
	
	<div>
	<label class="label_reg" for="password">Password <strong>*</strong></label>
	<input type="password" name="password" value="' . (isset($_POST['password']) ? null : null) . '">
	</div>
	
	<div>
	<label class="label_reg" for="email">Email <strong>*</strong></label>
	<input type="text" name="email" value="' . (isset($_POST['email']) ? $email : null) . '">
	</div>
	


	<div>
	<label class="label_reg" for="role">Role <strong>*</strong></label>
        <select class="input" name="role">
        <option value="">Select Role</option>             
       <!-- <option value="administrator">Administrator</option>
        <option value="editor">Editor</option><option value="author">Author</option>  -->  
        <option value="editor">Teacher</option>
        <option value="subscriber">Student</option>
        </select>
        </div>



	<div>
	<label class="label_reg" for="website">Website</label>
	<input type="text" name="website" value="' . (isset($_POST['website']) ? $website : null) . '">
	</div>
	
	<div>
	<label class="label_reg" for="firstname">First Name</label>
	<input type="text" name="fname" value="' . (isset($_POST['fname']) ? $first_name : null) . '">
	</div>
	
	<div>
	<label class="label_reg" for="website">Last Name</label>
	<input type="text" name="lname" value="' . (isset($_POST['lname']) ? $last_name : null) . '">
	</div>
	
	<div>
	<label class="label_reg" for="nickname">Nickname</label>
	<input type="text" name="nickname" value="' . (isset($_POST['nickname']) ? $nickname : null) . '">
	</div>
	
	<div>
	<label class="label_reg" for="bio">About / Bio</label>
	<textarea name="bio">' . (isset($_POST['bio']) ? $bio : null) . '</textarea>
	</div>
        

        
	<input type="submit" name="submit" value="Register"/>
	</form>
	';
 }
 else{
     
     echo 'You are already logged in. Please <a href="'.wp_logout_url( get_permalink() ).'">logout</a> if you want to signup';
 }
}

function registration_validation( $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio, $role )  {
    global $reg_errors;
    $reg_errors = new WP_Error;
    
     if ( empty( $username )){
      $reg_errors->add('field', 'Username field is missing');   
     } 
     
      if ( empty( $password )){
      $reg_errors->add('field', 'Password field is missing');   
     }

      if ( empty( $email )){
      $reg_errors->add('field', 'Email field is missing');   
     }
     
      if ( empty( $role )){
      $reg_errors->add('field', 'Select Role');   
     }     

   /* if ( empty( $username ) || empty( $password ) || empty( $email ) || empty( $role ) ) {
        $reg_errors->add('field', 'Required form field is missing');
    } */
if(!empty($username)){
    if ( strlen( $username ) < 4 ) {
        $reg_errors->add('username_length', 'Username too short. At least 4 characters is required');
    }
}
    if ( username_exists( $username ) )
        $reg_errors->add('user_name', 'Sorry, that username already exists!');
if(!empty($username)){
    if ( !validate_username( $username ) ) {
        $reg_errors->add('username_invalid', 'Sorry, the username you entered is not valid');
    }
}
if(!empty($password)){
    if ( strlen( $password ) < 5 ) {
        $reg_errors->add('password', 'Password length must be greater than 5');
    }
}
    
 if(!empty($email)){
    if ( !is_email( $email ) ) {
        $reg_errors->add('email_invalid', 'Email is not valid');
    }
}

    if ( email_exists( $email ) ) {
        $reg_errors->add('email', 'Email Already in use');
    }
    
    if ( !empty( $website ) ) {
        if ( !filter_var($website, FILTER_VALIDATE_URL) ) {
            $reg_errors->add('website', 'Website is not a valid URL');
        }
    }

    if ( is_wp_error( $reg_errors ) ) {

        foreach ( $reg_errors->get_error_messages() as $error ) {
            echo '<div style="color:red;">';
            echo '<strong>ERROR</strong>:';
            echo $error . '<br/>';

            echo '</div>';
        }
    }
}

function complete_registration($username, $password,$email, $website, $first_name, $last_name, $nickname, $bio, $role) {
    global $reg_errors, $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio, $role1;
    if ( count($reg_errors->get_error_messages()) < 1 ) {
        $userdata = array(
        'user_login'	=> 	$username,
        'user_email' 	=> 	$email,
        'user_pass' 	=> 	$password,
        'user_url' 	=>      $website,
        'first_name' 	=> 	$first_name,
        'last_name' 	=> 	$last_name,
        'nickname' 	=> 	$nickname,
        'description' 	=> 	$bio,
        //'wp_capabilities' 	=> $role           
		);
        //print_r($userdata);die;
        $user = wp_insert_user( $userdata );
        $my_user = new WP_User( $user );
$my_user->set_role( $role );
        //echo 'Registration complete. Goto <a href="' . get_site_url() . '/wp-login.php">login page</a>.'; 
        //echo 'Registration complete. Goto <a href="' . esc_url( get_permalink(8) ) . '">login page</a>.'; 
$username='';
$email=''; 
$password=''; 
$website=''; 
$first_name='';
$last_name=''; 
$nickname='';
$bio='';
//$role='';
         echo '<span style="color:green;">Registration Complete</span>';
          
	}
}

// Register a new shortcode: [cr_custom_registration]
add_shortcode('cr_custom_registration', 'custom_registration_shortcode');

// The callback function that will replace [book]
function custom_registration_shortcode() {
    ob_start();
    custom_registration_function();
    return ob_get_clean();
}



// user login form
function pippin_login_form() {

	if(!is_user_logged_in()) {
 
		global $pippin_load_css;
 
		// set this to true so the CSS is loaded
		$pippin_load_css = true;
 
		$output = pippin_login_form_fields();
	} else {
		// could show some logged in user info here
		$output = 'You are already logged in. Please <a href="'.wp_logout_url( get_permalink() ).'">logout</a> if you want to login again as other user';
	}
	return $output;
}
add_shortcode('login_form', 'pippin_login_form');



// login form fields
function pippin_login_form_fields() {
 
	ob_start(); ?>
		<h3 class="pippin_header"><?php //_e('Login'); ?></h3>
 
		<?php
		// show any error messages after form submission
		pippin_show_error_messages(); ?>
 
		<form id="pippin_login_form"  class="pippin_form" action="" method="post">
			<fieldset>
				<p>
					<label for="pippin_user_Login">Username</label>
					<input name="pippin_user_login" id="pippin_user_login" class="required" type="text"/>
				</p>
				<p>
					<label for="pippin_user_pass">Password</label>
					<input name="pippin_user_pass" id="pippin_user_pass" class="required" type="password"/>
				</p>
				<p>
					<input type="hidden" name="pippin_login_nonce" value="<?php echo wp_create_nonce('pippin-login-nonce'); ?>"/>
					<input id="pippin_login_submit" type="submit" value="Login"/>
				</p>
			</fieldset>
		</form>
	<?php
	return ob_get_clean();
}




// logs a member in after submitting a form
function pippin_login_member() {
 
	if(isset($_POST['pippin_user_login']) && wp_verify_nonce($_POST['pippin_login_nonce'], 'pippin-login-nonce')) {
 
		// this returns the user ID and other info from the user name
		$user = get_userdatabylogin($_POST['pippin_user_login']);
 
		if(!$user) {
			// if the user name doesn't exist
			pippin_errors()->add('empty_username', __('Invalid username'));
		}
 
		if(!isset($_POST['pippin_user_pass']) || $_POST['pippin_user_pass'] == '') {
			// if no password was entered
			pippin_errors()->add('empty_password', __('Please enter a password'));
		}
 
		// check the user's login with their password
		if(!wp_check_password($_POST['pippin_user_pass'], $user->user_pass, $user->ID)) {
			// if the password is incorrect for the specified user
			pippin_errors()->add('empty_password', __('Incorrect password'));
		}
 
		// retrieve all error messages
		$errors = pippin_errors()->get_error_messages();
 
		// only log the user in if there are no errors
		if(empty($errors)) {
 
			wp_setcookie($_POST['pippin_user_login'], $_POST['pippin_user_pass'], true);
			wp_set_current_user($user->ID, $_POST['pippin_user_login']);	
			do_action('wp_login', $_POST['pippin_user_login']);
 
			wp_redirect(home_url()); exit;
		}
	}
}
add_action('init', 'pippin_login_member');


// used for tracking error messages
function pippin_errors(){
    static $wp_error; // Will hold global variable safely
    return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
}

// displays error messages from form submissions
function pippin_show_error_messages() {
	if($codes = pippin_errors()->get_error_codes()) {
		echo '<div class="pippin_errors">';
		    // Loop error codes and display errors
		   foreach($codes as $code){
		        $message = pippin_errors()->get_error_message($code);
		        echo '<span class="error"><strong>' . __('Error') . '</strong>: ' . $message . '</span><br/>';
		    }
		echo '</div>';
	}	
}



// register our form css
function pippin_register_css() {
	wp_register_style('pippin-form-css', plugin_dir_url( __FILE__ ) . 'css/forms.css');
}
add_action('init', 'pippin_register_css');


// load our form css
function pippin_print_css() {
	global $pippin_load_css;
 
	// this variable is set to TRUE if the short code is used on a page/post
	if ( ! $pippin_load_css )
		return; // this means that neither short code is present, so we get out of here
 
	wp_print_styles('pippin-form-css');
}
add_action('wp_footer', 'pippin_print_css');


/** Start function - Full Calendar Agenda View Display */




function fullcalendar_agenda_view() {
    
    global $wpdb;
    if(is_user_logged_in()) {
   $user_id = get_current_user_id();
 if ( current_user_can( 'administrator' ) ) {
    //$request="select * from wp_wiziq_enroluser " ;
     $wiziq_wclasses = $wpdb->prefix."wiziq_wclasses";
     $request="select * from $wiziq_wclasses " ;
    $result = $wpdb->get_results($request); 
}
elseif(current_user_can( 'editor' ) || current_user_can('author') || current_user_can('contributor') ){ // editor as teacher
    //$request="select * from wp_wiziq_wclasses where created_by = $user_id " ;
   // $result= $wpdb->get_results($request);
    $wiziq_enroluser = $wpdb->prefix."wiziq_enroluser";
        $request="select * from $wiziq_enroluser where user_id = $user_id  " ;
    $result = $wpdb->get_results($request); 
        
}
else{ // for subscriber as student
    $wiziq_enroluser = $wpdb->prefix."wiziq_enroluser";
    $request="select * from $wiziq_enroluser where user_id = $user_id " ;
    $result = $wpdb->get_results($request); 
}

   $numcount=count($result);

    
   
   
   

                               

    ?>
                
                
  <script>
   
	jQuery(document).ready(function() {
		
		jQuery('#calendar').fullCalendar({
                    theme: true,
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			}, 
                                                                        
			//defaultDate: '2016-01-12',
			editable: true,
			eventLimit: true, // allow "more" link when too many events
			events: [ 
                            <?php 
                            for($i=0;$i<$numcount;$i++){
                            $courseid=$result[$i]->course_id;
                            $useridd=$result[$i]->user_id;   
                            $id=$result[$i]->id;
                            
          if ( current_user_can( 'administrator' ) ) {                       
                               $courseid=$result[$i]->courseid;
                               //$useridd=$result[$i]->user_id;
                                //$id=$result[$i]->id;
         }
         else{
             
                               $courseid=$result[$i]->course_id;
                               $useridd=$result[$i]->user_id;                          
         }                           
                            
                               
               if ( current_user_can( 'administrator' ) ) { 
                               $wiziq_wclasses = $wpdb->prefix."wiziq_wclasses";
                             $request1="select * from $wiziq_wclasses where id = $id " ;
                             $result1 = $wpdb->get_results($request1);
                               $class_id=$result1[0]->id;
                               $class_name=$result1[0]->class_name;
                               $class_time=$result1[0]->class_time;
             }             
               elseif( current_user_can( 'subscriber' )){
                   $wiziq_wclasses = $wpdb->prefix."wiziq_wclasses";
                 $request1="select * from $wiziq_wclasses where courseid = $courseid " ;
                             $result1 = $wpdb->get_results($request1);
                               $class_id=$result1[$i]->id;
                               $class_name=$result1[$i]->class_name;
                               $class_time=$result1[$i]->class_time;
                                
             } 
            else{
                
                $wiziq_wclasses = $wpdb->prefix."wiziq_wclasses";
                   $request1="select * from $wiziq_wclasses where courseid = $courseid OR created_by = $user_id AND courseid = $courseid   " ;
                  //echo "<pre>";
                  // print_r( $request1);
                             $result1 = $wpdb->get_results($request1);
                               $class_id=$result1[$i]->id;
                               $class_name=$result1[$i]->class_name;
                               $class_time=$result1[$i]->class_time;              
                
   
                
                $class_id=$result[$i]->id;
                $class_name=$result[$i]->class_name;
                $class_time=$result[$i]->class_time;
                
                
            }             
                               
               
             
             
             
                 if (is_admin()) { // to check calendar viewed from backend or frontend
                      $calendarfrom="backend";
                   }
                   else{                       
                   $calendarfrom="frontend";    
                   }             

                   
                foreach( $result1 as  $result1){ 
                    $courseid =$result1->courseid;
                                $class_id=$result1->id;
                               $class_name=$result1->class_name;
                               $class_time=$result1->class_time;                   
                        $wiziq_class = new Wiziq_Classes;
                        $Wiziq_Util = new Wiziq_Util;
			$res = $wiziq_class->wiziq_get_class_by_id ( $class_id );
                        $courses_url = get_permalink();
			$qvarsign = $Wiziq_Util->wiziq_frontend_url_structure();
                        $join_class_url = $courses_url.$qvarsign. "caction=view_front_class&course_id=".$courseid."&class_id=".$class_id."&subact=join_class";
                        $user_IDd = get_current_user_id();
                                ?>
                    	
                                {
                                        title:'<?php echo $class_name ; ?><?php  //if ( current_user_can( 'administrator' ) ) { echo " - $username"; } ?>',
					start: '<?php echo $class_time ; ?>',
                                        url:'<?php if($calendarfrom=="frontend"){ 
                                            if($res->created_by == $user_IDd) { echo $res->response_presenter_url; } else { echo $join_class_url;}
                                           
                                            }else{ echo $res->response_presenter_url; } ?>',
                                }
                                ,
                    
                            <?php 
                            } }
                            ?>         
                    
			],
                    eventRender: function(event, element) {
                        element.attr('title', event.title);
                    },
                    eventClick: function(event) {
                        if (event.url) {
                            window.open(event.url);
                            return false;
                        }
                    } 
		}); 
		
	});
</script>              
               
  <div id='calendar'></div>
  
<?php
    }
    else{
        echo "Please login to view the calendar";
    }
} 

/** End function - Full Calendar Agenda View Display */

 add_shortcode( 'fc_agenda_view', 'fullcalendar_agenda_view' );
 
 
 
 
 
 
 
 

?>
