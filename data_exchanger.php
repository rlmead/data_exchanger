<?php
class Data_Exchanger
{
  // function to properly format data from LFCHD database into HL7 message
  public function generate_hl7_message($input_data)
  {
    return $input_data;
  }

  // function to send a message to KYIR and receive the result
  public function call_kyir($kyir_username, $kyir_password, $kyir_facility_id, $input_data)
  {
    $hl7_message = $this->generate_hl7_message($input_data);

    // set up soap client
    $wsdl = "https://kyirqa.chfs.ky.gov/HL7Engine_QA/Cdc.aspx?WSDL";
    $kyir_server = "https://kyirqa.chfs.ky.gov/HL7Engine_QA/cdc/v1/iisservice.svc";
    $kyir_soap_client = new SoapClient(
      $wsdl,
      array(
        'soap_version' => SOAP_1_2,
        'location' => $kyir_server
      )
    );

    // define parameters for submitSingleMessage
    $submit_single_message_params = array(
      'username' => $kyir_username,
      'password' => $kyir_password,
      'facilityID' => $kyir_facility_id,
      'hl7Message' => $hl7_message
    );

    // send hl7 message to KYIR server and return result
    return $kyir_soap_client->submitSingleMessage($submit_single_message_params);
  }

}
