<?php
namespace App;

class WebApi {

  protected static $instance = NULL;
  //protected $api_url = 'https://webapi.paxforex.org:444';
  protected $api_url = 'http://webapi.paxforex.org';
  protected $auth_token = '';
  protected $header_string = '';

  protected function variable_get($api_token, $api_token_def){
      return 'api_token';
  }

  protected function watchdog($watchdog){
      $file = __DIR__ . '\cabinet.log';
      $current = file_get_contents($file);
      $current .= $watchdog . PHP_EOL;
      file_put_contents($file, $current);
  }

  protected function __construct() {
    $this->auth_token = $this->variable_get('api_token', '');
    if (!empty($this->auth_token)) {
      $this->header_string = "Authorization: Basic {$this->auth_token}\r\n" .
          "Content-Type: application/json\r\n";
    } else {
      $this->header_string = "Content-Type: application/json\r\n";
    }
  }

  public static function getInstance() {
    if (!isset(static::$instance)) {
      static::$instance = new static();
    }
    return static::$instance;
  }

  private function strposa($haystack, $needle, $offset = 0) {
    if (!is_array($needle))
      $needle = array($needle);
    foreach ($needle as $query) {
      if (strpos($haystack, $query, $offset) !== false)
        return true; // stop on first true result
    }
    return false;
  }

  private function webapiConnector($path, array $param, $method = 'GET') {
    if (!empty($path)) {
      $url = $this->api_url . $path;
      $context = stream_context_create(array(
      /*  'ssl' => array(
          'verify_peer' => false,
          'verify_peer_name' => false
        ), */
        'http' => array(
          'timeout' => 30,
          'method' => $method,
          'header' => $this->header_string,
          'content' => json_encode($param),
        //  'ignore_errors' => true
        )
      ));
      //Exclude from log
      //$no_logging = ['q'];
      $no_logging = ['get','check'];
      //$no_logging = [];
      //Get parent method name
       $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[1]['function'];
      //
      if (!empty($param)) {
        $this->watchdog('WebApi - data / ' . $backtrace, '<pre>' . print_r($param, 1) . '</pre>');
      }
      $response = file_get_contents($url, FALSE, $context);
      // Check for errors
      if ($response === FALSE) {
          $error = error_get_last();
          $this->watchdog('WebApi - error', 'Data not send to API server');
          $this->watchdog('WebApi - error data', '<pre>' . print_r($error, 1) . '</pre>');
      } else {
        if ($this->strposa($backtrace, $no_logging) === FALSE) {
          $this->watchdog('WebApi - success / ' . $backtrace, '<pre>' . print_r($response, 1) . '</pre>');
        }
      }
      // Decode the response
      $responseData = json_decode($response, TRUE);
      return $responseData;
    } else {
      return 0;
    }
  }

