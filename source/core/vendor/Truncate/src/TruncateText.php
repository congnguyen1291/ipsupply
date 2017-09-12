<?php

namespace Truncate;

class TruncateText {
    private $text = '';

    public function __construct( $text = '' ) {
        $this->setText($text);
        return $this;
    }

    private function getText() {
        return $this->text;
    }

    public function setText($text) {
        $this->text = $text;
        return $this;
    }

    function truncateChars($limit, $ellipsis = '...') {
        $text = $this->getText();
        if( strlen($text) > $limit ) 
            $text = trim(substr($text, 0, $limit)) . $ellipsis; 
        return $text;
    }

    function truncate_chars($limit, $ellipsis = '...') {
        $text = $this->getText();
        if( strlen($text) > $limit ) {
            $endpos = strpos(str_replace(array("\r\n", "\r", "\n", "\t"), ' ', $text), ' ', $limit);
            if($endpos !== FALSE)
                $text = trim(substr($text, 0, $endpos)) . $ellipsis;
        }
        return $text;
    }

    function truncateWords($text, $limit, $ellipsis = '...') {
        $text = $this->getText();
        $words = preg_split("/[\n\r\t ]+/", $text, $limit + 1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_OFFSET_CAPTURE);
        if (count($words) > $limit) {
            end($words); //ignore last element since it contains the rest of the string
            $last_word = prev($words);
               
            $text =  substr($text, 0, $last_word[1] + strlen($last_word[0])) . $ellipsis;
        }
        return $text;
    }
    
}
