<?php
if (!class_exists('Mustache')) {
    require_once 'Mustache.php';
}


/**
 * Convert the $_POST variable into a style hash
 *
 * @author Eric Lubow <elubow@simplereach.com>
 * @param Object $POST Post object being shown on the page
 * @return Array $style An associative array of styles for The Slide
 */
function srslide_create_style_hash($POST)
{
    $style = array();

    // Iterate over our $_POST var and rip out the styles to put into a hash
    foreach ($POST as $name => $value) {
        if (!preg_match('/^srslide_style_(.+?)_(.+?)$/',$name,$style_name)) { continue; }
        $style[$style_name[1]][$style_name[2]] = $value;
    }

    return srslide_clean_style_options($style);
}

/**
 * Remove the extra style options prior to save/send to SimpleReach
 *
 * @author Eric Lubow <elubow@simplereach.com>
 * @param Array $style The associative array of styles for The Slide
 * @return Array $style The clean associative array of styles for The Slide
 */
function srslide_clean_style_options($style)
{
    // Remove the link_color_text and just have link_color
    if ($style['link']['color'] == 'other') {
        $style['link']['color'] = $style['link']['color_text'];
    }
    unset($style['link']['color_text']);

    return $style;
}

/**
 * srslide_create_support_link 
 * 
 * @access public
 * @return void
 */
function srslide_create_support_link()
{
    $srslide_sr_login = get_option('srslide_sr_login');
    return '<a href="https://www.simplereach.com/contact?r=support&e=' . $srslide_sr_login . '&debug_info=' . urlencode(srslide_create_debug_info()).'">SimpleReach Support</a>';
}

/**
 * Put together some debug info to send to SimpleReach when support
 * is requested.
 *
 * @author Eric Lubow <elubow@simplereach.com>
 * @param None
 * @return String Debug info
 */
function srslide_create_debug_info()
{
    $attrs                       = array();
    $attrs['pid']                = get_option('srslide_pid');
    $attrs['email']              = get_option('srslide_sr_login');
    $attrs['website']            = get_option('siteurl');
    $attrs['account']            = get_option('blogname');
    $attrs['user_api_key']       = get_option('srslide_api_key');
    $attrs['wp_version']         = get_bloginfo('version');
    $attrs['plugin_version']     = SRSLIDE_PLUGIN_VERSION;
    $attrs['show_slide_logo']    = get_option('srslide_show_logo');
    $attrs['header_text']        = get_option('srslide_header_text');
    $attrs['use_wp_pages']       = get_option('srslide_use_pages');
    $attrs['styles']             = serialize(get_option('srslide_styles'));
    $attrs['account']            = get_option('blogname');
    $attrs['php_version']        = phpversion();
    $attrs['curl_version']       = _srslide_get_curl_version();
    $attrs['curl_ssl']           = _srslide_get_curl_ssl();
    $attrs['curl_https_enabled'] = _srslide_get_curl_ssl_enabled();
    $attrs['wp_plugins']         = _srslide_get_wp_plugins();
    if ($theme_name = get_current_theme()) {
	$attrs['theme']          = $theme_name;
	$theme_data = get_theme_data(get_stylesheet_directory().'/style.css' );
        if (is_array($theme_data)) {
            foreach ($theme_data as $k=>$v) {
                $attrs['theme_'.$k] = $v;
            }
        }
    }
    $str = '';
    foreach ($attrs as $attr => $value) {
        $str .= $attr .': ' . $value . "\n";
    }
    return $str;
}

/**
 * srslide_get_post_channels 
 * 
 * @param mixed $post 
 * @access public
 * @return void
 */
function srslide_get_post_channels($post)
{
    // Categories as channels are now mandatory
    // $cat_as_chan = get_option('srslide_cat_as_chan');
    // if (!$cat_as_chan) { return '[]'; }

    $post_categories = wp_get_post_categories($post->ID);
    $myCats = array();
    foreach ($post_categories as $c) {
        $cat = get_category($c);
        $myCats[] = $cat->slug;
    }
    return json_encode($myCats);
}

/**
 * _srslide_get_wp_plugins 
 * 
 * @access protected
 * @return void
 */
