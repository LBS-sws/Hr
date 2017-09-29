<?php
if (empty($model->id)&&$model->scenario != "new"){
    $this->redirect(Yii::app()->createUrl('holiday/index',array("type"=>$model->type,"only"=>$model->only)));
}
$this->pageTitle=Yii::app()->name . ' - Holiday Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'holiday-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo $model->getTypeName().Yii::t('contract',' Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('holiday/index',array("type"=>$model->type,"only"=>$model->only))));
		?>
        <?php if ($model->scenario == 'edit' && $model->only == 0): ?>
            <?php echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                'submit'=>Yii::app()->createUrl('holiday/new',array("type"=>$model->type))));
            ?>
        <?php endif ?>
        <?php if ($model->scenario == 'edit' && $model->only == 0 && $model->status == 2): ?>
            <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('contract','Finish'), array(
                'submit'=>Yii::app()->createUrl('holiday/finish')));
            ?>
        <?php endif ?>
        <?php if (!$model->getInputBool()): ?>
            <?php echo TbHtml::button('<span class="fa fa-file"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('holiday/save')));
            ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','Audit'), array(
                'submit'=>Yii::app()->createUrl('holiday/audit')));
            ?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                    'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
            );
            ?>
        <?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'type'); ?>
			<?php echo $form->hiddenField($model, 'only'); ?>
			<?php echo $form->hiddenField($model, 'employee_id'); ?>

            <?php if ($model->status == 3): ?>
                <div class="form-group has-error">
                    <?php echo $form->labelEx($model,'reject_remark',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-6">
                        <?php echo $form->textArea($model, 'reject_remark',
                            array('rows'=>3,'readonly'=>true)
                        ); ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'employee_name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'employee_name',
                        array('readonly'=>(true)));
                    ?>
                </div>
            </div>
			<div class="form-group">
                <label class="col-sm-2 control-label required"><?php echo $model->getTypeName().Yii::t("contract"," Cause");?><span class="required">*</span></label>
				<div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'holiday_id',$model->getHolidayAllList(),
                        array('disabled'=>($model->getInputBool()))
                    ); ?>
				</div>
			</div>
			<div class="form-group">
                <label class="col-sm-2 control-label required"><?php echo $model->getTypeName().Yii::t("contract","Time");?><span class="required">*</span></label>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'start_time',
                        array('readonly'=>($model->getInputBool())));
                    ?>
                </div>
                <div class="pull-left">
                    <p class="form-control-static">-</p>
                </div>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'end_time',
                        array('readonly'=>($model->getInputBool())));
                    ?>
                </div>
			</div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'remark',
                        array('rows'=>3,'readonly'=>($model->getInputBool()))
                    ); ?>
                </div>
            </div>
		</div>
	</div>
</section>

<?php
$this->renderPartial('//site/removedialog');
?>
<?php
$js = Script::genDeleteData(Yii::app()->createUrl('holiday/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

if ($model->scenario!='view') {
    $js = Script::genDatePicker(array(
        'HolidayForm_start_time',
        'HolidayForm_end_time',
    ));
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

