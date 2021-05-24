<?php

use Sentiment\Analyzer;

class VaderAnalyser extends Analyser
{
	/**
	 * @inheritDoc
	 */
    public function analyseCsvFile(string $filename): array
	{
        $csvData = $this->loadCsv($filename);

        $results = [];
        foreach ($csvData as $csvRow)
        {
            $results[] = $this->analyse($csvRow['comment'])['compound'];
        }

        return $results;
    }

	/**
	 * @inheritDoc
	 */
    public function analyse(string $string): array
	{
		$analyzer = new Analyzer();

		return $analyzer->getSentiment($string);
    }
}
