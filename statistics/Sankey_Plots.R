library(networkD3) # for sankey plot
library(stringr) 

setwd("~/Desktop/R Data/Statistical Testing")
Dataset <- read.csv("HumanEval.csv")

inter_translator_nodes <- data.frame(
  name = c( "CEF_1_Quantile", "CEF_2_Quantile", "CEF_3_Quantile", "CEF_4_Quantile", 
            "Deepl_1_Quantile", "Deepl_2_Quantile", "Deepl_3_Quantile", "Deepl_4_Quantile",
            "Google_1_Quantile", "Google_2_Quantile", "Google_3_Quantile", "Google_4_Quantile",
            "IBM_1_Quantile", "IBM_2_Quantile", "IBM_3_Quantile", "IBM_4_Quantile"
         )
)

inter_score_nodes <- data.frame(
  name = c( "BLEU_1_Quantile", "BLEU_2_Quantile", "BLEU_3_Quantile", "BLEU_4_Quantile", 
            "ROUGE_1_Quantile", "ROUGE_2_Quantile", "ROUGE_3_Quantile", "ROUGE_4_Quantile",
            "BLEURT_1_Quantile", "BLEURT_2_Quantile", "BLEURT_3_Quantile", "BLEURT_4_Quantile"
  )
)

quantileDefinition <- data.frame(
  name = c(1, 2, 3, 4),
  minPosition = c(0, 0.25, 0.50, 0.75),
  maxPosition = c(0.25, 0.5, 0.75, 1.0)
)

inter_score_bleu_quantiles <- c()
inter_score_rouge_quantiles <- c()
inter_score_bleurt_quantiles <- c()

translator_bleu_quantiles <- c()
translator_rouge_quantiles <- c()
translator_bleurt_quantiles <- c()

getQuantileNumber <- function(currentValue, allValues){
  relativePosition <- match(currentValue, sort(allValues)) / (length(allValues) + 1)
  currentQuantileName <- quantileDefinition[which(
    quantileDefinition$minPosition<=relativePosition&quantileDefinition$maxPosition>relativePosition)[1],"name"]
  return(as.numeric(currentQuantileName))
}

for (rowIndex in 1:nrow(Dataset)){
  row <- Dataset[rowIndex,]
  inter_score_bleu_quantiles <- c(inter_score_bleu_quantiles, getQuantileNumber(row$BLEU, Dataset$BLEU))
  inter_score_rouge_quantiles <- c(inter_score_rouge_quantiles, getQuantileNumber(row$ROUGE, Dataset$ROUGE))
  inter_score_bleurt_quantiles <- c(inter_score_bleurt_quantiles, getQuantileNumber(row$BLEURT, Dataset$BLEURT))
  
  translatorRows <- Dataset[which(Dataset$Translator_Source==row$Translator_Source),]
  translator_bleu_quantiles <- c(translator_bleu_quantiles, getQuantileNumber(row$BLEU, translatorRows$BLEU))
  translator_rouge_quantiles <- c(translator_rouge_quantiles, getQuantileNumber(row$ROUGE, translatorRows$ROUGE))
  translator_bleurt_quantiles <- c(translator_bleurt_quantiles, getQuantileNumber(row$BLEURT, translatorRows$BLEURT))
}

Dataset$inter_score_bleu_quantiles <- inter_score_bleu_quantiles
Dataset$inter_score_rouge_quantiles <- inter_score_rouge_quantiles
Dataset$inter_score_bleurt_quantiles <- inter_score_bleurt_quantiles

Dataset$translator_bleu_quantiles <- translator_bleu_quantiles
Dataset$translator_rouge_quantiles <- translator_rouge_quantiles
Dataset$translator_bleurt_quantiles <- translator_bleurt_quantiles

getQuantileName <- function(translator_name, quantile_number){
  return(paste(translator_name, quantile_number,"Quantile", sep = "_"))
}

interscore_string_links <- c()
link_seperator <- "_"

