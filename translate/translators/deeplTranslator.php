<?php

require_once 'translate/translators/abstractTranslator.php';
require_once 'translate/utils/log.php';

class DeeplTranslator extends AbstractTranslator {
  function translate(
    $runId,
    $requestId,
    $sourceLanguage,
    $targetLanguage,
    $textToTranslate
  ) {
    $this->startTime = microtime(true);
    $config = getConfig();

    define('API_URL', $config['deepl']['rest']); 
    define('AUTH_KEY', $config['deepl']['auth_key']);
   
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $url = API_URL . "?auth_key=" . AUTH_KEY;
    $body = '&text='.rawurlencode($textToTranslate);
    $body .= '&source_lang='.$sourceLanguage;
    $body .= '&target_lang='.$targetLanguage;

    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
      $code = 'request_curl_error';
      $this->handleError($runId, $requestId, $code);
    }

    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if ($httpCode != 200) {
      $code = 'HTTP '.$httpCode;
      $this->handleError($runId, $requestId, $code);
    }
    
    $responseArray = json_decode($response, true);
    $this->endTime = microtime(true);
    return $responseArray['translations'][0]['text'];

  }
}
