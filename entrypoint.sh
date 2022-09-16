#!/bin/bash

composer require google/cloud-translate --no-ansi &> composer.log

# CMD command from original PHP Dockerfile (otherwise overwritten)
apache2-foreground