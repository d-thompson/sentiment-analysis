<?php

class AfinnAnalyser extends Analyser
{
	protected array $dataSet;

	public function __construct(string $dataSet = 'AFINN-en-165.txt')
	{
		$this->dataSet = $this->loadDataset($dataSet);
	}

	/**
	 * Loads the specified lexicon dataset.
	 *
	 * @param string $dataset The specified lexicon dataset to load.
	 *
	 * @return array The loaded dataset.
	 */
	protected function loadDataset(string $dataset): array
	{
		$data = [];

		$handle = fopen($dataset, 'rb') or die("Error reading file!");
		while (($line = fgets($handle)) !== false) {
			$row = explode("\t", trim($line));
			$data[$row[0]] = $row[1];
		}

		fclose($handle);

		return $data;
	}

	/**
	 * @inheritDoc
	 */
    public function analyseCsvFile(string $filename): array
	{
        $csvData = $this->loadCsv($filename);

        $results = [];
        foreach ($csvData as $csvRow)
        {
            $results[] = $this->analyse($csvRow['comment']) + ['helpful' => (int)$csvRow['helpful']];
        }

        return $results;
    }

	/**
	 * @inheritDoc
	 */
    protected function analyse(string $string): array
	{
        // Split string into words, removing any special characters or double spaces from the string, except for hyphens or apostrophes.
        $words = explode(' ', preg_replace(['/[^a-z0-9 -]+/', '/\s+/'], ['', ' '], strtolower($string)));

		$score = 0;
		foreach ($words as $word)
		{
			if (array_key_exists($word, $this->dataSet))
			{
				$score += $this->dataSet[$word];
			}
		}

		return ['score' => $score, 'weightedScore' => $score / count($words)];
    }
}
