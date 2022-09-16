import csv
import nltk
import os

import calculateScore

translations_pointer_path = '/var/www/html/data/translations/lastResults.txt'
score_path = '/var/www/html/data/scores'
scores = {
    'BLEU': calculateScore.bleu,
    # 'NIST': calculateScore.nist,
    # 'GLEU': calculateScore.gleu,
    #'WER': calculateScore.wer,
    #'TER': calculateScore.ter,
    #'METEOR': calculateScore.meteor,
    # 'LEPOR': calculateScore.lepor,
    'ROUGE': calculateScore.rouge,
    'BLEURT': calculateScore.bleurt
}

def get_translations_file_path():
    with open(translations_pointer_path, 'r') as translations_pointer_file:
        return(translations_pointer_file.read())

def get_file_name():
    return os.path.basename(get_translations_file_path())

def get_translations_file():
    return open(get_translations_file_path())

def main():
    translations_file = get_translations_file()
    reader = csv.DictReader(translations_file)
    score_file_path = os.path.join(score_path, get_file_name())
    if not os.path.exists(score_path):
        os.mkdir(score_path)
    with open(score_file_path, 'w') as score_file:
        writer = csv.writer(score_file);
        header = reader.fieldnames.copy()
        header.extend(scores.keys())
        writer.writerow(header)
        nltk.download('wordnet')
        for index, row in enumerate(reader):
            print('Computing scores for line {}'.format(index + 1))
            expected_text = row['expected_text']
            target_text = row['target_text']
            results = list(row.values())
            for score, computeScore in scores.items():
                results.append(computeScore(target_text, expected_text))
            writer.writerow(results)
        print('Done computing scores          ')
    translations_file.close()

if __name__ == '__main__':
    main()
