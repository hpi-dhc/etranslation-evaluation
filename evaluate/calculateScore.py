import nltk
import pyter
from rouge_score import rouge_scorer
import jiwer
import datasets

def bleu(hypothesis, reference):
    return nltk.translate.bleu_score.sentence_bleu([ hypothesis.split() ], reference.split())

def nist(hypothesis, reference):
    return nltk.translate.nist_score.sentence_nist([ hypothesis.split() ], reference.split())

def gleu(hypothesis, reference):
    return nltk.translate.gleu_score.sentence_gleu([ hypothesis.split() ], reference.split())

def wer(hypothesis, reference):
    return jiwer.wer(reference, hypothesis)

def ter(hypothesis, reference):
    return pyter.ter(hypothesis.split(), reference.split())

def meteor(hypothesis, reference):
    return nltk.translate.meteor_score.meteor_score([ hypothesis ], reference)

def lepor(hypothesis, reference):
    return None

def rouge(hypothesis, reference):
    scorer = rouge_scorer.RougeScorer(['rouge1', 'rougeL'], use_stemmer=True)
    return scorer.score(reference, hypothesis)['rougeL'].fmeasure

# with https://huggingface.co/docs/datasets/using_metrics.html 
#def rouge1(hypothesis, reference):
#    metric = datasets.load_metric('rouge')
#    #print(metric)
#    #metric.add_batch(predictions=hypothesis, references=reference)
#    return metric.compute(predictions= [hypothesis], references= [reference])

# with https://huggingface.co/docs/datasets/using_metrics.html 
def bleurt(hypothesis, reference):
   metric = datasets.load_metric('bleurt')
   return metric.compute(predictions= [hypothesis], references= [reference])['scores'][0]