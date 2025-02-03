<div class="wrap">
    <h2>OTP Login Settings</h2>

    <form method="post" action="options.php" id="otpl-option-form">
        <?php settings_fields('otpl'); ?>
        <div class="otpl-setting">
            <!-- General Setting -->
            <div class="first otpl-tab" id="div-otpl-general">
                <table class="form-table">
                    <tr>
                        <td style="vertical-align:top;">
                            <table width="100%">
                                <tr valign="top">
                                    <th width="10">
                                        <input type="checkbox" value="1" name="otpl_enable" id="otpl_enable" <?php checked(get_option('otpl_enable'), 1); ?> />
                                        <label for="otpl_enable">Enable OTP Login</label>
                                    </th>
                                </tr>
                                <tr valign="top">
                                    <th>
                                        <label for="otpl_redirect_url">Redirect URL</label>
                                        <input type="text" value="<?php echo esc_attr(get_option('otpl_redirect_url')); ?>" name="otpl_redirect_url" id="otpl_redirect_url" size="40" />
                                        <em>define redirect URL after logged in user</em>
                                    </th>
                                </tr>
                                <tr valign="top">
                                    <th>
                                        <label for="otpl_register_url">Register Page URL</label>
                                        <input type="text" value="<?php echo esc_url(get_option('otpl_register_url')); ?>" name="otpl_register_url" id="otpl_register_url" size="40" />
                                        <em>define register URL for non-registered users</em>
                                    </th>
                                </tr>
								<tr valign="top">
                                    <th>
                                        <label for="otpl_login_attempt">Login Attempt</label>
                                        <input type="text" value="<?php echo esc_attr(get_option('otpl_login_attempt')); ?>" name="otpl_login_attempt" id="otpl_login_attempt" size="40" />
                                        <em>Define number of login attempt</em>
                                    </th>
                                </tr>
								<tr valign="top">
                                    <th>
                                        <label for="otpl_login_locktime">Lockout Period</label>
                                        <input type="text" value="<?php echo esc_attr(get_option('otpl_login_locktime')); ?>" name="otpl_login_locktime" id="otpl_login_locktime" size="40" />
                                        <em>Define lockout period in seconds</em>
                                    </th>
                                </tr>
                                <tr valign="top">
                                    <th><span>Form Steps</span><hr>
                                        <label for="otpl_login_welcomemessage">Step 1</label><br/>
                                        <input type="text" value="<?php echo esc_attr(get_option('otpl_login_welcomemessage')); ?>" name="otpl_login_welcomemessage" id="otpl_login_welcomemessage" size="100" />
                                    </th>
                                </tr> 
                                <tr valign="top">
                                    <th>
                                        <label for="otpl_login_verifymessage">Step 2</label><br/>
                                        <input type="text" value="<?php echo esc_attr(get_option('otpl_login_verifymessage')); ?>" name="otpl_login_verifymessage" id="otpl_login_verifymessage" size="100" />
                                    </th>
                                </tr>
                                <tr valign="top">
                                    <th><span>Button</span><hr>
                                        <label for="otpl_login_buttonstepone">Button Step 1</label><br/>
                                        <input type="text" value="<?php echo esc_attr(get_option('otpl_login_buttonstepone')); ?>" name="otpl_login_buttonstepone" id="otpl_login_buttonstepone" size="100" />
                                    </th>
                                </tr> 
                                <tr valign="top">
                                    <th>
                                        <label for="otpl_login_buttonsteptwo">Button Step 2</label><br/>
                                        <input type="text" value="<?php echo esc_attr(get_option('otpl_login_buttonsteptwo')); ?>" name="otpl_login_buttonsteptwo" id="otpl_login_buttonsteptwo" size="100" />
                                    </th>
                                </tr>
                                <tr valign="top">
                                    <th>
                                        <label for="otpl_login_otp_resend">Button Resend OTP</label><br/>
                                        <input type="text" value="<?php echo esc_attr(get_option('otpl_login_otp_resend')); ?>" name="otpl_login_otp_resend" id="otpl_login_otp_resend" size="100" />
                                    </th>
                                </tr>                                
                                <tr valign="top">
                                    <th><span>Status Message</span><hr>
                                        <label for="otpl_login_otp_match">OTP Matched</label><br/>
                                        <input type="text" value="<?php echo esc_attr(get_option('otpl_login_otp_match')); ?>" name="otpl_login_otp_match" id="otpl_login_otp_match" size="100" />
                                    </th>
                                </tr>
                                <tr valign="top">
                                    <th>
                                        <label for="otpl_login_not_logged_in">OTP Matched but not logged in</label><br/>
                                        <input type="text" value="<?php echo esc_attr(get_option('otpl_login_not_logged_in')); ?>" name="otpl_login_not_logged_in" id="otpl_login_not_logged_in" size="100" />
                                    </th>
                                </tr>                                
                                <tr valign="top">
                                    <th>
                                        <label for="otpl_login_otp_notmatch">OTP does not match</label><br/>
                                        <input type="text" value="<?php echo esc_attr(get_option('otpl_login_otp_notmatch')); ?>" name="otpl_login_otp_notmatch" id="otpl_login_otp_notmatch" size="100" />
                                    </th>
                                </tr>
                                <tr valign="top">
                                    <th>
                                        <label for="otpl_login_otp_error">OTP does not exist</label><br/>
                                        <input type="text" value="<?php echo esc_attr(get_option('otpl_login_otp_error')); ?>" name="otpl_login_otp_error" id="otpl_login_otp_error" size="100" /><br/>
                                        <hr>
                                    </th>
                                </tr>                                
                                <tr valign="top">
                                    <th>
                                        <label for="otpl_login_emailsubject">Email Subject</label><br/>
                                        <input type="text" value="<?php echo esc_attr(get_option('otpl_login_emailsubject')); ?>" name="otpl_login_emailsubject" id="otpl_login_emailsubject" size="100" />
                                    </th>
                                </tr>                                
                                <tr valign="top">
                                    <th>
                                        <label for="otpl_login_emailtemaplate">Email Template</label>
                                        <?php wp_editor(get_option('otpl_login_emailtemplate'), 'otpl_login_emailtemplate'); ?>
                                    </th>
                                </tr>                                
                                <tr>
                                    <td><?php @submit_button(); ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <hr>
                <h3>Login Popup Class Name:</h3>
                <p><strong>otpl-popup</strong> using this class you can add OTP login popup on your website</p>
                Example:
                <code>&lt;div class="otpl-popup"&gt;&lt;a href="javascript:"&gt;Login&lt;/a&gt;&lt;/div&gt;</code>

                <h3>Shortcode</h3>
                <p><strong>[otp_login title="Login with OTP"]</strong></p>
            </div>
        </div>
    </form>
</div>