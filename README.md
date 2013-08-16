Beeleads Advertiser API (PHP Library)
=======================

Official Beeleads Advertiser API library

#### Methods

- **getPossibleFeedbackValues**: retrieves a list of all possible feedback values
- **provideFeedback**: provides feedback on the specified Integration ID

#### Using 'getPossibleFeedbackValues'

	$blds = new BeeleadsAdvertiser('your-advertiser-id', 'your-api-secret');

	$arr_response = $blds->getPossibleFeedbackValues();


#### Using 'provideFeedback'

###### Requirements:

- Integration ID (provided by Beeleads)

###### Example


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