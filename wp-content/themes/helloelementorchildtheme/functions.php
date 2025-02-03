<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementorChild
 */

/**
 * Load child theme css and optional scripts
 *
 * @return void
 */

// Start a session
 function hello_elementor_child_enqueue_scripts() {
	$ver = rand(0,999999999);
	wp_enqueue_style('hello-elementor-child-style',get_stylesheet_directory_uri().'/style.css',['hello-elementor-theme-style'],$ver);
    
    // enqueue scripts
    // wp_enqueue_script('sp-child-script',get_stylesheet_directory_uri().'/script-ile-09-21-24.js',['jquery'],$ver);
    wp_enqueue_script('sp-child-script',get_stylesheet_directory_uri().'/script-ile-11-26-24.js',['jquery'],$ver);

    // only logged in users
    if($user_obj = wp_get_current_user()){        
        
        // user
        $user_id = get_current_user_id();        
        $courses_complete = false;
        $courses_total    = 0;

        // learndash
        // $course_enroll_meta = [
        //     1878 => 'level_one_enrolled_form_filled',
        //     2430 => 'foundation_enrolled_form_filled'
        // ];
        $course_enrolled_meta = 'enrolled_form_filled';    
        // $course_complete_meta = [
        //     1864 => 'level_one_graduated_form_filled',
        //     2437 => 'foundation_graduated_form_filled',
        //     2436 => 'refresh_graduated_form_filled'
        // ];
        $course_complete_meta = [
            1864 => 'graduate_form_filled',
            2437 => 'graduate_form_filled'
        ];
        $course_enroll_form = [
			1878 => '2e0a9398-b8a7-4ced-b4f7-3ac78620cc7e',
			2430 => 'c024fe69-9e74-4f2c-82f7-f6f55d6aa251'
		];
        $course_complete_form = [
			1864 => '759bace4-486c-4888-a52c-c541a9d2cf1d',
			2437 => 'c8006f15-cf56-4d77-b4a7-66e57d0fa75b',
			// 2436 => 'c8006f15-cf56-4d77-b4a7-66e57d0fa75b'
		];

        $courseId = (is_singular('sfwd-courses')) ? get_the_ID() : false;
        $lessonId = (is_singular('sfwd-lessons')) ? get_the_ID() : false;
        $courseId = (is_singular('sfwd-lessons')) ? learndash_get_course_id($lessonId) : $courseId;
        $learndash_user_stats = learndash_get_user_stats($user_id);
        // initialize
        $spobj = [ 
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => [
                'profile' => wp_create_nonce('update-profile'),
            ],
            'courses' => [                
                'completed' => false           
            ],
            'course' => [
                'id' => $courseId
            ],
            'lesson' => [
                'id' => $lessonId
            ],
            'count' => [
                'courses'   => $learndash_user_stats['courses'],
                'completed' => $learndash_user_stats['completed']
            ],
            'user' => [
                'id' => $user_id,
                'email' => $user_obj->user_email
            ],
        ];

        if($courseId !== false){
            // if (array_key_exists($courseId, $course_enroll_meta)) {
                // if (get_user_meta($user_id, $course_enroll_meta[$courseId], true) == 'enrolled') {
                if (get_user_meta($user_id, $course_enrolled_meta, true) == 'enrolled') {                    
                    $spobj['course']['form'] = false;                    
                }
                else{
                    $spobj['course']['meta'] = $course_enroll_meta[$courseId];
                    $spobj['course']['form'] = $course_enroll_form[$courseId];                    
                }
            // }
            $lessons = learndash_get_course_steps($courseId, array('sfwd-lessons'));
            if (!empty($lessons)) {
                $firstLessonId = reset($lessons);
                $spobj['lesson']['url'] = get_permalink($firstLessonId);
            } else {
                $spobj['lesson']['url'] = false;
            }
        }
        
        if($lessonId !== false){            
            if (array_key_exists($lessonId, $course_complete_meta)) {
                if (get_user_meta($user_id, $course_complete_meta[$lessonId], true) == 'graduate') {
                    $spobj['course']['form'] = false;
                }
                else{
                    $spobj['course']['meta'] = $course_complete_meta[$lessonId];
                    $spobj['course']['form'] = $course_complete_form[$lessonId];
                }
            }
        }

        // learndash
        if(is_page('all-courses')){
            // // get list of courses
            // $query_args = [
            //     'post_type'     =>  'sfwd-courses',
            //     'post_status'   =>  'publish',
            //     'fields'        =>  'ids',
            //     'numberposts'   =>  -1
            // ];
            // $course_ids = get_posts($query_args);
            // foreach($course_ids as $post_id){
            //     // check if course is not closed
            //     $course_pricing = learndash_get_course_price($post_id);
            //     if(isset($course_pricing['type']) && $course_pricing['type'] === 'closed'){
            //         continue;
            //     }else{
            //         if (!learndash_is_course_prerequities_completed($post_id, $user_id)) {
            //             continue;
            //         }
            //     }
            //     // check if course is not complete
            //     if(!learndash_course_completed($user_id, $post_id)){
            //         $courses_complete = false;
            //         break;
            //     }
            //     $courses_total++;
            // }
            // if($courses_total <= 0) $courses_complete = false;
            $course_ids = [1878]; // Add more course IDs as needed
            foreach ($course_ids as $course_id) {
                if (!learndash_course_completed($user_id, $course_id)) {
                    $spobj['courses']['completed'] = false;
                    break;
                } else {
                    $spobj['courses']['completed'] = true;
                }
            }            
        }
        wp_localize_script('sp-child-script', 'sp_obj', $spobj);
    }
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_enqueue_scripts' );

function my_custom_sidebar() {
	register_sidebar(
		array (
			'name' => __( 'Custom Sidebar Area', 'hello-elementor-child' ),
			'id' => 'custom-side-bar',
			'description' => __( 'This is the custom sidebar that you registered using the code snippet. You can change this text by editing this section in the code.', 'your-theme-domain' ),
			'before_widget' => '<div class="widget-content">',
			'after_widget' => "</div>",
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'my_custom_sidebar' );

// Pagebuilder Locale
function sp_unload_textdomain_elementor() {
	if (is_admin()) {
		$user_locale = get_user_meta( get_current_user_id(), 'locale', true );
		if ( 'en_US' === $user_locale ) {
			unload_textdomain( 'elementor' );
			unload_textdomain( 'elementor-pro' );
		}
	}
}
add_action( 'init', 'sp_unload_textdomain_elementor', 100 );

/* Icon Widget Fix - Link now applies to the whole element (not only icon & title) */ 

function tdau_link_whole_icon_box ( $content, $widget ) {
	
    if ( 'icon-box' === $widget->get_name() ) {
        $settings = $widget->get_settings_for_display();

		$wrapper_tag = 'div';

		$has_icon = ! empty( $settings['icon'] );

		if ( ! empty( $settings['link']['url'] ) ) {
			$wrapper_tag = 'a';
		}

		$icon_attributes = $widget->get_render_attribute_string( 'icon' );
		$link_attributes = $widget->get_render_attribute_string( 'link' );

		if ( ! $has_icon && ! empty( $settings['selected_icon']['value'] ) ) {
			$has_icon = true;
		}
		$migrated = isset( $settings['__fa4_migrated']['selected_icon'] );
        $is_new = ! isset( $settings['icon'] ) && Elementor\Icons_Manager::is_migration_allowed();
		
		ob_start();

		?>
		<?php echo implode( ' ', [ $wrapper_tag, $link_attributes ] ); ?> class="elementor-icon-box-wrapper elementor-icon-box-wrapper-tdau elementor-animation-<?php echo $settings['hover_animation']; ?>">
			<?php if ( $has_icon ) : ?>
			<div class="elementor-icon-box-icon">
				<?php echo implode( ' ', [ 'span', $icon_attributes ] ); ?>>
				<?php
				if ( $is_new || $migrated ) {
					Elementor\Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] );
				} elseif ( ! empty( $settings['icon'] ) ) {
					?><i <?php echo $widget->get_render_attribute_string( 'i' ); ?>></i><?php
				}
				?>
				</span>
			</div>
			<?php endif; ?>
			<div class="elementor-icon-box-content">
				<<?php echo $settings['title_size']; ?> class="elementor-icon-box-title">
					<?php echo $settings['title_text']; ?>
				</<?php echo $settings['title_size']; ?>>
				<?php if ( ! Elementor\Utils::is_empty( $settings['description_text'] ) ) : ?>
				<p <?php echo $widget->get_render_attribute_string( 'description_text' ); ?>><?php echo $settings['description_text']; ?></p>
				<?php endif; ?>
			</div>
		</<?php echo $wrapper_tag; ?>>
		<?php

		$content = ob_get_clean();

    }

    return $content;
}
add_filter( 'elementor/widget/render_content', 'tdau_link_whole_icon_box', 10, 2 );

// LEARN DASH //
/* logout shortcode */ 
add_shortcode( 'sp_logout', 'sp_learn_dash_logout_link' );
function sp_learn_dash_logout_link() {
	return wp_logout_url( home_url('?login=show') );
}
/* override learndash registraion form */ 
add_action( 'learndash_registration_form_override', 'sp_learn_dash_registration' );
// add_action( 'learndash_register_modal_register_form_override', 'sp_learn_dash_registration' );
function sp_learn_dash_registration() {
	echo apply_shortcodes( '[gravityform id="18" title="false" description="false" ajax="true"]' );
	echo '<p class="sp-lms-privacy">Your information will be used in accordance with our <a href="https://cyberwardens.com.au/privacy-policy/" target="_blank">Privacy Policy</a></p>';
	echo '<p class="sp-lms-or"><span>or</span></p>';
}
function learndash_custom_login_form() {
    if (is_user_logged_in()) {
        echo '<p>You are already logged in.</p>';
        return;
    }

    ob_start();
    ?>
    <form id="learndash-login-form" action="<?php echo esc_url(site_url('wp-login.php', 'login_post')); ?>" method="post">
        <p>
            <label for="user_login">Username</label>
            <input type="text" name="log" id="user_login" required>
        </p>
        <p>
            <label for="user_pass">Password</label>
            <input type="password" name="pwd" id="user_pass" required>
        </p>
        <p>
            <input type="submit" name="wp-submit" id="wp-submit" value="Log In">
            <input type="hidden" name="redirect_to" value="<?php echo esc_url(home_url('/all-courses/')); ?>"> <!-- Redirect after login -->
        </p>
        <?php wp_nonce_field('learndash_login', 'learndash_login_nonce'); ?>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('learndash_login_form', 'learndash_custom_login_form');
/* override learndash text */
add_filter('gettext', 'sp_learn_dash_replace_string',10,1);
add_filter('ngettext', 'sp_learn_dash_replace_string');
function sp_learn_dash_replace_string($translated) {
// 	if(str_contains($translated,'Back to Course')) $translated = str_replace('Back to Course', 'Graduation Kit', $translated);
//     if(str_contains($translated,'Mark Complete')) $translated = str_replace('Mark Complete', 'Graduate', $translated);
	if(str_contains($translated,'Show registration form')) $translated = str_replace('Show registration form', 'Sign up', $translated);
	if(str_contains($translated,"looks like you're already logged in.")) $translated = str_replace("looks like you're already logged in.", 'we’ve saved your progress, come back soon to complete your course.', $translated);
    return $translated;
}

/* add custom message login form */ 
add_filter( 'login_form_top', 'sp_learn_dash_login_field_top' );
function sp_learn_dash_login_field_top( $content = '' ) {
	if(is_front_page() && isset($_GET['reset'])){
		$content .= '<div class="ld-alert ld-alert-success"><div class="ld-alert-content"><div class="ld-alert-icon ld-icon ld-icon-alert"></div><div class="ld-alert-messages">Password reset, please log into your account.</div></div></div>';
	}
    $content .= '<h1>Hey, welcome back!</h1>';
	return $content;
}

/* hide admin bar */ 
add_action('after_setup_theme', 'sp_learn_dash_remove_admin_bar');
function sp_learn_dash_remove_admin_bar() {
    if (!current_user_can('administrator') && !is_admin() && !current_user_can('contributor')) {
        show_admin_bar(false);
    }
}

/* Add new condition */ 
add_filter('learndash_notifications_conditions','sp_learn_dash_notif_conditions');
function sp_learn_dash_notif_conditions($conditions){
	$conditions = array_merge( $conditions, array(
		'incomplete_course' => __( 'User has not completed a course.', 'learndash-notifications' ),
	) );
	return $conditions;
}

/* return as valid if  */ 
add_filter('learndash_notifications_are_conditions_valid','sp_learn_dash_notif_conditions_validation',10,4);
function sp_learn_dash_notif_conditions_validation($valid,$trigger,$notification,$args){
	if($valid):
	foreach( $notification->conditions as $key => $condition ) {
		if($condition['condition_type'] == 'incomplete_course'){
			if ( is_array( $condition['course_id'] ) && ! empty( $args['user_id'] ) && is_numeric( $args['user_id'] ) ) {
				if ( in_array( 'all', $condition['course_id'], true ) ) {
					$enrolled_courses = ld_get_mycourses( $args['user_id'] );
					foreach ( $enrolled_courses as $course_id ) {
						$completed = learndash_course_completed( $args['user_id'], $course_id );
						if ( !$completed ) {
							$valid = true;
							break;
						}
					}
				} else {
					foreach ( $condition['course_id'] as $course_id ) {
						$completed = learndash_course_completed( $args['user_id'], $course_id );
						if ( !$completed ) {
							$valid = true;
						}
					}
				}
			}
		}
	}
	endif;
	return $valid;
}

/* certification shortcode */ 
add_shortcode( 'sp_certificate', 'sp_learn_dash_certificate_link' );
function sp_learn_dash_certificate_link() {
	if(!is_user_logged_in()) return home_url();
	
	$user_id = get_current_user_id();
	$user_courses = ld_get_mycourses($user_id);
	$cert_link = false;
	foreach ( $user_courses as $course_id ) {
		$completed = learndash_course_completed($user_id, $course_id);
		if ($completed) {
			$cert_link = learndash_get_course_certificate_link( $course_id, $user_id );
			break;
		}
	}
	if($cert_link) return $cert_link;
	
	$redirect_uri  = 'lessons/level-one';
	$redirect_link = home_url($redirect_uri);
	
	return $redirect_link;
}

/* autologin after register */ 
add_action( 'gform_user_registered', 'sp_learn_dash_registration_autologin',  10, 4 );
function sp_learn_dash_registration_autologin( $user_id, $user_config, $entry, $password ) {
	if ( ! is_user_logged_in() ) {
        $user = get_userdata( $user_id );
        wp_signon([
            'user_login' => $user->user_login,
            'user_password' => $password,
            'remember' => false,
        ]);
        // register user in first lesson
        //sleep(1); // add 1 second delay
        //$course_id = 1878;
        //ld_update_course_access( $user_id, $course_id );
    }
}

/* auto login disable async */ 
add_filter( 'gform_is_feed_asynchronous', 'sp_learn_dash_autologin_async', 10, 2 );
function sp_learn_dash_autologin_async($is_asynchronous,$feed){
	if ( ! $is_asynchronous || rgar( $feed, 'addon_slug' ) !== 'gravityformsuserregistration' ) {
        return $is_asynchronous;
    }
    return gf_user_registration()->is_update_feed( $feed ) ? $is_asynchronous : false;
}

/* Restric admin access */
add_action( 'init', 'blockusers_init' );
function blockusers_init() {
    if ( is_admin() && ! current_user_can( 'administrator' ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
        wp_redirect( home_url() );
        exit;
    }
}

/* END Restric admin access */
/* change gravity form submit to button */ 
add_filter( 'gform_previous_button', 'sp_learn_dash_submit_button', 10, 2 );
function sp_learn_dash_submit_button( $button_input, $form ) {
	 $prevbtntxt = rgars( $form, 'lastPageButton/text' );
     preg_match( "/<input([^\/>]*)(\s\/)*>/", $button_input, $button_match );
     $button_atts = str_replace( "value='" . $prevbtntxt . "' ", "", $button_match[1] );
     return '<button ' . $button_atts . '><span>' . $prevbtntxt . '</span></button>';
}

/* redirect on completed course */ 
add_filter('learndash_completion_redirect','sp_learn_dash_complete_redirect',20,2);
function sp_learn_dash_complete_redirect($redirect_link, $step_id){
    $post_id = learndash_get_course_id($step_id);
    $course = get_post($post_id); 
    $course_slug = $course->post_name;
    $complete_uri = 'all-courses'; 
    $course_redirect = [
        1878 => 'all-courses',
    ];
	if(array_key_exists($post_id,$course_redirect)) $complete_uri = $course_redirect[$post_id];
	$redirect_link = home_url($complete_uri);
	return $redirect_link . '?ref=' . $course_slug;
}

/* graduation kit link */ 
add_filter( 'learndash_get_label_course_step_back', 'sp_learn_dash_graduation_kit_label',10,3 );
function sp_learn_dash_graduation_kit_label($step_label,$step_post_type, $plural){
	if(strpos($step_label, "Back to") !== false) $step_label = "Graduate now";
	return $step_label;
}

// END LEARN DASH //
/* change default error message if email already exists */
function replace_email_registered_message( $translated_text, $text, $domain ) {
    if ( $text === 'This email address is already registered' ) {
        $translated_text = 'The email address entered already exists. Please login to return to the course.';
    }
    return $translated_text;
}
add_filter( 'gettext', 'replace_email_registered_message', 20, 3 );

// Fix percentage field on Hubspot
function format_numbers_as_percent( $value, $field_type, $field ) {
    switch ($field) {
        case 'cw_level_1__course_progress':
        case 'cw_foundation__progress_percentage':
        case 'cw_level_2__progress_percentage':
        case 'cw_level_3__progress_percentage':
        case 'cw_refresh_2024__progress_percentage':
            $value = floatval( $value ) / 100;
            break;
    }

    return $value;
}
add_filter( 'wpf_format_field_value', 'format_numbers_as_percent', 10, 3 );

//add_action( 'gform_after_submission_21', 'set_post_content', 10, 2 );
function set_post_content( $entry, $form ) {
 
    // Get the post
    //$post = get_post( $entry['post_id'] );

    //$user = get_user_by( 'email', rgar( $entry, '4' ) );
	//$userId = $user->ID;
	//sci_learndash_mark_course_complete(1878,$userId);
	//add_action( 'wp_footer', 'custom_redirect_script' );
    // Redirect to the homepage
    wp_redirect('/all-courses');
    exit;
}
//add_action( 'wp_footer', 'custom_redirect_script' );
function custom_redirect_script() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Set the delay (in milliseconds)
            var delay = 2000; // 3 seconds delay

            // Redirect to the desired page after the delay
            setTimeout(function() {
                window.location.href = 'https://train.cyberwardens.com.au/all-courses';
            }, delay);
        });
    </script>
    <?php
}

