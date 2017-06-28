<?php
    // Initialize any vars we may have received via POST
    $srslide_api_key                = (!empty($_POST['srslide_api_key'])) ? $_POST['srslide_api_key'] : get_option('srslide_api_key');
    $srslide_pid                    = (!empty($_REQUEST['srslide_pid'])) ? $_REQUEST['srslide_pid'] : get_option('srslide_pid');
    $srslide_cat_as_chan            = (int)(!empty($_POST['srslide_cat_as_chan']));
    $srslide_header_text            = (!empty($_POST['srslide_header_text'])) ? $_POST['srslide_header_text'] : '';
    $srslide_register_hidden        = (!empty($_POST['srslide_register_hidden']));
    $srslide_validate_hidden        = (!empty($_POST['srslide_validate_hidden']));
    $srslide_save_hidden            = (!empty($_POST['srslide_save_hidden']));
    $srslide_style_link_color       = (!empty($_POST['srslide_style_link_color'])) ? $_POST['srslide_style_link_color'] : '';
    $srslide_style_link_color_text  = (!empty($_POST['srslide_style_link_color_text'])) ? $_POST['srslide_style_link_color_text'] : '';
    $srslide_show_logo              = (int)(!empty($_POST['srslide_show_logo']));
    $srslide_sr_login               = (!empty($_POST['srslide_sr_login'])) ? $_POST['srslide_sr_login'] : '';
    $srslide_sr_pass                = (!empty($_POST['srslide_sr_pass'])) ? $_POST['srslide_sr_pass'] : '';
    $srslide_style_element_bottom   = (!empty($_POST['srslide_style_element_bottom'])) ? $_POST['srslide_style_element_bottom'] : '';
    $srslide_use_pages              = (int)(!empty($_POST['srslide_use_pages']));
    $srslide_css_option             = (!empty($_POST['srslide_css_option'])) ? $_POST['srslide_css_option'] : 1;
    $srslide_css_url                = (!empty($_POST['srslide_css_url'])) ? $_POST['srslide_css_url'] : '';

    // Set up support link
    $supportEmailLink = '<a href="mailto:support@simplereach.com">SimpleReach Support</a>';
    $getSatisfactionLink = '<a href="http://getsatisfaction.com/simplereach">GetSatisfaction</a>';
    $twitterLink = '<a href="http://twitter.com/simplereach">follow us</a>';
    $simplereachLink = '<a href="http://simplereach.com">SimpleReach</a>';

    // for debugging
    $mock = 0;
    // If the api_key/pid was submitted for validation ...
    if ($srslide_validate_hidden) {
        $encoded_params = array();
        $encoded_params['api_key'] = $srslide_api_key;
        $encoded_params['pid'] = $srslide_pid;
        $encoded_params['ts'] = (int)gmdate('U');

        // Curl call for validation
        $url = 'https://simplereach.com/wordpress/validate';
        if ($mock) {
            $resp = new StdClass();
            $resp->success = true;
            $resp->api_key = 'xxxxxxxxxxx';
            $resp->pid = 'yyyyyyyyyyyyy';
        } else {
            $response = srslide_post_url($url, $encoded_params);
            // Decode the JSON
            $resp = json_decode($response);
        }

        if (!empty($resp->success)) {
            // Save the PID and the API Key to Wordpress
            update_option('srslide_pid', $resp->pid);
            update_option('srslide_api_key', $resp->api_key);

            // Set some defaults only now that we have a registration
            srslide_set_defaults($_POST);

            $updated[] = __('Your credentials have been validated.  <a href="'. str_replace("%7E", "~", $_SERVER['REQUEST_URI']) . '">Click here</a> to continue ....', 'srslide');
        } else {
            $errors = $resp->error;
        }

    // If we are saving the slide options ...
    } else if ($srslide_save_hidden) {

        // Set the show logo option
        update_option('srslide_show_logo', (int)$srslide_show_logo);

        // Set the recommend pages option
        update_option('srslide_use_pages', (int)$srslide_use_pages);

        // Set the option deciding whether categories should be used as channels
        update_option('srslide_cat_as_chan', (int)$srslide_cat_as_chan);

        // Store bottom offset locally
        update_option('srslide_style_element_bottom', $srslide_style_element_bottom);

        // Validate header
        $header_length = strlen($srslide_header_text);
        if ($header_length < 1 or $header_length > 30) {
            $errors[] = __("Header text must be between 1 and 30 characters.",'srslide');
        } else {
            update_option('srslide_header_text', strtoupper($srslide_header_text));
        }

        // Validate api_key
        if (!srslide_validate_api_key($srslide_api_key)) {
            $errors[] = __("API Key should be 32 characters. Please double check your entry.",'srslide');
        }

        // Validate pid
        if (!srslide_validate_pid($srslide_pid)) {
            $errors[] = __("PID should be 24 characters. Please double check your entry.",'srslide');
        }

        // Validate css options
	$url_error = false;
        if ($srslide_css_option == 1) {
            update_option('srslide_css_option', $srslide_css_option);
            update_option('srslide_css_url', '');
        } else if ($srslide_css_option == 2) {
            if (function_exists('filter_var')) {
                if (!filter_var($srslide_css_url, FILTER_VALIDATE_URL)) {
                    $errors[] = __("The custom CSS URL you entered is invalid.  It must start with 'http'",'srslide');
                    $url_error = true;
                }
            } else {
                $srslide_css_url = str_replace(array('"',"'",'<'), array(''), $srslide_css_url);
            }
            if (!$url_error) {
                update_option('srslide_css_option', $srslide_css_option);
                update_option('srslide_css_url', $srslide_css_url);
            }
        }
        // Validate link color
        if ($srslide_style_link_color == 'other') {
            if (!srslide_validate_hex_color($srslide_style_link_color_text)) {
                $errors[] = __("The link color specified is not a valid hex color.", 'srslide');
            }
            
        } else {
            if (!srslide_validate_hex_color($srslide_style_link_color)) {
                $errors[] = __("The link color specified is not a valid hex color.", 'srslide');
            }
        }
        // If we have no errors, validate api_key and pid
        if (!$errors) {
            // only make api request if api_key and/or pid has changed.
            if (($srslide_api_key != get_option('srslide_api_key')) ||
                ($srslide_pid != get_option('srslide_pid'))
                ) {

                // TODO Write comparison function to see if styles that need to be 
                // sent to CDN haven't been changed. Skip CURL if there was no change
                $encoded_params['api_key'] = $srslide_api_key;
                $encoded_params['pid'] = $srslide_pid;
                $encoded_params['ts'] = (int)gmdate('U');

                // Curl call for validation
                $url = 'http://simplereach.com/wordpress/validate';
                $response = srslide_post_url($url, $encoded_params);
                // Decode the JSON
                $resp = json_decode($response);

                if (!empty($resp->success)) {
                    // Save the PID and the API Key to Wordpress
                    update_option('srslide_pid', $resp->pid);
                    update_option('srslide_api_key', $resp->api_key);

                    // Convert the style settings into a hash and save it
                    update_option('srslide_styles', srslide_create_style_hash($_POST));
                    $updated[] = __('Options saved.', 'srslide');

                    $updated[] = __('Your credentials have been validated and settings saved!', 'srslide');
                } else {
                    $errors[] = (!empty($resp->error_msg)) ? $resp->error_msg : __('There was an unknown error', 'srslide');
                }
            } else {
                // Convert the style settings into a hash and save it
                update_option('srslide_styles', srslide_create_style_hash($_POST));
                $updated[] = __('Your settings have been saved', 'srslide');
            }
        }

    // If we are handling a new registration ...
    } else if ($srslide_register_hidden) {

        // Validations
        if (!srslide_validate_login($srslide_sr_login)) {
            $errors[] = 'Invalid login';
        } else {
            $encoded_params['srslide_sr_login'] = $srslide_sr_login;
        }

        if (!srslide_validate_password($srslide_sr_pass)) {
            $errors[] = 'Invalid password';
        } else {
            $encoded_params['srslide_sr_pass'] = $srslide_sr_pass;
        }

        if (count($errors)) {
            $resp = new StdClass;
            $resp->success = FALSE;
        } else {
            // Get the blog name
            $encoded_params['account_name'] = urlencode(get_option('blogname'));

            // Get the site URL
            $encoded_params['website'] = urlencode(get_option('siteurl'));
            $encoded_params['reg_type'] = 'slide';
            $encoded_params['ts'] = (int)gmdate('U');

            // Curl call for registration
            $url = 'https://simplereach.com/wordpress/register';
            if ($mock) {
                $resp = new StdClass();
                $resp->success = true;
                $resp->error = 'Invalid login';
            } else {
                $response = srslide_post_url($url, $encoded_params);
                $resp = json_decode($response);
            }
        }

        if (!empty($resp->success)) {
            // Save the PID and the API Key to Wordpress
            $received_api_key = $received_pid = 0;
            if (!empty($resp->api_key)) {
                $received_api_key = 1;
                update_option('srslide_api_key', $resp->api_key);
            }
            if (!empty($resp->pid)) {
                $received_pid = 1;
                update_option('srslide_pid', $resp->pid);
            }

            if ($received_pid && $received_api_key) {
                // Set some defaults only now that we have a registration
                srslide_set_defaults($_POST);
                $updated[] = __("Congrats! Your account is now set up.  Please give us some time to index your content.", 'srslide');
            } else if (!$received_api_key) {
                $errors[] = __("There was an error setting up your account. [invalid_api_key].  Please contact " . $supportEmailLink, 'srslide');
            } else if (!$received_pid) {
                $errors[] = __("There was an error setting up your account. [invalid_pid].  Please contact " . $supportEmailLink, 'srslide');
            }
        } else {
            $errors[] = __($resp->error_msg . "  <br/>Please try again or contact $supportEmailLink.", 'srslide');
        }
    }

    //
    // If we are fully registered, meaning we have both srslide_pid and 
    // srslide_api_key, show the slide admin page 
    //
    if (get_option('srslide_pid') && get_option('srslide_api_key')) {
        // Get options
        $srslide_pid         = get_option('srslide_pid');
        $srslide_sr_login    = get_option('srslide_sr_login');
        $srslide_header_text = get_option('srslide_header_text');
        $srslide_styles      = get_option('srslide_styles');
        $srslide_api_key     = get_option('srslide_api_key');
        $srslide_show_logo   = get_option('srslide_show_logo');
        $srslide_use_pages   = get_option('srslide_use_pages');
        $srslide_cat_as_chan = get_option('srslide_cat_as_chan');
        $srslide_css_option  = get_option('srslide_css_option');
        $srslide_css_url     = get_option('srslide_css_url');
                  
        $documentation_link = sprintf('<a href="https://www.simplereach.com/docs/publisher/overview">%s</a>', __('documentation', 'srslide'));
        $dashboard_link = sprintf('<a href="http://simplereach.com/publisher/reports/%s#articles">simplereach.com</a>', $srslide_pid);

        // Build link colors
        $srslide_default_link_colors = array('#FE3F10', '#3B5998', '#CC0000');
        foreach ($srslide_default_link_colors as $sdlc) {
            $SDLC[] = array('color' => $sdlc, 'selected' => $sdlc == $srslide_styles['link']['color']);
        }
        $custom_color_checked = (in_array($srslide_styles['link']['color'], $srslide_default_link_colors)) ? '' : ' CHECKED';
        $custom_color_value = ($custom_color_checked) ? $srslide_styles['link']['color'] : '';

        // Build shift-up values
        $shiftUpValues = array('0', '30', '40', '50', '60');
        foreach ($shiftUpValues as $suv) {
            $SUV[] = array('value' => $suv, 'selected' => $suv == $srslide_styles['element']['bottom']);
        }

        // Build css values
        $css_options[] = array('value' => 1, 'label' => 'Default', 'checked' => $srslide_css_option == 1);
        $css_options[] = array('value' => 2, 'label' => 'Custom URL', 'checked' => $srslide_css_option == 2);

        $data = array(
            'first_paragraph_text'      => sprintf(__("Head over to %s to access your analytics/reporting dashboard.", 'srslide'), $dashboard_link),
            'second_paragraph_text'     => sprintf(__("If you're a developer, you can fully customize the style and functionality of The Slide.  See our %s for details.", 'srslide'), $documentation_link),
            'third_paragraph_text'      => sprintf(__("Questions? Comments? We're actively answering questions on %s and can also be contacted via %s.", 'srslide'), $getSatisfactionLink, $supportEmailLink),
            'about_text'                => sprintf(__("TheSlide helps readers discover more of your content by recommending related articles at the bottom of each post. Our interactive widget slides out as the reader approaches the end of an article, prompting them to explore more of your content.", 'srslide')),
            'about_text_social'         => sprintf(__("TheSlide is brought to you by %s. Don't forget to %s on Twitter!" , 'srslide'), $simplereachLink, $twitterLink),
            'display_settings_text'     => __('Display Settings', 'srslide'),
            'request_uri'               => str_replace('%7E', '~', $_SERVER['REQUEST_URI']),
            'edit_header_text'          => __("Edit Header (Max 30 char):", 'srslide'),
            'srslide_header_text'       => $srslide_header_text,
            'srslide_style_element_bottom' => $SUV,
            'all_letters_text'          => __("All letters will be uppercase   (Default:  RECOMMENDED FOR YOU)", 'srslide'),
            'link_color_text'           => __("Link Color:", 'srslide'),
            'srslide_default_link_colors' => $SDLC,
            'hex_code_text'             => __("Hex code", 'srslide'),
            'hex_code_example'          => __("(eg #FE3F10):", 'srslide'),
            'shift_slide_text'          => __("Shift The Slide up by:", 'srslide'),
            'srslide_use_pages'         => $srslide_use_pages,
            'also_show_text'            => __("Also show The Slide on <em>Pages</em>", 'srslide'),
            'default_setting_text'      => __("Default setting is to show The Slide only on <em>Posts</em>", 'srslide'),
            'default_logo_setting_text' => __("Default setting is to show The Slide logo", 'srslide'),
            'show_logo_text'            => __("Show The Slide logo", 'srslide'),
            'srslide_cat_as_chan'       => $srslide_cat_as_chan,
            'custom_color_checked'      => $custom_color_checked,
            'custom_color_value'        => $custom_color_value,
            'categorize_text'           => __("Categorize Recommendations", 'srslide'),
            'only_recommend_text'       => __("Only recommend posts in the same category/channel", 'srslide'),
            'srslide_show_logo'         => $srslide_show_logo,
            'display_logo_text'         => __("Display The Slide logo (increases engagement)", 'srslide'),
            'srslide_css_option'        => $srslide_css_option,
            'srslide_pid'               => $srslide_pid,
            'default_text'              => __("Default", 'srslide'),
            'custom_url_text'           => __("Custom URL", 'srslide'),
            'css_options'               => $css_options,
            'save_text'                 => __('Save', 'srslide'),
            'stylesheet_text'           => __('Stylesheet', 'srslide'),
            'account_settings_text'     => __("Account Settings", 'srslide'),
            'errors'                    => $errors,
            'has_errors'                => count($errors) ? __('You have the following errors:', 'srslide') : '',
            'updated'                   => $updated,
            'api_key_text'              => __("API Key:", 'srslide'),
            'srslide_api_key'           => $srslide_api_key,
            'srslide_css_url'           => $srslide_css_url,
            'warning_text'              => __("Warning: Do not change unless instructed by SimpleReach", 'srslide'),
            'publisher_id_text'         => __("Publisher ID (PID):", 'srslide'),
            'support_label'             => __("Support", 'srslide'),
            'analytics_label'           => __("Analytics and Reporting", 'srslide'),
            'analytics_text'            => sprintf(__("In addition to TheSlide, you have immediate access to SimpleReach. Just head over to %s and login to track every social action from the instant you publish a post. You'll also be able to see stats for TheSlide.", 'srslide'), $simplereachLink),
            'about_label'               => __("About", 'srslide')
        );

        // slide_settings.html
        $templateEngine = new Mustache();
        $tmpl = file_get_contents(dirname(__FILE__) . '/templates/srslide_settings.html');
        print $templateEngine->render($tmpl, $data);
    } else {
        $regOnHold = 0;
        if ($regOnHold) {
            $current_status = '';
            if (get_option('srslide_pid') && get_option('srslide_api_key')) {
                $current_status = __('The Slide plugin is active.', 'srslide');
            } else {
                $current_status = __('The Slide plugin is turned off.  (Your account will activated once you enter a valid PID and API Key)', 'srslide');
            }

            $data = array('publisher_id_text' => __("Publisher ID (PID):", 'srslide'),
                          'header_text'       =>  __('The Slide by SimpleReach', 'srslide'),
                          'warning_text'      => __("Warning: Do not change unless instructed by SimpleReach", 'srslide'),
                          'api_key_text'      => __("API Key:", 'srslide'),
                          'request_uri'       => str_replace('%7E', '~', $_SERVER['REQUEST_URI']),
                          'beta_text'         => __("We are currently in private beta.  <a href=\"https://www.simplereach.com/contact#reach-out\">Contact us</a> to apply.", 'srslide'),
                          'creds_text'        => __('If you already have credentials, click <a href="#" id="showForm">here</a>.', 'srslide'),
                          'srslide_pid'       => $srslide_pid,
                          'srslide_api_key'   => $srslide_api_key,
                          'error_msg'         => $error_msg,
                          'errors'            => $errors,
                          'updated'           => $updated,
                          'current_status'    => $current_status,
                          'save'              => __('Save', 'srslide')
            );
            // slide_pid_screen.html
            $templateEngine = new Mustache();
            $tmpl = file_get_contents(dirname(__FILE__) . '/templates/srslide_pid_screen.html');
            print $templateEngine->render($tmpl, $data);
        } else {
            $data = array('publisher_id_text'     => __("Publisher ID (PID):", 'srslide'),
                          'warning_text'          => __("Warning: Do not change unless instructed by SimpleReach", 'srslide'),
                          'api_key_text'          => __("API Key:", 'srslide'),
                          'email_text'            => __("Email:", 'srslide'),
                          'email_example_text'    => __("ex: user@example.com", 'srslide'),
                          'password_text'         => __("Password:", 'srslide'),
                          'password_example_text' => __("ex: secretpass:", 'srslide'),
                          'beta_text'             => __("We are currently in private beta.  <a href=\"https://www.simplereach.com/contact#reach-out\">Contact us</a> to apply.", 'srslide'),
                          'creds_text'            => __('If you already have credentials, click <a href="#"> id="showForm">here</a>.', 'srslide'),
                          'display_logo_text'     => __("I would like to display The Slide logo (increases engagement)", 'srslide'),
                          'register_text'         => __("Register for a SimpleReach account. If you have already registered at simplereach.com, enter your login information below.", 'srslide'),
                          'signup_text'           => __("Sign up", 'srslide'),
                          'signin_text'           => __("Sign in",  'srslide'),
                          'save'                  => __('Save', 'srslide'),
                          'slide_show_logo'       => 'CHECKED=CHECKED',
                          'request_uri'           => str_replace('%7E', '~', $_SERVER['REQUEST_URI']),
                          'srslide_sr_pass'       => $srslide_sr_pass,
                          'srslide_sr_login'      => $srslide_sr_login,
                          'srslide_api_key'       => $srslide_api_key,
                          'srslide_pid'           => $srslide_pid,
                          'errors'                => $errors,
                          'has_errors'            => count($errors) ? __('You have the following errors:', 'srslide') : '',
                          'updated'               => $updated
            );
            // slide_registration.html
            $templateEngine = new Mustache();
            $tmpl = file_get_contents(dirname(__FILE__) . '/templates/srslide_registration.html');
            print $templateEngine->render($tmpl, $data);
        }
    } 
