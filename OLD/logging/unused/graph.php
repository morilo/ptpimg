<?php
require("/home/ptpimg/config.dat");
require("misc.class.php");
require("sql.class.php");
require("cache.php");
$DB=NEW DB_MYSQL;
$Cache=NEW CACHE;

session_start();
require_once ('jpgraph/jpgraph.php');
require_once ('jpgraph/jpgraph_line.php');
require_once ('jpgraph/jpgraph_mgraph.php');
//------------------------------------------------------------------
// Create some random data for the plot. We use the current time for the
// first X-position
//------------------------------------------------------------------
$datay = array();
$datax = array();
$datay2 = array();
$ts = time();
if(isset($_GET['i']) && is_numeric($_GET['i'])) {
	$Interval=$_GET['i'];
} else {
	$Interval=1;
}
// This is for the totals
// Data sets are too big for 12-48 hours
$Extra='';
if($Interval>12) {
	$Extra="AND DATE_FORMAT(Time, '%i') IN (0,15,30,45)";
}
$DateString=$Cache->get_value('graphd_'.$Interval);
list($TotalBW, $TotalHits)=$Cache->get_value('grapht_'.$Interval);
if(!($TData=$Cache->get_value('graph_'.$Interval))) {
	$DB->query("SELECT Time, Hits, Bandwidth FROM records WHERE Time > DATE_SUB(NOW(), INTERVAL %d hour) %s",$Interval,$Extra);
	$TData=$DB->to_array();
	$DB->query("SELECT sum(bandwidth), sum(hits) FROM records WHERE Time > DATE_SUB(NOW(), INTERVAL %d hour)",$Interval);
	list($TotalBW, $TotalHits)=$DB->next_record();
	$Cache->cache_value('grapht_'.$Interval, array($TotalBW, $TotalHits), 300);
	$Cache->cache_value('graph_'.$Interval, $TData, 150);
	$DateString="last updated: ".date("j M Y G:i", time());
	$Cache->cache_value('graphd_'.$Interval, $DateString, 300);
} 
$i=0;
while(list($Key,list($Time, $Hits, $Bandwidth))=each($TData)) {
	$datax[$i] = date("G:i", strtotime($Time));
	if(!$Hits) $Hits=0;
	if(!$Bandwidth) $Bandwidth=0;
	$datay[$i] = $Hits;
	$datay2[$i] = floor($Bandwidth/1024/1024);
	$i++;
}
$Avg1=calculate_average($datay);
$Avg2=calculate_average($datay2);
// Overall width of graphs
$w = 450;

//----------------------
// Setup the line graph
//----------------------
$graph = new Graph($w,200,'auto');
$graph->SetScale('textint');
$graph->SetFrame(false);
$graph->SetBox(true);
$graph->xaxis->SetPos('min');
$graph->xaxis->SetTickLabels($datax);
switch($Interval) {
	case 48:
		$graph->xaxis->SetTextLabelInterval(384);
	break;
	case 24:
		$graph->xaxis->SetTextLabelInterval(192);
	break;
	case 12:
		$graph->xaxis->SetTextLabelInterval(96);
	break;
	case 6:
		$graph->xaxis->SetTextLabelInterval(48);
	break;
	case 1:
	default:
		$graph->xaxis->SetTextLabelInterval(8);
	break;
}
$graph->yaxis->SetLabelFormatCallback('number_format');
$graph->yaxis->title->Set('Hits');
$graph->xaxis->scale->SetGrace(20);
$graph->footer->center->SetFont(FF_USERFONT,FS_NORMAL);
$Min=min($datay);
$Max=max($datay);
$graph->footer->center->Set("Average: ".number_format(floor($Avg1)).
							" Minimum: ".number_format($Min).
							" Maximum: ".number_format($Max).
							" Total: ".number_format($TotalHits));

$p1 = new LinePlot($datay);
$graph->Add($p1);
$p1->value->SetFormat('%d');
$p1->value->MinAndMax=true;
$p1->value->min=$Min;
$p1->value->max=$Max;
$p1->value->show();


//----------------------
// Setup the bar graph
//----------------------
$graph2 = new Graph($w,200,'auto');
$graph2->SetScale('textint');
$graph2->xaxis->SetTextLabelInterval(10);
$graph2->xaxis->SetPos('max');
$graph2->xaxis->scale->SetGrace(20);
$Min=min($datay2);
$Max=max($datay2);
$graph2->footer->SetMargin(0,0,15);
$graph2->footer->center->Set("Average: ".number_format(floor($Avg2)).
							" Minimum: ".number_format($Min).
							" Maximum: ".number_format($Max).
							" Total: ".number_format($TotalBW/1024/1024). " megabytes");
$graph2->xaxis->HideLabels();
$graph2->yaxis->SetLabelFormatCallback('number_format');
$graph2->yaxis->title->Set('Megabytes');
$graph2->SetFrame(false);
$graph2->SetBox(true);
$p2 = new LinePlot($datay2);
$graph2->Add($p2);
$p2->value->SetFormat('%d');
$p2->value->MinAndMax=true;
$p2->value->min=$Min;
$p2->value->max=$Max;
$p2->value->show();

// Moving average
/*$Median1 = new LinePlot($Avg1);
$Median1->SetWeight(2);
$Median1->SetColor("red");
$graph->Add($Median1);
$Median2 = new LinePlot($Avg2);
$Median2->SetWeight(2);
$Median2->SetColor("red");
$graph2->Add($Median2);*/

//-----------------------
// Create a multigraph
//----------------------
$mgraph = new MGraph();
$mgraph->title->SetFont(FF_USERFONT,FS_BOLD,14);
switch($Interval) {
	case 48:
		$mgraph->title->set("Hits/bandwidth usage, 48 hour");
	break;
	case 24:
		$mgraph->title->set("Hits/bandwidth usage, last day");
	break;
	case 12:
		$mgraph->title->set("Hits/bandwidth usage, 12 hour");
	break;
	case 6:
		$mgraph->title->set("Hits/bandwidth usage, 6 hour");
	break;
	case 1:
	default:
		$mgraph->title->set("Hits/bandwidth usage, last hour");
	break;
}

$mgraph->SetMargin(2,2,2,2);
$mgraph->SetFrame(true,'darkgray',2);
$mgraph->Add($graph);
$mgraph->Add($graph2,0,200);

$mgraph->footer->right->SetFont(FF_USERFONT,FS_NORMAL);
$mgraph->footer->right->Set($DateString);

//$mgraph->SetShadow();
$mgraph->Stroke();

?>