add_action( 'template_redirect', 'sp_learn_dash_template_redirect_if_not_login' );
function sp_learn_dash_template_redirect_if_not_login(){
    if(is_admin()) return;

	$restricted_pages = array(2313); // Replace with your Elementor page IDs
	
    if (!is_user_logged_in() && is_page($restricted_pages)) {
        wp_redirect(home_url()); // Redirect to login page
        exit;
    }	

    if(is_page('workshops') && is_user_logged_in()){
		wp_redirect('/all-courses');
		exit();	
    }else{
    	return; // Exit function if user is logged in
    }

    // Check if user is logged in
    if ( is_user_logged_in() ) {
        return; // Exit function if user is logged in
    }else if (!is_user_logged_in() && !is_home() && !is_front_page() && !is_page('workshops')) {
        wp_redirect(home_url()); // Redirect to the home page (or change to a custom URL)
        exit; // Stop further execution
    }     


    $uri  = 'reset-password';
    $reset = get_post_field( 'post_name', get_post() );
    if( !is_front_page() && $uri != $reset ){
        wp_redirect( home_url() );
        exit();
    }
}


function check_user_course_completion_and_redirect() {
    if (!current_user_can('administrator')) {
        $user_id = get_current_user_id();
        $course_ids = [1878]; // Add more course IDs as needed

        $has_completed_courses = false;
        foreach ($course_ids as $course_id) {
            if (!learndash_course_completed($user_id, $course_id)) {
                $has_completed_courses = false;
                break;
            } else {
                $has_completed_courses = true;
            }
        }

        if (!$has_completed_courses && basename(get_permalink()) === 'youre-a-cyber-warden') {
            wp_redirect('/all-courses');
            exit;
        }
    }
}
// add_action('template_redirect', 'check_user_course_completion_and_redirect');

