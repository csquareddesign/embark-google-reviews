<?php

/**

 * @package Embark Google Reviews

 */

/*

Plugin Name: Embark Google Reviews
Description: Used to display a grid or slider feed of Google Reviews.
Version: 1.0.0
Author: Embark Agency
Author URI: https://www.embarkagency.com.au/
License: GPLv2 or later
Text Domain: embark

GitHub Plugin URI: https://github.com/csquareddesign/embark-google-reviews

 */

if (!defined('ABSPATH')) // Or some other WordPress constant
{
    exit;
}

class Embark_GoogleReviews
{

    public function _default_template()
    {
        return <<<EOD
<div class='review-item'>
    <div class='review-body' data-review-index='{{index}}'>
        {{stars}}
        <p>{{body}}</p>
        <div class='review-read-more'>
            <a href='javascript: void(0);'>Read more</a>
        </div>
        {{google_icon}}
    </div>
    <div class='review-author-picture'>
        {{picture}}
    </div>
    <div class='review-meta'>
        <div class='author-name'>{{name}}</div>
        <div class='author-date'>{{date}}</div>
    </div>
</div>
EOD;
    }

    public function _config()
    {
        $config_options = self::_default_options();

        $config = (object) [];

        $config->api_key = isset($config_options["google_api_key_0"]) ? $config_options["google_api_key_0"] : "";
        $config->place_id = isset($config_options["place_id_1"]) ? $config_options["place_id_1"] : "";
        $config->is_slider = isset($config_options["is_slider_2"]) ? "true" : "false";
        $config->read_more = isset($config_options["read_more_3"]) ? "true" : "false";
        $config->min_rating = isset($config_options["minimum_star_rating_4"]) ? $config_options["minimum_star_rating_4"] : '4';
        $config->template = isset($config_options["template_0"]) ? $config_options["template_0"] : self::_default_template();

        return $config;
    }

    public function _default_options()
    {
        return get_option('_embark_google_reviews_settings_option_name');
    }

    public function _template()
    {
        $config = self::_config();

        echo '<template id="embark-reviews-html-template">';
        echo $config->template;
        echo '</template>';
    }

    public function _shortcode()
    {
        $config = self::_config();

        $output .= "<div
            class='embark reviews-container'
            data-min-rating='$config->min_rating'
            data-is-slider='$config->is_slider'
            data-read-more='$config->read_more'
            data-place-id='$config->place_id'
        >";

        if ($config->is_slider === 'true') {
            $output .= "<div class='reviews-arrow prev'></div>";
        }
        $output .= "<div class='reviews-grid'></div>";
        if ($config->is_slider === 'true') {
            $output .= "<div class='reviews-arrow next'></div>";
        }
        $output .= "</div>";

        return $output;
    }

    public function _embark_google_reviews_settings_add_plugin_page()
    {
        add_options_page(
            'Google Reviews Settings', // page_title
            'Google Reviews', // menu_title
            'manage_options', // capability
            'embark-google-reviews', // menu_slug
            array('Embark_GoogleReviews', '_embark_google_reviews_settings_create_admin_page') // function
        );
    }

    public function _embark_google_reviews_settings_create_admin_page()
    {
        ?>
		<div class="wrap">
			<h2>[Embark] Google Reviews Settings</h2>
			<?php settings_errors();?>

			<form method="post" action="options.php">
				<?php
settings_fields('_embark_google_reviews_settings_option_group');
        do_settings_sections('embark-google-reviews-settings-admin');
        submit_button();
        ?>
			</form>
		</div>
	<?php }

