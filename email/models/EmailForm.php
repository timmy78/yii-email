<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Email
 *
 * @author Timothée
 * 
 * @property ReplyTo[] $repliesTo
 * @property Address[] $addresses
 */
class EmailForm extends CFormModel
{
    
    const PRIORITY_LOW = 5;
    const PRIORITY_NORMAL = 3;
    const PRIORITY_HIGH = 1;
    
    const CONTENT_TYPE_HTML = 'text/html';
    const CONTENT_TYPE_PLAIN = 'text/plain';
    
    const MAILER_TYPE_SMTP = 'IsSMTP';
    const MAILER_TYPE_MAIL = 'IsMail';
    const MAILER_TYPE_SEND_MAIL = 'IsSendmail';
    const MAILER_TYPE_Q_MAIL = 'IsQmail';
    
    public $priority = self::PRIORITY_NORMAL;
    public $charSet = 'UTF-8';//iso-8859-1
    public $contentType = self::CONTENT_TYPE_HTML;
    public $mailerType = self::MAILER_TYPE_SMTP;
    public $host = 'smtp.free.fr';
    public $port = 25;
    public $from = 'support@streammind.com';
    public $fromName;
    public $sender;
    public $repliesTo = array();
    public $addresses = array();
    public $subject;
    public $body;
    
    public function init()
    {
        parent::init();
        
        $this->fromName = Yii::app()->name;
        
        //On set au début
        if(!Yii::app()->request->getPost('EmailForm'))
        {
            $replyTo = new ReplyTo();
            $replyTo->email = $this->from;
            $replyTo->name = $this->fromName;
            $this->repliesTo[] = $replyTo;
            
            $address = new Address();
            $address->email = 'destinataire@gmail.com';
            $this->addresses[] = $address;
        }
    }
    
    public function rules()
    {
        return array(
            array('priority, charSet, contentType, mailerType, host, port, from, fromName, sender, subject, body', 'safe'),
            array('from, fromName, subject', 'required'),
            
            array('from, sender', 'email')
        );
    }
    
    public function attributeLabels()
    {
        return array(
            
        );
    }
    
    /**
     * On surcharge pour setter les ReplyTo et les Address
     * @param array $values
     * @param boolean $safeOnly
     */
    public function setAttributes($values, $safeOnly = true)
    {
        parent::setAttributes($values, $safeOnly);
        
        //On set les ReplyTo
        $replies = Yii::app()->request->getPost('ReplyTo');
        if($replies != null)
        {
            foreach($replies as $replyAttributes)
            {
                $reply = new ReplyTo();
                $reply->setAttributes($replyAttributes);
                $this->repliesTo[] = $reply;
            }
        }
        
        //On set les Address
        $addresses = Yii::app()->request->getPost('Address');
        if($addresses != null)
        {
            foreach($addresses as $addressAttributes)
            {
                $address = new Address();
                $address->setAttributes($addressAttributes);
                $this->addresses[] = $address;
            }
        }
    }
    
    /**
     * On surcharge pour valider les ReplyTo et les Address
     * @param array $attributes
     * @param boolean $clearErrors
     * @return boolean
     */
    public function validate($attributes = null, $clearErrors = true)
    {
        $validate = parent::validate($attributes, $clearErrors);
        
        //On valide les ReplyTo
        $repliesValid = true;
        if(!empty($this->repliesTo))
        {
            foreach($this->repliesTo as $replyTo) {
                if(!$replyTo->validate()) {
                    $repliesValid = false;
                }
            }
        }
        
        //On valide les Address
        $addressesValid = true;
        if(!empty($this->addresses))
        {
            foreach($this->addresses as $address) {
                if(!$address->validate()) {
                    $addressesValid = false;
                }
            }
        }
        
        if($validate && $repliesValid && $addressesValid) {
            return true;
        }
        
        return false;
    }
    
    public static function getPriorities()
    {
        return array(
            self::PRIORITY_LOW => Yii::t('email', "Bas"),
            self::PRIORITY_NORMAL => Yii::t('email', 'Normal'),
            self::PRIORITY_HIGH => Yii::t('email', 'Haut')
        );
    }
    
    public function getPriority()
    {
        $priorities = self::getPriorities();
        
        return array_key_exists($this->priority, $priorities) ? $priorities[$this->priority] : $priorities[self::PRIORITY_NORMAL];
    }
    
    public static function getContents()
    {
        return array(
            self::CONTENT_TYPE_PLAIN => self::CONTENT_TYPE_PLAIN,
            self::CONTENT_TYPE_HTML => self::CONTENT_TYPE_HTML
        );
    }
    
    public static function getMailers()
    {
        return array(
            self::MAILER_TYPE_MAIL => 'Mail',
            self::MAILER_TYPE_SMTP => 'SMTP',
            self::MAILER_TYPE_SEND_MAIL => 'sendMail',
            self::MAILER_TYPE_Q_MAIL => 'QMail'
        );
    }
    
    public function getMailer()
    {
        $mailers = self::getMailers();
        
        return array_key_exists($this->mailerType, $mailers) ? $mailers[$this->mailerType] : $mailers[self::MAILER_TYPE_MAIL];
    }
}


class ReplyTo extends CFormModel
{
    public $email = '';
    public $name = '';
    
    public function rules()
    {
        return array(
            array('email, name', 'safe'),
            array('email', 'required'),
            array('email', 'email'),
            array('name', 'length', 'min' => 0, 'max' => 100)
        );
    }
    
    public function attributeLabels()
    {
        return array(
            'email' => Yii::t('email', "Email").' :',
            'name' => Yii::t('email', "Name").' :',
        );
    }
}

class Address extends ReplyTo
{
    
}