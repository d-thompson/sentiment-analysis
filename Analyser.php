<?php

/**
 * Base class to use as a template for shared analyser functions.
 */
abstract class Analyser
{
	/**
	 * Runs the sentiment analysis on a specified string.
	 *
	 * @param string $string The specified string to run sentiment analysis on.
	 *
	 * @return array The result of the sentiment analysis. This is a score, and also a weighted score.
	 */
	abstract protected function analyse(string $string): array;

	/**
	 * Runs the sentiment analysis on a specified CSV file.
	 *
	 * @param string $filename The specified CSV file to run the analysis on.
	 *
	 * @return array The calculated scores of the sentiment analysis.
	 */
	abstract public function analyseCsvFile(string $filename): array;


	/**
	 * Loads a specified CSV file.
	 *
	 * @param string $filename The specified CSV filename to load.
	 *
	 * @return array The loaded CSV file data.
	 */
	protected function loadCsv(string $filename): array
	{
		$data = [];

		$handle = fopen($filename, 'rb') or die("Error reading file!");
		while (($line = fgetcsv($handle, 1000, ',')) !== false)
		{
			$data[] = $line;
		}

		$newData = [];
		$columns = $data[0];
		unset($data[0]);
		foreach ($data as $row)
		{
			$newCol = [];
			foreach ($row as $index => $col)
			{
				$newCol[$columns[$index]] = $col;
			}

			$newData[] = $newCol;
		}

		fclose($handle);

		return $newData;
	}
}