    public function _embark_google_reviews_settings_page_init()
    {
        register_setting(
            '_embark_google_reviews_settings_option_group', // option_group
            '_embark_google_reviews_settings_option_name', // option_name
            array('Embark_GoogleReviews', '_embark_google_reviews_settings_sanitize') // sanitize_callback
        );

        add_settings_section(
            '_embark_google_reviews_settings_setting_section', // id
            'Settings', // title
            array('Embark_GoogleReviews', '_embark_google_reviews_settings_section_info'), // callback
            'embark-google-reviews-settings-admin' // page
        );

        add_settings_field(
            'google_api_key_0', // id
            'Google API Key', // title
            array('Embark_GoogleReviews', 'google_api_key_0_callback'), // callback
            'embark-google-reviews-settings-admin', // page
            '_embark_google_reviews_settings_setting_section' // section
        );

        add_settings_field(
            'place_id_1', // id
            'Place ID', // title
            array('Embark_GoogleReviews', 'place_id_1_callback'), // callback
            'embark-google-reviews-settings-admin', // page
            '_embark_google_reviews_settings_setting_section' // section
        );

        add_settings_field(
            'is_slider_2', // id
            'Is Slider', // title
            array('Embark_GoogleReviews', 'is_slider_2_callback'), // callback
            'embark-google-reviews-settings-admin', // page
            '_embark_google_reviews_settings_setting_section' // section
        );

        add_settings_field(
            'read_more_3', // id
            'Read More', // title
            array('Embark_GoogleReviews', 'read_more_3_callback'), // callback
            'embark-google-reviews-settings-admin', // page
            '_embark_google_reviews_settings_setting_section' // section
        );

        add_settings_field(
            'minimum_star_rating_4', // id
            'Minimum Star Rating', // title
            array('Embark_GoogleReviews', 'minimum_star_rating_4_callback'), // callback
            'embark-google-reviews-settings-admin', // page
            '_embark_google_reviews_settings_setting_section' // section
        );

        add_settings_field(
            'template_0', // id
            'Template', // title
            array('Embark_GoogleReviews', 'template_0_callback'), // callback
            'embark-google-reviews-settings-admin', // page
            '_embark_google_reviews_settings_setting_section' // section
        );

        add_settings_section(
            '_embark_google_reviews_settings_setting_section_2', // id
            'Tags/CSS Variables', // title
            array('Embark_GoogleReviews', '_embark_google_reviews_settings_section_info_2'), // callback
            'embark-google-reviews-settings-admin' // page
        );

    }

    public function _embark_google_reviews_settings_sanitize($input)
    {
        $sanitary_values = array();
        if (isset($input['google_api_key_0'])) {
            $sanitary_values['google_api_key_0'] = sanitize_text_field($input['google_api_key_0']);
        }

        if (isset($input['place_id_1'])) {
            $sanitary_values['place_id_1'] = sanitize_text_field($input['place_id_1']);
        }

        if (isset($input['is_slider_2'])) {
            $sanitary_values['is_slider_2'] = $input['is_slider_2'];
        }

        if (isset($input['read_more_3'])) {
            $sanitary_values['read_more_3'] = $input['read_more_3'];
        }

        if (isset($input['minimum_star_rating_4'])) {
            $sanitary_values['minimum_star_rating_4'] = $input['minimum_star_rating_4'];
        }

        if (isset($input['template_0'])) {
            $sanitary_values['template_0'] = $input['template_0'];
        }

        return $sanitary_values;
    }

    public function _embark_google_reviews_settings_section_info()
    {

    }

    public function _embark_google_reviews_settings_section_info_2()
    {
        $tags = [
            "index",
            "stars",
            "body",
            "picture",
            "name",
            "date",
            "google_icon",
        ];
        ?>
        These tags can be used in the template section
        <?php
foreach ($tags as $tag) {
            echo '<code style="border-radius: 5px; margin: 0 5px; display: inline-block; background-color: #dbdbdb;"><pre style="margin: 0;">{{' . $tag . '}}</pre></code>';
        }

        ?>
        <br />
        <div style="font-size: 14px;">
            <h4>Shortcode: <code style="display: inline"><pre style="display: inline; font-weight: normal">[embark_google_reviews]</pre></code></h4>
        </div>
        <br />
    <?php

        $vars = [
            "--google-reviews-primary",
            "--google-reviews-secondary",
            "--google-reviews-nav-primary",
            "--google-reviews-nav-secondary",

            "--google-reviews-body-fontsize",
            "--google-reviews-body-lineheight",

            "--google-reviews-star-icon",
            "--google-reviews-google-icon",
        ];

        ?>
        These css variables can be modified in your theme stylesheet
        <?php
foreach ($vars as $var) {
            echo '<br /><code style="margin: 0 5px; display: inline-block; background-color: #dbdbdb;"><pre style="margin: 0; min-width: 300px;">' . $var . '</pre></code>';
        }
        echo self::_template();
    }

    public function google_api_key_0_callback()
    {
        $config = self::_config();

        printf(
            '<input class="regular-text" type="text" name="_embark_google_reviews_settings_option_name[google_api_key_0]" id="google_api_key_0" value="%s">',
            isset($config->api_key) ? esc_attr($config->api_key) : ''
        );
    }

    public function place_id_1_callback()
    {
        $config = self::_config();

        printf(
            '<input class="regular-text" type="text" name="_embark_google_reviews_settings_option_name[place_id_1]" id="place_id_1" value="%s">',
            isset($config->place_id) ? esc_attr($config->place_id) : ''
        );
    }

