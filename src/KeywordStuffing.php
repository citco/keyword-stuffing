<?php namespace Citco;

class KeywordStuffing {

	const aggressive_chars = '\.|•|·|\*|!|:|\?|\$';
	private $text = null;
	private $exclude_words = [
		'and', 'for', 'the', 'with', 'that', 'all', 'they', 'she', 'you', 'your', 'our',
		'will', 'shall', 'have', 'has', 'are', 'may', 'need', 'needs',
	];
	private $min_word_chars;
	private $ratio;

	public $word_popularity = [];
	public $word_dispersion = [];
	public $word_dispersion_by_word = [];
	public $abnormal_words = [];
	public $mean = null;
	public $standard_deviation = null;
	public $cumulative_upper_range = null;
	public $lines = ['normal' => [], 'abnormal' => []];

	function __construct($text = null, $exclude_words = [], $min_word_chars = 3, $ratio = 0.6)
	{
		$this->text = $text;
		empty($exclude_words) OR $this->exclude_words = $exclude_words;
		$this->min_word_chars = $min_word_chars;
		$this->ratio = $ratio;

		empty($text) OR $this->findKeywordStuffing();
	}

	function init()
	{
		$this->word_popularity = [];
		$this->word_dispersion = [];
		$this->word_dispersion_by_word = [];
		$this->abnormal_words = [];
		$this->mean = null;
		$this->standard_deviation = null;
		$this->cumulative_upper_range = null;
		$this->lines = ['normal' => [], 'abnormal' => []];
	}

	function getSummary($text = null)
	{
		empty($text) OR $this->findKeywordStuffing($text);

		$summary = [
			'word_popularity'         => $this->word_popularity,
			'word_dispersion'         => $this->word_dispersion,
			'word_dispersion_by_word' => $this->word_dispersion_by_word,
			'abnormal_words'          => $this->abnormal_words,
			'mean'                    => round($this->mean, 2),
			'standard_deviation'      => round($this->standard_deviation, 2),
			'cumulative_upper_range'  => $this->cumulative_upper_range,
			'lines'                   => $this->lines,
		];

		return $summary;
	}

	static function removeNoiseWords($text, $exclude_words = [])
	{
		foreach ($exclude_words as $word)
		{
			$text = preg_replace("/\b{$word}\b/i", '', $text);
		}

		return $text;
	}

	static function removeShortWords($words, $min_word_chars = 1)
	{
		$words = array_filter($words, function ($word) use ($min_word_chars) {
			return strlen($word) >= $min_word_chars;
		});

		return $words;
	}

	static function trimNonAlphanumeric($words)
	{
		$words = array_map(function ($word) {
			return preg_replace('/^[^a-z0-9]+|[^a-z0-9]+$/i', '', $word);
		}, $words);

		return $words;
	}

	static function removeEmptyHead($array)
	{
		while (count($array) && empty(trim(static::stripTags(reset($array)))))
		{
			array_shift($array);
		}

		return $array;
	}

	static function removeEmptyTail($array)
	{
		while (count($array) && empty(trim(static::stripTags(end($array)))))
		{
			array_pop($array);
		}

		return $array;
	}

	static function insertNewLine($text, $aggressive = false)
	{
		$aggressive AND $text = preg_replace('/' . static::aggressive_chars . '/i', "\n", $text);

		$text = preg_replace('/<br(\s+)?\/?>/i', "\n", $text);

		$text = preg_replace('/&amp;/i', ' ', $text);

		$text = preg_replace('/<strong(\s+)?\/?>/i', ' ', $text);
		$text = preg_replace('/<\/strong(\s+)?\/?>/i', ' ', $text);

		$text = preg_replace('/<p(\s+)?\/?>/i', "\n", $text);
		$text = preg_replace('/<\/p(\s+)?\/?>/i', ' ', $text);

		$text = preg_replace('/<ul(\s+)?\/?>/i', "\n", $text);
		$text = preg_replace('/<\/ul(\s+)?\/?>/i', ' ', $text);

		$text = preg_replace('/<li(\s+)?\/?>/i', "\n", $text);
		$text = preg_replace('/<\/li(\s+)?\/?>/i', ' ', $text);

		$text = preg_replace('/<ol(\s+)?\/?>/i', "\n", $text);
		$text = preg_replace('/<\/ol(\s+)?\/?>/i', ' ', $text);

		return $text;
	}

