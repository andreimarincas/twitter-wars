<?php

 /// Standard inclusions   
 include("pChart/pData.class");
 include("pChart/pChart.class");

 // Dataset definition 
// $DataSet = new pData;
// $DataSet->ImportFromCSV("Sample/datawithtitle.csv",",",array(1,2,3),TRUE,0);
 
// $DataSet->AddPoint(array(1,4,3,4,3,3,2,1,0,7,4,3,2,3,3,5,1,0,7),"Serie1");
// $DataSet->AddPoint(array(1,4,2,6,2,3,0,1,5,1,2,4,5,2,1,0,6,4,2),"Serie2");
 
// $DataSet = new pData;
// $DataSet->AddPoint(array(0,1.204119983,4.294091292,9.632959861,17.47425011,28.01344501,41.40980396,57.79775917,77.29364326,100,126.0085149,155.4020994,188.2564265,224.641095,264.6205333,308.2547156,355.5997383,406.7082917,461.6300499,520.4119983),"Serie1");
// $DataSet->AddPoint(array(4,9,16,25,36,49,64,81,100,121,144,169,196,225,256,289,324,361,400),"Serie2");
// $DataSet->AddPoint(array(2,4.5,8,12.5,18,24.5,32,40.5,50,60.5,72,84.5,98,112.5,128,144.5,162,180.5,200),"Serie3");
 
// $DataSet->AddPoint(array(1,4,3,4,3,3,2,1,0,7,4,3,2,3,3,5,1,0,7),"Serie1");
// $DataSet->AddPoint(array(1,4,2,6,2,3,0,1,5,1,2,4,5,2,1,0,6,4,2),"Serie2");
// 
// $DataSet->AddAllSeries();
// $DataSet->SetAbsciseLabelSerie();
 
// $DataSet->SetSerieName("January","Serie1");
// $DataSet->SetSerieName("February","Serie2");
// $DataSet->SetSerieName("March","Serie3");
 
// $DataSet->SetSerieName("Count","Serie1");
// $DataSet->SetSerieName("Fitness","Serie2");
 
 $DataSet1 = new pData;
 $DataSet1->AddPoint(array(1,4,3,4,3,3,2,1,0,7,4,3,2,3,3,5,1,0,7),"Serie1");
 $DataSet1->AddAllSeries();
 $DataSet1->SetAbsciseLabelSerie();
 $DataSet1->SetSerieName("Count","Serie1");
 
 $DataSet2 = new pData;
 $DataSet2->AddPoint(array(1,4,2,6,2,3,0,1,5,1,2,4,5,2,1,0,6,4,2),"Serie2");
 $DataSet2->AddAllSeries();
 $DataSet2->SetAbsciseLabelSerie();
 $DataSet2->SetSerieName("Fitness","Serie2");
 
//1,0,1,0.5
//2,1.204119983,4,2
//3,4.294091292,9,4.5
//4,9.632959861,16,8
//5,17.47425011,25,12.5
//6,28.01344501,36,18
//7,41.40980396,49,24.5
//8,57.79775917,64,32
//9,77.29364326,81,40.5
//10,100,100,50
//11,126.0085149,121,60.5
//12,155.4020994,144,72
//13,188.2564265,169,84.5
//14,224.641095,196,98
//15,264.6205333,225,112.5
//16,308.2547156,256,128
//17,355.5997383,289,144.5
//18,406.7082917,324,162
//19,461.6300499,361,180.5
//20,520.4119983,400,200
 
// $DataSet->AddAllSeries();
// $DataSet->SetAbsciseLabelSerie();

 // Initialise the graph
 $Test = new pChart(700,230);
 $Test->setFontProperties("Fonts/tahoma.ttf",8);
 $Test->setGraphArea(60,30,680,200);
 $Test->drawFilledRoundedRectangle(7,7,693,223,5,240,240,240);
 $Test->drawRoundedRectangle(5,5,695,225,5,230,230,230);
 $Test->drawGraphArea(255,255,255,TRUE);
// $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
 $Test->drawScale($DataSet1->GetData(),$DataSet1->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
 $Test->drawGrid(4,TRUE,230,230,230,50);

 // Draw the 0 line
 $Test->setFontProperties("Fonts/tahoma.ttf",6);
 $Test->drawTreshold(0,143,55,72,TRUE,TRUE);

 // Draw the filled line graph
// $Test->drawFilledLineGraph($DataSet->GetData(),$DataSet->GetDataDescription(),100,TRUE);
// $Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
// $Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);
// $Test->drawFilledCubicCurve($DataSet->GetData(),$DataSet->GetDataDescription(),.1,35);

// var $Palette = array("0"=>array("R"=>188,"G"=>224,"B"=>46),
//                        "1"=>array("R"=>224,"G"=>100,"B"=>46),
//                        "2"=>array("R"=>224,"G"=>214,"B"=>46),
//                        "3"=>array("R"=>46,"G"=>151,"B"=>224),
//                        "4"=>array("R"=>176,"G"=>46,"B"=>224),
//                        "5"=>array("R"=>224,"G"=>46,"B"=>117),
//                        "6"=>array("R"=>92,"G"=>224,"B"=>46),
//                        "7"=>array("R"=>224,"G"=>176,"B"=>46));
 
 $Test->setColorPalette(0, 188, 224, 46);  // green
// $Test->setColorPalette(0, 224, 100, 46);  // red
// $Test->setColorPalette(0, 224, 214, 46);  // yellow
// $Test->setColorPalette(0, 46, 151, 224);  // blue
 
 // $Test->drawLineGraph($DataSet2->GetData(),$DataSet2->GetDataDescription());
// $Test->drawPlotGraph($DataSet2->GetData(),$DataSet2->GetDataDescription(),3,2,255,255,255);
 $Test->drawFilledCubicCurve($DataSet1->GetData(),$DataSet1->GetDataDescription(),.1,25);
 
// $Test->setColorPalette(0, 224, 100, 46);
// $Test->setColorPalette(0, 224, 100, 46);
// $Test->setColorPalette(0, 224, 100, 46);
// $Test->setColorPalette(0, 224, 100, 46);
// $Test->setColorPalette(0, 224, 100, 46);
 
// $Test->setColorPalette(0, 46, 151, 224);  // blue
 $Test->setColorPalette(0, 224, 100, 46);
// $Test->drawLineGraph($DataSet1->GetData(),$DataSet1->GetDataDescription());
// $Test->drawPlotGraph($DataSet1->GetData(),$DataSet1->GetDataDescription(),3,2,255,255,255);
 $Test->drawFilledCubicCurve($DataSet2->GetData(),$DataSet2->GetDataDescription(),.1,25);
 
 // Finish the graph
 $Test->setFontProperties("Fonts/tahoma.ttf",8);
// $Test->drawLegend(65,35,$DataSet->GetDataDescription(),255,255,255);
 $Test->drawLegend(65,35,$DataSet1->GetDataDescription(),255,255,255);
 $Test->drawLegend(65,55,$DataSet2->GetDataDescription(),255,255,255);
 $Test->setFontProperties("Fonts/tahoma.ttf",10);
 $Test->drawTitle(60,22,"Example 6",50,50,50,585);
 $Test->Render("tweet_plot.png");
?>