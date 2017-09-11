<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('company/index'));
}
$this->pageTitle=Yii::app()->name . ' - Company Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'company-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('contract','Company Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('company/index')));
		?>

        <?php if ($model->scenario!='view' && ($model->city == Yii::app()->user->city() || $model->scenario=='new')): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('company/save')));
            ?>
            <?php if ($model->scenario=='edit'): ?>
                <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                        'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
                );
                ?>
            <?php endif; ?>
        <?php endif ?>
	</div>
            <?php if ($model->scenario!='view' && $model->tacitly == 0): ?>
            <div class="btn-group pull-right">
                <?php echo TbHtml::button('<span class="fa fa-fire"></span> '.Yii::t('contract','Set Tacitly'), array(
                    'submit'=>Yii::app()->createUrl('company/tacitly',array("index"=>$model->id))));
                ?>
            </div>
            <?php endif ?>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'city'); ?>
			<?php echo $form->hiddenField($model, 'tacitly'); ?>

			<div class="form-group">
				<?php echo $form->labelEx($model,'name',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-5">
					<?php echo $form->textField($model, 'name',
						array('readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'head',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'head',$model->getUserList(),
                        array('disabled'=>($model->scenario=='view'))
                    ); ?>
                </div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'agent',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'agent',$model->getUserList(),
                        array('disabled'=>($model->scenario=='view'))
                    ); ?>
                </div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'address',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-8">
					<?php echo $form->textField($model, 'address',
						array('readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'phone',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php echo $form->textField($model, 'phone',
						array('readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'security_code',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php echo $form->textField($model, 'security_code',
						array('readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'organization_code',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php echo $form->textField($model, 'organization_code',
						array('readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>
			<div class="form-group">
                <?php echo $form->labelEx($model,'organization_time',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'organization_time',
                            array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),));
                        ?>
                    </div>
                </div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'license_code',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php echo $form->textField($model, 'license_code',
						array('readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>
			<div class="form-group">
                <?php echo $form->labelEx($model,'license_time',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'license_time',
                            array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),));
                        ?>
                    </div>
                </div>
			</div>

		</div>
	</div>
</section>

<?php
$this->renderPartial('//site/removedialog');
?>
<?php

if ($model->scenario!='view') {
    $js = Script::genDatePicker(array(
        'CompanyForm_organization_time',
        'CompanyForm_license_time',
    ));
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}
$js = Script::genDeleteData(Yii::app()->createUrl('company/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