function _srslide_get_wp_plugins()
{
    $plugins = get_plugins();
    if ($plugins) {
        foreach ($plugins as $plugin_file => $plugin_data) {
            if (is_plugin_active($plugin_file)) {
                $results[] = $plugin_data['Title'] .'-' . $plugin_data['Version'];
            }
        }
    }
    return join(',', $results);
}

function _srslide_get_curl_version()
{
    if (extension_loaded('curl')) {
        $cv = curl_version();
        return $cv['version'];
    } else {
        return 'NONE';
    }
}

function _srslide_get_curl_ssl()
{
    if (extension_loaded('curl')) {
        $cv = curl_version();
        if (!empty($cv['ssl_version'])) {
            return $cv['ssl_version'];
        } else {
            return 'NONE';
        }
    } else {
        return 'NONE';
    }
}

function _srslide_get_curl_ssl_enabled()
{
    if (extension_loaded('curl')) {
        $cv = curl_version();
        if (!empty($cv['protocols'])) {
            $protocols = $cv['protocols'];
            return (in_array('https', $protocols)) ? 'YES' : 'NO';
        } else {
            return 'NO';
        }
    } else {
        return 'NO';
    }
}

/**
 * Return the SimpleReach title for the post.
 *
 * @author Eric Lubow <elubow@simplereach.com>
 * @param Object $post Post object being shown on the page
 * @return String Title of the post with slashes escaped
 */
function srslide_get_post_title($post)
{
    $title = get_post_meta($post->ID, '_srslide_title', true);
    if (empty($title)) {
        $title = $post->post_title;
    }
    return addslashes($title);
}

function srslide_get_post_authors($post)
{
    $authors = get_post_meta($post->ID, '_srslide_author', true);
    if (empty($authors)) {
        $authors = get_the_author();
    }
    if (!is_array($authors)) {
        $authors = preg_split('/,/', $authors);
    }
    return json_encode($authors);
}

function srslide_get_post_article_icon($post)
{
    return get_post_meta($post->ID, '_srslide_article_icon', true);
}


/**
 * Return the SimpleReach tags for the post.
 *
 * @author Eric Lubow <elubow@simplereach.com>
 * @param Object $post Post object being shown on the page
 * @return String javascript array of tags
 */
function srslide_get_post_tags($post)
{
    $tags = get_post_meta($post->ID, '_srslide_tags', true);
    if (empty($tags)) {
        $tags = wp_get_post_tags($post->ID, array('fields' => 'slugs'));
    }
    if (!is_array($tags)) {
        $tags = preg_split('/,/', $tags);
    }
    return json_encode($tags);
}

/**
 * Set some defaults for the plugin
 *
 * @author Eric Lubow <elubow@simplereach.com>
 * @param Object $post_data The result of the $_POST data from the registration
 * @return None
 */
function srslide_set_defaults($post_data)
{
    update_option('srslide_sr_login', $post_data['srslide_sr_login']);
    update_option('srslide_sr_pass', $post_data['srslide_sr_pass']);
    update_option('srslide_show_logo', 1);
    update_option('srslide_header_text', 'RECOMMENDED FOR YOU');
    update_option('srslide_use_pages', 0);
    update_option('srslide_cat_as_chan', 1);
    update_option('srslide_css_option', 1);

    $style_defaults = array(
        'element' => array(
            'bottom' => '0px'
        ),
        'link' => array(
            'color' => '#FE3F10'
        )
    );
    update_option('srslide_styles',$style_defaults);
}


/**
 * Update The Slide styles on the SimpleReach site
 *
 * @author Eric Lubow <elubow@simplereach.com>
 * @param Array $styles Style to be converted to JSON
 * @return String OK or error string
 */
