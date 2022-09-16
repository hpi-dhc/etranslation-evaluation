<?php

require_once 'translate/translators/abstractTranslator.php';
require_once 'translate/helpers/cef/sendRequest.php';
require_once 'translate/helpers/cef/manageRequest.php';
require_once 'translate/utils/log.php';

class CefTranslator extends AbstractTranslator {
  function translate(
    $runId,
    $requestId,
    $sourceLanguage,
    $targetLanguage,
    $textToTranslate
  ) {
    prepareRequest($requestId);
    $this->startTime = microtime(true);
    $response = sendCefRequest($requestId, $sourceLanguage, $targetLanguage, $textToTranslate);

    if ($response <= 0) {
      tidyUp($requestId);
      return $this->handleError($runId, $requestId, $response);
    }

    writeLog($runId, microtime(true), 'Request received by CEF eTranslation service');

    $responseReady = responseReceived($requestId);
    while (!$responseReady) {
      $responseReady = responseReceived($requestId);
    }
    $this->endTime = microtime(true);

    $translatedText = getTranslatedText($requestId);
    tidyUp($requestId);
    return(preg_replace("/\r|\n/", "", $translatedText));
  }
}
