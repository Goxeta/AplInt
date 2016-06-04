<?php

class IndexController extends Zend_Controller_Action {

     protected $_flashMessenger = null;
    public function init()
    {
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
    }

    public function indexAction() {
        Zend_Loader::loadClass('loginForm');

        $form = new loginForm();
        $this->view->form = $form;
       
        $url=Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
        //Zend_Debug::dump($url);
        $base=new Zend_View_Helper_BaseUrl();
        //Zend_Debug::dump($base->baseUrl().'/');
        $servers=new Zend_View_Helper_ServerUrl();
        $servers->serverUrl();
        if($url!=$base->baseUrl().'/')
        {
            $this->redirect($servers->serverUrl().$base->baseUrl().'/');
        }
        
    }

    public function emailAction() {

        $cos = Zend_Loader::loadClass('emailForm');
//        Zend_Debug::dump();
        $form = new emailForm();
        $this->view->form = $form;
        $this->view->messages = $this->_flashMessenger->getMessages();
    }

    public function sendAction() {
       
        Zend_Loader::loadClass('emailForm');
        $form = new emailForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                Zend_Loader::loadClass('Email');
                
                $email = new Email();
                $email->send($values);
                $this->_flashMessenger->addMessage('Email sent');
                
                
            }
            else {$this->_flashMessenger->addMessage('Email not sent');}
        } $this->redirect('../public/index/email');
    }

    public function authAction() {
        Zend_Loader::loadClass('loginForm');
        $form = new loginForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                Zend_Loader::loadClass('Email');
                $email = new Email();
                $email->set($values);
                if ($email->auth()) {
                    $session = new Zend_Session_Namespace('Email');
                    $session->email = $values;
                    $this->redirect($this->view->serverUrl() . $this->view->baseUrl().'/index/all');
                }
             
                //Zend_Debug::dump($cos);
            }
        }
        $this->redirect($this->view->serverUrl() . $this->view->baseUrl());
    }

    public function allAction() {
        Zend_Loader::loadClass('Email');
        $session = new Zend_Session_Namespace('Email');
        
 
        $values = $session->email;
//        Zend_Debug::dump($values);
        $email = new Email();
        $email->set($values);
        $request = $this->getRequest()->getParam('page');
        //Zend_Debug::dump($request);
        $paginator = $email->getPaginatedHeader($request, 10);
        $this->view->emails = $paginator;
    }

    public function messageAction() {
        // action body
        $request = $this->getRequest()->getParam('id');
        //Zend_Debug::dump($request);
         Zend_Loader::loadClass('Email');
         $session = new Zend_Session_Namespace('Email');
        $values = $session->email;
        $email = new Email();
        $email->set($values);
        $this->view->content=$email->message($request);
            
        }
    

    public function testAction() {
        try {
            $storage = new Zend_Mail_Storage_Imap(array('host' => "poczta.o2.pl",
                'user' => "altownia",
                'password' => "dasfas",
                'ssl' => 'SSL'));
        } catch (Exception $e) {
            Zend_Debug::dump($e->getMessage());
        }
    }
    
    public function logoutAction(){
        $session = new Zend_Session_Namespace('Email');
        $session->email=null;
        $this->redirect($this->view->serverUrl() . $this->view->baseUrl());
    }

}
