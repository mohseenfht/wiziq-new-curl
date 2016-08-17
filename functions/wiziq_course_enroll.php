<?php
/*
 * This file contains functions for enroll users and access permissions
 * @since 1.0
 */

class Wiziq_Enroll_User {
    /*
     * Function to view enroll users and permissons
     * List function working from wordpress 3.1
     * @since 1.0
     */

    function wiziq_view_enroll_user() {
        global $wpdb;
        ?>         
        <h2><?php _e( 'Enroll Users for Course' , 'wiziq' ); ?></h2>
        <form method = "post" id= "enroll_users" >
            <input type="hidden" value="<?php echo $_REQUEST['course_id']; ?>" name="course_id"/>
            <?php
            global $wpdb;
            //get all admins
            $admins = get_users(array('role'=>"administrator"));
            foreach ($admins as $admin){ 
                $admin_ids[]=$admin->ID;
            }
            //all users excluding administator type
            $users = get_users(array('exclude' => $admin_ids));
            if(count($users)>0){
            $table_name = $wpdb->prefix . "wiziq_enroluser";
            $user_table = $wpdb->prefix . "users";
            $query = "SELECT t1.* , t2.user_login FROM $table_name AS t1, $user_table AS t2
                            WHERE t1.user_id = t2.ID AND t1.course_id =" . $_REQUEST['course_id'];
            $enrolled_users = $wpdb->get_results($query);
            ?>
            <table class= "wp-list-table widefat fixed pages" >
                <tbody>
                    <tr>
                        <td class="enroll-col"><?php _e('Enroll User','wiziq'); ?></td>
                        <td class="enroll-user-col">
                            <input value="" placeholder="Search..." id="s" type="text" /> 


                            <select name="enrolleduser" data-show-subtext="true" data-live-search="true" multiple size="10" class="wiziq-select selectpicker" id="wiziq-enroll-user">
                                <?php
                                $enrolled_ids = array();
                                if (count($enrolled_users) > 0) {
                                    foreach ($enrolled_users as $enrolled) {
										$user_info = get_userdata($enrolled->user_id);
										if(!empty($user_info->first_name)){
											$teacher_name = $user_info->first_name.' '.$user_info->last_name;
										} else	{
											$teacher_name = $user_info->display_name;
										}
										echo '<option data-subtext="' . $teacher_name . '" value="' . $enrolled->user_id . '" >' . $teacher_name . '</option>';
										$enrolled_ids[] = $enrolled->user_id;
                                    }
                                }
                                ?></select>
                            <input type="hidden" name="existing-users" value="<?php echo implode(",", $enrolled_ids); ?>"/>
                        </td>
                        <td class="action-col" style="text-align:center;">
                            <br/><br/><br/><br/><br/>
                            
                            <input type="button"  id="wiziq-add-user" value="<< <?php _e('Add','wiziq');?>" /><br/>
                            <input type="button" id="wiziq-remove-user" value="<?php _e('Remove','wiziq');?> >>" />
                            <br/><br/><br/>
                        </td>
                        <td class="enroll-all-col">
                            <input placeholder="Search..." id="allserach" type="text" />
                            <select name="alluser" multiple size="10" class="wiziq-select" id="wiziq-all-user"><?php
                        foreach ($users as $user) {
                            if (!in_array($user->ID, $enrolled_ids)) {
									$user_info = get_userdata($user->ID);
									if(!empty($user_info->first_name)){
										$teacher_name = $user_info->first_name.' '.$user_info->last_name;
									} else	{
										$teacher_name = $user_info->display_name;
									}
                                echo '<option value="' . $user->ID . '" >' . $teacher_name . '</option>';
							}
                        }
                        ?></select>
                        </td>
                        <?php 
							foreach ($users as $user) { 
								//removed on 24-nov-2014
								//$user_info = get_userdata($user->ID);
								//$user_rle = implode(', ', $user_info->roles);
								//if ( $user_rle == 'author' || $user_rle == 'editor') {
									//$role = "teacher";
								//} else {
									//$role = "student";
								//}
  /* start custom code to disable create class and upload content options for student or subscriber  */                                                                           
                                                                        
                        $user_info = get_userdata($user->ID);
			$user_rle = implode(', ', $user_info->roles);
			if ( $user_rle == 'editor' || $user_rle == 'author' || $user_rle == 'contributor') {
			$role = "teacher";
			} else {
			$role = "student";
			}   
                        
 /* end custom code to disable create class and upload content options for student or subscriber  */                                                                            
								echo '<input role = "'.$role.'" name= "user_permissions" type = "hidden" id= "user_role_'.$user->ID.'" value = "'.$role.'" />';
							}
							?>
                    </tr>
                </tbody>
            </table>
            <br/>
            <br/>
            <br/>
            <h2><?php _e('User Permissions','wiziq'); ?></h2>
            <table id="wiziq-course-permission"class= "wp-list-table widefat fixed pages" >
                <thead>
                    <tr>
                        <th><?php _e( 'User Name' , 'wiziq' );?></th>
                        <th>
							<?php _e( 'Create Class' , 'wiziq' );?>
							<!--<input class = "all_check" type = "checkbox" id = "create_class_all" />-->
						</th>
                        <th>
							<?php _e( 'View Recording','wiziq');?>
							<!--<input class = "all_check" type = "checkbox" id = "view_recording_all" />-->
						</th>
                        <th>
							<?php _e( 'Download Recording','wiziq' );?>
							<!--<input class = "all_check" type = "checkbox" id = "download_recording_all" />-->
						</th>
                        <th>
							<?php _e( 'Upload Content','wiziq' );?>
							<!--<input class = "all_check" type = "checkbox" id = "upload_content_all" />-->
						</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr colspan="5">
                        <td>
                        <?php
                        if(count($enrolled_users)==0){
                            _e('No users enrolled in this course','wiziq' );
                         }
                        ?>
                        </td>
                    </tr>
                </tfoot>
                <tbody>
                    <?php
                    if (count($enrolled_users) > 0) {
                        $tbody = array();
                        foreach ($enrolled_users as $enrolled) {
							
							$user_info = get_userdata($enrolled->user_id);
							$user_rle = implode(', ', $user_info->roles);
                                                       
							//removed on 24-nov-2014
							//if ( $user_rle == 'author' || $user_rle == 'editor') {
								//$user_return = true;
							//} else {
								//$user_return = false;
							//}
                        /* start custom code to disable create class and upload content options for student or subscriber  */               
                 					if ( $user_rle == 'editor'|| $user_rle == 'author' || $user_rle == 'contributor') {
								$user_return = true;
							} else {
								$user_return = false;
							} 
                                                      
                                                        
                      /* end custom code to disable create class and upload content options for student or subscriber */  
							if(!empty($user_info->first_name)){
								$teacher_name = $user_info->first_name.' '.$user_info->last_name;
							} else	{
								$teacher_name = $user_info->display_name;
							}
							$tbody[] = '<tr id=enroll_' . $enrolled->user_id . ' class="alternate iedit">';
							$tbody[] = '<td><input type="hidden" name="user_id[]" value="' . $enrolled->user_id . '" />' . $teacher_name . '</td>';
							$tbody[] = '<td><input type="hidden" value="0" name="create_class[' . $enrolled->user_id . ']" /><input value="1" type="checkbox" name="create_class[' . $enrolled->user_id . ']" class="create_class" id="create_class_' . $enrolled->user_id . '" ';
                            if ( $user_return && $enrolled->create_class == 1)
								$tbody[] = ' checked class="create_class"';
							else if ( !$user_return )
								$tbody[] = ' disabled class="create_class permission_disable"'; 
							$tbody[] = '/> </td><td><input type="hidden" value="0" name="view_recording[' . $enrolled->user_id . ']" /><input value="1" type="checkbox"  name="view_recording[' . $enrolled->user_id . ']" class="view_recording" ';
							if ($enrolled->view_recording == 1)
								$tbody[] = ' checked ';
							$tbody[] = '/> </td><td><input type="hidden" value="0" name="download_recording[' . $enrolled->user_id . ']" /><input value="1" type="checkbox" name="download_recording[' . $enrolled->user_id . ']" class="download_recording"';
							if ($enrolled->download_recording == 1)
								$tbody[] = ' checked ';
							$tbody[] = '/> </td><td><input type="hidden" value="0" name="upload_content[' . $enrolled->user_id . ']" /><input value="1" type="checkbox"  name="upload_content[' . $enrolled->user_id . ']"  class="upload_content" id="' . $enrolled->user_id . '" ';
							if ( $user_return && $enrolled->upload_content == 1)
								$tbody[] = ' checked class="upload_content"';
							else if ( !$user_return )
								$tbody[] = ' disabled class="upload_content permission_disable"'; 
							$tbody[] = '/> </td></tr>';
                        }
                        echo join("", $tbody);
                    }
                    ?>
                </tbody>
            </table>
            <br/>
            <input type="Submit" value="<?php _e( 'Save' , 'wiziq' ) ;?>" name="wiziq_enroll_users" id="wiziq_enroll_users" class="button button-primary">
             <a class="button button-primary" href="<?php echo admin_url('admin.php?page=wiziq');?>"><?php _e( 'Cancel' , 'wiziq' ) ;?></a>
        </form>
        <?php
            }else{
                ?>
        <p><?php _e('There are no user','wiziq');?>. <a href="<?php echo admin_url('user-new.php'); ?>"><?php _e('Add New','wiziq');?></a> </p>
        <?php
            }
    }// end enroll users function
    