function srslide_update_slide_styles()
{
    // Pull all the necessary options
    // Setup our POST parameters
    $params = array();
    $params['ts'] = (int)gmdate('U');
    $params['pid'] = get_option('srslide_pid');

    $srslide_api_key = get_option('srslide_api_key');
    // Handle if the user's API key isn't saved in WP
    if (strlen($srslide_api_key) == 32) {
	$params['api_key'] = get_option('srslide_api_key');
    } else {
	$params['login'] = get_option('srslide_sr_login');
	$params['pass'] = get_option('srslide_sr_pass');
    }

    $params['style'] = get_option('srslide_styles');
    if (!srslide_validate_hex_color($params['style']['link']['color'])) {
	return __('Please specify a valid link color which starts with a "#" and has 6 of the following characters [A-F], [0-9]', 'srslide');
    }
    $encoded_params = http_build_query($params);

    $url = 'https://www.simplereach.com/publisher/generate_slide_css';
    $response = srslide_post_url($url, $encoded_params);

    // Decode the JSON
    $resp = json_decode($response);
    if ($resp === NULL) {
	return __('There was an http error contacting SimpleReach. Please try saving your options again.', 'srslide');
    }

    if ($resp->success) {
        if ($resp->api_key && !srslide_validate_api_key(get_option('srslide_api_key'))) {
            update_option('srslide_api_key', $resp->api_key);
        }
        return 'OK';
    } else {
	$errorResponse = (!empty($resp->error)) ? strtolower($resp->error) : '';
	$errorFooter =  sprintf(__("Please try again or contact <a href='mailto:%s'>SimpleReach support</a> if you believe you have received this message in error.", 'srslide'), SRSLIDE_PLUGIN_SUPPORT_EMAIL);
	switch ($errorResponse) {
		case 'incorrect timestamp':
			$errorMessage = __('There was an error while you were attempting to register.', 'srslide');
		break;
		case 'invalid pid':
            		$errorMessage = __('The PID (publisher ID) that you are attempting to sign in with does not exist.', 'srslide');
		break;
		case 'invalid user':
			$errorMessage = __('The user name that you are attempting to sign in with does not exist.', 'srslide');
		break;
		case 'invalid password':
			$errorMessage = __('The user name that you are attempting to register with already exists, but the password you entered was incorrect.', 'srslide');
		break;
		case 'invalid api key':
			$errorMessage = __('The API key that you are using does not have access to this account or is invalid.', 'srslide');
		break;
		case 'invalid user on account':
			$errorMessage = __('The user name that you are registered with does not have access to this account.', 'srslide');
		break;
		default:
			$errorMessage = __('There was an error contacting SimpleReach. Please try saving your options again.', 'srslide');
		break;
	}
        return $errorMessage . " " . $errorFooter;
    }
}

/**
 * Validate api key
 *
 * @author Eric Lubow <elubow@simplereach.com>
 * @param None
 * @return Boolean
 */
function srslide_validate_api_key($api_key)
{
    return strlen($api_key) == 32;
}

/**
 * srslide_post_url
 *
 * @param mixed $url
 * @param array $data
 * @access public
 * @return void
 */
function srslide_post_url($url, $data=array())
{
    if (extension_loaded('curl')) {
        try {
            return _srslideCurlPostUrl($url, $data);
        } catch (Exception $e) {
            $url = preg_replace('/https/', 'http', $url);
            return _srslideCurlPostUrl($url, $data);
        }
    } else {
        return json_encode(array(
                            'error' => __('Required library [libcurl] not installed', 'srslide'), 
                            'error_msg' => __('Required libary [libcurl] not installed', 'srslide')
                          )
               );
    }
}

function _srslideCurlPostUrl($url, $data=array()) 
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    // curl_setopt($curl, CURLOPT_HEADER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $cv = curl_version();
    // Is SSL Supported?
    if ($cv['features'] && constant('CURL_VERSION_SSL')) {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    }

    $result = curl_exec($curl);
    if ($result === false) {
        $error = curl_error($curl);
        curl_close($curl);
        throw new Exception($error);
    }
    //
    // If we receive a 301, parse the final url and post to it.
    //
    $lastUrl = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
    if ($lastUrl != $url) {
        curl_close($curl);
        return _srslideCurlPostUrl($lastUrl, $data);
    }
    curl_close($curl);
    return $result;
}

function _srslideStreamPostUrl($url, $data=array())
{
    if (!extension_loaded('openssl')) {
        $url = preg_replace('/https/', 'http', $url);
    }
    $params = array('http' => array(
                        'method' => 'POST',
                        'content' => $data
                    )
                );
    if ($optional_headers !== null) {
        $params['http']['header'] = $optional_headers;
    }
    $ctx = stream_context_create($params);
    $fp = fopen($url, 'rb', false, $ctx);
    if (!$fp) {
        throw new Exception("Problem with $url, $php_errormsg");
    }
    $response = @stream_get_contents($fp);
    if ($response === false) {
        throw new Exception("Problem reading data from $url, $php_errormsg");
    }
    return $response;
}

