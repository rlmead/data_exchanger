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
    $time_current = date('YmdHis');
    $time_incrementing = date('ymd').'KY000001';

    $hl7_message = "MSH|^~\&||$sending_facility|KY0000|KY0000|$time_current||VXU^V04^VXU_V04|$time_incrementing|T|2.5.1|||||||||
    PID|1||012345^^^$sending_facility^MR||LASTNAME^FIRSTNAME^^^^^L||19750101|F||1002-5^American Indian or Alaskan Native^CDCREC~2028-9^Asian^CDCREC~2106-3^White^CDCREC|STREET1^STREET2^LEXINGTON^KY^40507^USA^L||^PRN^PH^^^123^1234567|||||||||2186-5^Not Hispanic or Latino^CDCREC
    ORC|RE||5ce43a4029b04817bb6|||||||||
    RXA|0|1|20210331||212^Janssen COVID-19 Vaccine^CVX^59676-0580-05^SARS-COV-2 (COVID-19) vaccine, vector non-replicating, recombinant spike protein-Ad26, preservative free, 0.5 mL^NDC|0.5|mL^^UCUM||00^NEW IMMUNIZATION RECORD^NIP001|^OLAMINA^LAUREN|^^^$sending_facility||||123456|20210620|JSN^Janssen Products, LP^MVX|||CP|A
    RXR|C28161^IM^NCIT^IM^^HL70162|LD^Left Deltoid^HL70163
    OBX|1|CE|64994-7^Vaccine fund pgm elig cat^LN|1|V01^Not VFC Eligible^HL70064||||||F||||||VXC40^Eligibility captured at the immunization level^CDCPHINVS
    OBX|2|CE|30963-3^Vaccine funding source^LN|1|VXC50^Public^CDCPHINVS||||||F|||
    OBX|3|CE|29768-9^Date vaccine information statement published^LN|1|20210115||||||F
    OBX|4|CE|29769-7^Date vaccine information statement presented^LN|1|20210331||||||F";

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
