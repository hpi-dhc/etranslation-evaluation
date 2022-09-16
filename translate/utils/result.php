<?php

function basePath() {
  return '/var/www/html/data/translations/';
}

function resultPath($runId) {
  return basePath() . $runId . '.csv';
}

function createResultFile($runId) {
  $header = [
    'request_id',
    'corpus',
    'original_language',
    'target_language',
    'start_time',
    'end_time',
    'original_text',
    'expected_text',
    'target_text'
  ];

  $resultFile = fopen(resultPath($runId), 'w');
  fputcsv($resultFile, $header);
  fclose($resultFile);
  $resultPointerFile = fopen(basePath() . 'lastResults.txt', 'w');
  fwrite($resultPointerFile, resultPath($runId));
  fclose($resultPointerFile);
}

function writeResult($runId, $fields) {
  $resultPath = resultPath($runId);
  if (!file_exists($resultPath)) {
    createResultFile($runId);
  }
  $resultFile = fopen($resultPath, 'a');
  fputcsv($resultFile, $fields);
  fclose($resultFile);
}
