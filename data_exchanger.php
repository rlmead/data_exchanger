<?php
class Data_Exchanger
{
  // function to properly format data from LFCHD database into HL7 message
  public function generate_hl7_message($input_data)
  {
    // define variables used by hl7 message
    // hard-coded variables that can remain hard-coded for Janssen covid vaccinations
    $sending_facility = "LFUCGOVERNMENT";
    $receiving_application = "KY0000";
    $receiving_facility = "KY0000";
    $address_state = "KY";
    $vaccine_code = "212^Janssen COVID-19 Vaccine^CVX^59676-0580-05^SARS-COV-2 (COVID-19) vaccine, vector non-replicating, recombinant spike protein-Ad26, preservative free, 0.5 mL^NDC";
    $vaccine_amount = "0.5";
    $vaccine_units = "mL^^UCUM";
    $administration_notes = "00^NEW IMMUNIZATION RECORD^NIP001";
    $vaccine_manufacturer = "JSN^Janssen Products, LP^MVX";
    $administration_route = "C28161^IM^NCIT^IM^^HL70162";
    $vaccine_eligibility = "V01^Not VFC Eligible^HL70064";
    $vaccine_funding_source = "VXC50^Public^CDCPHINVS";
    $observation_method = "VXC40^Eligibility captured at the immunization level^CDCPHINVS";
    // hard-coded variables that cannot remain hard-coded (currently fake data)
    $identifier_type = "MR";
    $provider_last_name = "Olamina";
    $provider_first_name = "Lauren";
    $administered_location = "LFUCGOVERNMENT";
    $vaccine_lot_number = "123456";
    $vaccine_expiration_date = "20210620";
    $administration_site = "LD^Left Deltoid^HL70163";
    $vis_publish_date = "20210115";
    // info from $input_data (many require reformatting)
    // assumes data organized with the following columns/order:
    // pefId,subFirstName,subLastName,subStreet,subCity,subZip,subBirthdate,subPhone,subWhite,subBlack,subNAmerican,subAsian,subPacific,subRaceNope,subEth,subSex,checkIn,kyirNum
    $input_data_array = explode(",", $input_data);
    $pef_id = $input_data_array[0];
    $patient_first_name = $input_data_array[1];
    $patient_last_name = $input_data_array[2];
    $address_street = $input_data_array[3];
    $address_city = $input_data_array[4];
    $address_zip = $input_data_array[5];
    $dob_unformatted = explode("/", $input_data_array[6]);
    $dob = $dob_unformatted[2] . str_pad($dob_unformatted[0], 2, "0", STR_PAD_LEFT) . str_pad($dob_unformatted[1], 2, "0", STR_PAD_LEFT);
    $phone_number = substr($input_data_array[7], 0, 3) . "^" . substr($input_data_array[7], 3);
    if ($input_data_array[8]) {
      $race = $race . "~2106-3^White^CDCREC";
    }
    if ($input_data_array[9]) {
      $race = $race . "~2054-5^Black or African-American^CDCREC";
    }
    if ($input_data_array[10]) {
      $race = $race . "~1002-5^American Indian or Alaska Native^CDCREC";
    }
    if ($input_data_array[11]) {
      $race = $race . "~2028-9^Asian^CDCREC";
    }
    if ($input_data_array[12]) {
      $race = $race . "~2076-8^Native Hawaiian or Other Pacific Islander^CDCREC";
    }
    if ($input_data_array[13]) {
      $race = $race . "";
    }
    if (strlen($race) > 0) {
      $race = substr($race, 1);
    }
    switch ($input_data_array[14]) {
      case "0":
        $ethnic_group = "2186-5^Not Hispanic or Latino^CDCREC";
        break;
      case "1":
        $ethnic_group = "2135-2^Hispanic or Latino^CDCREC";
        break;
      default:
        $ethnic_group = "";
        break;
    }
    switch ($input_data_array[15]) {
      case "1":
        $sex = "M";
        break;
      case "2":
        $sex = "F";
        break;
      default:
        $sex = "";
        break;
    };
    $kyir_number = $input_data_array[17];
    $check_in_date_split = substr($input_data_array[16], 0, strpos($input_data_array[16], " "));
    $check_in_date_unformatted = explode("/", $check_in_date_split);
    $check_in_date = $check_in_date_unformatted[2] . str_pad($check_in_date_unformatted[0], 2, "0", STR_PAD_LEFT) . str_pad($check_in_date_unformatted[1], 2, "0", STR_PAD_LEFT);
    // automatically-generated variables
    $time_current = date("YmdHis");
    $time_incrementing = date("ymd") . "KY000001";

    // declare hl7 message
    $hl7_message = "MSH|^~\&||$sending_facility|$receiving_application|$receiving_facility|$time_current||VXU^V04^VXU_V04|$time_incrementing|T|2.5.1
    PID|1||$kyir_number^^^$sending_facility^$identifier_type||$patient_last_name^$patient_first_name^^^^^L||$dob|$sex||$race|$address_street^^$address_city^$address_state^$address_zip^USA^L||^PRN^PH^^^$phone_number|||||||||$ethnic_group
    ORC|RE||$pef_id|||||||||
    RXA|0|1|$check_in_date||$vaccine_code|$vaccine_amount|$vaccine_units||$administration_notes|^$provider_last_name^$provider_first_name|^^^$administered_location||||$vaccine_lot_number|$vaccine_expiration_date|$vaccine_manufacturer|||CP|A
    RXR|$administration_route|$administration_site
    OBX|1|CE|64994-7^Vaccine fund pgm elig cat^LN|1|$vaccine_eligibility||||||F||||||$observation_method
    OBX|2|CE|30963-3^Vaccine funding source^LN|1|$vaccine_funding_source||||||F|||
    OBX|3|CE|29768-9^Date vaccine information statement published^LN|1|$vis_publish_date||||||F
    OBX|4|CE|29769-7^Date vaccine information statement presented^LN|1|$check_in_date||||||F";

    return $hl7_message;
  }

  // function to send a message to KYIR and receive the result
  public function connect_to_server($server, $wsdl, $server_username, $server_password, $server_facility_id, $input_data)
  {
    $hl7_message = $this->generate_hl7_message($input_data);

    // set up soap client
    $soap_client = new SoapClient(
      $wsdl,
      array(
        "soap_version" => SOAP_1_2,
        "location" => $server
      )
    );

    // define parameters for submitSingleMessage
    $submit_single_message_params = array(
      "username" => $server_username,
      "password" => $server_password,
      "facilityID" => $server_facility_id,
      "hl7Message" => $hl7_message
    );

    // send hl7 message to KYIR server and return result
    return $soap_client->submitSingleMessage($submit_single_message_params);
  }
}
