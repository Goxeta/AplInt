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
class loginForm extends Zend_Form
{
    public function init()
    {
        $name = new Zend_Form_Element_Text('username');
        $name->setLabel('Username:')->addFilters(array('StringTrim', 'StripTags'))
            ->addValidator('EmailAddress', TRUE) ->setRequired(true);

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Password:')->setRequired(true);

        $server = new Zend_Form_Element_Text('server');
        $server->setLabel('Server:')->setRequired(true);


        $submit = new Zend_Form_Element_Submit('Login');
        $submit->class = 'formsubmit';
        $submit->setValue('login');

      
        $servers=new Zend_View_Helper_ServerUrl();
        $base=new Zend_View_Helper_BaseUrl();
        $this->addElements(array($name, $password, $server, $submit));
        $this->setAction($servers->serverUrl().$base->baseUrl().'/index/auth');
        $this->setMethod('post');
    }


}

