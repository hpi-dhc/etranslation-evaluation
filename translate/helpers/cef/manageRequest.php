<?php

function responseFilePath($requestId) {
  return 'data/translations/' . $requestId . '.temp';
}

function prepareRequest($requestId) {
  $responseFile = fopen(responseFilePath($requestId), 'w');
  fwrite($responseFile, $requestId);
  fclose($responseFile);
}

function writeTranslatedText($requestId, $response) {
  $responseFile = fopen(responseFilePath($requestId), 'w');
  fwrite($responseFile, $response);
  fclose($responseFile);
}

function getTranslatedText($requestId) {
  $translatedText = file_get_contents(responseFilePath($requestId));
  return($translatedText);
}

function responseReceived($requestId) {
  $translatedText = getTranslatedText($requestId);
  $responseReceived =  !($translatedText === $requestId);
  $responseWritten = $translatedText !== "";
  return $responseReceived && $responseWritten;
}

function tidyUp($requestId) {
 unlink(responseFilePath($requestId));
}
