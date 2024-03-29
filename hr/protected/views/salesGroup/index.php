<?php

$this->pageTitle=Yii::app()->name . ' - salesGroup';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'salesGroup-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','Sales Group'); ?></strong>
    </h1>
    <!--
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Layout</a></li>
            <li class="active">Top Navigation</li>
        </ol>
    -->
</section>

<section class="content">
    <div class="box"><div class="box-body">
            <div class="btn-group" role="group">
                <?php
                //var_dump(Yii::app()->session['rw_func']);
                if (Yii::app()->user->validFunction('ZR14'))
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('contract','Add Sales Group'), array(
                        'submit'=>Yii::app()->createUrl('salesGroup/new'),
                    ));
                ?>
            </div>
        </div>
    </div>
    <?php
    $search = array(
        'group_name',
    );
    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('contract','Sales Group List'),
        'model'=>$model,
        'viewhdr'=>'//salesGroup/_listhdr',
        'viewdtl'=>'//salesGroup/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search'=>$search,
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

<?php
$js = Script::genTableRowClick();
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

