<?php

require_once 'config/config.php';

function sendCefRequest($externalReference, $sourceLanguage, $targetLanguage, $textToTranslate) {

    $config = getConfig();

    define('SERVER_URL', $config['server']);
    define('SERVER_USER', $config['user']);
    define('SERVER_PASSWORD', $config['password']);
    define('API_URL', $config['cef']['rest']);
    define('USERNAME', $config['cef']['user']);
    define('PASSWORD', $config['cef']['password']);
    define('RESPONSE_EMAIL', $config['cef']['email']);

    $callerInformation = array(
            'application' => USERNAME,
            'username' => USERNAME
        );
    $requesterCallback = 'https://' .
      SERVER_USER .
      ':' . SERVER_PASSWORD .
      '@' . SERVER_URL . '/cefCallback.php';
    $translationData = array(
            'externalReference' => $externalReference,
            'textToTranslate' => $textToTranslate,
            'sourceLanguage' => $sourceLanguage,
            'targetLanguages' => array(
                $targetLanguage
            ),
            'domain' => 'PUBHEALTH',
            'requesterCallback' => $requesterCallback,
            'callerInformation' => $callerInformation
        );

    if (RESPONSE_EMAIL !== FALSE) {
      $emailDestinations = array(RESPONSE_EMAIL);
      $translationData['destinations']['emailDestinations'] = $emailDestinations;
    }

    $post = json_encode($translationData);
    $client = curl_init(API_URL);

    curl_setopt($client, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($client, CURLOPT_POST, 1);
    curl_setopt($client, CURLOPT_POSTFIELDS, $post);
    curl_setopt($client, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
    curl_setopt($client, CURLOPT_USERPWD, USERNAME . ':' . PASSWORD);
    curl_setopt($client, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($client, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($client, CURLOPT_TIMEOUT, 30);

    curl_setopt($client, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($post)
    ));

    $response = curl_exec($client);
    return json_decode($response);
}
