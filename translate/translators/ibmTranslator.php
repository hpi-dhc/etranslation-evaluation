<?php

require_once 'translate/translators/abstractTranslator.php';
require_once 'translate/utils/log.php';

class IbmTranslator extends AbstractTranslator {
  function translate(
    $runId,
    $requestId,
    $sourceLanguage,
    $targetLanguage,
    $textToTranslate
  ) {
    $config = getConfig();
    $this->startTime = microtime(true);

    define('API_URL', $config['ibm']['rest']);
    define('USERNAME', $config['ibm']['user']);
    define('PASSWORD', $config['ibm']['password']);

    # Conceptually copied from cefTranslator (but auth type); might want to refactor different POST types in the future
    $translationData = array(
            'text' => array($textToTranslate),
            'model_id' => $sourceLanguage . "-" . $targetLanguage
        );
    $post = json_encode($translationData);
    $client = curl_init(API_URL);

    curl_setopt($client, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($client, CURLOPT_POST, 1);
    curl_setopt($client, CURLOPT_POSTFIELDS, $post);
    curl_setopt($client, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($client, CURLOPT_USERPWD, USERNAME . ':' . PASSWORD);
    curl_setopt($client, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($client, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($client, CURLOPT_TIMEOUT, 30);

    curl_setopt($client, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($post)
    ));

    $response = curl_exec($client);

    # Conceptually copied from deeplTranslator; might want to refactor HTTP error handling in the future
    if (curl_errno($client)) {
      $code = 'request_curl_error';
      $this->handleError($runId, $requestId, $code);
    }

    $httpCode = curl_getinfo($client, CURLINFO_HTTP_CODE);
    if ($httpCode != 200) {
      $code = 'HTTP '.$httpCode;
      $this->handleError($runId, $requestId, $code);
    }

    $responseArray = json_decode($response, true);
    $resultText = $responseArray['translations'][0]['translation'];

    $this->endTime = microtime(true);
    return $resultText;
  }
}
