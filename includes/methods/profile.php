<?php

/**
 * @param null $user_id
 * @return false|WP_User
 */
function get_user_details($user_id = null)
{
    if (empty($user_id)) {
        $user_id = get_current_user_id();
    }

    $user_data = get_userdata($user_id);

    if (!$user_data || empty($user_data)) {
        return null;
    }

    $user_data->data->first_name = get_user_meta( $user_data->ID, 'first_name', true );
    $user_data->data->last_name = get_user_meta( $user_data->ID, 'last_name', true );
    $user_data->data->full_name = $user_data->data->first_name . ' ' . $user_data->data->last_name;
    $user_data->data->name_and_surname = get_user_meta($user_data->ID, 'first_and_last_name', true);
    $user_data->data->phone = get_user_meta($user_data->ID, 'phone', true);
    $user_data->data->birthday = get_user_meta($user_data->ID, 'birthday', true);
    $user_data->data->city = get_user_meta($user_data->ID, 'city', true);
    $user_data->data->school = get_user_meta($user_data->ID, 'school', true);
    $user_data->data->grade = get_user_meta($user_data->ID, 'grade', true);
    $user_data->data->child_first_and_last_name = get_user_meta($user_data->ID, 'child_first_and_last_name', true);
    $user_data->data->occupation = get_user_meta($user_data->ID, 'occupation', true);
    $user_data->data->gradegroup = get_user_meta($user_data->ID, 'gradegroup', true);

    return $user_data;
}
