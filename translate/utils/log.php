<?php

function logPath($runId) {
  $logDirectory = 'data/translations/';
  if (!file_exists($logDirectory)) {
    mkdir($logDirectory);
}
  return $logDirectory . $runId . '.log';
}

function createLog($runId) {
  $logFile = fopen(logPath($runId), 'w');
  fwrite($logFile, '# Log for translation test ' . $runId . "\n\n");
  fclose($logFile);
}

function printTime($time) {
  return '[' . (int) $time . ']';
}

function writeLog($runId, $time, $message, $requestId = null) {
  $logFile = fopen(logPath($runId), 'a');
  $requestIdText = '';
  if ($requestId !== null) {
    $requestIdText = '[' . $requestId . ']';
  }
  $logText = printTime($time) . $requestIdText . ' ' . $message;
  fwrite($logFile, $logText . "\n");
  fclose($logFile);
  echo '[' . $runId . ']' . $logText . "\n";
}
