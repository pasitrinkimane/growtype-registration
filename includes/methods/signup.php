<?php

/**
 * Signup form shortcode
 */
add_shortcode('signup_form', 'signup_form_shortcode_function');

function signup_form_shortcode_function($args)
{
    global $wp_session;

    /**
     * Disable form if user is logged in
     */
    if (is_user_logged_in()) {
        return null;
    }

    if (empty($args)) {
        return null;
    }

    /**
     * Settings
     */

    $placeholder_enabled = false;

    $fields = !empty($args['fields']) ? explode(',', $args['fields']) : null;

    if (empty($fields)) {
        return null;
    }

    $recaptchav3 = isset($args['recaptchav3']) && !empty($args['recaptchav3']) ? $args['recaptchav3'] : null;
    $accept_text = $args['accept_text'] ?? null;
    $confirm_text = $args['confirm_text'] ?? null;
    $promo_text = $args['promo_text'] ?? null;

    if (!empty($recaptchav3)) {
        add_action('wp_footer', function () use (&$recaptchav3) {
            recaptcha_setup($recaptchav3);
        }, 99);
    }

    if (isset($_POST["signup-form-submit"]) && sanitize_text_field($_POST["signup-form-submit"]) === 'true') {
        $fields_values = map_form_fields_values($fields, $_POST);

        if (isset($_POST['promo_checkbox'])) {
            $fields_values['promo_checkbox'] = $_POST['promo_checkbox'];
        }

        if (!empty($fields_values)) {
            $registration = register_user($fields_values);

            if ($registration['success']) {
                $user_login = $registration['user']['id'];
                $user = get_user_by('id', $user_login);
                $user_id = $user->ID;
                wp_set_current_user($user_id, $user_login);
                wp_set_auth_cookie($user_id);
                do_action('wp_login', $user_login);

                return wp_redirect(get_permalink(get_page_by_path('profile')->ID));
            }
        } else {
            $registration['success'] = false;
            $registration['message'] = __("Please fill all required fields.", "growtype-registration");
        }

        /**
         * Prepare redirect details
         */
        $status_args = array (
            'status' => $registration['success'] ? 'success' : 'fail',
            'message' => $registration['message'],
        );

        $fields_values_args = $fields_values;

        $query_args = array_merge($status_args, $fields_values_args);

        $redirect = add_query_arg($query_args, get_permalink());

        return wp_redirect($redirect);
    }

    ob_start();
    ?>
    <div class="signup-form-wrapper">
        <?php if (isset($_GET['status']) && !empty($_GET['status'])) {
            $status_message = sanitize_text_field(filter_input(INPUT_GET, 'message'));
            if ($_GET['status'] === 'success') { ?>
                <div class="alert alert-success" role="alert">
                    <?= __($status_message, "growtype-registration") ?>
                </div>
            <?php } else { ?>
                <div class="alert alert-danger" role="alert">
                    <?= __($status_message, "growtype-registration") ?>
                </div>
            <?php }
        } ?>

        <div id="signup-container" class="container">
            <div class="form-wrapper">
                <h2><?= __("Registration", "growtype-registration") ?></h2>
                <form id="signup-form" class="b-form" action="<?php the_permalink(); ?>" method="post">
                    <div class="row g-3">
                        <?php
                        foreach ($fields as $field) { ?>
                            <?php
                            $field_name = str_replace('*', '', str_replace(' ', '_', $field));
                            $required = str_contains($field, '*');
                            $field_type = 'input';
                            $field_label_enabled = true;
                            $field_hidden = false;
                            $field_value = sanitize_text_field(filter_input(INPUT_GET, $field_name));

                            if (str_contains($field, ':') || str_contains($field, '=')) {
                                $field_settings = substr($field, strpos($field, "=") + 1);
                                $field_settings_values = explode('|', $field_settings);

                                if (str_contains($field, ':')) {
                                    $field_name = str_replace(':' . substr($field_name, strpos($field_name, ":") + 1), '', $field_name);
                                    $field_options = str_replace('=' . $field_settings, '', substr($field, strpos($field, ":") + 1));
                                    $field_options = explode('|', $field_options);
                                } else {
                                    $field_name = str_replace('=' . substr($field, strpos($field, "=") + 1), '', $field_name);
                                    $field_options = [];
                                }

                                if (in_array('radio', $field_settings_values)) {
                                    $field_type = 'radio';
                                } elseif (in_array('select', $field_settings_values)) {
                                    $field_type = 'select';
                                }

                                if (in_array('nolabel', $field_settings_values)) {
                                    $field_label_enabled = false;
                                }

                                if (in_array('hidden', $field_settings_values)) {
                                    $field_hidden = true;
                                }
                            }

                            $types = [
                                'email' => 'email',
                                'password' => 'password',
                                'repeat_password' => 'password',
                            ];

                            $type = $types[$field_name] ?? 'text';

                            $labels = [
                                'email' => __('Email address', 'growtype-registration'),
                                'password' => __('Password', 'growtype-registration'),
                                'repeat_password' => __('Repeat Password', 'growtype-registration'),
                                'first_name' => __('First name', 'growtype-registration'),
                                'last_name' => __('Last name', 'growtype-registration'),
                                'first_and_last_name' => __('First and Last name', 'growtype-registration'),
                                'phone' => __('Phone', 'growtype-registration'),
                                'birthday' => __('Birthday', 'growtype-registration'),
                                'city' => __('City', 'growtype-registration'),
                                'occupation' => __('Occupation', 'growtype-registration'),
                                'country' => __('Country', 'growtype-registration'),
                                'school' => __('School', 'growtype-registration'),
                                'grade' => __('Grade', 'growtype-registration'),
                                'gradegroup' => __('Grade group', 'growtype-registration'),
                                'child_first_and_last_name' => __('Child first and last name', 'growtype-registration'),
                                'username' => __('User name', 'growtype-registration'),
                            ];

                            $label = $labels[$field_name] ?? ucfirst($field_name);
                            $label = str_replace('_', ' ', $label);
                            $label = $required ? $label . '*' : $label;

                            if ($placeholder_enabled) {
                                $placeholder = __('Enter your', 'growtype-registration') . ' ' . strtolower($label);
                            }

                            ?>
                            <div class="col-auto" style="<?= $field_hidden ? 'display:none;' : '' ?>" data-name="<?= $field_name ?>">
                                <?php
                                if ($field_label_enabled) { ?>
                                    <label for="<?= $field_name ?>" class="form-label">
                                        <?= $label ?>
                                    </label>
                                <?php } ?>

                                <?php
                                if ($field_type === 'select') { ?>
                                    <select name="<?= $field_name ?>" id="<?= $field_name ?>">
                                        <?php
                                        foreach ($field_options as $field_option) { ?>
                                            <option value="<?= sanitize_text_field(strtolower(str_replace(' ', '_', $field_option))) ?>"><?= str_replace('_', ' ', $field_option) ?></option>
                                        <?php } ?>
                                    </select>
                                <?php } elseif ($field_type === 'radio') { ?>
                                    <?php
                                    foreach ($field_options as $field_option) { ?>
                                        <div class="radio-wrapper">
                                            <input type="radio" id="<?= str_replace(' ', '_', strtolower($field_option)) ?>" name="<?= $field_name ?>" value="<?= strtolower($field_option) ?>" <?= $required ? 'required' : '' ?>>
                                            <label for="<?= str_replace(' ', '_', strtolower($field_option)) ?>"><?= str_replace('_', ' ', $field_option) ?></label>
                                        </div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <input type="<?= $type ?>"
                                           class="form-control"
                                           name="<?= $field_name ?>"
                                           id="<?= $field_name ?>"
                                           placeholder="<?= $placeholder ?? null ?>"
                                        <?= $required ? 'required' : '' ?>
                                           value="<?= !str_contains($field_name, 'password') ? $field_value : null ?>"
                                    >
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="mt-2 pt-1">
                        <?php if (!empty($accept_text)) { ?>
                            <div class="form-check mt-3" data-name="terms_checkbox">
                                <input type="checkbox" name="terms_checkbox" class="form-check-input" id="terms_checkbox" required>
                                <label class="form-check-label" for="terms_checkbox"><?= $accept_text ?></label>
                            </div>
                        <?php } ?>

                        <?php if (!empty($confirm_text)) { ?>
                            <div class="form-check mt-3" data-name="confirm_checkbox">
                                <input type="checkbox" name="confirm_checkbox" class="form-check-input" id="confirm_checkbox" required>
                                <label class="form-check-label" for="confirm_checkbox"><?= $confirm_text ?></label>
                            </div>
                        <?php } ?>

                        <?php if (!empty($promo_text)) { ?>
                            <div class="form-check mt-3" data-name="promo_checkbox">
                                <input type="checkbox" name="promo_checkbox" class="form-check-input" id="promo_checkbox">
                                <label class="form-check-label" for="promo_checkbox"><?= $promo_text ?></label>
                            </div>
                        <?php } ?>
                    </div>

                    <input type="text" hidden="" name='signup-form-submit' value="true"/>

                    <?php if (!empty($recaptchav3)) { ?>
                        <div class="g-recaptcha"
                             data-sitekey="<?= $recaptchav3 ?>"
                             data-size="invisible"
                             data-callback="signupFormSubmit">
                        </div>
                    <?php } ?>

                    <button type="submit" class="btn btn-primary"><?= __("Register", "growtype-registration") ?></button>
                </form>

                <div class="b-actions">
                    <a class="btn btn-link" href="<?= growtype_registration_login_url() ?>"><?= __("Log In", "growtype-registration") ?></a>
                </div>
            </div>
        </div>
    </div>

    <script>
        if (window.location.search.length > 0 && window.location.search.indexOf('action') !== -1) {
            window.history.replaceState(null, null, window.location.pathname);
        } else if (window.location.search.length > 0 && window.location.search.indexOf('message') !== -1) {
            window.signupFormFailed = true;
            window.history.replaceState(null, null, window.location.pathname);
        }
    </script>

    <?php

    $form = ob_get_clean();

    return $form;
}

/**
 * @param $data
 * @return array
 * Register user method
 */
function register_user($data)
{
    global $wpdb, $user_ID;

    $email = isset($data['email']) ? sanitize_text_field($data['email']) : null;
    $username = isset($data['username']) ? sanitize_text_field($data['username']) : null;
    $username = !empty($username) ? $username : $email;
    $password = isset($data['password']) ? sanitize_text_field($_REQUEST['password']) : null;
    $repeat_password = isset($data['repeat_password']) ? sanitize_text_field($_REQUEST['repeat_password']) : null;

    if (empty($username) || empty($password) || empty($email)) {
        $response['success'] = false;
        $response['message'] = __("Missing required values", "growtype-registration");
        return $response;
    }

    if (!empty($repeat_password)) {
        if ($password !== $repeat_password) {
            $response['success'] = false;
            $response['message'] = __("Passwords do not match", "growtype-registration");
            return $response;
        }
    }

    $validate_password = validate_password($password);

    if ($validate_password['success'] === false) {
        $response['success'] = $validate_password['success'];
        $response['message'] = $validate_password['message'];
        return $response;
    }

    /**
     * Save with unique email. Check if username is provided and email already exists in database.
     */
    if ($username !== $email && email_exists($email)) {
        $email_exploded = explode('@', $email);
        $username_formatted = urlencode(str_replace(' ', '', $username));
        $email = $email_exploded[0] . '+' . $username_formatted . '@' . $email_exploded[1];
    }

    $status = wp_create_user($username, $password, $email);

    if (is_wp_error($status)) {
        $response['success'] = false;
        $response['message'] = __("Profile already registered.", "growtype-registration");
    } else {
        $user_id = $status;

        /**
         * Save extra values
         */
        $skipped_values = ['username', 'password', 'repeat_password', 'email', 'submit'];
        foreach ($data as $key => $value) {
            if (!in_array($key, $skipped_values) && !str_contains($value, 'password') && !empty($value)) {
                if ($key === 'first_and_last_name') {
                    $first_name = explode(' ', $value)[0] ?? null;
                    $last_name = explode(' ', $value)[1] ?? null;
                    $middle_name = explode(' ', $value)[2] ?? null;
                    if (empty($middle_name)) {
                        update_user_meta($user_id, 'first_name', sanitize_text_field($first_name));
                        update_user_meta($user_id, 'last_name', sanitize_text_field($last_name));
                    } else {
                        update_user_meta($user_id, 'first_name', sanitize_text_field($value));
                    }
                }
                update_user_meta($user_id, $key, sanitize_text_field($value));
            }
        }

        $response['user']['id'] = $user_id;
        $response['user']['username'] = $username;
        $response['success'] = true;
        $response['message'] = __("Registration successful.", "growtype-registration");
    }

    return $response;
}

/**
 * @param $fields
 * @param $posted_data
 * @return array
 * Map post fields with shortcode fields
 */
function map_form_fields_values($fields, $posted_data)
{
    $fields_values = [];

    foreach ($fields as $key => $field) {
        $required = str_contains($field, '*');
        $skip_backend_validation = str_contains($field, 'skip_backend_validation');
        $field = str_replace('*', '', str_replace(' ', '_', $field));
        $field = str_replace(':' . substr($field, strpos($field, ":") + 1), '', $field);
        $field = str_replace('=' . substr($field, strpos($field, "=") + 1), '', $field);
        $fields_values[$field] = $posted_data[$field] ?? null;

        if ($required && isset($posted_data[$field]) && empty($fields_values[$field]) && !$skip_backend_validation) {
            return [];
        }
    }

    return $fields_values;
}

/**
 * Change wp default registration url
 */
add_filter('register', 'custom_register_url');

function custom_register_url($link)
{
    $signup = growtype_registration_signup_page();
    return str_replace(site_url('wp-login.php?action=register', 'login'), get_permalink($signup), $link);
}

/**
 * @param $password
 * @return array
 * Validate password
 */
function validate_password($password)
{
    $status['success'] = true;

    if (!empty($password)) {
        if (strlen($password) <= '8') {
            $status['success'] = false;
            $status['message'] = __("Your Password Must Contain At Least 8 Characters!", "growtype-registration");
        } elseif (!preg_match("#[0-9]+#", $password)) {
            $status['success'] = false;
            $status['message'] = __("Your Password Must Contain At Least 1 Number!", "growtype-registration");
        } elseif (!preg_match("#[A-Z]+#", $password)) {
            $status['success'] = false;
            $status['message'] = __("Your Password Must Contain At Least 1 Capital Letter!", "growtype-registration");
        } elseif (!preg_match("#[a-z]+#", $password)) {
            $status['success'] = false;
            $status['message'] = __("Your Password Must Contain At Least 1 Lowercase Letter!", "growtype-registration");
        }
    } else {
        $status['success'] = false;
        $status['message'] = __("Please enter password.", "growtype-registration");
    }

    return $status;
}

function recaptcha_setup($recaptchav3)
{
    ?>
    <style>
        .grecaptcha-badge {
            display: none !important;
        }
    </style>
    <script src="https://www.google.com/recaptcha/api.js?render=<?= $recaptchav3 ?>"></script>
    <script>
        $('#signup-form').submit(function (event) {
            event.preventDefault();
            $(this).find('button[type="submit"]').attr('disabled', true);
            grecaptcha.reset();
            grecaptcha.execute();
        });

        function signupFormSubmit(token) {
            document.getElementById("signup-form").submit();
        }
    </script>
    <?php
}
