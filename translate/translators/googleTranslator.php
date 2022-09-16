<?php

require_once 'translate/translators/abstractTranslator.php';
require_once 'translate/utils/log.php';
require_once 'vendor/autoload.php';
use Google\Cloud\Translate\TranslateClient;

class GoogleTranslator extends AbstractTranslator {
  function translate(
    $runId,
    $requestId,
    $sourceLanguage,
    $targetLanguage,
    $textToTranslate
  ) {
    $this->startTime = microtime(true);

    $translate = new TranslateClient();
    $result = $translate->translate($textToTranslate, [
      'source' => $sourceLanguage,
      'target' => $targetLanguage  
    ]);
    
    $resultText = $result["text"];
    if (is_null($resultText)) {
      $code = 'google_translate_error';
      $this->handleError($runId, $requestId, $code);
    }

    $this->endTime = microtime(true);
    return $resultText;
  }
}
