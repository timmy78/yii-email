<?php
/**
 * @see https://github.com/advancedrei/BootstrapForEmail
 * @see http://htmlemailboilerplate.com/
 * @see http://templates.mailchimp.com/resources/inline-css/
 * 
 * 
 * @property EmailForm $model
 * @property EMailer $mailer
 */
class DefaultController extends Controller
{
    protected $model;
    protected $mailer;
    private $_pathView;
    
    public function init()
    {
        parent::init();
        
        $this->_pathView = 'application.modules.email.views.email';
        
        $this->breadcrumbs=array(
            Yii::t('email', "Module email"),
        );
        $this->setPageTitle(Yii::t('email', "Envoie"));
        
        $this->model = new EmailForm;
        
        $this->mailer = Yii::createComponent('application.modules.email.extensions.mailer.EMailer');
        $this->mailer->setPathViews($this->_pathView);
        
    }
    
    public function actionIndex()
    {
        $values = Yii::app()->request->getPost('EmailForm');
        if($values != null)
        {
            $this->model->setAttributes($values);
            
            //RepliesTo
            if(Yii::app()->request->getPost('addReplyTo')) {
                $this->model->repliesTo[] = new ReplyTo();
            }
            
            //Address
            if(Yii::app()->request->getPost('addAddress')) {
                $this->model->addresses[] = new Address();
            }
            
            if($this->model->validate() && $this->send())
            {
                Yii::app()->user->setFlash('success', Yii::t('email', "E-mail correctement envoyé"));
                $this->refresh();
            }
        }
        
        $this->render('index');
    }
    
    /*
     * Préparation du mail et envoie
     */
    private function send()
    {
        $success = false;
        
        //On set les valeurs du EMailer
        foreach($this->model->getAttributes() as $attribute => $value)
        {
            $attributeName = ucFirst($attribute);
            //if(property_exists(get_class($this->mailer), $attributeName)) {
                $this->mailer->$attributeName = $value;
            //}
        }
        
        //Le mailer
        $this->mailer->{$this->model->mailerType}();
        
        //On set les ReplyTo et les Address
        if(!empty($this->model->repliesTo))
        {
            foreach($this->model->repliesTo as $replyTo) {
                $this->mailer->AddReplyTo($replyTo->email, $replyTo->name);
            }
        }
        if(!empty($this->model->addresses))
        {
            foreach($this->model->addresses as $address) {
                $this->mailer->AddAddress($address->email, $address->name);
            }
        }
        
        $this->mailer->Body = $this->renderPartial($this->_pathView.'.email', array('model' => $this->model), true);
        
        //echo ($this->mailer->getView('email'));die;
        //print_r($this->mailer);die;
        
        try
        {
            $success = $this->mailer->Send();
        }
        catch(Exception $e)
        {
            throw new CHttpException(500, $e->getMessage());
        }
        
        return $success;
    }
}