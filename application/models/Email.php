<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Email
 *
 * @author goxeta
 */
class Email {
    //put your code here
    private $_username;
    private $_password;
    private $_server;
    public function set($values){
        $this->_username=$values["username"];
        $this->_password=$values["password"];
        $this->_server=$values["server"];
        
    }
    
    public function auth()
    {
        try{
         $storage = new Zend_Mail_Storage_Imap(array('host' => $this->_server,
            'user' => $this->_username,
            'password' => $this->_password,
            'ssl' => 'SSL'));
         return TRUE;
        } catch (Exception $e){
            return false;
        }
    }
    
    public function getPaginatedHeader($page = 1, $limit = 0)
    {
                        
                       

         $storage = new Zend_Mail_Storage_Imap(array('host' => $this->_server,
            'user' => $this->_username,
            'password' => $this->_password,
            'ssl' => 'SSL'));
         
        //Zend_Debug::dump($storage);

   //mb_internal_encoding('UTF-8');
//$v = str_replace("_"," ", mb_decode_mimeheader($or));
   $id=1;
    foreach($storage as $mail){
        //Zend_Debug::dump($mail->getUniqueId());
       // var_dump($mail->getUniqueId());
        $email[]=array('id'=>$id,
            'from'=>str_replace("_"," ", mb_decode_mimeheader($mail->from)),
            'subject'=>str_replace("_"," ", mb_decode_mimeheader($mail->subject)),
            'time'=>utf8_encode(htmlentities(date("Y-m-d H:s" ,strtotime($mail->date)))));
        $id++;
    }
        $paginator = Zend_Paginator::factory($email);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($limit);
     
        return $paginator;

    }
    
    public function message($id)
    {
          $storage = new Zend_Mail_Storage_Imap(array('host' => $this->_server,
            'user' => $this->_username,
            'password' => $this->_password,
            'ssl' => 'SSL'));
      
        $foundPart = null;
        $mail=$storage->getMessage($id);
        $array=array();
           // header('Content-Type: text/html; charset=utf-8');
           // echo '----------------------<br />' . "\n";
            //echo "From: " . utf8_encode($mail->from) . "<br />\n";
            $array["from"]=utf8_encode($mail->from);
          //  echo "To: " . utf8_encode(htmlentities($mail->to)) . "<br />\n";
            //echo "Time: " . utf8_encode(htmlentities(date("Y-m-d H:s", strtotime($mail->date)))) . "<br />\n";
            $array["time"]=utf8_encode(htmlentities(date("Y-m-d H:s", strtotime($mail->date))));
            //echo "Subject: " . utf8_encode($mail->subject) . "<br />\n";
            $array["subject"]=utf8_encode($mail->subject);

            foreach (new RecursiveIteratorIterator($mail) as $part) {
                try {
                    if (strtok($part->contentType, ';') == 'text/plain') {
                        $foundPart = $part;
                        break;
                    }
                } catch (Zend_Mail_Exception $e) {
                    // ignore
                }
            }
            if (!$foundPart) {
                $content="No plain text part found <br /><br /><br /><br />\n\n\n";
            } else {
                $content = str_replace("\n", "\n<br />", trim(quoted_printable_decode(strip_tags($foundPart))));
                //$content= str_replace("Å¼", "ż", $content);
                //$content= Encoding::fixUTF8($content);

               // echo "plain text part: <br />" .
               // $content
               // . " <br /><br /><br /><br />\n\n\n";
            }
            $array["content"]=$content;
            return $array;
    }
    
    public function send($values)
    {
         $session = new Zend_Session_Namespace('Email');
                $valuesSession = $session->email;
                $list=array('auth'=>'login',
                                'username'=>$valuesSession["username"],
                                'password'=>$valuesSession["password"],
                                'ssl'=>'ssl',
                                'smtpPort'=>465);
                try {
                    $transport = new Zend_Mail_Transport_Smtp($valuesSession["server"], $list);
                    Zend_Mail::setDefaultTransport($transport);
                    $mail = new Zend_Mail();
                    $mail->setBodyText($values["message"]);
                    $mail->setFrom($valuesSession["username"],"");
                    $mail->addTo($values["to"], 'Some Recipient');
                    $mail->setSubject($values["title"]);
                    $cos=$mail->send();
                    $return ="Sucessfull";
                } catch (Exception $e) {
                   $return=$e->getMessage();
                    
                }
        return $return;
    }
}
