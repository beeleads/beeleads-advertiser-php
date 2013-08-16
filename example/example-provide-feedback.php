<?php

require_once '../lib/BeeleadsAdvertiser.class.php';

$blds = new BeeleadsAdvertiser('your-advertiser-id', 'your-api-secret');

$integration_id = 12345; /* Integration ID (provided by Beeleads) */
$arr_feedback = array(
    'feedback' => "0-INVALID_DATA",
    'observations' => "Phone does not exist"
);

$arr_response = $blds->provideFeedback($integration_id, $arr_feedback);

if (true == $arr_response['status'])
{
    echo "OK - Feedback provided successfully\n";
}
else
{
    echo "ERROR - Could not provide feedback\n";
}

echo "Response: \n";
print_r($arr_response);
