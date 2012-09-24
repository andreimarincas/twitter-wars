<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
            require_once("twitter_plot.php");
            
            $untilDate = isset($_REQUEST['until']) ? $_REQUEST['until'] : NULL;
            $daysCount = isset($_REQUEST['days']) ? $_REQUEST['days'] : 7;
            
            $tsSerie = getPastTimestampSerie($daysCount, $untilDate);
            
            // tweets fitness
            
            $LondonSpellFitness = getSpellingFitness($tsSerie, 'london_tweets');
            $ExeterSpellFitness = getSpellingFitness($tsSerie, 'exeter_tweets');
            
            plotTwitterSpelling($LondonSpellFitness, $ExeterSpellFitness, $tsSerie);
            
            // tweets count
            
            $LondonStatuses = getStatusesCount($tsSerie, 'london_tweets');
            $ExeterStatuses = getStatusesCount($tsSerie, 'exeter_tweets');
            
            plotTwitterCount($LondonStatuses, $ExeterStatuses, $tsSerie);
            
            echo "<p align='center'><img src='images/twitter_spelling.png' /></p>";
            echo "<p align='center'><img src='images/twitter_count.png' /></p>";
        ?>
    </body>
</html>
