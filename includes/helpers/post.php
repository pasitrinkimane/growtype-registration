<?php

/**
 * @return array|WP_Post|null
 */
function growtype_registration_login_page()
{
    return get_option('growtype_registration_login_page') ? get_post(get_option('growtype_registration_login_page')) : false;
}

/**
 * @return array|WP_Post|null
 */
function growtype_registration_login_page_is_active()
{
    return growtype_registration_login_page() && !empty(growtype_registration_login_page()) &&
        is_page(growtype_registration_login_page()->post_name);
}

/**
 * @return array|WP_Post|null
 */
function growtype_registration_signup_page()
{
    return get_option('growtype_registration_signup_page') ? get_post(get_option('growtype_registration_signup_page')) : false;
}

/**
 * @return array|WP_Post|null
 */
function growtype_registration_signup_page_is_active()
{
    return !empty(growtype_registration_signup_page()) &&
        is_page(growtype_registration_signup_page()->post_name);
}

/**
 * @return array|WP_Post|null
 */
function growtype_registration_redirect_after_login_page()
{
    return get_post(get_option('growtype_registration_redirect_after_login_page'));
}