/* User Profile Page functions */
function custom_profile_shortcode() {
    // Get current user info
    $current_user = wp_get_current_user();
    $first_name = get_user_meta($current_user->ID, 'first_name', true);
    $last_name = get_user_meta($current_user->ID, 'last_name', true);
    $email = $current_user->user_email;
    ob_start();
    ?>
    <div id="profile-main-div">
        <div id="profile-form-div">
            <form id="profile-form">
                <div class="elementor-field-type-text elementor-field-group elementor-column elementor-col-70">
					<label for="first_name" class="elementor-field-label">First Name</label>
					<input type="text" name="first_name" id="form-first_name" class="elementor-field elementor-size-md  elementor-field-textual" placeholder="Name" value="<?php echo esc_attr($first_name); ?>">
                </div>
                <div class="elementor-field-type-text elementor-field-group elementor-column elementor-col-70">
					<label for="last_name" class="elementor-field-label">Last Name</label>
					<input type="text" name="last_name" id="form-last_name" class="elementor-field elementor-size-md  elementor-field-textual" placeholder="Name"  value="<?php echo esc_attr($last_name); ?>">
                </div>
                <div class="elementor-field-type-text elementor-field-group elementor-column elementor-col-70">
					<label for="email" class="elementor-field-label">Email</label>
					<input type="email" name="email" id="form-email" disabled class="profile-email-field elementor-field elementor-size-md  elementor-field-textual" placeholder="Email"  value="<?php echo esc_attr($email); ?>" required>
                </div>
				<div class="elementor-field-group elementor-column elementor-field-type-submit elementor-col-100">
                	<button class="elementor-button elementor-size-md edit_profile_submit_btn" type="button" id="edit-profile-btn">EDIT PROFILE</button>
				</div>
            </form>
        </div>
        <div id="return-message-div"></div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('custom_profile', 'custom_profile_shortcode');

function update_profile() {
    check_ajax_referer('update-profile', 'security');

    $user_id = get_current_user_id();
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    //$email = sanitize_email($_POST['email']);
	add_filter( 'send_email_change_email', '__return_false' );
//     if (empty($email) || !is_email($email)) {
//         wp_send_json_error();
//     }

    $update = wp_update_user([
        'ID' => $user_id,
        'first_name' => $first_name,
        'last_name' => $last_name,
    ]);
    remove_filter( 'send_email_change_email', '__return_false' );
    if (is_wp_error($update)) {
        wp_send_json_error();
    } else {
        update_user_meta($user_id, 'first_name', $first_name);
        update_user_meta($user_id, 'last_name', $last_name);
        wp_send_json_success();
    }
}
add_action('wp_ajax_update_profile', 'update_profile');
add_action('wp_ajax_nopriv_update_profile', 'update_profile');


/* Courses Grid Text Changes */
add_filter('learndash_course_grid_custom_button_text', function($button_text = '', $post_id = 0) {
    // Get the current user ID
    $user_id = get_current_user_id();
    if ($button_text === 'Enroll Now') {
		$button_text = 'Start Now';
		$course_pricing = learndash_get_course_price($post_id);

		if (isset($course_pricing['type']) && $course_pricing['type'] === 'closed') {
			$button_text = 'LEARN MORE';			
		}else{
			if (!learndash_is_course_prerequities_completed($post_id, $user_id)) {
				$button_text = 'LEARN MORE';				
			}
		}
	}
    elseif ($button_text === 'Continue Study') {
        if (learndash_course_completed($user_id, $post_id)) {
            $button_text = 'Completed';
        } else {
			if (!learndash_is_course_prerequities_completed($post_id, $user_id)) {
				$button_text = 'LEARN MORE';
			} else {
				$button_text = 'Continue';
			}
        }
    }

    // Always return $button_text
    return $button_text;
}, 10, 2);

function ld_course_content_after_alert() {
    $courseid = learndash_get_course_id();
    $user_id = get_current_user_id();
    if ( ! learndash_course_completed( $user_id, $courseid ) ) {
        $coursecontent = get_the_content();
        echo $coursecontent;
    }   
}
add_action( 'learndash-alert-after', 'ld_course_content_after_alert' );

function custom_learndash_price_label($label) {
    // Assuming we need to get the course pricing type within this function
    global $post;
    if (empty($post)) {
        return $label;
    }
    
    $course_pricing = learndash_get_course_price($post->ID);
	if (isset($course_pricing['type'])) {
        // Print the course pricing type
        //echo "<script>console.log('Course Pricing Type: " . esc_js($course_pricing['type']) . "');</script>";
		return 'Free';
    }
    
    // Return the original or modified label
    return $label;
}

add_filter('learndash_no_price_price_label', 'custom_learndash_price_label', 10);


/* Hupspot popup form upon enrolling and graduating from the course */
add_filter(
    'learndash_mark_complete',
    function($return, $post) {
        // Define the new button HTML
        $new_button = '<button class="learndash_mark_complete_button complete_popup_form_btn">GRADUATE</button>';
		// $lesson_meta_map = array(
		// 	1864 => 'level_one_graduated_form_filled',
		// 	2437 => 'foundation_graduated_form_filled',
		// //	2436 => 'refresh_graduated_form_filled'
		// ); // Map lesson IDs to their corresponding meta keys
        $lesson_meta_map = [
            1864 => 'graduate_form_filled',
            2437 => 'graduate_form_filled'
        ];
		$lessonId = get_the_ID();
		$user_id = get_current_user_id();
        $user_obj = wp_get_current_user();
        $user_email = $user_obj->user_email;
		if (array_key_exists($lessonId, $lesson_meta_map)) {
    		$meta_key = $lesson_meta_map[$lessonId];
			// Check if the specific user meta key is already set to true
			if (get_user_meta($user_id, $meta_key, true)) {
				return $return; // Exit the function if the user has already graduated via HubSpot form for this lesson
			}
			else{
				// Append the new button to the return HTML
        		$return .= $new_button;
				return $return;
			}
		}else{
            $return .= '<style>.ld-content-action form.sfwd-mark-complete{display:block!important;}</style>';
		}
        // Return the modified HTML
        return $return;
    },
    10,
    2
);

function update_graduated_hubspot_meta() {
    // Check if the request contains the required parameters
    if (isset($_POST['user_id']) && isset($_POST['lesson_id'])) {
        $user_id = intval($_POST['user_id']);
        $lesson_id = intval($_POST['lesson_id']);
        // $meta_keys = array(
        //     1864 => 'level_one_graduated_form_filled',
        //     2437 => 'foundation_graduated_form_filled',
        //     2436 => 'refresh_graduated_form_filled'
        // );
        $meta_keys = [
            1864 => 'graduate_form_filled',
            2437 => 'graduate_form_filled'
        ];
        if (array_key_exists($lesson_id, $meta_keys)) {
            $meta_key = $meta_keys[$lesson_id];

            // Update or create the corresponding user meta as true
            // update_user_meta($user_id, $meta_key, true);
            update_user_meta($user_id, $meta_key, 'graduate');

            // Send a success response
            wp_send_json_success('User meta updated successfully');
        } else {
            wp_send_json_error('Invalid lesson ID');
        }
    } else {
        wp_send_json_error('Invalid request');
    }
}
add_action('wp_ajax_update_graduated_hubspot_meta', 'update_graduated_hubspot_meta');
add_action('wp_ajax_nopriv_update_graduated_hubspot_meta', 'update_graduated_hubspot_meta');

add_filter(
    'learndash_payment_button_free',
    function($button, $params) {
		$user_id = get_current_user_id();
		$course_ids = array(1878, 2430, 2433); // Add more course IDs as needed
        $courseId = get_the_ID();
		if(in_array($courseId, $course_ids)){
			if (get_user_meta($user_id, 'enrolled_hubspot_form_filled', true)) {
				return $button; // Exit the function if the user is already enrolled via HubSpot form
			}
			$new_button = '<button class="enroll_popup_form_btn" data-course-id="' . esc_attr($courseId) . '">Start Now</button>';
			return $new_button;
		}
		else{
			return $button;
		}     
    },
    10,
    2
);

add_action('wp_ajax_ld_enroll_user', 'ld_enroll_user_callback');
add_action('wp_ajax_nopriv_ld_enroll_user', 'ld_enroll_user_callback'); // For non-logged-in users if needed

function ld_enroll_user_callback() {
    // Check if the request contains the required parameters
    if (isset($_POST['user_id']) && isset($_POST['course_id'])) {
        $user_id = intval($_POST['user_id']);
        $course_id = intval($_POST['course_id']);

        // Define the mapping of course IDs to their corresponding meta keys
        // $course_meta_map = array(
        //     1878 => 'level_one_enrolled_form_filled',
        //     2430 => 'foundation_enrolled_form_filled'
        // );
        $course_enrolled_meta = 'enrolled_form_filled';

        // Check if the course ID exists in the mapping array
        // if (array_key_exists($course_id, $course_meta_map)) {
            // Enroll user in the course
            $result = ld_update_course_access($user_id, $course_id);

            // Send a response back
            if ($result) {
                // Update the corresponding meta key for the enrolled course
                // $res = update_user_meta($user_id, $course_meta_map[$course_id], 'enrolled');
                $res = update_user_meta($user_id, $course_enrolled_meta, 'enrolled');
                wp_send_json_success($res);
            } else {
                wp_send_json_error('Failed to enroll user');
            }
        // } else {
        //     wp_send_json_error('Invalid course ID');
        // }
    } else {
        wp_send_json_error('Invalid request');
    }
}

/* Change course progress steps text */
$contextForProgress = 'course'; // Update as needed
// Capture and store progress stats in a global variable
add_filter('learndash-' . $contextForProgress . '-progress-stats', function($progress) {
    // Convert progress data to JSON
    $progress_json = json_encode($progress);

    // Add inline script to output the progress data and modify text content
    add_action('wp_footer', function() use ($progress_json) {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function(jQuery) {
                // Log the LearnDash Progress Stats to the console
                //console.log('LearnDash Progress Stats:', <?php echo $progress_json; ?>);
                
                // Find all elements with the specified DOM
                jQuery('.ld-course-status.ld-course-status-enrolled .ld-progress-stats .ld-progress-percentage').each(function() {
                    // Get the text content of the element and trim whitespace
                    var text = jQuery(this).text().trim();
                    
                    // Extract the percentage value from the text
                    var percentageMatch = text.match(/(\d+)%/);
                    
                    if (percentageMatch) {
                        var percentage = parseInt(percentageMatch[1], 10);
                        
                        if (percentage === 100) {
                            jQuery(this).text('');
                        } else {
                            jQuery(this).text('started');
                        }
                    } else {
                        console.log('Percentage value not found in text.');
                    }
                });
            });
        </script>
        <?php
    }, 100); // Priority 100 to ensure it runs after other content

    return $progress;
}, 10);


