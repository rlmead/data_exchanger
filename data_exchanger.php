<?php
class Data_Exchanger
{
  // KYIR db credentials - pass in to new instance
  public $kyir_username;
  public $kyir_password;
  public $kyir_facility_id;
  public $wsdl_method;
  public $hl7_message;
  public $patient;
  public $shot;

  function __construct($kyir_username, $kyir_password, $kyir_facility_id)
  {
    $this->kyir_username = $kyir_username;
    $this->kyir_password = $kyir_password;
    $this->kyir_facility_id = $kyir_facility_id;
  }

  // methods for LFCHD vaccine registration app

  // connect to KYIR - called by other functions
  function call_kyir($wsdl_method, $hl7_message)
  {
    // input: kyir wsdl method required by function
    // input: message defined by function

    // connect to KYIR IIS
    // https://kyirqa.chfs.ky.gov/HL7Engine_QA/CDC/V1/IISService.svc

    // return: connection status / error
  }

  // query a patient's vaccine status in KYIR
  function get_vaccine_status($patient)
  {
    // input: patient identifier

    // define $hl7_message
    // call_kyir($wsdl_method, $hl7_message)

    // return: patient vaccine status / error
  }

  // update a patient's vaccine status in KYIR (?)
  function update_vaccine_status($patient, $shot)
  {
    // input: patient identifier
    // input: which shot was administered

    // define $hl7_message
    // call_kyir($wsdl_method, $hl7_message)

    // return: update status / error
  }

  // close client/connection (in functions?)

}
