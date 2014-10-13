<?php

class EmailModule extends CWebModule
{
    private $_assetsUrl;
    
    public function init()
    {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application

        // import the module-level models and components
        $this->setImport(array(
            'email.models.*',
            'email.components.*',
        ));
        
        $this->_assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.email.assets'));
    }

    public function beforeControllerAction($controller, $action)
    {
        set_error_handler(function($errno, $errstr, $errfile, $errline) {
                $catchError = new CatchError();
                if(count($errstr) > 0 && $catchError->controlError($errstr)){
                        throw new Exception($errstr, 0);
                }
        });
        
        if(parent::beforeControllerAction($controller, $action))
        {
            // this method is called before any module controller action is performed
            // you may place customized code here
            
            Yii::app()->getClientScript()->registerCssFile($this->getAssetsUrl().'/css/bootstrap.min.css');
            Yii::app()->getClientScript()->registerCssFile($this->getAssetsUrl().'/css/font-awesome.css');
            //Yii::app()->getClientScript()->registerScriptFile(CClientScript::POS_HEAD, $this->getAssetsUrl().'/js/bootstrap.min.js');
            
            return true;
        }
        else
            return false;
    }
    
    public function getAssetsUrl()
    {
        return $this->_assetsUrl;
    }
}
