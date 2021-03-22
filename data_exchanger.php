<?php
class Data_Exchanger
{
  // KYIR db credentials - pass in to new instance
  public $kyir_username;
  public $kyir_password;
  public $kyir_facility_id;
  public $hl7_message;

  function __construct($kyir_username, $kyir_password, $kyir_facility_id)
  {
    $this->kyir_username = $kyir_username;
    $this->kyir_password = $kyir_password;
    $this->kyir_facility_id = $kyir_facility_id;
    $this->hl7_message = $hl7_message;
  }

  // connect to KYIR - called by other functions
  function call_kyir($hl7_message)
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
    print_r($kyir_soap_client->__getFunctions());

    echo ("<br><br>");

    $connectivity_test_params = array(
      'echoBack' => 'hellooooo'
    );
    $result = $kyir_soap_client->connectivityTest($connectivity_test_params);
    var_dump($result);

    // return: connection status / error
  }

  // close client/connection?

}

$test_run = new Data_Exchanger('username', 'password', 'facility_id');
print_r($test_run->call_kyir('message'));
