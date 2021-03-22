<?php
class Data_Exchanger
{
  // data to send to KYIR - pass in to new instance
  public $kyir_username;
  public $kyir_password;
  public $kyir_facility_id;
  public $hl7_message;

  function __construct($kyir_username, $kyir_password, $kyir_facility_id, $hl7_message)
  {
    $this->kyir_username = $kyir_username;
    $this->kyir_password = $kyir_password;
    $this->kyir_facility_id = $kyir_facility_id;
    $this->hl7_message = $hl7_message;
  }

  // function to send a message to KYIR and receive the result
  function call_kyir()
  {
    $wsdl = "https://kyirqa.chfs.ky.gov/HL7Engine_QA/Cdc.aspx?WSDL";
    $kyir_server = "https://kyirqa.chfs.ky.gov/HL7Engine_QA/cdc/v1/iisservice.svc";
    $kyir_soap_client = new SoapClient(
      $wsdl,
      array(
        'soap_version' => SOAP_1_2, // need this to read content correctly
        'location' => $kyir_server
      )
    );

    // testing: get functions from WSDL
    print_r($kyir_soap_client->__getFunctions());

    echo ("<br><br>");

    // testing: echoBack
    $connectivity_test_params = array(
      'echoBack' => 'hellooooo'
    );
    $connectivity_result = $kyir_soap_client->connectivityTest($connectivity_test_params);
    var_dump($connectivity_result);

    echo ("<br><br>");

    // testing: submitSingleMessage
    $submit_single_message_params = array(
      'username' => $kyir_username,
      'password' => $kyir_password,
      'facilityID' => $kyir_facility_id,
      'hl7Message' => $hl7_message
    );
    $message_result = $kyir_soap_client->connectivityTest($submit_single_message_params);
    var_dump($message_result);

    // return: connection status / error
  }

  // close client/connection?

}

$test_run = new Data_Exchanger('username', 'password', 'facility_id', 'hl7_message');
print_r($test_run->call_kyir());
