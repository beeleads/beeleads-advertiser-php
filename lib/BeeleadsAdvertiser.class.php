<?php

/**
 * BeeleadsAdvertiser
 *
 * @author Tiago Carvalho <tiago.carvalho@adclick.pt>
 */
class BeeleadsAdvertiser
{

    const API_VERSION = '1.0';
    const API_URL = 'https://hive.bldstools.com/api.php/v1/';

    private $advertiser_id;
    private $secret;
    private $integration_id;

    public function __construct($advertiser_id, $secret)
    {
        $this->advertiser_id = (int) $advertiser_id;
        $this->secret = '' . $secret;
    }

    /**
     * Retrieves all possible feedback values
     * 
     * @return array possible feedback values list (status => boolean, message => string, details => array)
     */
    public function getPossibleFeedbackValues()
    {
        $arr_ret = array(
            'status' => false,
            'message' => 'Invalid response from API. Please try again later. If the problem persists, please contact suporte@beeleads.com.br',
            'details' => array()
        );
        $arr = array('get_feedbacks' => 1);
        $arr = array_map("urlencode", $arr);

        /* Generate Token */
        $token = sha1($this->secret . http_build_query($arr));

        /* Prepare data and build URL */
        $data = http_build_query(array('integration' => $arr));

        $url = self::API_URL . "integration/get_feedbacks/?token={$token}&advertiser_id={$this->advertiser_id}&{$data}";

        /* Call URL and parse the response */
        $arr_call = self::callApi($url);

        if (200 == $arr_call['http_code'])
        {
            $arr_response = @json_decode($arr_call['response'], true);
            if (json_last_error() == JSON_ERROR_NONE)
            { /* This means the API replied a valid JSON response */
                $arr_ret['details'] = $arr_response;

                if (200 == $arr_response['response']['status'])
                { /* Feedback list successfully received */

                    $arr_possible_confirm_reasons = $arr_response['response']['details']['positive'];
                    $arr_possible_invalidate_reasons = $arr_response['response']['details']['negative'];

                    $txt_reasons = '';
                    foreach ($arr_possible_confirm_reasons as $k => $v)
                    {
                        $txt_reasons .= "1-{$k}, ";
                    }
                    foreach ($arr_possible_invalidate_reasons as $k => $v)
                    {
                        $txt_reasons .= "0-{$k}, ";
                    }
                    $txt_reasons = trim($txt_reasons, " ,");
                    $arr_ret['status'] = true;
                    $arr_ret['message'] = $txt_reasons;
                }
                else
                { /* Invalid feedback list */
                    $arr_ret['message'] = 'Could not retrieve possible feedback values';
                }
            }
            else
            { /* Invalid JSON response, something is wrong with the API */
                $arr_ret['details'] = array('Invalid JSON response');
            }
        }
        else
        {
            $arr_ret['details'] = array("Invalid HTTP code. Expected 200, got {$arr_call['http_code']}");
        }

        return $arr_ret;
    }

    /**
     * Provides feedback on a given integration
     * 
     * @param int $integration_id The IntegrationID, provided by Beeleads
     * @param array $arr_feedback desired feedback and optional observations (feedback => string, observations => string)
     * @return array result of feedback operation (status => boolean, message => string, details => array)
     */
    public function provideFeedback($integration_id, $arr_feedback)
    {

        $arr_ret = array(
            'status' => false,
            'message' => 'Invalid response from API. Please try again later. If the problem persists, please contact suporte@beeleads.com.br',
            'details' => array()
        );

        $this->integration_id = (int) $integration_id;

        $arr_feedback['id'] = $this->integration_id;

        $arr_feedback = array_map("urlencode", $arr_feedback);

        /* Generate Token */
        $token = sha1($this->secret . http_build_query($arr_feedback));

        /* Prepare data and build URL */
        $data = http_build_query(array('integration' => $arr_feedback));
        $url = self::API_URL . "integration/feedback/?token={$token}&advertiser_id={$this->advertiser_id}&{$data}";

        /* Call URL and parse the response */
        $arr_call = self::callApi($url);

        if (200 == $arr_call['http_code'])
        {
            $arr_response = @json_decode($arr_call['response'], true);
            if (json_last_error() == JSON_ERROR_NONE)
            { /* This means the API replied a valid JSON response */
                $arr_ret['details'] = $arr_response;

                if (200 == $arr_response['response']['status'])
                { /* Feedback successfully provided */
                    $arr_ret['status'] = true;
                    $arr_ret['message'] = 'Feedback successfully provided';
                }
                else
                { /* API rejected the feedback */
                    $arr_ret['message'] = 'Could not provide feedback';
                }
            }
            else
            { /* Invalid JSON response, something is wrong with the API */
                $arr_ret['details'] = array('Invalid JSON response');
            }
        }
        else
        {
            $arr_ret['details'] = array("Invalid HTTP code. Expected 200, got {$arr_call['http_code']}");
        }

        return $arr_ret;
    }

    /**
     * Calls an URL via GET using cURL or file_get_contents
     * 
     * @param string $url
     * @return array the response (http_code => int, response => string)
     */
    private static function callApi($url)
    {

        if (is_callable('curl_init'))
        {
            $curl_handle = curl_init();
            curl_setopt($curl_handle, CURLOPT_URL, $url);
            curl_setopt($curl_handle, CURLOPT_HEADER, 0);
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT_MS, 10000);
            curl_setopt($curl_handle, CURLOPT_TIMEOUT_MS, 10000);
            curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl_handle, CURLOPT_USERAGENT, 'BeeleadsAffiliate API/1.0');

            $response = curl_exec($curl_handle);

            $http_code = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);

            curl_close($curl_handle);
        }
        else
        {
            $http_code = 200;
            $response = file_get_contents($url);
        }

        return array(
            'http_code' => $http_code,
            'response' => $response
        );
    }

}
