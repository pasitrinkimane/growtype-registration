<?php

/**
 * @param $user
 */

add_action('show_user_profile', 'extra_user_profile_fields');
add_action('edit_user_profile', 'extra_user_profile_fields');

function extra_user_profile_fields($user)
{
    ?>
    <h3><?= __("Extra profile information", "growtype-registration"); ?></h3>

    <table class="form-table">
        <tr>
            <th><label for="address"><?= __("Occupation", "growtype-registration"); ?></label></th>
            <td>
                <input type="text" name="occupation" id="occupation" value="<?php echo esc_attr(get_the_author_meta('occupation', $user->ID)); ?>" class="regular-text"/><br/>
                <span class="description"><?= __("Please enter your occupation.", "growtype-registration"); ?></span>
            </td>
        </tr>
        <tr>
            <th><label for="address"><?= __("School", "growtype-registration"); ?></label></th>
            <td>
                <input type="text" name="school" id="school" value="<?php echo esc_attr(get_the_author_meta('school', $user->ID)); ?>" class="regular-text"/><br/>
                <span class="description"><?= __("Please enter your school.", "growtype-registration"); ?></span>
            </td>
        </tr>
        <tr>
            <th><label for="address"><?= __("Birthday", "growtype-registration"); ?></label></th>
            <td>
                <input type="text" name="birthday" id="birthday" value="<?php echo esc_attr(get_the_author_meta('birthday', $user->ID)); ?>" class="regular-text"/><br/>
                <span class="description"><?= __("Please enter your birth date.", "growtype-registration"); ?></span>
            </td>
        </tr>
        <tr>
            <th><label for="postalcode"><?= __("Phone", "growtype-registration"); ?></label></th>
            <td>
                <input type="text" name="phone" id="phone" value="<?php echo esc_attr(get_the_author_meta('phone', $user->ID)); ?>" class="regular-text"/><br/>
                <span class="description"><?= __("Please enter your phone.", "growtype-registration"); ?></span>
            </td>
        </tr>
        <tr>
            <th><label for="address"><?= __("Address", "growtype-registration"); ?></label></th>
            <td>
                <input type="text" name="address" id="address" value="<?php echo esc_attr(get_the_author_meta('address', $user->ID)); ?>" class="regular-text"/><br/>
                <span class="description"><?= __("Please enter your address.", "growtype-registration"); ?></span>
            </td>
        </tr>
        <tr>
            <th><label for="city"><?= __("City", "growtype-registration"); ?></label></th>
            <td>
                <input type="text" name="city" id="city" value="<?php echo esc_attr(get_the_author_meta('city', $user->ID)); ?>" class="regular-text"/><br/>
                <span class="description"><?= __("Please enter your city.", "growtype-registration"); ?></span>
            </td>
        </tr>
        <tr>
            <th><label for="postalcode"><?= __("Postal Code", "growtype-registration"); ?></label></th>
            <td>
                <input type="text" name="postal_code" id="postal_code" value="<?php echo esc_attr(get_the_author_meta('postal_code', $user->ID)); ?>" class="regular-text"/><br/>
                <span class="description"><?= __("Please enter your postal code.", "growtype-registration"); ?></span>
            </td>
        </tr>
        <tr>
            <th><label for="postalcode"><?= __("Country", "growtype-registration"); ?></label></th>
            <td>
                <input type="text" name="country" id="country" value="<?php echo esc_attr(get_the_author_meta('country', $user->ID)); ?>" class="regular-text"/><br/>
                <span class="description"><?= __("Please enter your country.", "growtype-registration"); ?></span>
            </td>
        </tr>
    </table>
    <?php
}

add_action('personal_options_update', 'save_extra_user_profile_fields');
add_action('edit_user_profile_update', 'save_extra_user_profile_fields');

function save_extra_user_profile_fields($user_id)
{
    if (empty($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'update-user_' . $user_id)) {
        return;
    }

    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    update_user_meta($user_id, 'school', $_POST['school']);
    update_user_meta($user_id, 'occupation', $_POST['occupation']);
    update_user_meta($user_id, 'birthday', $_POST['school']);
    update_user_meta($user_id, 'address', $_POST['address']);
    update_user_meta($user_id, 'city', $_POST['city']);
    update_user_meta($user_id, 'postal_code', $_POST['postal_code']);
    update_user_meta($user_id, 'country', $_POST['country']);
    update_user_meta($user_id, 'phone', $_POST['phone']);
}
