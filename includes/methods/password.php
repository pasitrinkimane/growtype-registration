<?php

add_shortcode('lostpassword_form', 'lostpassword_form_callback');

function lostpassword_form_callback()
{
    ob_start();
    if (!is_user_logged_in()) {
        global $getPasswordError, $getPasswordSuccess;

        if (isset($_POST['formType']) && wp_verify_nonce($_POST['formType'], 'userGetPassword')) {
            $user_login = trim($_POST['user_login']);

            if (empty($user_login)) {
                $getPasswordError = __("Please provide a valid username or email address.", "growtype-registration");
            } else {

                if (is_email($user_login)) {
                    $user = get_user_by('email', $user_login);
                } else {
                    $user = get_user_by('login', $user_login);
                }

                if (empty($user)) {
                    $getPasswordError = __("Please provide a valid username or email address.", "growtype-registration");
                } else {
                    $email = $user->user_email;

                    if (empty($email)) {
                        $getPasswordError = __("Email address is missing.", "growtype-registration");
                    } else {
                        $random_password = wp_generate_password(12, false);

                        $update_user = wp_update_user(array (
                                'ID' => $user->ID,
                                'user_pass' => $random_password
                            )
                        );

                        if ($update_user) {
                            $to = $email;
                            $subject = __("Your new password", "growtype-registration");
                            $sender = get_bloginfo('name');

                            $message = __("Your new password is:", "growtype-registration") . ' ' . $random_password;

                            /* $headers[] = 'MIME-Version: 1.0' . "\r\n";
                              $headers[] = 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                              $headers[] = "X-Mailer: PHP \r\n";
                              $headers[] = 'From: ' . $sender . ' < ' . $email . '>' . "\r\n"; */
                            $headers = array ('Content-Type: text/html; charset=UTF-8');

                            $mail = wp_mail($to, $subject, $message, $headers);

                            if ($mail) {
                                $getPasswordSuccess = __("Check your email", "growtype-registration");
                            }

                            return wp_redirect(add_query_arg('action', 'recovered', growtype_registration_login_url()));
                        } else {
                            $getPasswordError = __("Sorry, something went wrong.", "growtype-registration");
                        }
                    }
                }
            }

            return wp_redirect(add_query_arg('message', $getPasswordError, growtype_registration_lostpassword_url()));
        }

        ?>
        <div id="lostpassword" class="container">

            <div class="logo-wrapper">
                <a href="<?= get_home_url() ?>" class="e-logo"><img src="<?= get_header_logo()['url'] ?>" class="img-fluid"/></a>
            </div>

            <?php
            if (isset($_GET['message']) && !empty($_GET['message'])) {
                $getPasswordError = $_GET['message'];
            }
            ?>

            <?php if (!empty($getPasswordError)) { ?>
                <div class="alert alert-danger">
                    <?php echo $getPasswordError; ?>
                </div>
            <?php } ?>

            <?php if (!empty($getPasswordSuccess)) { ?>
                <div class="alert alert-success">
                    <?php echo $getPasswordSuccess; ?>
                </div>
            <?php } ?>

            <?php if (empty($getPasswordSuccess) && empty($getPasswordError)) { ?>
                <div class="alert alert-secondary" role="alert">
                    <?= __("Please enter your username or email address. You will receive an email message with instructions on how to reset your password.", "growtype-registration") ?>
                </div>
            <?php } ?>

            <div class="form-wrapper lostpassword-wrapper">
                <form method="post" class="wc-forgot-pwd-form">
                    <div class="forgot_pwd_form">
                        <div class="log_user">
                            <label for="user_login"><?= __("Username or Email Address", "growtype-registration") ?></label>
                            <?php $user_login = isset($_POST['user_login']) ? $_POST['user_login'] : ''; ?>
                            <input type="text" name="user_login" id="user_login" value="<?php echo $user_login; ?>" required/>
                        </div>
                        <div class="log_user">
                            <?php
                            ob_start();
                            do_action('lostpassword_form');
                            echo ob_get_clean();
                            ?>
                            <?php wp_nonce_field('userGetPassword', 'formType'); ?>
                            <div class="">
                                <button type="submit" class="btn btn-primary get_new_password"><?= __("Get New Password", "growtype-registration") ?></button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="b-actions">
                    <a class="btn btn-link" href="<?= growtype_registration_login_url() ?>" title="Recover Lost Password"><?= __("Log In", "growtype-registration") ?></a>
                    <span class="e-dot">•</span>
                    <a class="btn btn-link" href="<?= growtype_registration_signup_url() ?>" title="Recover Lost Password"><?= __("Register", "growtype-registration") ?></a>
                </div>
            </div>
        </div>
        <?php
    }

    $forgot_pwd_form = ob_get_clean();
    return $forgot_pwd_form;
}