//Add Course Pricing Type to the Body class for styling purpose
function add_course_pricing_type_to_body_class($classes) {
    // Get the global post object
    global $post;
    
    // Check if we're on a course page
    if (is_singular('sfwd-courses')) {
        // Get the course pricing details
        $course_pricing = learndash_get_course_price($post->ID);
        
        // Check if the pricing type exists
        if (isset($course_pricing['type'])) {
            // Add the pricing type as a class to the body tag
            $classes[] = 'course-pricing-' . sanitize_html_class($course_pricing['type']);
        }
		// Check if the course prerequisites are not completed
        if (!learndash_is_course_prerequities_completed($post->ID, get_current_user_id())) {
            $classes[] = 'course-prerequisite-uncomplete';
        }
    }
    
    return $classes;
}
add_filter('body_class', 'add_course_pricing_type_to_body_class');



/*** Replace "Not Enrolled" with "Not Started"***/
function my_custom_learndash_status_filter($status) {
    print_r($status);
  if ($status == 'Not Started') {
    $status = 'Not Started';
  }
  return $status;
}
//add_filter('learndash_course_progress_text', 'my_custom_learndash_status_filter', 10, 2);

//Added RM 
add_filter('learndash_course_status',function( $course_status_str, $course_id, $user_id, $name ) {
        // May add any custom logic using $course_status_str, $course_id, $user_id, $name.
        // Always return $course_status_str.
            add_action('wp_footer', function() use ($course_status_str) {
                    ?>
                    <script type="text/javascript">
                        jQuery(document).ready(function(jQuery) {
                            jQuery('.ld-course-status.ld-course-status-not-enrolled  .ld-course-status-seg-status .ld-course-status-content span').each(function() {
                                jQuery(this).text('<?php echo $course_status_str?>');
                            });
                        });
                    </script>
                    <?php
                }, 100); // Priority 100 to ensure it runs after other content        
        return $course_status_str;
    },
    10,
    4
);

