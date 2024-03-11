<?php
$this->pageTitle=Yii::app()->name . ' - Report';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'report-form',
'action'=>Yii::app()->createUrl('report/generate'),
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('report','Staff Rpt List'); ?></strong>
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
		<?php echo TbHtml::button(Yii::t('misc','Submit'), array(
				'submit'=>Yii::app()->createUrl('report/staffRpt')));
		?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'name'); ?>
			<?php echo $form->hiddenField($model, 'fields'); ?>


            <?php if ($model->showField('city')): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'city',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->dropDownList($model, 'city', ReportY06Form::getCityList(),
                            array('disabled'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                </div>
            <?php else: ?>
                <?php echo $form->hiddenField($model, 'city'); ?>
            <?php endif ?>

			<div class="form-group">
				<?php echo $form->labelEx($model,'search_start',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
                    <?php echo $form->textField($model, 'search_start',
                        array('id'=>'search_start','prepend'=>'<span class="fa fa-calendar"></span>')
                    ); ?>
				</div>
			</div>
		
			<div class="form-group">
				<?php echo $form->labelEx($model,'search_end',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
                    <?php echo $form->textField($model, 'search_end',
                        array('id'=>'search_end','prepend'=>'<span class="fa fa-calendar"></span>')
                    ); ?>
				</div>
			</div>

		</div>
	</div>
</section>

<?php
$language = Yii::app()->language;

$js="
		$('#search_start,#search_end').datepicker({autoclose: true,language: '$language', format: 'yyyy/mm',maxViewMode:2,minViewMode:1});
	";
Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

