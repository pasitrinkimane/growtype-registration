<?php

/**
 * Redirect after login
 */
if (!function_exists('custom_login_redirect')) {
    add_filter('login_redirect', 'custom_login_redirect');

    function custom_login_redirect()
    {
        return growtype_redirect_url_after_login();
    }
}
