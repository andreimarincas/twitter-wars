<?php
require_once('config.php');
date_default_timezone_set(G_TIMEZONE);

// Standard inclusions
 include("pchart/pChart/pData.class");
 include("pchart/pChart/pChart.class");
 
 function plotTwitterSpelling($LondonSerie, $ExeterSerie, $timestampSerie)
 {
    // Dataset definition
    $DataSet = new pData;
    $DataSet->AddPoint($LondonSerie,"LondonSerie");
    $DataSet->AddPoint($ExeterSerie,"ExeterSerie");
    
    $tsSerie = array();
    for ($i = 0; $i < count($timestampSerie); $i++) {
        $tsSerie[] = $timestampSerie[$i];
    }
    $DataSet->AddPoint($tsSerie,"timestampSerie");

    $DataSet->AddSerie("LondonSerie");
    $DataSet->AddSerie("ExeterSerie");
    $DataSet->SetAbsciseLabelSerie("timestampSerie");
    $DataSet->SetSerieName("London","LondonSerie");
    $DataSet->SetSerieName("Exeter","ExeterSerie");
    $DataSet->SetYAxisName("Spelling Correctness");
    $DataSet->SetXAxisFormat("date");

    // Initialise the graph   
    $Test = new pChart(700,230);
    $Test->setFontProperties("pchart/Fonts/tahoma.ttf",8);
    $Test->setGraphArea(85,30,650,200);
    $Test->drawFilledRoundedRectangle(7,7,693,223,5,240,240,240);
    $Test->drawRoundedRectangle(5,5,695,225,5,230,230,230);
    $Test->drawGraphArea(255,255,255,TRUE);
    $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
    $Test->drawGrid(4,TRUE,230,230,230,50);

    // Draw the 0 line   
    $Test->setFontProperties("pchart/Fonts/tahoma.ttf",6);
    $Test->drawTreshold(0,143,55,72,TRUE,TRUE);

    $Test->setColorPalette(1, 224, 100, 46);  // red
    $Test->setColorPalette(0, 188, 224, 46);  // green

    // Draw the line graph
    $Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
    $Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);

    // Finish the graph
    $Test->setFontProperties("pchart/Fonts/tahoma.ttf",8);
    $Test->drawLegend(90,35,$DataSet->GetDataDescription(),255,255,255);
    $Test->setFontProperties("pchart/Fonts/tahoma.ttf",10);
    $Test->drawTitle(60,22,"Twitter Spelling",50,50,50,585);
    $Test->Render("images/twitter_spelling.png");
 }
 
 function plotTwitterCount($LondonSerie, $ExeterSerie, $timestampSerie)
 {
    // Dataset definition
    $DataSet = new pData;
    $DataSet->AddPoint($LondonSerie,"LondonSerie");
    $DataSet->AddPoint($ExeterSerie,"ExeterSerie");
    
    $tsSerie = array();
    for ($i = 0; $i < count($timestampSerie); $i++) {
        $tsSerie[] = $timestampSerie[$i];
    }
    $DataSet->AddPoint($tsSerie,"timestampSerie");

    $DataSet->AddSerie("LondonSerie");
    $DataSet->AddSerie("ExeterSerie");
    $DataSet->SetAbsciseLabelSerie("timestampSerie");
    $DataSet->SetSerieName("London","LondonSerie");
    $DataSet->SetSerieName("Exeter","ExeterSerie");
    $DataSet->SetYAxisName("Statuses Count");
    $DataSet->SetXAxisFormat("date");

    // Initialise the graph   
    $Test = new pChart(700,230);
    
//    $Test->setFixedScale(-200, 1600);
    $maxCount = max(max($LondonSerie), max($ExeterSerie));
    $padding = $maxCount / 6.0;
    $Test->setFixedScale(-$padding, $maxCount + 2 * $padding);
    
    $Test->setFontProperties("pchart/Fonts/tahoma.ttf",8);
    $Test->setGraphArea(85,30,650,200);
    $Test->drawFilledRoundedRectangle(7,7,693,223,5,240,240,240);
    $Test->drawRoundedRectangle(5,5,695,225,5,230,230,230);
    $Test->drawGraphArea(255,255,255,TRUE);
    $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
    $Test->drawGrid(4,TRUE,230,230,230,50);

    // Draw the 0 line   
    $Test->setFontProperties("pchart/Fonts/tahoma.ttf",6);
    $Test->drawTreshold(0,143,55,72,TRUE,TRUE);

    $Test->setColorPalette(1, 224, 100, 46);  // red
    $Test->setColorPalette(0, 188, 224, 46);  // green

    // Draw the line graph
//    $Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
    $Test->drawFilledCubicCurve($DataSet->GetData(),$DataSet->GetDataDescription(),.1,25);
    $Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);

    // Finish the graph
    $Test->setFontProperties("pchart/Fonts/tahoma.ttf",8);
    $Test->drawLegend(90,35,$DataSet->GetDataDescription(),255,255,255);
    $Test->setFontProperties("pchart/Fonts/tahoma.ttf",10);
    $Test->drawTitle(60,22,"Twitter Statuses",50,50,50,585);
    $Test->Render("images/twitter_count.png");
 }
 
 function getPastTimestampSerie($n, $date = NULL)
 {
     if (!$date) {
         $date = date('Y-m-d');
     }
     $ts = strtotime($date);
     
     $year   = date('Y', $ts);
     $month  = date('m', $ts);
     $day    = date('d', $ts);
     
     $timestamps = array();
     
     for ($i = 1; $i <= $n; $i++) { 
         $timestamps[$n - $i] = strtotime($date);
         $date = date("Y-m-d", mktime(0, 0, 0, $month, $day - $i, $year));
     }
     return $timestamps;
 }
 
 function getStatusesCount($timestampSerie, $tableName)
 {
    if (!is_array($timestampSerie)) return NULL;
    
    $con = mysql_connect(DB_HOST, DB_USER, DB_PASS);

    if (!$con) {
        die('Could not connect: ' . mysql_error());
    }

    $db_selected = mysql_select_db(DB_NAME, $con);

    if (!$db_selected) {
        die('Can\'t use database ' . DB_NAME . ' : ' . mysql_error());
    }
    
    $statusesCount = array();
    $n = count($timestampSerie);
    
    if ($n > 0) {
        $untilTS = $timestampSerie[0];
        $year   = date('Y', $untilTS);
        $month  = date('m', $untilTS);
        $day    = date('d', $untilTS);
        $sinceTS = strtotime(date("Y-m-d", mktime(0, 0, 0, $month, $day - 1, $year)));
        
        for ($i = 0; $i < $n; $i++) { 
            $untilTS = $timestampSerie[$i];
//            print $tableName . ": fetching statuses since " . date('Y-m-d', $sinceTS) . " (" . $sinceTS . ") until " . date('Y-m-d', $untilTS) . " (" . $untilTS . ")\n";
            
            $query = sprintf("SELECT COUNT(*) FROM %s WHERE spell_checked = 1 AND unix_ts >= %s AND unix_ts < %s", 
                              $tableName, $sinceTS, $untilTS);
            
//            $query = sprintf("SELECT COUNT(*) FROM %s WHERE unix_ts >= %s AND unix_ts < %s", 
//                              $tableName, $sinceTS, $untilTS);
//            print "query = " . $query . "\n";
            $result = mysql_query($query);
            
            if (($row = mysql_fetch_row($result))) {
                $statusesCount[$i] = $row[0];
//                print "statuses count: " . $statusesCount[$i] . "\n";
            }
            $sinceTS = $untilTS;
        }
    }
    mysql_close($con);
    return $statusesCount;
 }
 
 function getSpellingFitness($timestampSerie, $tableName)
 {
    if (!is_array($timestampSerie)) return NULL;
    
    $con = mysql_connect(DB_HOST, DB_USER, DB_PASS);

    if (!$con) {
        die('Could not connect: ' . mysql_error());
    }

    $db_selected = mysql_select_db(DB_NAME, $con);

    if (!$db_selected) {
        die('Can\'t use database ' . DB_NAME . ' : ' . mysql_error());
    }
    
    $spellingFitness = array();
    $n = count($timestampSerie);
    
    if ($n > 0) {
        $untilTS = $timestampSerie[0];
        $year   = date('Y', $untilTS);
        $month  = date('m', $untilTS);
        $day    = date('d', $untilTS);
        $sinceTS = strtotime(date("Y-m-d", mktime(0, 0, 0, $month, $day - 1, $year)));
        
        for ($i = 0; $i < $n; $i++) { 
            $untilTS = $timestampSerie[$i];
//            print $tableName . ": get spell fitness since " . date('Y-m-d', $sinceTS) . " (" . $sinceTS . ") until " . date('Y-m-d', $untilTS) . " (" . $untilTS . ")\n";
            
            $query = sprintf("SELECT COUNT(*), SUM(spell_fitness)  FROM %s WHERE spell_checked = 1 AND unix_ts >= %s AND unix_ts < %s", 
                              $tableName, $sinceTS, $untilTS);
//            print "query = " . $query . "\n";
            $result = mysql_query($query);
            
            if (($row = mysql_fetch_row($result))) {
                $count = $row[0];
                $fitness = $row[1];
//                print "statuses count: " . $count . "\n";
//                print "statuses spelling fitness: " . $fitness . "\n";
                $spellingFitness[$i] = $fitness / $count;
//                print "fitness: " . $spellingFitness[$i] . "\n";
            }
            $sinceTS = $untilTS;
        }
    }
    mysql_close($con);
    return $spellingFitness;
 }
 
 /*
// $today = date('Y-m-d');
// print "today: ".$today."\n";
// $yesterday = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')));
// print "yesterday: ".$yesterday."\n";
// $tomorrow = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')));
 
 $tsSerie = getPastTimestampSerie(7);
// for ($i = 0; $i < count($tsSerie); $i++) {
//     print $tsSerie[$i] . "\n";
// }
 
 // tweets spelling fitness
 
 $LondonSpellFitness = getSpellingFitness($tsSerie, 'london_tweets');
 $ExeterSpellFitness = getSpellingFitness($tsSerie, 'exeter_tweets');
 
 plotTwitterSpelling($LondonSpellFitness, $ExeterSpellFitness, $tsSerie);
 
 // tweets count
 
 $LondonStatuses = getStatusesCount($tsSerie, 'london_tweets');
 $ExeterStatuses = getStatusesCount($tsSerie, 'exeter_tweets');
 
 plotTwitterCount($LondonStatuses, $ExeterStatuses, $tsSerie);

*/
?>