<?php 
/* start function to get all courses */

function get_front_courses(){
global $wpdb;
$tbname = $wpdb->prefix.'wiziq_courses';
$sqry = "select * from $tbname order by id desc";
$result = $wpdb->get_results($sqry);
if(!empty($result)){ return $result;}
else { return false; }
}

/* close  function to get all courses */


function get_front_courses_forloggedin_user(){
   $user_id = get_current_user_id();
global $wpdb;

$tbname1 = $wpdb->prefix.'wiziq_enroluser';
$sqry1 = "select * from $tbname1 where user_id=$user_id";
$result1 = $wpdb->get_results($sqry1);

foreach($result1 as $result1){
   $courseid[]= $result1->course_id;
}

$courseidd=implode($courseid,',');
$tbname = $wpdb->prefix.'wiziq_courses';
if(current_user_can( 'subscriber' )){
 $sqry = "select * from $tbname where id in($courseidd) order by id desc";
}
elseif(current_user_can( 'editor' ) || current_user_can('author') || current_user_can('contributor')){
 $sqry = "select * from $tbname where id in($courseidd) OR created_by=$user_id order by id desc";
}
$result = $wpdb->get_results($sqry);

//die;
if(!empty($result)){ return $result;}
else { return false; }
}

/* start function to send mail to students when a new class is added from frontend */