	static function getLines($text)
	{
		$lines = preg_split('/(\r\n|\n|\r)/', $text);

		$lines = static::removeEmptyHead(static::removeEmptyTail($lines));

		return $lines;
	}

	static function joinLines($lines)
	{
		$text = join("\n", $lines);

		return $text;
	}

	static function stripTags($text)
	{
		$text = preg_replace('/<[^>]*>/', ' ', $text);

		return $text;
	}

	static function getWords($text, $exclude_words = [], $min_word_chars = 1)
	{
		$text = static::stripTags($text);

		$text = strtolower($text);

		$text = static::removeNoiseWords($text, $exclude_words);

		$words = str_word_count($text, 1);

		$words = static::trimNonAlphanumeric($words);

		$words = static::removeShortWords($words, $min_word_chars);

		return $words;
	}

	static function getWordsPopularity($words)
	{
		$popularity = [];

		foreach ($words as $word)
		{
			@$popularity[$word]++;
		}

		return $popularity;
	}

	static function getWordsDispersion($popularity)
	{
		$dispersion = [];
		$min = $max = null;

		foreach ($popularity as $count)
		{
			is_null($min) || $min > $count AND $min = $count;
			is_null($max) || $max < $count AND $max = $count;

			@$dispersion[$count]++;
		}

		if (! is_null($min))
		{
			foreach (range($min, $max) as $count)
			{
				empty($dispersion[$count]) AND $dispersion[$count] = 0;
			}

			ksort($dispersion);
		}

		return $dispersion;
	}

	static function getWordsDispersionByWord($popularity)
	{
		$dispersion = static::getWordsDispersion($popularity);

		foreach ($dispersion as $index => $cnt)
		{
			$dispersion[$index] = [];
			foreach ($popularity as $word => $count)
			{
				$index === $count AND $dispersion[$index][] = $word;
			}
			$dispersion[$index] = join(', ', $dispersion[$index]);
		}

		return $dispersion;
	}

	static function getAbnormalWords($popularity, $upper_range)
	{
		$abnormal_words = [];

		foreach ($popularity as $word => $count)
		{
			$count > $upper_range AND $abnormal_words[] = $word;
		}

		return $abnormal_words;
	}

	static function getMean($dispersion)
	{
		count($dispersion) OR trigger_error('The array is empty', E_USER_ERROR);

		$mean = array_sum($dispersion) / count($dispersion);

		return $mean;
	}

	static function getStandardDeviation($dispersion)
	{
		$standard_deviation = 0;

		count($dispersion) > 1 AND $standard_deviation = stats_standard_deviation($dispersion, true);

		return $standard_deviation;
	}

	static function getCumulativeUpperRange($mine, $standard_deviation, $ratio)
	{
		$cumulative_upper_range = round($mine + ($standard_deviation * $ratio));

		return $cumulative_upper_range;
	}

	static function convertTextToMultipleLine($text)
	{
		$text = static::insertNewLine($text);
		$lines = static::getLines($text);
		if (count($lines) === 1)
		{
			$text = static::insertNewLine($text, true);
			$lines = static::getLines($text);
		}

		return $lines;
	}

