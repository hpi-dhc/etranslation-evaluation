<?php

require_once 'translate/utils/log.php';

abstract class AbstractTranslator {
  public $startTime = null;
  public $endTime = null;

  abstract protected function translate(
    $runId,
    $requestId,
    $sourceLanguage,
    $targetLanguage,
    $textToTranslate
  );

  function handleError($runId, $requestId, $code = null) {
    if ($code === null) {
      $code = '(unknown)';
    }
    writeLog($runId, microtime(true), 'Error ' . $code, $requestId);
    return null;
  }
}
