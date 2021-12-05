<?php

/**
 * @return false|string|WP_Error|null
 * Custom profile page url
 */
function growtype_registration_user_profile_url()
{
    return !empty(get_page_by_path('profile')) ? get_permalink(get_page_by_path('profile')) : null;
}

/**
 * @return false|string|WP_Error|null
 * Custom registration url
 */
function growtype_registration_login_url()
{
    return !empty(growtype_registration_login_page()) ? get_permalink(growtype_registration_login_page()) : null;
}

/**
 * @return false|string|WP_Error|null
 * Custom signup url
 */
function growtype_registration_signup_url()
{
    return !empty(growtype_registration_signup_page()) ? get_permalink(growtype_registration_signup_page()) : null;
}

/**
 * @return false|string|WP_Error|null
 * Custom lost password url
 */
function growtype_registration_lostpassword_url()
{
    return wp_lostpassword_url();
}

/**
 * @return false|string|WP_Error|null
 * Custom lost password url
 */
function growtype_redirect_url_after_login()
{
    $redirect_page = growtype_registration_redirect_after_login_page();

    if (isset($_SERVER['HTTP_REFERER']) && str_contains($_SERVER['HTTP_REFERER'], 'wp/wp-login')) {
        $redirect_url = get_dashboard_url();
    } elseif ($redirect_page === 'dashboard') {
        $redirect_url = get_dashboard_url();
    } else {
        $redirect_url = get_permalink($redirect_page);
    }

    return $redirect_url;
}
