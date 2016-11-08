<?php
/*
*Function to create an api auth code for use when calling methods from the Panopto API.
*/
function generate_auth_code($userkey, $servername, $applicationkey) {
    $payload = $userkey . "@" . $servername;
    $signedpayload = $payload . "|" . $applicationkey;
    $authcode = strtoupper(sha1($signedpayload));
    echo $authcode;
    return $authcode;
}