for (rowIndex in 1:nrow(Dataset)){
  row <- Dataset[rowIndex,]
  inter_score_bleu_quantile <- row$inter_score_bleu_quantiles
  inter_score_rouge_quantile <- row$inter_score_rouge_quantiles
  inter_score_bleurt_quantile <- row$inter_score_bleurt_quantiles
  bleu_node_index <- match(getQuantileName("BLEU", inter_score_bleu_quantile), inter_score_nodes$name) - 1
  rouge_node_index <- match(getQuantileName("ROUGE", inter_score_rouge_quantile), inter_score_nodes$name) - 1
  bleurt_node_index <- match(getQuantileName("BLEURT", inter_score_bleurt_quantile), inter_score_nodes$name) - 1
  interscore_string_links <- c( interscore_string_links,paste(bleu_node_index, bleurt_node_index, sep = link_seperator))
  interscore_string_links <- c( interscore_string_links,paste(bleurt_node_index, rouge_node_index, sep = link_seperator))
  interscore_string_links <- c( interscore_string_links,paste(bleu_node_index, rouge_node_index, sep = link_seperator))
}

build_translator_string_links <- function(Dataset, score_name){
  translator_score_string_links <- c()
  for (source_sentence in unique(Dataset$original_text)){
    source_sentence_rows <- Dataset[which(Dataset$original_text==source_sentence),]
    #if(nrow(source_sentence_rows)!=4){
     # print(paste("Warning:expected 4 sentences, received", nrow(source_sentence_rows), "for sentence", source_sentence))
    #}
    columnName <- paste("translator", score_name, "quantiles", sep = "_")
    cefQuantile <- source_sentence_rows[which(source_sentence_rows$Translator_Source=="CEF")[1],columnName]
    cef_node_index <- match(getQuantileName("CEF", cefQuantile), inter_translator_nodes$name) - 1
    deeplQuantile <- source_sentence_rows[which(source_sentence_rows$Translator_Source=="Deepl")[1],columnName]
    deepl_node_index <- match(getQuantileName("Deepl", deeplQuantile), inter_translator_nodes$name) - 1
    googleQuantile <- source_sentence_rows[which(source_sentence_rows$Translator_Source=="Google")[1],columnName]
    google_node_index <- match(getQuantileName("Google", googleQuantile), inter_translator_nodes$name) - 1
    ibmQuantile <- source_sentence_rows[which(source_sentence_rows$Translator_Source=="IBM")[1],columnName]
    ibm_node_index <- match(getQuantileName("IBM", ibmQuantile), inter_translator_nodes$name) - 1
    translator_score_string_links <- c(translator_score_string_links,paste(cef_node_index, deepl_node_index, sep = link_seperator))
    translator_score_string_links <- c(translator_score_string_links,paste(cef_node_index, google_node_index, sep = link_seperator))
    translator_score_string_links <- c(translator_score_string_links,paste(cef_node_index, ibm_node_index, sep = link_seperator))
    translator_score_string_links <- c(translator_score_string_links,paste(deepl_node_index, google_node_index, sep = link_seperator))
    translator_score_string_links <- c(translator_score_string_links,paste(deepl_node_index, ibm_node_index, sep = link_seperator))
    translator_score_string_links <- c(translator_score_string_links,paste(google_node_index, ibm_node_index, sep = link_seperator))
  }
  return(translator_score_string_links)
}


#count sum count=4
#score Name = Human Eval


translator_bleu_string_links <- build_translator_string_links(Dataset,"bleu")
translator_rouge_string_links <- build_translator_string_links(Dataset,"rouge")
translator_bleurt_string_links <- build_translator_string_links(Dataset,"bleurt")


# uncomment for Scores
#string_links <- interscore_string_links
#sankey_nodes <- inter_score_nodes

#uncomment one for Translators by scores
#string_links <- translator_bleu_string_links
#string_links <- translator_rouge_string_links
string_links <- translator_bleurt_string_links
sankey_nodes <- inter_translator_nodes

source <- c()
target <- c()
count <- c()

for (string_link in unique(string_links)){
  nodes <- unlist(str_split(string_link, link_seperator))
  source <- c(source, as.numeric(nodes[1]))
  target <- c(target, as.numeric(nodes[2]))
  count <- c(count, sum(string_links == string_link))
}

sankey_links <- data.frame(source, target, count)

sankeyNetwork(Links = sankey_links, Nodes = sankey_nodes,
              Source = "source", Target = "target", Value = "count",
              NodeID = "name", 
              sinksRight=FALSE,
              fontSize = 18, nodeWidth = 20, iterations = 100)

