<?php

require_once 'translate/helpers/cef/manageRequest.php';

function receiveCefCallback($request) {
  $requestId = $request["external-reference"];
  $translatedText = $request["translated-text"];
  writeTranslatedText($requestId, $translatedText);
}
