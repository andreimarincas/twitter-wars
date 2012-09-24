<?php
//    header('content-type: application/json; charset=utf-8');
    
    class SpellCheck
    {
        public function checkSpelling($text)
        {
            if (!$text || !isset($text) || !is_string($text) || $text == "") {
                return NULL;
            }
            $body = '<?xml version="1.0" encoding="utf-8" ?>';
            $body .= '<spellrequest textalreadyclipped="0" ignoredubs="1" ignoredigits="1" ignoreallcaps="1">';
            $body .= "<text>" . urldecode($text) . "</text>";
            $body .= '</spellrequest>';
            $lang = 'en';
            $url = "https://www.google.com/tbproxy/spell?lang=" . $lang;
            $output = $this->post($url, $body);

            if ($output) {
//                print "output: " . print_r($output) . "\n";
                $data = array();
                $resp = NULL;
                
                try {
                    $resp = new SimpleXMLElement($output);
                } catch (Exception $e) {
                    print "Exception caught: " . $e->getMessage() . "\n";
                    print "output: " . print_r($output) . "\n";
                    return NULL;
                }

                if ($resp) foreach($resp->c as $r) {
                    $data[] = array(
                        'o' => (int) $r['o'],
                        'l' => (int) $r['l'],
                        's' => (int) $r['s'],
                        'a' => explode("\t",$r)
                    );
                }
                return $data;
            }
            return NULL;
        }

        public function post($url, $xml)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            $content = curl_exec($ch);
            curl_close($ch);

            if (empty($content)) {
                // Server timeout
                return NULL;
            }
            return $content;
        }

        public function getIncorrectWords($text)
        {
            $spellData = $this->checkSpelling($text);

            if (is_array($spellData)) {
                $incorrectWords = array();

                foreach ($spellData as $wordSpell) {
                    $o = $wordSpell['o'];
                    $l = $wordSpell['l'];
                    $word = substr($text, $o, $l);
                    $incorrectWords[] = $word;
                }
                return $incorrectWords;
            }
            // No spelling results
            return NULL;
        }
    }
    
    /*
//    $text = "I have never ben so hapy";
//    $text = "@Indigo_Blues_ @aquamarine_jo @brookesey66 @indigo_blues_ was that a deleted scene! LOL";
//    $text = "Accidently cloned myself again. #GodsSake http://t.co/ah7nlPPT";
    
    $text = "@Indigo_Blues_ @aquamarine_jo @brookesey66 @indigo_blues_ was that a deleted scene! LOL #ddasd #ddasdsad sdas";
    
//    $textToSpell = preg_replace('/(@\w+)/', '', $text);
    $textToSpell = preg_replace('/([@#]\w+)/', '', $text);
    
    echo "original text: [" . $text . "]\n";
    echo "spelling text: [" . $textToSpell . "]\n";
    
    $incorrectWords = getIncorrectWords($textToSpell);
//    $incorrectWords = getIncorrectWords($text);
    
    if (is_array($incorrectWords)) {
        echo "incorrect words count: " . count($incorrectWords) . "\n";
        $incorrectText = implode(" ", $incorrectWords);    
        echo "incorrect text: [" . $incorrectText . "]\n";
    } else {
        echo "Cannot check spelling.\n";
    }
    */
?>