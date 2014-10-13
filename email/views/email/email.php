<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?php echo CHtml::encode($model->subject) ?></title>
    <style media="all" type="text/css">
       
    </style>
</head>
<body>
    <table cellspacing="0" cellpadding="0" border="0" width="650px">
        <tr>
            <td align="center">
                
            </td>
        </tr>
        <tr>
            <td bgcolor="#FFFFFF" align="center">
                <table width="650px" cellspacing="0" cellpadding="3" class="container">
                    <tr>
                        <td>
                            <hr/>
                            <?php echo nl2br($model->body); ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#FFFFFF" align="center">
                <table width="650px" cellspacing="0" cellpadding="3" class="container">
                    <tr>
                        <td>
                            <hr>
                            <p class="text-center">
                                Copyright &copy; <?php echo date('Y'); ?> StreamMind.  
                                <?php
                                    echo Yii::t('email','Tous droits réservés.');
                                    echo Yii::app()->params['version'].'.';
                                ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>