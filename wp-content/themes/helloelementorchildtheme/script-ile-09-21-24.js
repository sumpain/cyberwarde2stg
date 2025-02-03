(function($){
	// document ready
    $(document).ready(function(){
		var url_query = window.location.search;			
		var url_param = new URLSearchParams(url_query);
		var url_path  = window.location.pathname;
		var is_login  = url_param.get('login');
		if($("#user_new_password").length){

		}else{
			$('#learndash-reset-password-wrapper input[type="submit"]').on('click', function(event) {
			    event.preventDefault();
			    $(".msgerror").remove();
			    // Get the input value
			    var user_login = $('#user_login').val();

			    // Make the AJAX request
			    $.ajax({
			        url: sp_obj.ajax_url,
			        method: 'POST',
			        data: {
			            action: 'send_password_reset',
			            user_login: user_login
			        },
			        success: function(response) {
			            if (response.success) {
			                console.log(response.data.message);  // Display success message
							jQuery( "<div class='msgerror'>"+response.data.message+"</div>" ).insertAfter( "#learndash-reset-password-wrapper>form" );
			            } else {
			            	jQuery( "<div class='msgerror'>"+response.data.message+"</div>" ).insertAfter( "#learndash-reset-password-wrapper>form" );
			                console.log(response.data.message);  // Display error message
			            }
			        },
			        error: function() {
			        	jQuery( "<div class='msgerror'>There was an error processing your request.</div>" ).insertAfter( "#learndash-reset-password-wrapper>form" );
			        }
			    });
			});	
		}
			
		$('#learndash-reset-password-wrapper .learndash-wrapper').remove();
		$('#learndash-reset-password-wrapper ul').remove();
		if ($('#learndash-registration-wrapper .registration-login-form').length) {
		    var showpass = '<button type="button" class="gform_show_password gform-theme-button gform-theme-button--simple" onclick="javascript:gformToggleShowPassword(\'user_pass\');" aria-live="polite" aria-label="Show Password" data-label-show="Show Password" data-label-hide="Hide Password"><span class="dashicons dashicons-hidden" aria-hidden="true"></span></button>';
			$(showpass).insertAfter( "#user_pass" );
		}
		if(is_login=='failed' || window.location.hash === '#login'){
			$('#otpl_contact .heading h3').text('Hey, welcome back!');
			$('#otpl_contact').appendTo('.registration-login-form');
			$('.registration-login-form #loginform').hide();
			$('#loginform').prepend('<div class="ld-alert ld-alert-success"><div class="ld-alert-content"><div class="ld-alert-icon ld-icon ld-icon-alert"></div><div class="ld-alert-messages">Incorrect username or password. Please try again.</div></div></div>');
		}
		if(is_login=='failed' || is_login=='show' || window.location.hash === '#login'){
			$('#otpl_contact .heading h3').text('Hey, welcome back!');
			$('#otpl_contact').appendTo('.registration-login-form');
			$('.registration-login-form #loginform').hide();
			$('#learndash-registration-wrapper .show-register-form').show();
			$('#learndash-registration-wrapper .registration-login-form').show();
			$('#learndash-registration-wrapper .gform-registration').hide();
			$('#learndash-registration-wrapper .registration-login').hide();
		}
		$('#learndash-registration-wrapper .registration-login-form input[name="redirect_to"]').val('/all-courses');


		/* profile page */ 
		if (url_path === '/profile' || url_path === '/profile/') {
			$('.ld-course-list #ld-main-course-list .ld-item-list-item').each(function() {
				// Find the .ld-progress-stats .ld-progress-steps element
				var progressSteps = jQuery(this).find('.ld-progress-stats .ld-progress-steps');

				// Check if the item has the class 'learndash-incomplete'
				if ($(this).hasClass('learndash-incomplete')) {
					// Replace its text with 'started'
					progressSteps.text('started');
				} 
				// Check if the item has the class 'learndash-complete'
				else if ($(this).hasClass('learndash-complete')) {
					// Replace its text with 'completed'
					progressSteps.text('completed');
				}
			});
		}

		/* all courses page */
		if (url_path === '/all-courses' || url_path === '/all-courses/') {
			if($('.learndash-course-grid ').length > 0){

				var articles = document.querySelectorAll('.learndash-course-grid .items-wrapper.grid article.sfwd-courses');
				articles.forEach(function(article) {
					var itemButton = article.closest('.item').querySelector('.content .button > a').textContent;
					/*if (itemButton == "LEARN MORE") {
						article.classList.add('closed-or-comingSoon');
						var closestItem = article.closest('.item');
						if (closestItem) {
							closestItem.classList.add('closed-course-item');
						}

						var thumbnail = article.querySelector('.thumbnail');
						if (thumbnail) {
							// Check if the overlay text is already present
							if (!thumbnail.querySelector('.coming-soon-text')) {
								var overlayText = document.createElement('div');
								overlayText.className = 'coming-soon-text';
								overlayText.textContent = 'LAUNCHING SOON';
								thumbnail.style.position = 'relative'; // Ensure the thumbnail is positioned relative
								thumbnail.appendChild(overlayText);
							}
						}
					}*/
					if (itemButton == "Continue") {
						article.classList.add('alreadyEnrolled');
						var articleButton = article.querySelector('.content .button a');
						if (articleButton) {
							articleButton.classList.add('l-enrolled-course');
						}
					}
					else if (itemButton == "Completed") {
						var button = article.closest('.item').querySelector('.content .button > a');
						button.classList.add('l-completed-course');
						// Check if the next sibling exists and is not the image we want to add
						if (!button.nextElementSibling || !button.nextElementSibling.classList.contains('l-completed-course-vector')) {
							var img = document.createElement('img');
							img.src = '/wp-content/uploads/2024/09/Vector-1.png';
							img.classList.add('l-completed-course-vector');
							button.parentNode.insertBefore(img, button.nextSibling);
						}
					}
				});
			}
			
			if($('#getResourseBtn').length > 0){
				var getResourceBtn = $('#getResourseBtn');
				if (sp_obj.courses.completed) {
					getResourceBtn.attr('href', '/youre-a-cyber-warden');
					getResourceBtn.addClass('youreCyberWarden');
				} else {
					getResourceBtn.removeAttr('href');
					getResourceBtn.addClass('courseCompletionRequired');
				}
			}
		}

		/* edit profile */ 
		if (url_path === '/profile' || url_path === '/profile/') {
			// Course stats
			var enrolledCoursesCount = sp_obj.count.courses;
			var completedCoursesCount = sp_obj.count.completed;

			// Function to update course stats
			function updateCourseStats() {
				var enrolledElements = $('.user_enrolled_courses_count .elementor-heading-title');
				var completedElements = $('.user_completed_courses_count .elementor-heading-title');
				if (enrolledElements.length && completedElements.length) {
					enrolledElements.each(function() {
						$(this).text(enrolledCoursesCount);
					});
					completedElements.each(function() {
						$(this).text(completedCoursesCount);
					});
					clearInterval(interval);
				}else{
					console.log('Still finding');
				}
			}

			// Check for elements every second
			var interval = setInterval(updateCourseStats, 500);
		}
			/* enroll */
			$(document).on('click','.enroll_popup_form_btn',function(){
				var courseId = sp_obj.course.id;
				var userId = sp_obj.user.id;

				// Update course access for the user via AJAX using jQuery
				jQuery.ajax({
					url: sp_obj.ajax_url,
					method: 'POST',
					data: {
						action: 'ld_enroll_user',
						user_id: userId,
						course_id: courseId
					},
					success: function(response) {
						// console.log(response);
						if (response.success) {
							if (sp_obj.lesson.url) {
								window.location.href = sp_obj.lesson.url;
							} else {
								window.location.reload();
							}
						} else {
							alert('Something went wrong, please try again or contact support.');
						}
					},
					error: function(xhr, status, error) {
						console.log('AJAX request failed:', status, error);
					}
				});
			});
		//}

		/* hubspot graduate form */
		if(sp_obj.course.id !== false && sp_obj.course.form !== false && sp_obj.lesson.id !== false){
			// Create the hidden div for the popup form
			var hiddenDiv = document.createElement('div');
			hiddenDiv.className = 'complete_popup_form';
			hiddenDiv.style.display = 'none';

			// Create the inner div for the form content
			var innerDiv = document.createElement('div');
			innerDiv.className = 'complete_popup_form_inside';

			var closeButton = document.createElement('div');
			closeButton.className = 'popup_close_button';
			closeButton.textContent = 'X';
			closeButton.addEventListener('click', function() {
				hiddenDiv.style.display = 'none'; // Hide the popup
				hiddenDiv.classList.remove('popup_opened'); // Remove the popup opened class
			});

			// Create the actual form div
			var actualDiv = document.createElement('div');
			actualDiv.className = 'complete_popup_form_actual';

			// Append the inner div to the hidden div
			innerDiv.appendChild(closeButton);
			innerDiv.appendChild(actualDiv);
			hiddenDiv.appendChild(innerDiv);
			document.body.appendChild(hiddenDiv);

			// Hide all forms with the class 'sfwd-mark-complete'
			var formsToHide = document.querySelectorAll('form.sfwd-mark-complete');
			formsToHide.forEach(function(form) {
				form.style.display = 'none !important';
			});

			// Attach click event to all buttons with class 'learndash_mark_complete_button.complete_popup_form_btn'
			var completeButtons = document.querySelectorAll('.learndash_mark_complete_button.complete_popup_form_btn');
			completeButtons.forEach(function(button) {
				button.addEventListener('click', function(event) {
					event.preventDefault(); // Prevent default action
					var closestContainer = this.closest('.tclr-mark-complete-button');
					if (closestContainer) {
						var formToMark = closestContainer.querySelector('form.sfwd-mark-complete');
						if (formToMark) {
							formToMark.classList.add('popup_clicked');
						}
					}
					hiddenDiv.style.display = 'flex'; // Show the popup
				});
			});

			// Load the HubSpot form script
			var hubspotScript = document.createElement('script');
			hubspotScript.setAttribute('charset', 'utf-8');
			hubspotScript.setAttribute('type', 'text/javascript');
			// hubspotScript.setAttribute('src', '//js-eu1.hsforms.net/forms/embed/v2.js');
			hubspotScript.setAttribute('src', '//js.hsforms.net/forms/embed/v2.js');
			hubspotScript.onload = function() {
				// Create the HubSpot form and define callbacks
				//hubspot graduation form
				hbspt.forms.create({
					region: 'na1',
					portalId: '44598769',
					formId: sp_obj.course.form,
					target: '.complete_popup_form_actual',
					onFormReady: function(form) {
						// Form is ready
						jQuery('input[name=\"email\"]').val(sp_obj.user.email).prop('disabled',true).change();
					},
					onFormSubmitted: function(form) {
						var userId = sp_obj.user.id;
						var lessonId = sp_obj.lesson.id;
						// Update user meta via AJAX
						jQuery.ajax({
							url: sp_obj.ajax_url,
							method: 'POST',
							data: {
								action: 'update_graduated_hubspot_meta',
								user_id: userId,
								lesson_id: lessonId
							},
							success: function(response) {
								if (response.success) {
									// Trigger the form's submit button
									var clickedForm = document.querySelector('form.sfwd-mark-complete.popup_clicked');
									if (clickedForm) {
										var submitButton = clickedForm.querySelector('.learndash_mark_complete_button');
										if (submitButton) {
											submitButton.click();
										}
										clickedForm.classList.remove('popup_clicked');
									} else {
										var allForms = document.querySelectorAll('form.sfwd-mark-complete .learndash_mark_complete_button');
										if (allForms.length > 0) {
											allForms[0].click();
										}
									}
									setTimeout(function() {
										hiddenDiv.style.display = 'none';
									}, 2000);
								} else {
									console.log('Failed to update user meta:', response.data);
								}
							},
							error: function(xhr, status, error) {
								console.log('AJAX request failed:', status, error);
							}
						});
					}
				});
			};
			document.body.appendChild(hubspotScript); 
		}
	});
	
	/* login page */
	$(document).on('click','.show-register-form',function(){
		$('#learndash-registration-wrapper .gform-registration').show();
	});
	$(document).on('click','.registration-login-link',function(){
			$('#otpl_contact .heading h3').text('Hey, welcome back!');
			$('#otpl_contact').appendTo('.registration-login-form');
			$('.registration-login-form #loginform').hide();		
		$('#learndash-registration-wrapper .gform-registration').hide();
		$('#learndash-registration-wrapper .registration-login-form input[name="redirect_to"]').val('/all-courses');
	});
	$(document).on('submit','form.gform-registration',function(){
		$(this).find('input[type="submit"]').removeClass('active').addClass('active');
	});
	$(document).on('focus','.gfield input,.gfield select,.gfield textarea',function(){
		if($(this).closest('.gfield').hasClass('gfield_error')){
			$(this).closest('.gfield').removeClass('gfield_error');
		}
	});

	/* edit profile */ 
	$(document).on('click','#edit-profile-btn', function() {
		var first_name = $('#form-first_name').val();
		var last_name = $('#form-last_name').val();
		//var email = $('#form-email').val();

		$.ajax({
			url: sp_obj.ajax_url,
			type: 'POST',
			cache: false,
			data: {
				action: 'update_profile',
				first_name: first_name,
				last_name: last_name,
				security: sp_obj.nonce.profile
			},
			success: function(response) {
				if (response.success) {
					$('#return-message-div').text('Successfully updated');
				} else {
					$('#return-message-div').text('Something went wrong, please try again later');
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.error('AJAX Error:', textStatus, errorThrown);
				$('#return-message-div').text('Something went wrong, please try again later');
			}
		});
	});

	//added by RM to check if form 18 step 1 input has a value
	$(document).on('blur','.ginput_container_text input[type="text"],.ginput_container_email input[type="email"]', function(e){
		if($(this).val() == ""){
		}else{
			if($(this).attr('type') === 'email'){
			}else{
				$(this).parent().addClass('valid');
			}
		}
	})
	// Added by RM to check if validation has change
	$(document).ready(function() {
		if($('#optl-form').length){
			//$('<div class="emailerror"></div>').insertAfter($('#optl-form #otpl-body #email'));
			
		}
		$('#generateOtp').on('click',function(){
			$('.customessage').remove();
			$('<span class="customessage">If we recognise this email, we will send you a one-time verification code</span>').insertAfter($('#optl-form #otpl-body #generateOtp'));
		});
		if($('#submitotpsec').length > 0){
			$('<div class="seq-opt"></div>').insertBefore('#submitotpsec #email_otp');
			const otpContainer = $('#submitotpsec .seq-opt');
			otpContainer.empty(); // Clear the current input

			// Create 6 separate input fields
			for (let i = 0; i < 6; i++) {
			    const input = $('<input>', {
			        type: 'tel',
			        class: 'otp-input',
			        maxlength: 1,
			        'data-index': i,
			        style:'padding:0px 5px!important; text-align:center; margin:10px 5px !important',
			        pattern:'\d{1}'
			    });
			    // On input, move to the next field
			    input.on('paste',function(e){
					// Get the pasted text
			    let pasteData = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
				pasteData = pasteData.replace(/\D/g, '').slice(0, 6);
					$('.otp-input').each(function(index) {
			            $(this).val(pasteData[index] || ''); // Set each input to the corresponding digit or leave empty
			        });				
			    });

			    input.on('input', function () {
			        const index = $(this).data('index');
			        if ($(this).val()) {
			            // Move to the next input field
			            if (index < 5) {
			                $(this).next().focus();
			            }
			        } else if (index > 0) {
			            // Move to the previous input field if empty
			            $(this).prev().focus();
			        }
			        $('#email_otp').val($('.otp-input[data-index=0]').val() + $('.otp-input[data-index=1]').val() + $('.otp-input[data-index=2]').val() + $('.otp-input[data-index=3]').val() + $('.otp-input[data-index=4]').val() + $('.otp-input[data-index=5]').val());
			    });
			    otpContainer.append(input);
			}
			otpContainer.children().first().focus();			
		}
		
		$(document).on('gform_post_render', function(event, formData, formId) {
			$('#cg-otp-password').prepend('<div class="optobject"><div class="gfield_description">Check your email inbox for your verification code, enter the number and click verify.</div></div>');
			const otpContainer = $('#cg-otp-password').find('.optobject');
			otpContainer.empty(); // Clear the current input
			
			// Create 6 separate input fields
			for (let i = 0; i < 6; i++) {
			    const input = $('<input>', {
			        type: 'tel',
			        class: 'otp-input',
			        maxlength: 1,
			        'data-index': i,
			        style:'padding:0px 5px!important; text-align:center; margin:10px 5px !important',
			        pattern:'\d{1}'
			    });
			    // On input, move to the next field
			    input.on('paste',function(e){
					// Get the pasted text
			    let pasteData2 = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
			    console.log(pasteData2);
				pasteData = pasteData2.replace(/\D/g, '').slice(0, 6);
					$('.optobject .otp-input').each(function(index) {
			            $(this).val(pasteData2[index] || ''); // Set each input to the corresponding digit or leave empty
			        });				
			    });

			    input.on('input', function () {
			        const index = $(this).data('index');
			        if ($(this).val()) {
			            // Move to the next input field
			            if (index < 5) {
			                $(this).next().focus();
			            }
			        } else if (index > 0) {
			            // Move to the previous input field if empty
			            $(this).prev().focus();
			        }
			        var datainput = $('.gfield--type-cg_otp_password').data('js-reload').replace('field','input');
			        $('#'+ datainput +'_1').val($('.optobject .otp-input[data-index=0]').val() + $('.optobject .otp-input[data-index=1]').val() + $('.optobject .otp-input[data-index=2]').val() + $('.optobject .otp-input[data-index=3]').val() + $('.optobject .otp-input[data-index=4]').val() + $('.optobject .otp-input[data-index=5]').val());
//			        $('#input_21_55_1').val($('.optobject .otp-input[data-index=0]').val() + $('.optobject .otp-input[data-index=1]').val() + $('.optobject .otp-input[data-index=2]').val() + $('.optobject .otp-input[data-index=3]').val() + $('.optobject .otp-input[data-index=4]').val() + $('.optobject .otp-input[data-index=5]').val());
			    });
			    otpContainer.append(input);
			}
			otpContainer.children().first().focus();
			
			$('#cg-otp-password-send, #cg-otp-password #cg-otp-password-resend').on('click', function() {
				//$('.cg-otp-password__validation').addClass('hidden_gform');

				setTimeout(function(){
				//if($('.cg-otp-password__validation').hasClass('cg-otp-password__validation--success')){
			  	//		$(".cg-otp-password__validation").detach().insertBefore("#cg-otp-password");
			  			$('label[for="input_18_51_1"]').hide();
			  			$('label[for="input_21_55_1"]').hide();
			  	//		$('.cg-otp-password__validation').css('display','block');			  			
			  	//	}
				},1000); 
			});


/*			const s = {
			    verify: document.getElementById("cg-otp-password-verify"),
			    error: document.getElementById("gfield_description"),
			};

			s.verify.addEventListener("click", function() {
			  console.log('Button clicked!');
			});
			//s.error.addEventListener("change", function() {
//			  console.log('Element change!');
//			});			*/

			$('#gform_page_18_1 .ginput_container_email input[type="email"], #gform_page_21_1 .ginput_container_email input[type="email"]').on('blur', function() {
				var email = $(this).val();

				$.ajax({
				    url: sp_obj.ajax_url,
				    type: 'POST',
				    data: {
				        action: 'check_email_exists',
				        email: email
				    },
				    success: function(response) {
				        if (response.exists) {
				        	$('#field_18_4').addClass('gfield_error');
				        	$('#field_18_4').find('#validation_message_18_4').remove();
				        	$('#field_18_4').append('<div id="validation_message_18_4" class="gfield_description validation_message gfield_validation_message">We found an existing account with this email. Log in to access your account.</div>');
				        	$('#field_18_51').hide();
				        	$('#field_18_51').hide();
				        	$('#gform_18 .gform_page_footer').hide();

				        	$('#field_21_4').addClass('gfield_error');
				        	$('#field_21_4').find('#validation_message_21_4').remove();
				        	$('#field_21_4').append('<div id="validation_message_21_4" class="gfield_description validation_message gfield_validation_message">We found an existing account with this email. Log in to access your account.</div>');
				        	$('#field_21_51').hide();
				        	$('#field_21_51').hide();
				        	$('#gform_21 .gform_page_footer').hide();

				        } else {
				        	$('#field_18_4').removeClass('gfield_error');
				            $('#gform_18 .ginput_container_email').addClass('valid');
				            $('#field_18_4').find('#validation_message_18_4').remove();
				        	$('#field_18_51').show();
				        	$('#gform_18 .gform_page_footer').show();

				        	$('#field_21_4').removeClass('gfield_error');
				            $('#gform_21 .ginput_container_email').addClass('valid');
				            $('#field_21_4').find('#validation_message_21_4').remove();
				        	$('#field_21_51').show();
				        	$('#gform_21 .gform_page_footer').show();
				        }
				    }
				});				
			});
		});
	});

		

	
})(jQuery);