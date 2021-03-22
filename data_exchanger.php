<?php
// experiments in connecting to the KYIR server
$wsdl = "https://kyirqa.chfs.ky.gov/HL7Engine_QA/Cdc.aspx?WSDL";
$kyir_server = "https://kyirqa.chfs.ky.gov/HL7Engine_QA/cdc/v1/iisservice.svc";
$kyir_soap_client = new SoapClient(
  $wsdl,
  array(
    'soap_version' => SOAP_1_2, // need this to read content correctly
    'location' => $kyir_server
  ));
print_r($kyir_soap_client->__getFunctions());

echo ("<br><br>");

$connectivity_test_params = array(
  'echoBack' => 'hellooooo'
);
$result = $kyir_soap_client->connectivityTest($connectivity_test_params);
var_dump($result);

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
  function call_kyir($wsdl_method, $hl7_message)
  {
    // input: kyir wsdl method required by function
    // input: message defined by function

    // connect to KYIR IIS
    // https://kyirqa.chfs.ky.gov/HL7Engine_QA/CDC/V1/IISService.svc

    // return: connection status / error
  }

  // close client/connection?

}
