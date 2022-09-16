#!/bin/bash

echo -e "Starting new run at $(date +%s)"

echo "Translating..."
php translate/getTranslations.php

echo "Evaluating..."
python3 evaluate/getScores.py