function srslide_validate_pid($pid)
{
    return preg_match('/^[0-9a-fA-F]{24}$/', $pid);
}

function srslide_validate_login($login) {
    $emailValidatorRegex = '/^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i';
    return preg_match($emailValidatorRegex, $login);
}

function srslide_validate_password($password) {
    return preg_match('/^[a-z0-9@#\$%^\+\&\_\-\!\;\,]{6,99}$/i', $password);
}

/**
 * Validate css hex color:
 * Must begin with '#'
 * May only contain [0-9a-f]
 * Must contain either 3 or 6 chars
 *
 * @author Malaney J. Hill <malaney@gmail.com>
 * @param String $value string
 * @return Boolean true if valid / false if invalid
 */
function srslide_validate_hex_color($val)
{
    return preg_match('/^#[a-f0-9]{3}$/i', $val) || preg_match('/^#[a-f0-9]{6}$/i', $val);
}

function srslide_insert_slide_js($content)
{
    $srslide_pid = get_option('srslide_pid');
    $srslide_api_key = get_option('srslide_api_key');

    // Validate PID and api_key
    if (!srslide_validate_pid($srslide_pid)) { 
        return $content;
    }
    if (!srslide_validate_api_key($srslide_api_key)) {
        return $content;
    }

    // Return the content on anything other than post pages
    if (!is_singular()) {
        return $content;
    }

    // Skip attachments and only show on posts
    if (is_attachment()) {
        return $content;
    }

    // Only return slide_js on pages if srslide_use_pages is true
    if (is_page() && !get_option('srslide_use_pages')) {
        return $content;
    }

    global $post;
    $post_id = $post->ID;

    // If the post isn't published yet, we don't need a slide
    if ($post->post_status != 'publish') {
        return $content;
    }

    // Prep the variables
    $slide_article_icon = get_post_meta($post->ID, '_srslide_article_icon', true); 
    $slide_article_icon = preg_replace('/"/', '', $slide_article_icon);
    
    $data['srslide_pid'] = $srslide_pid;
    $data['title'] = srslide_get_post_title($post);
    $data['authors'] = srslide_get_post_authors($post);
    $data['tags'] = srslide_get_post_tags($post);
    $data['channels'] = srslide_get_post_channels($post);
    $data['published_date'] = $post->post_date_gmt;
    $data['canonical_url'] = addslashes(get_permalink($post->ID));
    $data['srslide_plugin_version'] = SRSLIDE_PLUGIN_VERSION;
    $data['srslide_css_url'] = get_option('srslide_css_url');
    $data['srslide_header_text'] = addslashes(get_option('srslide_header_text'));
    $data['srslide_disable_on_post'] = json_encode(!(bool)get_post_meta($post->ID, '_srslide_disable_on_post', true));
    $data['slide_logo_elem'] = (get_option('srslide_show_logo')) ? '' : "\n      slide_logo: false,";
    $data['slide_icon_elem'] = ($slide_article_icon) ? "\n      icon: '" . addslashes($slide_article_icon) . "'," : '';

    $templateEngine = new Mustache();
    $tmpl = file_get_contents(dirname(__FILE__) . '/templates/srslide_js_tag.js');
    return $templateEngine->render($tmpl, $data) . $content;
}

/**
 * srslide_admin_init
 * Inititalize admin / register and load srslide.css
 *
 * @access public
 * @return void
 */
function srslide_admin_init()
{
    wp_register_style('srslide.css', SRSLIDE_PLUGIN_URL . 'srslide.css');
    wp_enqueue_style('srslide.css');
}

/**
 * Add the SimpleReach admin section
 *
 * @author Eric Lubow <elubow@simplereach.com>
 * @param None
 * @return None
 */
function srslide_admin()
{
    include_once('srslide_admin.php');
}

/**
 * Add the SimpleReach admin options to the post CRUD page
 *
 * @author Eric Lubow <elubow@simplereach.com>
 * @param None
 * @return None
 */
function srslide_admin_actions()
{
    add_options_page('SimpleReach Slide', 'SimpleReach Slide', 'manage_options', 'SimpleReach-Slide', 'srslide_admin');
}

