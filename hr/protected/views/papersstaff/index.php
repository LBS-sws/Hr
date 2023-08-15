<?php
$this->pageTitle=Yii::app()->name . ' - Papersstaff';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'Employeesignature-type',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content">
    <div class="box"><div class="box-body">
            <div class="btn-group" role="group">
                <?php
                if (Yii::app()->user->validRWFunction('SS01'))
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('papersstaff','Add Papersstaff'), array(
                        'submit'=>Yii::app()->createUrl('papersstaff/new'),
                    ));
                ?>
            </div>
        </div></div>
    <?php $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('papersstaff','Papersstaff List'),
        'model'=>$model,
        'viewhdr'=>'//papersstaff/_listhdr',
        'viewdtl'=>'//papersstaff/_listdtl',
        'search'=>array(
            'city',
            'staffid',
            'staffname',
        ),
    ));
    ?>
</section>
<?php
echo $form->hiddenField($model,'pageNum');
echo $form->hiddenField($model,'totalRow');
echo $form->hiddenField($model,'orderField');
echo $form->hiddenField($model,'orderType');
?>
<?php $this->endWidget(); ?>


<form class="form-horizontal MultiFile-intercepted" action="" method="post">
    <!--    --><?php //$this->renderPartial('//materiallist/redeemApply',array(
    //        'submit'=> Yii::app()->createUrl('materiallist/apply'),
    //    ));
    //    ?>
</form>
<?php
$js = Script::genTableRowClick();
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

