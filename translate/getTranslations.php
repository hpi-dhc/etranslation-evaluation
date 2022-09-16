<?php

require_once 'translate/translators/cefTranslator.php';
require_once 'translate/translators/deeplTranslator.php';
require_once 'translate/translators/googleTranslator.php';
require_once 'translate/translators/ibmTranslator.php';
require_once 'translate/utils/log.php';
require_once 'translate/utils/result.php';

set_time_limit(0);

$services = [
  #'CEF' => new CefTranslator(),
  #'DeepL' => new DeeplTranslator(),
  #'GoogleTranslate' => new GoogleTranslator(),
  'IBM' => new IbmTranslator()
];

$corpora = [ 'medlineDeTest'];
#$corpora = [ 'medlineDe' , 'medlineEs', 'medlineFr', 'ecdcDe' , 'ecdcEs' , 'ecdcFr' , 'emeaDe' , 'emeaEs' , 'emeaFr' ];


$runId = uniqid('run_');
createLog($runId);
writeLog($runId, microtime(true), 'Getting translations');
writeLog($runId, microtime(true), 'Services: ' . implode(', ', array_keys($services)));
writeLog($runId, microtime(true), 'Corpora:  ' . implode(', ', $corpora));
getTranslations($runId);

function runTranslation($runId, $service, $translator, $corpus, $sourceLanguage, $targetLanguage, $textToTranslate, $expectedText) {
  $requestId = uniqid($service . '_');
  $requestText = 'Requesting translation for "' . $textToTranslate . '" from ' . $sourceLanguage . ' to ' . $targetLanguage;
  writeLog($runId, microtime(true), $requestText, $requestId);
  $translatedText = $translator->translate($runId, $requestId, $sourceLanguage, $targetLanguage, $textToTranslate);
  if ($translatedText !== null) {
    writeLog($runId, microtime(true), 'Received translation "' . $translatedText . '"', $requestId);
    writeResult($runId, [
      $requestId,
      $corpus,
      $sourceLanguage,
      $targetLanguage,
      $translator->startTime,
      $translator->endTime,
      $textToTranslate,
      $expectedText,
      $translatedText
    ]);
  }
}

function getTranslations($runId) {
  global $services, $corpora;
  if (!is_dir(basePath())) {
    mkdir(basePath(), 0755);
  }
  foreach ($corpora as $corpus) {
    $corpusFilePath = '/var/www/html/preprocessed-corpora/' . $corpus . '.csv';
    $corpusFile = fopen($corpusFilePath, 'r');
    $header = fgetcsv($corpusFile);

    while(($data = fgetcsv($corpusFile)) !== FALSE) {
      $languageA = $data[1];
      $languageB = $data[2];
      $aText = $data[3];
      $bText = $data[4];
      foreach ($services as $service => $translator) {
        runTranslation($runId, $service, $translator, $corpus, $languageA, $languageB, $aText, $bText);
        #runTranslation($runId, $service, $translator, $corpus, $languageB, $languageA, $bText, $aText);
      }
    }
    fclose($corpusFile);
    writeLog($runId, microtime(true), 'Translations done');
  }
}
