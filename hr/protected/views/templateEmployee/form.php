<?php
$this->pageTitle=Yii::app()->name . ' - TemplateEmployee Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'templateEmployee-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>
<style>
    select[readonly="readonly"]{pointer-events: none;}
</style>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('contract','Template Employee Form'); ?></strong>
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
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('templateEmployee/index')));
		?>

        <?php if ($model->scenario!='view'): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('TemplateEmployee/save')));
            ?>
        <?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'employee_id'); ?>
			<?php echo $form->hiddenField($model, 'city'); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'employee_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $model->getEmployeeListHtml(); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'tem_name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $model->getCityListHtml(); ?>
                </div>
            </div>

		</div>

	</div>
</section>
<?php

$js = "
var oldCity = '".$model->city."';
$('#changeEmployee').on('change',function(){
    var city = $(this).find('option:selected').data('city');
    if(oldCity != city){
        $('#changeCity').val('');
        oldCity = city;
    }
    $('#changeCity option').removeClass('hide');
    $('#changeCity option:not([data-city=\"'+city+'\"])').addClass('hide');
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

