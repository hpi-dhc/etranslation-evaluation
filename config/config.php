<?php

function getConfig() {
  $config_string = file_get_contents('config/config.json');
  return json_decode($config_string, true);
}
