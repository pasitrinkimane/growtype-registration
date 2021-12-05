<?php

/**
 * Wp default login page enqueue scripts
 */
add_action('login_enqueue_scripts', 'growtype_registration_extend_login_enqueue_scripts');
function growtype_registration_extend_login_enqueue_scripts()
{
    wp_enqueue_style('growtype-registration-css', plugin_dir_url(dirname(dirname(__FILE__))) . '/public/styles/growtype-registration.css', array (), '1.0', 'all');
    wp_enqueue_style('growtype-registration-login-css', plugin_dir_url(dirname(dirname(__FILE__))) . '/public/styles/growtype-registration-login.css', array (), '1.0', 'all');
}

/**
 * Extend default scripts
 */
add_action('wp_enqueue_scripts', 'growtype_registration_extend_enqueue_scripts');
function growtype_registration_extend_enqueue_scripts()
{
    if (growtype_registration_login_page_is_active()) {
        wp_enqueue_style('growtype-registration-css', plugin_dir_url(dirname(dirname(__FILE__))) . '/public/styles/growtype-registration.css', array (), '1.0', 'all');
        wp_enqueue_style('growtype-registration-login-css', plugin_dir_url(dirname(dirname(__FILE__))) . '/public/styles/growtype-registration-login.css', array (), '1.0', 'all');
    }
}

/**
 * Signup form shortcode
 */
add_shortcode('login_form', 'login_form_shortcode_function');

function login_form_shortcode_function($args)
{
    global $wp_session;

    if (is_user_logged_in()) {
        return wp_redirect(get_dashboard_url());
    }

    $args = array (
        'redirect' => admin_url(),
        'form_id' => 'loginform-custom',
        'label_username' => __('Username', "growtype-registration"),
        'label_password' => __('Password', "growtype-registration"),
        'label_remember' => __('Remember Me', "growtype-registration"),
        'label_log_in' => __('Log In', "growtype-registration"),
        'remember' => true
    );

    ob_start();

    echo wp_login_form($args);

    $form = ob_get_clean();

    return $form;
}

/**
 * Change the login url sitewide to the custom login page
 */
add_filter('login_url', 'custom_login_url', 10, 2);

function custom_login_url($login_url = '', $redirect = '')
{
    if (isset($_GET["action"]) && $_GET["action"] === 'lostpassword') {
        $login_url = growtype_registration_login_url();
    }

    return $login_url;
}

/**
 * Updates authentication to return an error when one field or both are blank
 */
add_filter('authenticate', 'custom_authenticate_username_password', 30, 3);

function custom_authenticate_username_password($user, $username, $password)
{
    if (is_a($user, 'WP_User')) {
        return $user;
    }

    if (empty($username) || empty($password)) {
        $error = new WP_Error();
        $user = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.', "growtype-registration"));

        return $error;
    }
}

function login_page_body_class($classes)
{
    if (growtype_registration_login_page_is_active()) {
        $classes[] = 'login-template-' . growtype_registration_get_login_template();
    }

    return $classes;
}

add_filter('body_class', 'login_page_body_class');

/**
 * Automatically ads the login form to "login" page
 */
add_filter('the_content', 'custom_default_login_form_to_login_page');

function custom_default_login_form_to_login_page($content)
{
    $login_page = growtype_registration_login_page();

    if (!empty($login_page) && is_page($login_page->post_name) && in_the_loop()) {
        $message = "";
        if (!empty($_GET['action'])) {
            if ('failed' == $_GET['action']) {
                $message = __("Wrong login details. Please try again.", "growtype-registration");
            } elseif ('loggedout' == $_GET['action']) {
                $message = __("You are now logged out.", "growtype-registration");
            } elseif ('recovered' == $_GET['action']) {
                $message = __("Check your e-mail for login information.", "growtype-registration");
            }
        }

        ob_start();
        ?>
        <div class="login-container">
            <div class="login-container-inner">
                <div class="intro">
                    <?= get_the_content(); ?>
                </div>
                <div id="login" class="login">
                    <div class="login-logo-wrapper logo-wrapper">
                        <a href="<?= get_home_url() ?>" class="e-logo"><img src="<?= get_login_logo()['url'] ?>" class="img-fluid"/></a>
                    </div>

                    <?php if ($message) { ?>
                        <?php if (isset($_GET['action']) && $_GET['action'] === 'failed') { ?>
                            <div class="alert alert-danger" role="alert">
                                <?= __($message, 'growtype-registration') ?>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-success" role="alert">
                                <?= __($message, 'growtype-registration') ?>
                            </div>
                        <?php } ?>
                    <?php } ?>

                    <div class="form-wrapper loginform-wrapper">
                        <?= wp_login_form('echo=0&redirect=' . site_url()) ?>

                        <div class="b-actions">
                            <a class="btn btn-link" href="<?= growtype_registration_signup_url() ?>" title="Recover Lost Password"><?= __("Register", "growtype-registration") ?></a>
                            <span class="e-dot">|</span>
                            <a class="btn btn-link" href="<?= growtype_registration_lostpassword_url() ?>"><?= __("Lost your password?", "growtype-registration") ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $content = ob_get_clean();
    }

    return $content;
}

/**
 * Updates login failed to send user back to the custom form with a query var
 */
add_action('wp_login_failed', 'custom_login_failed', 10, 2);

function custom_login_failed($username)
{
    $referrer = wp_get_referer();

    if (isset($_SERVER['HTTP_REFERER']) && str_contains($_SERVER['HTTP_REFERER'], 'wp/wp-admin')) {
        return wp_login_url();
    }

    if (!empty($referrer) && !empty(growtype_registration_login_page())) {
        if (!empty($_GET['loggedout'])) {
            return wp_redirect(add_query_arg('action', 'loggedout', growtype_registration_login_url()));
        } else {
            return wp_redirect(add_query_arg('action', 'failed', growtype_registration_login_url()));
        }
    }
}