function send_mail_to_student($courseid, $classid, $class_action,$oldclasstime,$newclasstime){
  // echo "test";die;
    $emailnotification = get_option( 'email_notification' );
    if($emailnotification=="YES"){
 
    if($class_action=="added"){
        $action="Added";        
    }
    elseif($class_action=="updated"){
            $action="Updated";            
    }
    elseif($class_action=="deleted"){
        $action="Deleted";        
        
    }
    //echo $action;die;
global $wpdb;
$tbname = $wpdb->prefix.'wiziq_wclasses ';
$sqry = "select * from $tbname where id=$classid ";
$result_class_info = $wpdb->get_results($sqry);

//print_r($result_class_info); die;
$class_name=$result_class_info[0]->class_name;
$class_time1=$result_class_info[0]->class_time;
$class_time1 = date("l, jS F, Y, h:i A ", strtotime($class_time1));
$class_timezone=$result_class_info[0]->classtimezone;
$class_time =$class_time1.' '.$class_timezone;

$tbname1 = $wpdb->prefix.'wiziq_enroluser';
$sqry1 = "select * from $tbname1 where course_id=$courseid";
$result_enroll_info = $wpdb->get_results($sqry1);
   //print_r($result_enroll_info); 

$tbname2 = $wpdb->prefix.'wiziq_courses';
$sqry2 = "select * from $tbname2 where id=$courseid ";
$result_course_info = $wpdb->get_results($sqry2);

$coursename=$result_course_info[0]->fullname;

foreach($result_enroll_info as $result_enroll_infoo){
    $userid=$result_enroll_infoo->user_id;
    $user_info = get_userdata($userid);
    
    $created_by=$result_class_info[0]->created_by;
    $user_infoo = get_userdata($created_by);
    
    
    //echo "<pre>";
    //print_r($user_info); 
if($action == "Added")
{
  $the_page_idd = get_option( 'my_plugin_page_id' );
   $pagevar= esc_url( get_permalink($the_page_idd) ); 
   
     if(get_option('permalink_structure') != ''){
         $claslinkk =  "<a href='$pagevar?caction=view_front_class&class_id=$classid&course_id=$courseid'>View Class </a>";
      } else {
         $claslinkk="<a href='$pagevar&caction=view_front_class&class_id=$classid&course_id=$courseid'>View Class </a>";
      }    
   
  $user_info1 = get_userdata(1);   
   $subject="An invitation to join a live, online class ";
  $mail= $user_info->user_email;
  
  $message="Hi $user_info->user_login, <br>
You have been invited to attend a live class. Refer to the details of the class below. <br>

<b>Class Details:</b> <br>
<b>Instructor:</b> $user_infoo->user_login <br>
<b>Title:</b> $class_name <br>
<b>Date & Time:</b> $class_time <br>
 $claslinkk <br>        
You will need a headset and a microphone for audio interaction. <br>
Sincerely,<br>
$user_info1->user_login ";


} else if($action == "Updated")
{
    $the_page_idd = get_option( 'my_plugin_page_id' );
  $pagevar= esc_url( get_permalink($the_page_idd) ); 
  
       if(get_option('permalink_structure') != ''){
         $claslinkk =  "<a href='$pagevar?caction=view_front_class&class_id=$classid&course_id=$courseid'>Enter the Class </a>";
      } else {
         $claslinkk="<a href='$pagevar&caction=view_front_class&class_id=$classid&course_id=$courseid'>Enter the Class </a>";
      }
  
  
    if($oldclasstime==$newclasstime){
    $user_info1 = get_userdata(1);   
     $subject= get_bloginfo( 'name' )." has updated class ";
     $mail= $user_info->user_email;      
     $message=" Hi $user_info->user_login, <br>
        ".get_bloginfo( 'name' )." has updated the class $class_name . You may use the same class link to enter the classroom at its changed date and time. <br>
        $claslinkk <br>
       Regards,<br>
        $user_info1->user_login "; 

}
elseif($oldclasstime!=$newclasstime){
    
      if(get_option('permalink_structure') != ''){
         $claslinkk =  "<a href='$pagevar?caction=view_front_class&class_id=$classid&course_id=$courseid'>Enter the Class </a>";
      } else {
         $claslinkk="<a href='$pagevar&caction=view_front_class&class_id=$classid&course_id=$courseid'>Enter the Class </a>";
      }   
     $user_info1 = get_userdata(1);   
     $subject= get_bloginfo( 'name' )." has rescheduled class ";
     $mail= $user_info->user_email;       
     $message=" Hi $user_info->user_login, <br>
        ".get_bloginfo( 'name' )." has rescheduled the class on.  $class_name to $class_time. You may use the same class link to enter the classroom at its changed date and time. <br>
         $claslinkk <br>
        Regards,<br>
        $user_info1->user_login ";
    
}
}else if($action == "Deleted"){
     $subject= $class_name." class cancelled ";
     $mail= $user_info->user_email;
  
     $user_info1 = get_userdata(1);   
     
    $message = "  Hi , <br>
    ".get_bloginfo( 'name' )." has cancelled the class on  $class_name scheduled for $class_time. <br>
    Regards, <br>
    $user_info1->user_login ";

}
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

   mail($mail,$subject,$message, $headers);
    
}

/* code to send mail to user who created the class */

 
    $created_by=$result_class_info[0]->created_by;
    $user_infoo = get_userdata($created_by);
    //echo "<pre>";
    //print_r($user_info); 

    $subject1="Class $action";
  $mail1= $user_infoo->user_email;
  
   
   if($action == "Added")
{
  $the_page_idd = get_option( 'my_plugin_page_id' );
   $pagevar= esc_url( get_permalink($the_page_idd) ); 
   
        if(get_option('permalink_structure') != ''){
         $claslinkk =  "<a href='$pagevar?caction=view_front_class&class_id=$classid&course_id=$courseid'>View Class </a>";
      } else {
         $claslinkk="<a href='$pagevar&caction=view_front_class&class_id=$classid&course_id=$courseid'>View Class </a>";
      }  
   
  $user_info1 = get_userdata(1);   
   $subject1="An invitation to join a live, online class ";
  $mail1= $user_infoo->user_email;
  
  $message1="Hi $user_infoo->user_login, <br>
You have been invited to attend a live class. Refer to the details of the class below. <br>

<b>Class Details:</b> <br>
<b>Instructor:</b> $user_infoo->user_login <br>
<b>Title:</b> $class_name <br>
<b>Date & Time:</b> $class_time <br>
 $claslinkk <br>        
You will need a headset and a microphone for audio interaction. <br>
Sincerely,<br>
$user_info1->user_login ";


} else if($action == "Updated")
{
    
     
    $the_page_idd = get_option( 'my_plugin_page_id' );    
    $pagevar= esc_url( get_permalink($the_page_idd) ); 
   
    
     if(get_option('permalink_structure') != ''){
         $claslinkk =  "<a href='$pagevar?caction=view_front_class&class_id=$classid&course_id=$courseid'>Enter the Class </a>";
      } else {
         $claslinkk="<a href='$pagevar&caction=view_front_class&class_id=$classid&course_id=$courseid'>Enter the Class </a>";
      } 
    
    if($oldclasstime==$newclasstime){
    $user_info1 = get_userdata(1);   
     $subject1= get_bloginfo( 'name' )." has updated class ";
     $mail1= $user_infoo->user_email;      
     $message1=" Hi $user_infoo->user_login, <br>
        ".get_bloginfo( 'name' )." has updated the class $class_name . You may use the same class link to enter the classroom at its changed date and time. <br>
        $claslinkk <br>
        Regards,<br>
        $user_info1->user_login "; 

}
elseif($oldclasstime!=$newclasstime){
    
     if(get_option('permalink_structure') != ''){
         $claslinkk =  "<a href='$pagevar?caction=view_front_class&class_id=$classid&course_id=$courseid'>Enter the Class </a>";
      } else {
         $claslinkk="<a href='$pagevar&caction=view_front_class&class_id=$classid&course_id=$courseid'>Enter the Class </a>";
      }
      
     $user_info1 = get_userdata(1);   
     $subject1= get_bloginfo( 'name' )." has rescheduled class ";
     $mail1= $user_infoo->user_email;       
     $message1=" Hi $user_infoo->user_login, <br>
        ".get_bloginfo( 'name' )." has rescheduled the class on.  $class_name to $class_time. You may use the same class link to enter the classroom at its changed date and time. <br>
         $claslinkk <br>
         Regards,<br>
        $user_info1->user_login ";
    
}
}else if($action == "Deleted"){
     $subject1= $class_name." class cancelled ";
     $mail1= $user_infoo->user_email;
  
     $user_info1 = get_userdata(1);   
     
    $message1 = "  Hi , <br>
    ".get_bloginfo( 'name' )." has cancelled the class on  $class_name scheduled for $class_time. <br>
    Regards, <br>
    $user_info1->user_login ";

}

$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
  mail($mail1,$subject1,$message1, $headers); 

  }
}
/*  end function */ 
?>