    public function is_slider_2_callback()
    {
        $config = self::_config();

        if ($config->is_slider === 'true') {
            $config->is_slider = 'is_slider_2';
        }

        printf(
            '<input type="checkbox" name="_embark_google_reviews_settings_option_name[is_slider_2]" id="is_slider_2" value="is_slider_2" %s> <label for="is_slider_2">Check this if you want the Google Reviews feed to display in a carousel</label>',
            (isset($config->is_slider) && $config->is_slider === 'is_slider_2') ? 'checked' : ''
        );
    }

    public function read_more_3_callback()
    {
        $config = self::_config();

        if ($config->read_more === 'true') {
            $config->read_more = 'read_more_3';
        }

        printf(
            '<input type="checkbox" name="_embark_google_reviews_settings_option_name[read_more_3]" id="read_more_3" value="read_more_3" %s> <label for="read_more_3">Check this if you want to keep Reviews the same height and display a read more button</label>',
            (isset($config->read_more) && $config->read_more === 'read_more_3') ? 'checked' : ''
        );
    }

    public function minimum_star_rating_4_callback()
    {
        $config = self::_config();

        ?> <select name="_embark_google_reviews_settings_option_name[minimum_star_rating_4]" id="minimum_star_rating_4">
			<?php $selected = (isset($config->min_rating) && $config->min_rating === '1') ? 'selected' : '';?>
			<option value="1" <?php echo $selected; ?>>One</option>
			<?php $selected = (isset($config->min_rating) && $config->min_rating === '2') ? 'selected' : '';?>
			<option value="2" <?php echo $selected; ?>>Two</option>
			<?php $selected = (isset($config->min_rating) && $config->min_rating === '3') ? 'selected' : '';?>
			<option value="3" <?php echo $selected; ?>>Three</option>
			<?php $selected = (isset($config->min_rating) && $config->min_rating === '4') ? 'selected' : '';?>
			<option value="4" <?php echo $selected; ?>>Four</option>
			<?php $selected = (isset($config->min_rating) && $config->min_rating === '5') ? 'selected' : '';?>
			<option value="5" <?php echo $selected; ?>>Five</option>
		</select> <?php
}

    public function template_0_callback()
    {
        $config = self::_config();

        printf(
            '<textarea class="large-text" data-editor="html" data-gutter="1" style="width: 600px;" rows="15" name="_embark_google_reviews_settings_option_name[template_0]" id="template_0">%s</textarea><button type="button" onclick="resetTemplate()">Reset</button>',
            isset($config->template) ? esc_attr($config->template) : ''
        );
    }

    public function init_admin()
    {
        if (is_admin()) {
            add_action('admin_menu', array('Embark_GoogleReviews', '_embark_google_reviews_settings_add_plugin_page'));
            add_action('admin_init', array('Embark_GoogleReviews', '_embark_google_reviews_settings_page_init'));

            add_action('admin_enqueue_scripts', function ($hook) {
                wp_enqueue_script('jquery');
                wp_enqueue_script('ace-editor-main', 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.min.js', array(), '', true);
                wp_enqueue_script('ace-editor-html', 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/mode-html.min.js', array(), '', true);
                wp_enqueue_script('ace-editor-github', 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/theme-github.min.js', array(), '', true);
                wp_enqueue_script('embark-admin-google-reviews-js', plugin_dir_url(__FILE__) . 'js/embark-google-reviews-admin.js');
            });
        }
    }

    public function init()
    {
        $config = self::_config();

        add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) {
            array_unshift($links, '<a href="' .
                admin_url('options-general.php?page=embark-google-reviews') .
                '">' . __('Settings') . '</a>');
            return $links;
        });

        wp_enqueue_script('maps-js', 'https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key=' . $config->api_key, array(), '', false);
        wp_enqueue_script('embark-google-reviews-js', plugin_dir_url(__FILE__) . 'js/embark-google-reviews.js', array(), '', false);
        wp_enqueue_style('embark-google-reviews-css', plugin_dir_url(__FILE__) . 'css/embark-google-reviews.css');

        if ($config->is_slider === 'true') {
            wp_enqueue_style('slick-css', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
            wp_enqueue_script('slick-js', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array(), '', true);
        }

        self::init_admin();

        add_shortcode('embark_google_reviews', ['Embark_GoogleReviews', '_shortcode']);

        add_action('wp_footer', ['Embark_GoogleReviews', '_template']);
    }
}

Embark_GoogleReviews::init();