/**
 * Add the settings link to the plugin listings menu page
 *
 * @author Eric Lubow <elubow@simplereach.com>
 * @param None
 * @return None
 */
function srslide_plugin_action_links($links,$file)
{
    if ($file == plugin_basename(dirname(__FILE__).'/srslide.php')) {
        $links[] = '<a href="options-general.php?page=SimpleReach-Slide" class="bold">' . __('Settings', 'srslide') . '</a>';
    }
    return $links;
}

/**
 * Setup the locales for i18n
 *
 * @author Malaney Hill <engineering@simplereach.com>
 * @param None
 * @return None
 */
function srslide_textdomain()
{
        $locale        = apply_filters( 'srslide_locale', get_locale() );
        $mofile        = sprintf( 'srslide-%s.mo', $locale );
        $mofile_local  = SRSLIDE_PLUGIN_DIR . '/lang/' . $mofile;

        if (file_exists($mofile_local)) {
                return load_textdomain( 'srslide', $mofile_local );
        } else {
                return false;
	}
}

/**
 * Run the appropriate actions on hooks
 *
 * @author Malaney Hill <engineering@simplereach.com>
 * @param None
 * @return None
 */
function srslide_loaded()
{
	do_action('srslide_loaded');
}

/**
 * srslide_query_vars
 * Register new query string variable to render srslide_css
 *
 * @access public
 * @return array
 */
function srslide_query_vars($qvars)
{
    $qvars[] = 'srslide_css';
    return $qvars;
}

/**
 * srslide_render_css
 * Generate css markup if our query var is set
 *
 * @access public
 * @return string
 */
function srslide_render_css()
{
    global $wp_query;
    if (isset($wp_query->query_vars['srslide_css'])) {
        $srslideStyleArray = get_option('srslide_styles');
        $cssMarkup = '';
        if (!empty($srslideStyleArray)) {
            foreach ($srslideStyleArray as $cssKey => $cssArray) {
                $cssMarkup .= "#simplereach-slide-$cssKey {";
                foreach ($cssArray as $k => $v) {
                    $cssMarkup .= $k . ': ' . $v . ' !important;';
                }
                $cssMarkup .= "}\n";
            }
            header('Content-type: text/css');
            print $cssMarkup;
            exit;
        }
    }
}

// For anyone still running PHP < 5.2
if (!function_exists('json_encode')) {
    function json_encode($data) {
        switch ($type = gettype($data)) {
            case 'NULL':
                return 'null';
            case 'boolean':
                return ($data ? 'true' : 'false');
            case 'integer':
            case 'double':
            case 'float':
                return $data;
            case 'string':
                return '"' . addslashes($data) . '"';
            case 'object':
                $data = get_object_vars($data);
            case 'array':
                $output_index_count = 0;
                $output_indexed = array();
                $output_associative = array();
                foreach ($data as $key => $value) {
                    $output_indexed[] = json_encode($value);
                    $output_associative[] = json_encode($key) . ':' . json_encode($value);
                    if ($output_index_count !== NULL && $output_index_count++ !== $key) {
                        $output_index_count = NULL;
                    }
                }
                if ($output_index_count !== NULL) {
                    return '[' . implode(',', $output_indexed) . ']';
                } else {
                    return '{' . implode(',', $output_associative) . '}';
                }
            default:
                return ''; // Not supported
        }
    }
}

/**
 * Add The Slide options box to the post CRUD page
 *
 * @author Eric Lubow <elubow@simplereach.com>
 * @param None
 * @return None
 */
function srslide_post_meta_box()
{
    if (function_exists('add_meta_box')) {
        add_meta_box('srslide',__('The Slide', 'srslide'), 'srslide_meta','post');
	add_meta_box('srslide',__('The Slide', 'srslide'), 'srslide_meta','page');
    }
}

/**
 * The code for The Slide options box on the post CRUD page
 *
 * @author Eric Lubow <elubow@simplereach.com>
 * @param None
 * @return None
 */
