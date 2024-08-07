<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('tripResultSet/index'));
}
$this->pageTitle=Yii::app()->name . ' - TripResultSet Info';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'tripResultSet-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    .input-group .input-group-addon{background: #eee;}
</style>
<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','trip result set'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('tripResultSet/index')));
		?>
<?php if ($model->scenario!='view'): ?>
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('tripResultSet/save')));
			?>
<?php endif ?>
        <?php if ($model->scenario=='edit'): ?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                'submit'=>Yii::app()->createUrl('tripResultSet/delete')));
            ?>
        <?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'pro_name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'pro_name',
                        array('size'=>50,'maxlength'=>250,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'pro_num',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->numberField($model, 'pro_num',
                        array('max'=>100,'readonly'=>($model->scenario=='view'),'append'=>"%")
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'z_display',array('class'=>"col-sm-2 control-label")); ?>

                <div class="col-sm-4">
                    <?php echo $form->inlineRadioButtonList($model, 'z_display',array(0=>Yii::t("contract","none"),1=>Yii::t("contract","show")),
                        array('min'=>0,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'z_index',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->numberField($model, 'z_index',
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
		</div>
	</div>
</section>


<?php
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


