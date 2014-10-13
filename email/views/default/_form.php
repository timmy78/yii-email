<?php
/**
 * @property EmailForm $model
 * @property CActiveForm $form
 */
$class = get_class($model);

if(empty($attributes)) {
    $attributes = $model->getSafeAttributeNames();
}

?>
    
<div class="form row" id="<?php echo $class; ?>">
    <div class="col-sm-8 col-centered">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
                'id' => 'form-'.$class,
                'enableClientValidation'=>true,
                'enableAjaxValidation'=>false,
                'htmlOptions' => array(
                    'class' => 'form-horizontal',
                    'role' => 'form',
                    //'enctype' => 'multipart/form-data'
                ),
                'clientOptions' => array(
                    'errorCssClass' => 'has-error',
                    'successCssClass' => 'has-success'
                ),
                'errorMessageCssClass' => 'help-block'
        ));

        $columnsLabel = 3;
        $columnsInput = 9;

        $i=0;
        foreach($attributes as $attribute)
        {
            $htmlOptions = array('class' => 'form-control');
            $title = str_replace(' :','',$model->getAttributeLabel($attribute));
            $dropDownList = false;

            /*
             * Types
             */
            if(in_array($attribute, array('sndbic', 'rcvbic'))) {
                $htmlOptions['style'] = 'text-transform:uppercase;';
            }
            else if($attribute == 'priority') {
                $dropDownList = EmailForm::getPriorities();
            }
            else if($attribute == 'contentType') {
                $dropDownList = EmailForm::getContents();
            }
            else if($attribute == 'mailerType') {
                $dropDownList = EmailForm::getMailers();
            }
            else if(in_array($attribute, array('status'))) {
                $dropDownList = $model::getStatutes();
            }

            //Afficher période activité
            if($attribute == 'datevaliditystart')
            {
                if(!empty($model->datevaliditystart) || !empty($model->datevalidityend))
                    $checked = true;
                else if(Yii::app()->request->getPost('periode') != null)
                    $checked = true;
                else
                    $checked = false;
                echo '<div class="champ form-group">'
                        .CHtml::label(Yii::t('email', 'Activer la période de validité').' :', false, array( 'class' => 'col-sm-'.$columnsLabel.' control-label' ))
                        .'<div class="col-sm-'.$columnsInput.'">'
                            .CHtml::checkBox('periode', $checked, array('class'=>'checkbox'))
                        .'</div>'
                    .'</div>';
            }

            echo '<div class="champ form-group" id="'.$attribute.'">';
                echo $form->labelEx($model, $attribute, array( 'class' => 'col-sm-'.$columnsLabel.' control-label' ));
                echo '<div class="col-sm-'.$columnsInput.'">';

                    if($attribute == 'activated') {
                        echo $form->radioButtonList(
                                $model,
                                $attribute,
                                array( User::ACTIVATED => Yii::t('email', 'Activé'), User::DESACTIVATED => Yii::t('email', 'Désactivé') ),
                                array(
                                    'separator' => '<span style="margin-left:20px;"></span>'
                                )
                            );
                    }
                    else if($attribute == 'file') {
                        echo $form->fileField($model, $attribute);
                    }
                    else if(in_array($attribute, array('acqsta'))) {
                        $htmlOptions['class'] = 'checkbox';
                        echo $form->checkBox($model, $attribute, $htmlOptions);
                    }
                    else if($attribute == 'admin') {
                        $data = array(User::IS_ADMIN => Yii::t('email', 'Oui'), 0 => Yii::t('email', 'Non'));
                        echo $form->radioButtonList($model, $attribute, $data, array('separator' => '&nbsp;'));
                    }
                    else if($dropDownList !== false) {
                        echo $form->dropDownList($model, $attribute, $dropDownList, $htmlOptions);
                        if($attribute == 'scenarios')
                            echo '<div id="scenariosButtons"></div>';
                    }
                    else if(in_array($attribute, array('email', 'from'))) {
                        echo $form->emailField($model, $attribute, $htmlOptions);
                    }
                    else if(in_array($attribute, array('password', 'passwordConf'))) {
                        echo $form->passwordField($model, $attribute, $htmlOptions);
                    }
                    else if(in_array($attribute, array('body'))) {
                        echo $form->textArea($model, $attribute, $htmlOptions);
                    }
                    else {
                        echo $form->textField($model, $attribute, $htmlOptions);
                    }
                    echo $form->error($model, $attribute);
                echo '</div>';

            echo '</div>';

            $i++;
        }

        /*
         * REPLIES
         */
        if(!empty($model->repliesTo))
        {
            foreach($model->repliesTo as $i => $replyTo)
            {
                $attributes = $replyTo->getSafeAttributeNames();
                echo '<hr/>'
                    .'<div class="receiver panel panel-default"><div class="panel-heading">'
                        .'<h4 class="panel-title">'.Yii::t('email', 'Reply to').' ('.($i+1).')</h4>'
                    .'</div><div class="panel-body">';

                foreach($attributes as $attribute)
                {
                    $function = $attribute == 'email' ? 'emailField' : 'textField';
                    echo '<div class="champ form-group '.$attribute.'">';
                        echo $form->labelEx($replyTo, $attribute, array( 'class' => 'col-sm-3 control-label' ));
                        echo '<div class="col-sm-9">';
                            echo $form->$function($replyTo, '['.$i.']'.$attribute, $htmlOptions);
                            echo $form->error($replyTo, '['.$i.']'.$attribute, array('class' => 'help-block'));
                        echo '</div>';
                    echo '</div>';
                }
                
                echo '</div></div>';
            }
        }

        /*
         *  ADRESSES
         */
        if(!empty($model->addresses))
        {
            foreach($model->addresses as $i => $address)
            {
                $attributes = $address->getSafeAttributeNames();
                echo '<hr/>'
                    .'<div class="receiver panel panel-default"><div class="panel-heading">'
                        .'<h4 class="panel-title">'.Yii::t('email', "Address").' ('.($i+1).')</h4>'
                    .'</div><div class="panel-body">';

                foreach($attributes as $attribute)
                {
                    $function = $attribute == 'email' ? 'emailField' : 'textField';
                    echo '<div class="champ form-group '.$attribute.'">';
                        echo $form->labelEx($address, $attribute, array( 'class' => 'col-sm-3 control-label' ));
                        echo '<div class="col-sm-9">';
                            echo $form->$function($address, '['.$i.']'.$attribute, $htmlOptions);
                            echo $form->error($address, '['.$i.']'.$attribute, array('class' => 'help-block'));
                        echo '</div>';
                    echo '</div>';
                }

                echo '</div></div>';
            }
        }

        ?>

        <div class="text-right" style="margin-bottom:50px;">
            <?php
                echo CHtml::htmlButton(
                    '<span class="fa fa-plus"></span> '.Yii::t('email', "Add replyTo"),
                    array(
                        'type' => 'submit',
                        'class' => 'btn btn-success btn-sm',
                        'name' => 'addReplyTo',
                        'value' => 1
                    )
                )."\n";
                
                echo CHtml::htmlButton(
                    '<span class="fa fa-plus"></span> '.Yii::t('email', "Add address"),
                    array(
                        'type' => 'submit',
                        'class' => 'btn btn-success btn-sm',
                        'name' => 'addAddress',
                        'value' => 1
                    )
                );
            ?>
        </div>

        <div class="form-group text-right">
            <div class="col-xs-12">
                <?php
                    echo CHtml::htmlButton('<span class="fa fa-undo"></span> '.Yii::t('email', 'Reset'), array(
                        'type' => 'reset',
                        'class' => 'btn btn-primary'
                    ))."\n"; 

                    $text = '<span class="fa fa-check"></span> '.Yii::t('email', 'Valider');

                    //Validate
                    echo CHtml::htmlButton(
                        $text,
                        array(
                            'type' => 'submit',
                            'class' => 'btn btn-primary validate'
                        )
                    );
                ?>
            </div>
        </div>

        <?php  
            $this->endWidget();
        ?>
    </div>
    
    <script type="text/javascript">
        
        $(document).ready(function(){
            
        });
    </script>
</div>