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

    public function konwerter($string) {
        $string = str_replace("Ã³", "ó", $string);
        $string = str_replace("Å", "ł", $string);
        $string = str_replace("Ä", "ę", $string);
        $string = str_replace("Ã«", "ë", $string);
        $string = str_replace("łº", "ź", $string);
        $string = str_replace("ł¼", "ż", $string);
        $string = str_replace("Åº", "ź", $string);
        return $string;
    }

    public function set($values) {
        $this->_username = $values["username"];
        $this->_password = $values["password"];
        $this->_server = $values["server"];
    }

    public function auth() {
        try {
            $storage = new Zend_Mail_Storage_Imap(array('host' => $this->_server,
                'user' => $this->_username,
                'password' => $this->_password,
                'ssl' => 'SSL'));
            return TRUE;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getPaginatedHeader($page = 1, $limit = 0) {
        $storage = new Zend_Mail_Storage_Imap(array('host' => $this->_server,
            'user' => $this->_username,
            'password' => $this->_password,
            'ssl' => 'SSL'));
        $id = 1;
        foreach ($storage as $mail) {
            $email[] = array('id' => $id,
                'from' => $this->konwerter(str_replace("_", " ", mb_decode_mimeheader($mail->from))),
                'subject' => $this->konwerter(str_replace("_", " ", mb_decode_mimeheader($mail->subject))),
                'time' => utf8_encode(htmlentities(date("Y-m-d H:s", strtotime($mail->date)))));
            $id++;
        }
        $paginator = Zend_Paginator::factory($email);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($limit);

        return $paginator;
    }

    public function message($id) {
        $storage = new Zend_Mail_Storage_Imap(array('host' => $this->_server,
            'user' => $this->_username,
            'password' => $this->_password,
            'ssl' => 'SSL'));

        $foundPart = null;
        $mail = $storage->getMessage($id);
        $array = array();
        $array["from"] = $this->konwerter(utf8_encode($mail->from));
        $array["time"] = utf8_encode(htmlentities(date("Y-m-d H:s", strtotime($mail->date))));
        $array["subject"] = $this->konwerter(utf8_encode($mail->subject));
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
            $array["content"] = $mail->getContent();
        } else {
            $array["content"] = str_replace("\n", "\n<br />", trim(quoted_printable_decode(strip_tags($foundPart))));
        }
        return $array;
    }

    public function send($values) {
        $session = new Zend_Session_Namespace('Email');
        $valuesSession = $session->email;
        $list = array('auth' => 'login',
            'username' => $valuesSession["username"],
            'password' => $valuesSession["password"],
            'ssl' => 'ssl',
            'port' => 465);
        try {
            $transport = new Zend_Mail_Transport_Smtp($valuesSession["server"], $list);
            $mail = new Zend_Mail();
            $mail->setDefaultTransport($transport);
            $mail->setBodyText($values["message"]);
            $mail->setFrom($valuesSession["username"], "");
            $mail->addTo($values["to"], 'Some Recipient');
            $mail->setSubject($values["title"]);
            $mail->send();
            $return = "Sucessfull";
        } catch (Exception $e) {
            $return = $e->getMessage();
        }
        return $return;
    }

}
