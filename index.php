<?php

require_once 'vendor/autoload.php';
require_once 'Analyser.php';
require_once 'AfinnAnalyser.php';
require_once 'VaderAnalyser.php';

$analyser = new AfinnAnalyser();
$results = $analyser->analyseCsvFile('reviews_pseudoanonymised.csv');

$analyser = new VaderAnalyser();
$results2 = $analyser->analyseCsvFile('reviews_pseudoanonymised.csv');


$dataPoints = [];
foreach ($results as $index => $result)
{
	$dataPoints[] = [
		'y' => $result['score'],
		'label' => $index,
	];
}

/**
 * Calculates the median score out of an array of values.
 *
 * @param array $array The array of values use to work out the median.
 *
 * @return float The calculated median.
 */
function median(array $array): float
{
	$count = count($array);
	$mid = floor(($count - 1) / 2);
	if ($count % 2)
	{
		$median = $array[$mid];
	}
	else
	{
		$low = $array[$mid];
		$high = $array[$mid + 1];
		$median = (($low + $high) / 2);
	}

	return $median;
}

/**
 * Works out the various inter-quartile range values based on a specified array.
 *
 * @param array $array The array of values to use to calculate the inter-quartile range values.
 *
 * @return array The different iqr range values.
 */
function IQR(array $array)
{
	sort($array);

	$median = median($array);

	$q1 = ($median + min($array)) / 2;
	$q3 = (max($array) + $median) / 2;

	return [min($array), $q1, $q3, max($array), $median];
}

?>
<!DOCTYPE HTML>
<html>
<head>
	<script>
		window.onload = function()
		{
			var chart = new CanvasJS.Chart("chartContainer", {
				animationEnabled: true,
				theme: "light2",
				title: {
					text: "Sentiment Analysis Scores"
				},
				axisY: {
					title: "AFINN Scores",
					titleFontColor: "#4F81BC",
					lineColor: "#4F81BC",
					labelFontColor: "#4F81BC",
					tickColor: "#4F81BC"
				},
				data: [
					{
						type: "column",
						dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
					},
				]
			});
			chart.render();


			var chart = new CanvasJS.Chart("chartContainer2", {
				animationEnabled: true,
				title: {
					text: "AFINN Sentiment Analysis Scores Distribution"
				},
				axisY: {
					title: "AFINN Score",
					interval: 4
				},
				data: [{
					type: "boxAndWhisker",
					upperBoxColor: "#FFC28D",
					lowerBoxColor: "#9ECCB8",
					color: "black",
					dataPoints: [
						{
							label: "AFINN Analysis",
							y: <?= json_encode(IQR(array_column($results, 'score')), JSON_NUMERIC_CHECK); ?>
						},
					]
				}]
			});
			chart.render();

			var chart = new CanvasJS.Chart("chartContainer3", {
				animationEnabled: true,
				title: {
					text: "Sentiment Analysis Scores Distribution"
				},
				axisY: {
					title: "Score",
					interval: 0.1,
				},
				data: [{
					type: "boxAndWhisker",
					upperBoxColor: "#FFC28D",
					lowerBoxColor: "#9ECCB8",
					color: "black",
					dataPoints: [
						{
							label: "AFINN Analysis",
							y: <?= json_encode(IQR(array_map(fn($x) => $x / 42, array_column($results, 'score'))),
								JSON_NUMERIC_CHECK); ?>
						},
						{
							label: "AFINN Weighted Analysis",
							y: <?= json_encode(IQR(array_map(fn($x) => $x * 1.37,
								array_column($results, 'weightedScore'))), JSON_NUMERIC_CHECK); ?>
						},
						{label: "VADER Analysis", y: <?= json_encode(IQR($results2), JSON_NUMERIC_CHECK); ?>},
					]
				}],
			});
			chart.render();
		}
	</script>
</head>
<body>
<div id="chartContainer" style="height: 370px; width: 100%;"></div>
<div id="chartContainer2" style="height: 370px; width: 100%;"></div>
<div id="chartContainer3" style="height: 370px; width: 100%; margin-bottom: 50px;"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</body>
</html>
