#!/usr/bin/php

<?php

$testing=FALSE;

$limit=107;

if($testing)  $limit=3;

$outputFile='/tmp/results.csv';
$outputContent='';

$ridingsNames = array();
$resultsByRidingByParty = array();
$resultsByPartyByRiding = array();






for($i=1;  $i<=$limit; $i++) {
 
$district=sprintf('%03d', $i);

$url="http://wemakevotingeasy.ca/en/general-election-district-results.aspx?d=$district";

echo "Getting results for riding $i $url...";
$html = file_get_contents($url);

$doc = new DOMDocument();
$doc->loadHTML($html);
$xpath = new DOMXPath($doc);


$ridingNode = $xpath->query('/html/body/div[2]/div[2]/div[2]/div[2]/h2');
$ridingNames[$i] = substr($ridingNode->item(0)->textContent, 5);


$tables = $doc->getElementsByTagName('table');

$nodes  = $xpath->query('/html/body/div[2]/div[2]/div[2]/div[3]/table/tbody');

#echo "For " . $ridingNames[$i] . "\n";
#var_export($tables->item(0)->textContent);
#echo "\n\n";

$rows = $tables->item(0)->getElementsByTagName('tr');


$numRows = $rows->length;

$j=0;
for($j=0; $j<$numRows; $j++) {



	if($j==0)  continue;
	#echo "\n\n";

	$row = $rows->item($j);

	#echo "Content of row is:";
	#echo $row->textContent;
	#echo "\n\n";

	echo "\n\n";
	$cells = $row->getElementsByTagName('td');

	#echo "Cells is a:";
	#var_export($cells);
	#echo "\n\n";


	#echo "\n\n";
	#echo "Cells length:";
	#var_export($cells->length);
	#echo "\n\n";


	$partyNode = $cells->item(0);

	if(!is_object($partyNode))  die("partyNode is not an object\n");

	$party = $cells->item(1)->textContent;

	$votes = $cells->item(2)->textContent;

	#echo "Scrapped: $party\t$votes\n";


	$resultsByRidingByParty[$i][$party] = $votes; 
	$resultsByPartyByRiding[$party][$i] = $votes;

	#var_export($resultsByRidingByParty);
	#echo "\n\n";
	#var_export($resultsByPartyByRiding);
	#echo "\n\n";
}

}




$listOfParties  =array_keys($resultsByPartyByRiding);
$listOfRidings 	=array_keys($resultsByRidingByParty);

$outputContent .= " \t \t";
foreach($listOfParties as $partyName) {
	$outputContent .= $partyName . "\t"; 
}
$outputContent .= "\n";


foreach($ridingNames as $ridingID => $ridingName) {
	$outputContent .= "$ridingID\t$ridingName\t";


	foreach($listOfParties as $partyName) {
		$votes = "";
		
		if(isset($resultsByRidingByParty[$ridingID][$partyName])) {
			$votes = $resultsByRidingByParty[$ridingID][$partyName];
		}
		$outputContent .= $votes . "\t";
	}

	$outputContent .= "\n";
}

echo "\n\n\n\n";
echo $outputContent;

file_put_contents($outputFile, $outputContent);

echo "\n\n\n\n\n";


echo "List of parties are:\n";

sort($listOfParties);
foreach($listOfParties as $partyName) {
	echo "$partyName\n";
}
