<?php
require './vendor/autoload.php';

use Citco\KeywordStuffing;

$text = file_get_contents('./sample.txt');

$ks = new KeywordStuffing();
$text = $ks->removeKeywordStuffing($text);
$summary = $ks->getSummary();

echo $text;

print_r($summary['word_dispersion_by_word']);
