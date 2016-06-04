<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of konwerter
 *
 * @author goxeta
 */
class Konwerter {
    //put your code here
    public function utf82iso88592($text) {
$text = str_replace("\xC4\x85", 'ą', $text);
$text = str_replace("\xC4\x84", 'Ą', $text);
$text = str_replace("\xC4\x87", 'ć', $text);
$text = str_replace("\xC4\x86", 'Ć', $text);
$text = str_replace("\xC4\x99", 'ę', $text);
$text = str_replace("\xC4\x98", 'Ę', $text);
$text = str_replace("\xC5\x82", 'ł', $text);
$text = str_replace("\xC5\x81", 'Ł', $text);
$text = str_replace("\xC3\xB3", 'ó', $text);
$text = str_replace("\xC3\x93", 'Ó', $text);
$text = str_replace("\xC5\x9B", 'ś', $text);
$text = str_replace("\xC5\x9A", 'Ś', $text);
$text = str_replace("Ã…Â¼", 'ż', $text);
$text = str_replace("\xC5\xBB", 'Ż', $text);
$text = str_replace("\xC5\xBA", 'ź', $text);
$text = str_replace("\xC5\xB9", 'Ż', $text);
$text = str_replace("\xc5\x84", 'ń', $text);
$text = str_replace("\xc5\x83", 'Ń', $text);

return $text;
}
    
}