function srslide_meta()
{
    global $post;
    $post_id = $post;
    if (is_object($post_id)) {
        $post_id = $post_id->ID;
    }

    wp_register_style('srslide.css', SRSLIDE_PLUGIN_URL . 'srslide.css');
    wp_enqueue_style('srslide.css');
    wp_nonce_field( plugin_basename(__FILE__), 'srslide_noncename' );

    $title 			= htmlspecialchars(stripcslashes(get_post_meta($post_id, '_srslide_title', true)));
    $article_icon 		= htmlspecialchars(stripcslashes(get_post_meta($post_id, '_srslide_article_icon', true)));
    $tags 			= htmlspecialchars(stripcslashes(get_post_meta($post_id, '_srslide_tags', true)));
    $disable_slide_on_post 	= htmlspecialchars(stripcslashes(get_post_meta($post_id, '_srslide_disable_on_post', true)));

    $data = array(
        'title_text' => __('Title', 'srslide'),
        'tags_text' => __('Tags:', 'srslide'),
        'article_icon_text' => __('Article Icon URL:', 'srslide'),
        'disable_slide_text' => __('Disable Slide on post:', 'srslide'),
        'tags' => $tags,
        'title' => $title,
        'article_icon' => $article_icon,
        'post_end_meridian' => $post_end_meridian,
        'disable_slide_on_post' => $disable_slide_on_post
    );

    $templateEngine = new Mustache();
    $tmpl = file_get_contents(dirname(__FILE__) . '/templates/srslide_meta_box.html');
    print $templateEngine->render($tmpl, $data);
}

/**
 * Save the post meta data for The Slide
 *
 * @author Eric Lubow <elubow@simplereach.com>
 * @param Integer Post ID
 * @return None
 */
function srslide_post_save_data($post_id)
{
    // Ignore if auto-saving
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    $srslide_noncename = (!empty($_POST['srslide_noncename'])) ? $_POST['srslide_noncename'] : '';
    if (!wp_verify_nonce($srslide_noncename, plugin_basename(__FILE__))) {
        return $post_id;
    }

    // Check permissions
    $post_type = (!empty($_POST['post_type'])) ? $_POST['post_type'] : '';
    if ('page' == $post_type) {
        if (!current_user_can( 'edit_page', $post_id ))
            return $post_id;
    } else {
        if (!current_user_can( 'edit_post', $post_id ))
            return $post_id;
    }

    // OK, we're auth'd, time to make the donuts
    global $post;
    $id = $post;
    if (is_object($id)) {
        $id = $post->ID;
    }

    $disable_slide_on_post	= (!empty($_POST['srslide_disable_on_post'])) 	? $_POST['srslide_disable_on_post'] : '';
    $title			= (!empty($_POST['srslide_title'])) 		? $_POST['srslide_title'] : '';
    $tags 			= (!empty($_POST['srslide_tags'])) 		? $_POST['srslide_tags'] : '';
    $icon 			= (!empty($_POST['srslide_article_icon'])) 	? $_POST['srslide_article_icon'] : '';

    // Clear everything out first to start with a clean slate
    delete_post_meta($id, '_srslide_title');
    delete_post_meta($id, '_srslide_tags');
    delete_post_meta($id, '_srslide_article_icon');
    delete_post_meta($id, '_srslide_disable_on_post');
    delete_post_meta($id, '_srslide_post_end_date');

    // Ignore if the slide should be hidden
    add_post_meta($id, '_srslide_disable_on_post', (int)!empty($disable_slide_on_post));

    // Set to post title if the field is empty
    if (empty($title) && ($post->post_status == 'publish')) {
        if (!empty($_POST['post_title'])) {
            add_post_meta($id, '_srslide_title', $_POST['post_title']);
        }
    } else {
        add_post_meta($id, '_srslide_title', $title);
    }

    // Set to post tags if the field is empty
    if (empty($tags) && ($post->post_status == 'publish')) {
        $tagset = array();
        foreach (wp_get_post_tags($id) as $tag) {
	    $tagset[] = $tag->name;
        }
        add_post_meta($id, '_srslide_tags', join(',', $tagset));
    } else {
        add_post_meta($id, '_srslide_tags', $tags);
    }

    if (empty($icon) && ($post->post_status == 'publish')) {
        $thumbnail_id = get_post_meta( $post->ID, '_thumbnail_id', true );
        if ($thumbnail_id) {
            list($img, $h, $w) = wp_get_attachment_image_src($thumbnail_id);
            add_post_meta($id, '_srslide_article_icon', $img);
        }
    } else {
        add_post_meta($id, '_srslide_article_icon', $icon);
    }
}
?>
