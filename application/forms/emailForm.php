<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of loginForm
 *
 * @author goxeta
 */
class emailForm extends Zend_Form
{
    public function init()
    {
        $to = new Zend_Form_Element_Text('to');
        $to->setLabel('To:')->addFilters(array('StringTrim', 'StripTags'))
            ->addValidator('EmailAddress', TRUE)->setRequired(true);
        
        
        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Title:')->setRequired(TRUE);
        
        $message = new Zend_Form_Element_Textarea('message');
        $message->class = 'formtextarea';
        $message->setLabel('Message');

        $submit = new Zend_Form_Element_Submit('Send');
        $submit->class = 'formsubmit';
        $server=new Zend_View_Helper_ServerUrl();
        $base=new Zend_View_Helper_BaseUrl();
      

        $this->addElements(array($to,$title,$message,$submit));
        $this->setAction($server->serverUrl().$base->baseUrl().'/index/send');
        $this->setMethod('post');
    }


}

