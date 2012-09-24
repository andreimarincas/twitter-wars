<?php
    require_once('config.php');
    require_once('spellcheck.php');
    
    date_default_timezone_set(G_TIMEZONE);
    
    $con = mysql_connect(DB_HOST, DB_USER, DB_PASS);

    if (!$con) {
        die("Could not connect: " . mysql_error() . "\n");
    }

    $db_selected = mysql_select_db(DB_NAME, $con);

    if (!$db_selected) {
        die ("Can't use database " . DB_NAME . " : " . mysql_error() . "\n");
    }
    
    function getFitness($originalText, $spellText)
    {   
        $c1 = str_word_count($originalText);
        $c2 = str_word_count($spellText);
        
        if ($c1 > 0) {
            return (1 - min(max((1.0 * $c2) / $c1, 0), 1));
        }
        return 1.0;
    }
    
    function spellDatabase($tableName, $sinceDate, $untilDate)
    {
        $sinceTS = strtotime($sinceDate);
        $untilTS = strtotime($untilDate);
        
        $query = sprintf("SELECT id_str FROM %s WHERE spell_checked = 0 AND unix_ts >= %s AND unix_ts <= %s;", 
                          $tableName, $sinceTS, $untilTS);
        $results = mysql_query($query);
        $count = 0;
        $spellCheck = new SpellCheck();
        
        while (($row = mysql_fetch_assoc($results))) {
            $id = $row['id_str'];
            $query2 = sprintf("SELECT * FROM %s WHERE id_str = '%s';", $tableName, $id);
            $results2 = mysql_query($query2);
            
            if (($row2 = mysql_fetch_assoc($results2))) {
                $text = $row2['text'];
                $textToSpell = preg_replace('/([@#]\w+)/', '', $text);
                
                echo "original text: [" . $text . "]\n";
                echo "spelling text: [" . $textToSpell . "]\n";
                
                $incorrectWords = $spellCheck->getIncorrectWords($textToSpell);
                
                if (is_array($incorrectWords)) {
                    echo "incorrect words count: " . count($incorrectWords) . "\n";
                    $spellText = implode(" ", $incorrectWords);    
                    echo "spell text: [" . $spellText . "]\n";
                    $fit = getFitness($text, $spellText);
                    print "fit: " . $fit . "\n";
                    
                    $dbSpellText = mysql_real_escape_string($spellText);
//                    if (!$dbSpellText || $dbSpellText == "") {
//                        $dbSpellText = "NULL";
//                    } else {
//                        $dbSpellText = "\"".$dbSpellText."\"";
//                    }
                    
                    $updateQuery = sprintf("UPDATE %s SET spell_text = '%s', spell_fitness = %s, spell_checked = 1 WHERE id_str = '%s';", 
                                            $tableName, $dbSpellText, $fit, $id);
                    print "update query: " . $updateQuery . "\n";
                    mysql_query($updateQuery);
                    print "rows affected: " . mysql_affected_rows() . "\n";
                    if (mysql_affected_rows() == 0) {
                        print mysql_error() . "\n";
                    }
                    
                } else {
                    echo "Cannot check spelling.\n";
                }
                echo "\n";
            }
            $count++;
        }
        print "count: " . $count . "\n";
        unset($spellCheck);
    }
    
    if (isset($argv[1]) && isset($argv[2])) {
        $sinceTS = strtotime($argv[1]);
        $sinceDate = date("Y-m-d", mktime(0, 0, 0, date("m", $sinceTS), date("d", $sinceTS), date("Y", $sinceTS)));
        print "since: " . $sinceDate . "\n";
        
        $untilTS = strtotime($argv[2]);
        $untilDate = date("Y-m-d", mktime(0, 0, 0, date("m", $untilTS), date("d", $untilTS), date("Y", $untilTS)));
        print "until: " . $untilDate . "\n";
        
        if (isset($argv[3])) {
            $place = strtolower($argv[3]);
            
            if ($place == "london") {
                spellDatabase("london_tweets", $sinceDate, $untilDate);
            
            } elseif ($place == "exeter") {
                spellDatabase("exeter_tweets", $sinceDate, $untilDate);
                
            } else {
                print "Wrong parameter.\n";
                print "The 3rd parameter must be either London or Exeter.\n";
            }
        } else {
            print "No param given.\n";
            print "The 3rd parameter must be either London or Exeter.\n";
        }
    } else {
        print "Please give me correct parameters: sinceDate, untilDate and place.\n";
    }
    
    mysql_close($con);
?>