    /*
     * function to save enrolled users and access permissions
     * @since 1.0
     */ 
    function wiziq_save_permission($enroll_data, $returnurl) {
        //echo "<pre>";
        //print_r($enroll_data);die;
        global $wpdb;
        $table_name = $wpdb->prefix . "wiziq_enroluser";
        $users = $enroll_data['user_id'];
        $create_class = $enroll_data['create_class'];
        $view_recording = $enroll_data['view_recording'];
        $download_recording = $enroll_data['download_recording'];
        $upload_content = $enroll_data['upload_content'];
        $query = "DELETE FROM " . $table_name . " WHERE `user_id` IN ( " . $enroll_data['existing-users'] . ") AND `course_id` =" . $enroll_data['course_id'] . ';';
        $wpdb->query($query);
        if(count($users)>0){
            $query = 'INSERT INTO ' . $table_name . ' (`user_id`, `course_id`, `create_class`, `edit_class`, `delete_class`, `view_recording`, `download_recording`, `upload_content`) VALUES';
            foreach ($users as $key => $value) {
                $query .= "($value, " . $enroll_data['course_id'] . ", $create_class[$value], $create_class[$value], $create_class[$value], $view_recording[$value], $download_recording[$value], $upload_content[$value])";
                if ($key != (count($users) - 1))
                    $query.=',';
            }
            $wpdb->query($query);
        }
        ?>
        <script>
            window.location="<?php echo $returnurl; ?>";
        </script>
        <?php
    }// end wiziq save permissions

}
