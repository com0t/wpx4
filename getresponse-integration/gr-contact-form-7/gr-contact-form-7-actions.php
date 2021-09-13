<?php

defined( 'ABSPATH' ) || exit;

// Add new contact to campaign.
add_action( 'wpcf7_before_send_mail', 'gr_add_contact_from_contact_form_7', 5, 1 );

/**
 * Add contact to GetResponse.
 */
function gr_add_contact_from_contact_form_7() {

    if ( false === gr()->contactForm7->is_enabled() ) {
        return;
    }

    $posted_data = WPCF7_Submission::get_instance()->get_posted_data();

    $name = $posted_data['your-name'];
    $email = $posted_data['email'];
    $signup_to_newsletter = $posted_data['signup-to-newsletter'];

    $customs = [];

    foreach ($posted_data as $key => $value) {
        if (preg_match('/gr\_custom\:(\w*)/i', $key, $result) && !empty($value)) {
            $customs[$result[1]] = $value;
        }
    }

    if (is_array($signup_to_newsletter)) {
        $signup_to_newsletter = join('', $signup_to_newsletter);
    }

    if (empty($signup_to_newsletter) || empty($email)) {
        return;
    }

    try {
        gr()->contactForm7->add_contact($name, $email, $customs);
    } catch (Exception $e) {
        return;
    }
}