function custom_ld_status_text( $status, $post_id, $user_id ) {
    if ( $status == 'waiting' ) {
        return 'Pending Approval'; // Change this to your preferred text
    }
    return $status;
}
add_filter( 'learndash_status_label', 'custom_ld_status_text', 10, 3 );

function accessCompletedCourseContent() {
    if (is_singular('sfwd-courses')) {
        $course_id = get_the_ID();

        // Check if the current user has completed the course
        if (is_user_logged_in() && learndash_course_completed(get_current_user_id(), $course_id)) {
            // Get the lessons for the course
            $lessons = learndash_get_course_steps($course_id, array('sfwd-lessons'));

            // Get the first lesson URL if available
            if (!empty($lessons)) {
                $first_lesson_id = reset($lessons);
                $first_lesson_url = get_permalink($first_lesson_id);

                // Return the button with the lesson URL
                return '<a href="' . esc_url($first_lesson_url) . '" class="course_completed_sidebar_btn">Completed</a>';
            }
        }
    }

    // Return the fallback paragraph if no lesson URL is found or the course is not completed
    return '<p class="course_completed_sidebar_btn">Completed</p>';
}

add_shortcode('access_completed_course', 'accessCompletedCourseContent');

//Added By Ruel To check if email already in use before signup

add_action('wp_ajax_check_email_exists', 'check_email_exists');
add_action('wp_ajax_nopriv_check_email_exists', 'check_email_exists');


