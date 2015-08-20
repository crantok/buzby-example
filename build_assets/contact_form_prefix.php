<?php

// CONFIG
$to_email_address = 'example@example.com';
$to_munged_email_address = 'example (at) example (dot) com';
$email_subject = 'Website contact form';


// POST - user form submission

function has_post($name) {
    return ! empty( $_POST[$name] );
}

function get_post($name) {
    if ( has_post($name) ) return urldecode( htmlspecialchars( $_POST[$name] ) );
}

// If this is a POST request then validate the POST variables and try to send
// an email.
//
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $params = [];
    $is_form_complete =
        has_post('message') && has_post( 'name' ) && has_post( 'email' );

    if ( $is_form_complete ) {

        $sender = '"' . get_post('name') . '" <'. get_post( 'email' ) . '>';

        $success = mail(
            $to_email_address,
            $email_subject,
            get_post( 'message' ),
            implode( "\r\n", [ "From: $sender", "Reply-To: $sender" ] )
        );

        if ( $success ) {
            $params['result-status'] = 'success';
            $params['result-message'] = urlencode('Your contact message has been sent. We will do our best to respond.');
        }
        else { // Email failed
            $params['result-status'] = 'error';
            $params['result-message'] =
                urlencode('Something went wrong when we tried to send your email. Please try contacting us directly, care of: ' . $to_munged_email_address );
        }

    }
    else { // Form is incomplete
        $params['result-status'] = 'error';
        $params['result-message'] = urlencode('ERROR: All fields are required. Please complete all fields.');
    }

    // Did something go wrong?
    if ( $params['result-status'] != 'success' ) {
        // Add the user entered data to the redirect so that they don't have to
        // type it all in again.
        $params['name'] = get_post('name');
        $params['email'] = get_post('email');
        $params['message'] = get_post('message');
    }

    foreach ( $params as $key => $val ) {
        $params[$key]= "$key=$val";
    }
    $query_string = implode( '&', $params );

    header( 'Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '?' . $query_string );
    exit;
}
else {
    // This was not a post request. The rest of the file will be executed.
}
?>
