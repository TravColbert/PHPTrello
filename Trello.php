<?php
class Trello {
  private $config;
  private $curlHandle;

  function __construct($inifile, $userConfig = array()) {
    $ini_config = parse_ini_file($inifile);
    if($ini_config===false) exit(100); // could not load ini
    if($this->config = $ini_config); 

    $this->setOptions($userConfig);
    $this->curlHandle = curl_init();
  }

  public function showConfig() {
    var_dump($this->config);
  }

  private function insertUserValues($val,$key) {
    $this->config[$key] = $val;
  }

  private function unsetValue($key) {
    unset($this->config[$key]);
  }

  private function launchRequest() {
    curl_setopt($this->curlHandle, CURLOPT_HEADER, $this->config["header"]);
    curl_setopt($this->curlHandle, CURLOPT_VERBOSE, $this->config["verbose"]);
    curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, $this->config["returnResults"]);
    curl_setopt($this->curlHandle, CURLOPT_URL, $this->config["url"]);
    if(array_key_exists("method",$this->config)) {
      switch ($this->config["method"]) {
        case "POST":
          curl_setopt($this->curlHandle, CURLOPT_POST, TRUE);
          break;
        case "PUT":
          curl_setopt($this->curlHandle, CURLOPT_CUSTOMREQUEST, $this->config["method"]);
          break;
        case "DELETE":
          return "Not implemented";
          break;
        default:
          curl_setopt($this->curlHandle, CURLOPT_HTTPGET, TRUE);
          break;
      };
    }
    $result = curl_exec($this->curlHandle);
    return json_decode($result);
  }

  private function setOptions($options=array()) {
    return array_walk($options,array($this,'insertUserValues'));
  }

  private function unsetOptions($options=array()) {
    return array_walk($options,array($this,'unsetValue'));
  }

  private function setPostFields($relevantKeys) {
    $relevantConfig = array();
    $relevantConfig['key'] = $this->config['key'];
    $relevantConfig['token'] = $this->config['token'];
    foreach($relevantKeys as $key) {
      if(array_key_exists($key,$this->config))
        $relevantConfig[$key] = $this->config[$key];
    }
    return curl_setopt($this->curlHandle, CURLOPT_POSTFIELDS, http_build_query($relevantConfig));
  }

  private function buildUrl($resource,$relevantKeys = false) {
    if($relevantKeys) {
      $relevantConfig = array();
      $relevantConfig['key'] = $this->config['key'];
      $relevantConfig['token'] = $this->config['token'];
      foreach($relevantKeys as $key) {
        if(array_key_exists($key,$this->config))
          $relevantConfig[$key] = $this->config[$key];
      }
      $url = $this->config["target"] . $resource . http_build_query($relevantConfig);
    } else {
      $url = $this->config["target"] . $resource;
    }
    return $url;
  }

  public function search($options=array()) {
    $searchOptions = array("query","idBoards","idOrganizations","idCards","modelTypes","board_fields","boards_limit","card_fields","cards_limit","cards_page","card_board","card_list","card_members","card_stickers","card_attachments","organization_fields","organizations_limit","member_fields","members_limit","partial");
    
    if(!$options['query']) return false;
    $resource = "search/?";
    $this->setOptions($options);
    $this->config["url"] = $this->buildUrl($resource,$searchOptions);
    $result = $this->launchRequest();
    $this->unsetOptions($searchOptions);
    return $result;
  }

  public function getCard($cardId,$options=array()) {
    if(!$cardId) return false;
    $resource = "cards/$cardId/?";
    $this->setOptions($options);
    $this->config["url"] = $this->buildUrl($resource,array());
    $result = $this->launchRequest();
    return $result;
  }

  public function getBoards() {
    $resource = "members/me/boards/?";
    $this->config["url"] = $this->buildUrl($resource,array());
    $result = $this->launchRequest();
    return $result;
  }

  public function createCard($options=false) {
    $createOptions = array("name","desc","pos","due","dueComplete","idList","idMembers","idLabels","urlSource","fileSource","idCardSource","keepFromSource");
    if(!$options || !is_array($options)) return $options;
    $resource = "cards/?";
    $this->setOptions($options);
    $this->setOptions(array("method"=>"POST"));
    $this->config["url"] = $this->buildUrl($resource,$createOptions);
    $result = $this->launchRequest();
    $this->unsetOptions($createOptions);
    return $result;
  }

  public function updateCard($options=false) {
    $updateOptions = array("name","desc","closed","idMembers","idAttachmentCover","idList","idLabels","idBoard","pos","due","dueComplete","subscribed");
    if(!$options || !is_array($options)) return $options;
    if(!array_key_exists("id",$options)) return false;
    $resource = "cards/{$options["id"]}/?";
    $this->setOptions($options);
    $this->setOptions(array("method"=>"PUT"));
    $this->config["url"] = $this->buildUrl($resource,$updateOptions);
    $this->setPostFields($updateOptions);
    $result = $this->launchRequest();
    $this->unsetOptions($updateOptions);
    return $result;
  }
}