function check_email_exists() {
    // Check if email is set in the request
    if (isset($_POST['email'])) {
        $email = sanitize_email($_POST['email']);
        $exists = email_exists($email);

        // Return JSON response
        if ($exists) {
            wp_send_json(['exists' => true, 'message' => 'Email already registered.']);
        } else {
            wp_send_json(['exists' => false, 'message' => 'Email is available.']);
        }
    }
    wp_die();
}

function my_text_strings( $translated_text, $text, $domain ) {
    switch ( $translated_text ) {
        case 'Invalid password. Please try again.' :
            $translated_text = __( 'Invalid code. Please try again.', 'cosmicgiant_onetimepassword' );
            break;
    }
    return $translated_text;
}
add_filter( 'gettext', 'my_text_strings', 20, 3 );

//added Jan 13 2025 RM
add_action( 'tincanny_before_process_request', function(){
  $contents = file_get_contents( 'php://input' );
  $decoded  = json_decode( $contents, true );
  // Your custom logic here. Make sure to add a die or exit call
} );


add_action( 'tincanny_before_process_request', function(){
    $contents = file_get_contents( 'php://input' );
    $decoded  = json_decode( $contents, true );

    $log_dir = WP_CONTENT_DIR . '/uploads/logs/';
    $log_file = $log_dir . 'tincanny.log';

    if ( ! file_exists( $log_dir ) ) {
        wp_mkdir_p( $log_dir ); 
    }

    if ( ! file_exists( $log_file ) ) {
        touch( $log_file ); 
    }

    $log_message = date('Y-m-d H:i:s') . " - Request: " . print_r($decoded, true) . "\n";
    file_put_contents($log_file, $log_message, FILE_APPEND);

    $throttle_limit = 1; // Requests per second
    $throttle_key = ''; // Initialize throttle key

    if ( isset($decoded['client']) && $decoded['client'] === 'AR2017' && 
         isset($decoded['stateId']) && $decoded['stateId'] === 'suspend_data' ) { 
        // Throttle only "suspend_data" requests from "AR2017"
        $user_id = get_current_user_id(); 
        $throttle_key = 'tincanny_throttle_' . $user_id . '_suspend_data';
    } 

     if ( ! empty($throttle_key) ) {
        $last_request = get_transient( $throttle_key );
        if ( $last_request !== false && (time() - $last_request) < $throttle_limit ) {
            wp_die( 'Too many requests. Please try again later.' ); 
        }
        set_transient( $throttle_key, time(), $throttle_limit ); 
    }

} );



//Added Jan 22 2025 RM
//Set user as gradute

function update_user_course_status() {
    // Check if the request contains the required parameters
    if (isset($_POST['user_id']) && isset($_POST['lesson_id'])) {
        $user_id = intval($_POST['user_id']);
        $lesson_id = intval($_POST['lesson_id']);
        $course_id = intval($_POST['course_id']);

        
        $user_lesson_status = learndash_process_mark_complete( $user_id, $course_id );
      if ( $user_lesson_status ) {
        learndash_user_course_complete_all_steps( $user_id,  $course_id );
        learndash_update_user_activity( $user_id, $course_id, 'course', 1 ); 
        wp_send_json_success('Course completed');
      }else{
        wp_send_json_error('Course not completed');
      }
    }
    wp_die();
}
add_action('wp_ajax_update_user_course_status', 'update_user_course_status');
add_action('wp_ajax_nopriv_update_user_course_status', 'update_user_course_status');

//
//Render the opt general option
include_once('otp-login/otp-login.php');
// Render the hooks functions
include_once('otp-login/lib/otpl-class.php');