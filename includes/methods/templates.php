<?php

/**
 * Load a template.
 *
 * Handles template usage so that we can use our own templates instead of the theme's.
 *
 * Templates are in the 'templates' folder.
 * @param string $template Template to load.
 * @return string
 */

add_filter('page_template', 'growtype_registration_page_template_loader');

function growtype_registration_page_template_loader($template)
{
    $signup_page = growtype_registration_signup_page();
    $login_page = growtype_registration_login_page();
    $lostpassword_page = get_page_by_path('lostpassword');
    $profile_page = get_page_by_path('profile');

    switch (get_the_ID()) {
        case $profile_page->ID ?? null:
            $template_file = 'page-profile.blade.php';
            break;
        case $login_page->ID ?? null:
            $template = get_option('growtype_registration_login_page_template');
            if ($template === 'style-1') {
                $template_file = 'login/page-style-1.blade.php';
            } else {
                $template_file = 'login/page-default.blade.php';
            }
            break;
        case $signup_page->ID ?? null:
            $template_file = 'page-signup.blade.php';
            break;
        case $lostpassword_page->ID ?? null:
            $template_file = 'page-lostpassword.blade.php';
            break;
    }

    if (isset($template_file)) {
        if (file_exists(get_stylesheet_directory() . '/views/' . $template_file)) {
            $template = get_stylesheet_directory() . '/views/' . $template_file;
        } else {
            $template = plugin_dir_path(dirname(dirname(__FILE__))) . 'resources/views/' . $template_file;
        }
    }

    return $template;
}
