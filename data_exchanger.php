<?php
class Data_Exchanger
{
  // function to properly format data from LFCHD database into HL7 message
  public function generate_hl7_message($input_data)
  {
    // create variables from $input_data
    // $kyir_num = $input_data[0];
    // $last_name = $input_data[1];
    // $first_name = $input_data[2];
    // $dob = $input_data[3];
    // $sex = $input_data[4];
    // $race = $input_data[5];
    // $address = $input_data[6];
    // $phone_number = $input_data[7];
    // $ethnic_group = $input_data[8];
    // $date_of_administration = $input_data[9];
    // $substance_lot_number = $input_data[10];
    // $substance_expiration_date = $input_data[11];
    // $administration_site = $input_data[12];
    $sending_facility = $input_data;

    $hl7_message = "MSH|^~\&||$sending_facility|KY0000|KY0000|20180726102500-0600||VXU^V04^VXU_V04|1cuT-A.01.01.3n|P|2.5.1|||ER|AL|||||Z22^CDCPHINVS
    PID|1||L05M820^^^AIRA^MR||Northumberland^Kaja^Hetal^^^^L|Iversen^Trinidad^^^^^M|20020715|F||2106-3^White^CDCREC|93 Scott Cir^^Fountain^MI^49410^USA^P||^PRN^PH^^^231^6541667|||||||||2186-5^not Hispanic or Latino^CDCREC||N||||||N
    PD1|||||||||||02^Reminder/Recall - any method^HL70215|N|20180726|||A|20180726|20180726
    NK1|1|Northumberland^Iversen^Marion^^^^L|MTH^Mother^HL70063|93 Scott Cir^^Fountain^MI^49410^USA^P|^PRN^PH^^^231^6541667
    ORC|RE||BL05M820.1^AIRA
    RXA|0|1|20060726||03^MMR^CVX|999|||01^Historical^NIP001||^^^$sending_facility|||||||||CP|A";

    return $hl7_message;
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
