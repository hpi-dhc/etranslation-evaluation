# eTranslation Evaluation

Testing framework for the CEF building block eTranslation and other translation services in the medical context, containing experiment setup, analyses, and results.

This repository accompanies a paper about this experiment (**TODO: Link when possible**).

## Installation

Being in the directory where this README resides...

1. Rename  `config/example.config.json` to `config/config.json` and fill in the required information
  * Your server information (needs to be publicly accessible to receive CEF callbacks; HTTP basic authentication expected)
  * Your service credentials
  * For Google Translate, you will receive a JSON file to configure your connection; this needs to be added to `config/auth`
  * If you want to receive email responses from the CEF translation service, include your email address instead of `false`.
2. (At least on Ubuntu) make `www-data` own the `data` directory to write on it with `sudo chown www-data:www-data -R data`
3. Make scripts executable with `chmod +x run.sh stop.sh`
4. Build the Docker image with `docker build -t etranslation-evaluation .`.
5. Run Docker container
  * You might need to adapt the absolute repository path `~/etranslation-evaluation`
  * `docker run -d -v ~/etranslation-evaluation:/var/www/html -p 8001:80 --rm --cidfile docker.cid etranslation-evaluation`
6. Execute `run.sh` in Docker container
  * Run as job with `(docker exec -u www-data:www-data $(cat docker.cid) ./run.sh) > application_log.txt 2>&1 &` (keeps running when connection to VM is lost)
  * Follow log with `tail -f application_log.txt`

For callbacks from services, the application will be available at `localhost:8001`; these callback routes need to be added to the `.htaccess`.

To stop the application run `./stop.sh`.

To run code directly in the container, use `docker exec -u www-data:www-data -it  $(cat docker.cid) /bin/bash`.

## Translation Services

We included the following translation services:
* [CEF eTranslation](https://ec.europa.eu/cefdigital/wiki/display/CEFDIGITAL/How+to+submit+a+translation+request+via+the+CEF+eTranslation+webservice)
* [DeepL](https://www.deepl.com)
* [IBM Language Translator](https://www.ibm.com/cloud/watson-language-translator)
* [Google Translate](https://translate.google.com/)

## Corpora

We included medical parallel corpora from different domains:
* [Medline Corpus from WMT19](https://drive.google.com/drive/u/0/folders/18pOeV5R4MkzxfUvTXk82hm5Htg5Ys4GI)
* [UFAL Medical Corpus v. 1.0](https://ufal.mff.cuni.cz/ufal_medical_corpus), specifically:
  * EMEA
  * ECDC

The final corpus was assembled from these (see `preprocessed-corpora`).

## Scores

We included the following scores that mainly rely on similarity metrics in our automated evalutation:
* [BLEU](https://www.nltk.org/api/nltk.translate.bleu_score.html), specifically `sentence_bleu`
* [ROGUE](https://pypi.org/project/rouge-score/), specifically ROUGE-L
* [BLEURT](https://github.com/google-research/bleurt#readme), used with [Huggingface](https://huggingface.co/docs/datasets/how_to_metrics)

In addition to automated scores, a human validation was conducted in a subset of sentences, in which two humans pairwise assessed the similarity of translated sentences to expected sentences and the translation quality.

## Project Structure

The project has the following overall structure:

```
etranslation-evaluation/
├── config/
│   ├── auth/
│   ├── config.json
│   └── config.php
├── data/
├── evaluate/
├── preprocessed-corpora/
├── statistics/
├── translate/
│   ├── helpers/
│   ├── translators/
│   │   ├── abstractTranslator.php
│   │   └── [specific translator].php
│   └── getTranslations.php
├── index.php
└── cefCallback.php
```

The `config` directory holds the `config.json` (which was renamed and adapted from `example.config.json`), the Google Translate configuration file, and the PHP file for reading the configuration.

The `data` directory, includes the data that is generated at run time, which is `translations`, and `scores`.

In the `evaluate` directory all Python scripts used for evaluate purposes are stored.

The `preprocessed-corpora` directory contains filtered CSV files based on the corpora desecribed above.

The code for corpus preprocessing and statistical testing can be found in `statistics`. Additionally, the resulting CSV files from our experiments and files for the additional human validation are included.

The `translate` directory includes `helpers/` for specific translator classes in `translators/` that extend the `abstractTranslator.php`. The `getTranslations.php` script executes all tests for all services.

On root level, the routes reside (next to the Dockerfile, this README, helper scripts, and configuration files, which are left out in this explanation).
The route `cefCallback.php` receives and handles the callback sent by the CEF translation service.
