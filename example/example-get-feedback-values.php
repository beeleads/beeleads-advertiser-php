<?php

require_once '../lib/BeeleadsAdvertiser.class.php';

$blds = new BeeleadsAdvertiser('your-advertiser-id', 'your-api-secret');

/* retrieving all possible feedbacks */
$arr_response = $blds->getPossibleFeedbackValues();
if (true == $arr_response['status'])
{
    echo "Possible Feedback Values: {$arr_response['message']}\n";
}
else
{
    echo "ERROR - could not retrieve possible Feedback Values\n";
}

echo "Response: \n";
print_r($arr_response);