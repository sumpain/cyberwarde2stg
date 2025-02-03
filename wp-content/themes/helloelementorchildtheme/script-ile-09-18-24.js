(function($){
    $(document).ready(function(){
		var url_query = window.location.search;			
		var url_param = new URLSearchParams(url_query);
		var url_path  = window.location.pathname;
		var is_login  = url_param.get('login');

		/* login page */
		if(is_login=='failed' || is_login=='show'){
			$('#learndash-registration-wrapper .show-register-form').show();
			$('#learndash-registration-wrapper .registration-login-form').show();
			$('#learndash-registration-wrapper .gform-registration').hide();
			$('#learndash-registration-wrapper .registration-login').hide();
		}
		$('#learndash-registration-wrapper .registration-login-form input[name="redirect_to"]').val('/all-courses');

		/* all courses page */
		if (url_path === '/all-courses' || url_path === '/all-courses/') {
			var getResourceBtn = $('#getResourseBtn');
			if (typeof sp_obj !== undefined && courses in sp_obj) {
				if (sp_obj.courses.completed) {
					getResourceBtn.attr('href', '/all-courses');
					getResourceBtn.addClass('youreCyberWarden');
				} else {
					getResourceBtn.removeAttr('href');
					getResourceBtn.addClass('courseCompletionRequired');
				}
			}
		}
    });

	/* login page */
	$(document).on('click','.show-register-form',function(){
		$('#learndash-registration-wrapper .gform-registration').show();
	});
	$(document).on('click','.registration-login-link',function(){
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
})(jQuery);