  /*
   * WebApi GET method implementation
   *
   */
  public function getClientMessages($acct_id) {
    try {
      $result = $this->webapiConnector("/client/{$acct_id}/siteMessages", array(), 0);
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }
  
  public function getPrices() {
    try {
      $result = $this->webapiConnector("/prices", array(), 0);
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  // $cid = company id
  public function getAccountTypesData($cid) {
    try {
      $result = $this->webapiConnector("/accountTypes?companyID={$cid}", array(), 0);
    }
    catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  // $acct_id = account type id
  public function getAccountTypesSelectedData($acct_id) {
    try {
      $result = $this->webapiConnector("/accountTypes/{$acct_id}", array(), 0);
    }
    catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  public function getAccountTypeData($acct_id) {
    try {
      $result = $this->webapiConnector("/accountType/{$acct_id}", array(), 0);
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  public function getAccountTypeCurr($acct_id) {
    try {
      $result = $this->webapiConnector("/accountType/{$acct_id}/currencies", array(), 0);
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }
  
  public function getAccountTypeLev($acct_id, $curr_id) {
    try {
      $result = $this->webapiConnector("/accountTypes/{$acct_id}/leverages?currencyID={$curr_id}", array(), 0);
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  public function getPaymentVariants() {
    try {
      $result = $this->webapiConnector("/payment/variants", array(), 0);
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  public function getClientPaymentVariants($ext_id) {
    try {
      $result = $this->webapiConnector("/client/{$ext_id}/payment/variants", array(), 0);
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  public function getContestPips() {
    try {
      $result = $this->webapiConnector("/contest/pips", array(), 0);
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  public function getClientData($ext_id, $path) {
    try {
      $sub_path = !empty($path) ? $path : '';
      $result = $this->webapiConnector("/client/{$ext_id}{$sub_path}", array(), 0);
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  public function getClientDocs($ext_id) {
    try {
      $result = $this->webapiConnector("/client/{$ext_id}/docs", array(), 0);
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  public function getClientCards($ext_id) {
    try {
      $result = $this->webapiConnector("/client/{$ext_id}/verifyCards", array(), 0);
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  public function getIBLinks($ext_id) {
    try {
      $result = $this->webapiConnector("/client/{$ext_id}/ibLinks", array(), 0);
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  public function getBTC($ext_id) {
    try {
      $result = $this->webapiConnector("/client/{$ext_id}/btcWallet", array(), 0);
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  public function getETH($ext_id) {
    try {
      $result = $this->webapiConnector("/client/{$ext_id}/etcWallet", array(), 0);
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * Get IB reports
   * $path = '/ib/accounts?StartDate=' . $start_date . '&EndDate=' . $end_date . 'T23:59:59'
   * or
   * $path = '/subib/' . $subib_id . '/clients'
   * or
   * $path = '/ib/trades?isShowCurrentMonth=' . $month
   * or
   * $path = '/ib/trades?StartDate=' . $start_date . '&EndDate=' . $end_date . 'T23:59:59'
   * or
   * $path = '/ib/summary/countRegisteredClients?StartDate=' . $start_date . '&EndDate=' . $end_date . 'T23:59:59'
   * or
   * $path = '/ib/summary/payments?StartDate=' . $start_date . '&EndDate=' . $end_date . 'T23:59:59'
   * or
   * $path = '/ib/summary/payments/revShareDetails?StartDate=' . $start_date . '&EndDate=' . $end_date . 'T23:59:59'
   * or
   * $path = '/ib/summary/countRegisteredClientsForMap'
   */

  public function getIB($ext_id, $path) {
    try {
      $result = $this->webapiConnector("/client/{$ext_id}{$path}", array(), 0);
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * Get custom client + IB report
   * $path = '?startReportDate=' . $start_date . '&EndReportDate=' . $end_date . 'T23:59:59'
   */

  public function getIBS($ext_id, $path) {
    try {
      $result = $this->webapiConnector("/ib/pivot?clientid={$ext_id}{$path}", array(), 0);
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * Get CPA reports
   * $path = '?dateOfFirstPayment=' . $start_date . '&EndReportDate=' . $end_date . 'T23:59:59'
   */

  public function getCPA($ext_id, $path) {
    try {
      $result = $this->webapiConnector("/ib/cpa/{$ext_id}{$path}", array(), 0);
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * Get custom CPA reports
   * $path = '?startReportDate . $start_date . '&EndReportDate=' . $end_date . 'T23:59:59'
   */

  public function getCustomCPA($ext_id, $path) {
    try {
      $result = $this->webapiConnector("/ib/customCpa/{$ext_id}{$path}", array(), 0);
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * Get orders from master
   * $path = '?StartDate . $start_date . '&EndDate=' . $end_date . 'T23:59:59'
   */

  public function getOrdersFromMaster($ext_id, $path) {
    try {
      $result = $this->webapiConnector("/client/{$ext_id}/account/{$path}", array(), 0);
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }
  
  /*
   * Get clients from friends programm
   * $path = '?startReportDate . $start_date . '&endReportDate=' . $end_date . 'T23:59:59'
   */

  public function getFriendClients($ext_id, $path) {
    try {
      $result = $this->webapiConnector("/ib/friends/{$ext_id}{$path}", array(), 0);
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * WebApi DELETE method implementation
   */

  public function rmFile($ext_id, $file) {
    try {
      $result = $this->webapiConnector("/client/{$ext_id}/docs/{$file}", array(), 'DELETE');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  public function rmCLient($ext_id) {
    try {
      $result = $this->webapiConnector("/client/{$ext_id}", array(), 'DELETE');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * WebApi POST method implementation
   */

  /*  Add lead
    $data = [
    'FirstName' => $name,
    'LastName' => $lname,
    'Email' => $mail,
    'CompanyID' => $coid ? $coid : 2,
    'Country' => $realcountry,
    'City' => $city,
    'Adress' => $address,
    'Phone' => $phone,
    'IpAdress' => $realip,
    'PaymentSystemID' => $paysys_id,
    'PaymentAmount' => $pay_amount,
    'SourceID' => 3'(1 MT4 2 SMM 3 Lead List 4 Sent Request)
    'RegSitePrefix' => 'ru'
    'SourceSite' => 'paxforex.org'
    'Comment' => $comment,
    'TypeID' => $type_id,
    'RefID' => $refid,
    'AdvID' => $advid,
    'Referer' => $referer,
    'UtmOffset' => $utm (+ or - minut GMT offest)
    ];
   */

  public function addLead(array $data) {
    try {
      $result = $this->webapiConnector("/client/lead/add", $data, 'POST');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }
 /*
   * Client login get token
   * 
    $data = [
    'Email' => trim($email),
    'Password' => trim($pass),
    'IpAddress' => trim(filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP))
    ];
   */

  public function getToken(array $data) {
    try {
      $result = $this->webapiConnector("/client/login", $data, 'POST');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }
  
  /*
   * Client login check token
   * 
    $data = [
    'Token' => trim($token),
    'IpAddress' => trim(filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP))
    ];
   */

  public function checkToken(array $data) {
    try {
      $result = $this->webapiConnector("/client/login/check", $data, 'POST');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }
  
  /*
   * Check email
   * $data = ['Email' => strtolower(trim($mail)), 'CompanyID' => 2];
   */

  public function checkEmail(array $data) {
    try {
      $result = $this->webapiConnector("/client/checkEmail", $data, 'POST');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * Check phone
   * $data = ['Phone' => preg_replace('~\D+~', '', trim($phone)), 'CompanyID' => 2];
   */

  public function checkPhone(array $data) {
    try {
      $result = $this->webapiConnector("/client/checkPhone", $data, 'POST');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * Add client
    $data = [
    'CompanyID' => $coid ? $coid : 2,
    'FirstName' => !empty($name) ? $name : 'Unknown',
    'LastName' => !empty($lastname) ? $lastname : 'Unknown',
    'Email' => strtolower(trim($mail)),
    'Password' => trim($pass)
    'PhoneNumber' => !empty($phone) ? preg_replace('~\D+~', '', trim($phone)) : 'Unknown',
    'IsPhoneConfirm' => $phone_checked,
    'City' => !empty($city) ? $city : 'Unknown',
    'Address' => !empty($address) ? $address : 'Unknown',
    'IP' => $realip,
    'PhoneCountry' => strtoupper($phonecountry),
    'IPCountry' => strtoupper($realcountry),
    'AccountTypeID' => $accttype,
    'AccountCurrencyID' => $acctcurrency,
    'AccountLeverageID' => $acctlev * 100,
    'AccountIniBalance' => $acct_bal,
    'PaymentSystemID' => $paysys_id,
    'PaymentAmount' => $pay_amount,
    'RefID' => $refid,
    'AdvID' => $advid,
    'FriendID' => $fid,
    'KadamID' => $kadamid,
    'Referer' => $referer,
    'Param1' => $param1,
    'Param2' => $param2,
    'Param3' => $param3,
    'ga_cid' => $ga_cid,
    'IsRegFromRuPage' => $html_lang,
    'SourceSite' => 'paxforex.org'
    'RegSitePrefix' => 'en',
    'IbExperience' => $ib_0,
    'IbIsIbHasClientDatabase' => $ib_1,
    'IbCountOfNewClients' => $ib_2,
    'IbAmountOfDeposits' => $ib_3,
    'IbClientsRegion' => $ib_4,
    'IbHowPromoteUs' => $ib_5,
    'IbWebsite' => $ib_6,
    'IbConditionType' => $ib_7,
    'IbExtraComments' => $ib_8,
    'ExtraComment' => $xtra,
    'LandingUrl' =>
    'CampaignSource' =>
    'CampaignMedium' =>
    'CampaignName' =>
    'CampaignTerm' =>
    'CampaignContent' =>
    'UtmOffset' => $utm (+ or - minut GMT offset)
    ];
   *
   */

  public function addClient(array $data) {
    try {
      $result = $this->webapiConnector("/client/add", $data, 'POST');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * Confirm client
   * $data = ['ConfirmCode' => $link];
   */

  public function confirmClient(array $data) {
    try {
      $result = $this->webapiConnector("/client/confirm", $data, 'POST');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * Login client
   *
    $data = [
    'ClientID' => trim($ext_id),
    'IP' => trim(filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)),
    'Country' => trim(strtoupper(filter_input(INPUT_SERVER, 'GEOIP_COUNTRY_CODE', FILTER_SANITIZE_STRING))),
    'Agent' => trim(filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_ENCODED, FILTER_FLAG_STRIP_LOW))
    ];
   */

  public function loginClient(array $data) {
    try {
      $result = $this->webapiConnector("/client/{$data['ClientID']}/login", $data, 'POST');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * Update client message as read
   *
    $data = [
    'ClientID' => trim($ext_id),
    'MessageID' => trim($msg_id)
    ];
   */

  public function updateClientMessageStatus(array $data) {
    try {
      $result = $this->webapiConnector("/client/{$data['ClientID']}/siteMessages/{$data['MessageID']}", $data, 'POST');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }
  
  /*
   * Update client time offset
   *
    $data = [
    'ClientID' => trim($ext_id),
    'TimeOffset' => trim(filter_input(INPUT_POST, 'timeoffset', FILTER_SANITIZE_STRING))
    ];
   */

  public function updateClientTime(array $data) {
    try {
      $result = $this->webapiConnector("/client/{$data['ClientID']}/timeOffset", $data, 'POST');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * Add MT4 account
   *
    $data = [
    'ClientID' => $ext_id,
    'TypeID' => $accttype,
    'CurrencyID' => $acctcurrency,
    'LeverageID' => $acctlev * 100
    'IniDeposit' => $amnt
    ];
   */

  public function addAccount(array $data) {
    try {
      $result = $this->webapiConnector("/client/{$data['ClientID']}/accounts", $data, 'POST');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }
  
  /*
   * Add AllPips account
   *
    $data = [
    'ClientID' => $ext_id,
    'TypeID' => $accttype,
    'CurrencyID' => $acctcurrency,
    'LeverageID' => $acctlev * 100
    'IniDeposit' => $amnt
    ];
   */

  public function addAccountV2(array $data) {
    try {
      $result = $this->webapiConnector("/v2/client/{$data['ClientID']}/accounts", $data, 'POST');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * Add IB
   *
    $data = [
    'ClientID' => $ext_id,
    'IbExperience'
    'IbIsIbHasClientDatabase'
    'IbCountOfNewClients'
    'IbAmountOfDeposits'
    'IbClientsRegion'
    'IbHowPromoteUs'
    'IbWebsite'
    'IbConditionType'
    'IbExtraComments'
    ];
   */

  public function addIB(array $data) {
    try {
      $result = $this->webapiConnector("/client/{$data['ClientID']}/ib/request", $data, 'POST');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }
  
  /*
   * Add Friend
   *
    $data = [
    ];
   */

  public function addFriend(array $data) {
    try {
      $result = $this->webapiConnector("/client/{$data['ClientID']}/friendProgram/request", $data, 'POST');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * Add docs
   *
    $data = [
    'ID' => $path['filename'],
    'ClientID' => $ext_id,
    'TypeID' => $type,
    'DocSize' => $file->filesize,
    'DocExt' => '.'.$path['extension']
    ];
   */

  public function addDocs(array $data) {
    try {
      $result = $this->webapiConnector("/client/{$data['ClientID']}/docs", $data, 'POST');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * Add cards
   *
    $data = [
    'ClientID' => $ext_id,
    'CardID' => $cid,
    'FullInfo' => $info
    ];
   */

  public function addCards(array $data) {
    try {
      $result = $this->webapiConnector("/verifyCards", $data, 'POST');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * Add payment
   *
    $data = [
    'AccountID'   => $paydata['acct_id'],
    'PaymentTypeID' => $paydata['tsysid'],
    'PaymentSumma' => $paydata['tamount'],
    'CurrencyID' => $paydata['tcurr'],
    'TranNumber' => $paydata['tuid'],
    'Raw' => $paydata['traw'],
    'Comment'  => $paydata['tinfo']
    ];
   */

  public function trsfrMoney(array $data) {
    try {
      $result = $this->webapiConnector("/payment/add", $data, 'POST');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * Move client's founds beetwen accounts
   *
    $data = [
    'ClientID' => $ext_id,
    'FromAccountID' => $from_acct_id,
    'ToAccountID' => $to_acct_id,
    'FromSumma' => $from_amnt,
    'ToSumma' => $to_amnt,
    'Rate' => 1.56
    ];
   */

  public function trsfrClMoney(array $data) {
    try {
      $result = $this->webapiConnector("/payment/transfer", $data, 'POST');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }
  
  /*
   * Move IB founds beetwen accounts
   *
    $data = [
    'IbClientID' => $ext_id,
    'FromAccountID' => $from_acct_id,
    'ToAccountID' => $to_acct_id,
    'Summa' => $amount
    ];
   */

  public function trsfrIBMoney(array $data) {
    try {
      $result = $this->webapiConnector("/client/{$data['IbClientID']}/ib/cashTransfer", $data, 'POST');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * Transfer founds to account
   *
    $data = [
    'ID'          => $paydata['guid'],
    'ClientID'    => $paydata['ext_id'],
    'AccountID'   => $paydata['acct_id'],
    'Summa'       => $paydata['amount'],
    'CurrencyID'  => $curr_id,
    'PaySystemID' => $paydata['sys_id'],
    'IP' => trim(filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)),
    'Country' => trim(strtoupper(filter_input(INPUT_SERVER, 'GEOIP_COUNTRY_CODE', FILTER_SANITIZE_STRING)))
    ];
   */

  public function addPayData(array $data) {
    try {
      $result = $this->webapiConnector("/client/{$data['ClientID']}/paymentRequest", $data, 'POST');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * Withdraw founds to account
   *
    $data = [
    'ClientID'    => $paydata['ext_id'],
    //'AccountID' => $paydata['acct_id'],
    'Account'     => $paydata['acct']
    'Amount'      => $paydata['amount'],
    //'CurrencyID'=> $curr_id,
    'PaySystemID' => $paydata['sys_id'],
    //'IP' => trim(filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)),
    //'Country' => trim(strtoupper(filter_input(INPUT_SERVER, 'GEOIP_COUNTRY_CODE', FILTER_SANITIZE_STRING)))
    ];
   */

  public function addWithData(array $data) {
    try {
      $result = $this->webapiConnector("/client/{$data['ClientID']}/withdraw", $data, 'POST');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * Client CC data
   *
    $data = [
    'ClientID'  => $paydata['ext_id'],
    'CardType'  => $paydata['cc_type'],
    'Bin'       => $paydata['cc_bin'],
    'ExpiryDate'  => $paydata['cc_exp'],
    'LastFourDigits' => $paydata['cc_last']
    ];
   */

  public function addСС(array $data) {
    try {
      $result = $this->webapiConnector("/client/{$data['ClientID']}/creditCard", $data, 'POST');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * Add MT4 account to contest
   *
    $data = [
    'ClientID' => trim($ext_id),
    'Login' => trim($account)
    ];
   */

  public function addContest(array $data) {
    try {
      $result = $this->webapiConnector("/contest/pips", $data, 'POST');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * Add IB link
   * $data = [
     "ClientID" : "xxx"
   * ];
   */

  public function addIBLink(array $data) {
    try {
      $result = $this->webapiConnector("/client/{$data['ClientID']}/ibLink", array('' => ''), 'POST');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * WebApi PUT method implementation
   */

  /*
   * Put new password
   *
    $data = [
    'ClientID' => $ext_id,
    'Password' => $pass
    ];
   */

  public function updatePass(array $data) {
    try {
      $result = $this->webapiConnector("/client/{$data['ClientID']}/password", $data, 'PUT');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * Put profile data
   *
    $data = [
    'ClientID'    => $ext_id,
    'FirstName'   => trim($profile['fname']),
    'SecondName'  => trim($profile['lname']),
    'Birthday'    => !empty($profile['birthday']) ? trim($profile['birthday']) : '',
    'Adress'      => trim($profile['address']),
    'City'        => trim($profile['city']),
    'Country'     => strtoupper(trim($profile['country'])),
    'PhoneNumber' => trim($profile['phone']),
    'IsConfirmPhoneNumber' => $verified
    ];
   */

  public function updateProfile(array $data) {
    try {
      $result = $this->webapiConnector("/client/{$data['ClientID']}", $data, 'PUT');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * Put payment response data
   *
    $data = [
    'ID' => trim($merch_data['tuid']),
    'Amount' => trim($merch_data['tamount']),
    'Currency' => trim($merch_data['tcurr']),
    'PaymentSystemResponse' => $details
    ];
   */

  public function updatePayData(array $data) {
    try {
      $result = $this->webapiConnector("/client/paymentRequest", $data, 'PUT');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * Put new IB link comment
   *
    $data = [
     "ClientID" : "xxx",
     "LinkID" : "yyy",
     "Comment" : "текст комментария"
    ];
   */

  public function updateIBLink(array $data) {
    try {
      $result = $this->webapiConnector("/client/{$data['ClientID']}/ibLink/{$data['LinkID']}", $data, 'PUT');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * Add signal subs
   *
    $data = [
    'ClientID' => trim($cid)
    ];
  */

  public function addSignalSubs(array $data) {
    try {
      $result = $this->webapiConnector("/client/confirmSignalEmailSubscribe", $data, 'POST');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }
  
  /*
   * Add subs
   *
    $data = [
    'ClientID' => trim($cid)
    ];
  */

  public function addSubs(array $data) {
    try {
      $result = $this->webapiConnector("/client/confirmEmailSubscribe", $data, 'POST');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * Add reminder
   *
    $data = [
    'ClientID' => trim($cid)
    ];
  */

  public function addReminders($data) {
    try {
      $result = $this->webapiConnector("/client/{$data['ClientID']}/reminders/creditCard", $data, 'POST');
    } catch (Exception $e) {
      $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    return $result;
  }

  /*
   * Test API
   */

  public function testApi() {
    try {
      $result = $this->webapiConnector("/client/e53168c9-d254-47e2-8e61-bcacfc8fd56e/accounts", array(), 0);
    } catch (Exception $e) {
      $this->watchdog('WebApi - test exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
      //die($e->getMessage());
    }
    $this->watchdog('WebApi - test result', '<pre>' . print_r($result, 1) . '</pre>');
    return $result;
  }
  
  public function getCopyTrades($ext_id) {
    try {
        $result = $this->webapiConnector("/client/{$ext_id}/copyMt4TradesToAllPips", array(), 0);
    }
    catch (Exception $e) {
        $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
        //die($e->getMessage());
    }
    return $result;
  }
  

  /*
     * Add Copy Trade
     *
      $data = [
        'FromAccountID'
        'ToAccountID'
      ];
     */

    public function addCopyTrade($data) {
        try {
            $result = $this->webapiConnector("/client/{$data['ext_id']}/copyMt4TradesToAllPips", $data, 'POST');
        }
        catch (Exception $e) {
            $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
            //die($e->getMessage());
        }
        return $result;
    }
    
    /*
     * DeleteCopy Trade
     *
      $data = [
        'ext_id'
        'copytrade_id'
      ];
     */

    public function rmCopyTrade($data) {
        try {
            $result = $this->webapiConnector("/client/{$data['ext_id']}/copyMt4TradesToAllPips/{$data['copytrade_id']}", array(), 'DELETE');
        }
        catch (Exception $e) {
            $this->watchdog('WebApi - exception', '<pre>' . print_r($e->getMessage(), 1) . '</pre>');
            //die($e->getMessage());
        }
        return $result;
    }

}