	function findKeywordStuffing($lines = null)
	{
		$this->init();

		empty($lines) OR $this->text = $lines;

		$lines = is_array($this->text) ? $this->text : $this->convertTextToMultipleLine($this->text);

		$processed_lines = ['normal' => $lines, 'abnormal' => []];

		if (count($lines) > 1)
		{
			$text = $this->joinLines($lines);
			$words = $this->getWords($text, $this->exclude_words, $this->min_word_chars);
			if (count($words))
			{
				$this->word_popularity = $this->getWordsPopularity($words);
				$this->word_dispersion = $this->getWordsDispersion($this->word_popularity);
				$this->word_dispersion_by_word = $this->getWordsDispersionByWord($this->word_popularity);
				$this->mean = $this->getMean($this->word_dispersion);
				$this->standard_deviation = $this->getStandardDeviation($this->word_dispersion);
				$this->cumulative_upper_range = $this->getCumulativeUpperRange($this->mean, $this->standard_deviation, $this->ratio);
				$this->abnormal_words = $this->getAbnormalWords($this->word_popularity, $this->cumulative_upper_range);

				if ($this->standard_deviation > $this->mean)
				{
					$abnormal_lines = [];
					foreach ($lines as $index => $line)
					{
						$words = $this->getWords($line, $this->exclude_words, $this->min_word_chars);
						$word_popularity = $this->getWordsPopularity($words);
						$count = 0;
						foreach ($this->abnormal_words as $word)
						{
							$count += empty($word_popularity[$word]) ? 0 : $word_popularity[$word];
						}
						if ($count > $this->cumulative_upper_range)
						{
							$abnormal_lines[$index] = $lines[$index];
							unset($lines[$index]);
						}
					}
					$processed_lines = ['normal' => $lines, 'abnormal' => $abnormal_lines];
				}
			}
		}

		$this->lines = $processed_lines;

		return $this;
	}

	private function removeArrayItems()
	{
		$text = $this->text;

		foreach ($this->lines['abnormal'] as $index => $abnormal)
		{
			unset($text[$index]);
		}

		return $text;
	}

	private function generateSinglePattern($words, $group = false)
	{
		$words = array_map(function ($word) {
			return "{$word}.*?";
		}, $words);

		$words = join('', $words);

		$pattern = $group ? "({$words})" : "{$words}";

		return $pattern;
	}

	private function generatePattern($normal_lines, $abnormal_lines)
	{
		$patterns = [];

		foreach ($normal_lines as $index => $normal)
		{
			$keywords = $this->getWords($normal);
			empty($keywords) OR $patterns[$index] = $this->generateSinglePattern($keywords, true);
		}

		foreach ($abnormal_lines as $index => $abnormal)
		{
			$keywords = $this->getWords($abnormal);
			empty($keywords) OR $patterns[$index] = $this->generateSinglePattern($keywords);
		}

		ksort($patterns);

		$patterns = join('(.*?)', $patterns);
		$pattern = "/(.*?){$patterns}(.*?)/isu";

		return $pattern;
	}

	private function generateReplacement($pattern)
	{
		$replacement = '';
		$count = substr_count($pattern, '(');

		for ($i = 1; $i <= $count; $i++)
		{
			$replacement .= "\${$i}";
		}

		return $replacement;
	}

	private function generateEmptyLinePattern()
	{
		$aggressive_chars = static::aggressive_chars;
		$aggressive_chars = "{$aggressive_chars}| |;|,|\(|\)";

		$patterns = [
			"(<br>)[{$aggressive_chars}]+(<br>)",
			"^[{$aggressive_chars}]+((?:<br>))",
			"(<br>)[{$aggressive_chars}]+$",
			"^[{$aggressive_chars}]+$",
		];

		$patterns = join('|', $patterns);

		return "/{$patterns}/m";
	}

	private function removeAbnormalLines($lines, $text)
	{
		$pattern = $this->generatePattern($lines['normal'], $lines['abnormal']);

		$replacement = $this->generateReplacement($pattern);

		$text = preg_replace($pattern, $replacement, $text);

		return $text;
	}

	private function removeEmptyLines($text)
	{
		$pattern = $this->generateEmptyLinePattern();

		do
		{
			$old_text = $text;
			$text = preg_replace($pattern, '$1', $old_text);
		}
		while ($old_text <> $text);

		return $text;
	}

	private function removeStringItems()
	{
		$text = $this->text;

		if (! empty($this->lines['abnormal']))
		{
			$text = $this->removeAbnormalLines($this->lines, $text);

			$text = $this->removeEmptyLines($text);
		}

		return $text;
	}

	function removeKeywordStuffing($text = null)
	{
		$this->findKeywordStuffing($text);

		$text = is_array($this->text) ? $this->removeArrayItems() : $this->removeStringItems();

		return $text;
	}

}
