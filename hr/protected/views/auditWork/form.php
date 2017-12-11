<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('auditWork/index'));
}
$this->pageTitle=Yii::app()->name . ' - Work Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'work-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('fete','Overtime work Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('auditWork/index',array("only"=>$model->only))));
		?>

        <?php if ($model->scenario!='view'&&$model->status!='3'): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','Audit'), array(
                'submit'=>Yii::app()->createUrl('auditWork/audit')));
            ?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('contract','Rejected'), array(
                'name'=>'btn88','id'=>'btn88','data-toggle'=>'modal','data-target'=>'#jectdialog'));
            ?>
        <?php endif; ?>
	</div>
            <div class="btn-group pull-right" role="group">
                <?php echo TbHtml::button('<span class="fa fa-file-text-o"></span> '.Yii::t('fete','Overtime record this month'), array(
                    'name'=>'btn99','id'=>'btn99','data-toggle'=>'modal','data-target'=>'#workList'));
                ?>
            </div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'status'); ?>
			<?php echo $form->hiddenField($model, 'only'); ?>

            <?php
            $this->renderPartial('//site/workform',array('model'=>$model,
                'form'=>$form,
                'model'=>$model,
            ));
            ?>
            <legend>&nbsp;</legend>
            <div class="form-group text-danger">
                <label class="col-sm-2 control-label">
                    加班工资计算公式
                </label>
                <div class="form-control-static col-sm-10">
                    1、非法定节假日加班费= 员工合同约定月工资÷(21.76×8)×150%×加班小时数<br>
                    2、法定节假日加班费= 员工合同约定月工资÷ 21.76×工资倍率×加班天数
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'wage',array('class'=>"col-sm-2 control-label")); ?>
                <div class="form-control-static col-sm-10">
                    <?php echo $model->wage;?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'work_cost',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'work_cost',
                        array('readonly'=>(true)));
                    ?>
                </div>
            </div>
            <?php
            $this->renderPartial('//site/ject',array(
                'form'=>$form,
                'model'=>$model,
                'rejectName'=>"reject_cause",
                'submit'=>Yii::app()->createUrl('auditWork/reject'),
            ));
            ?>
		</div>
	</div>
</section>
<?php
$this->renderPartial('//site/workList',array(
    'model'=>$model,
    'tableName'=>Yii::t("fete","Overtime work List"),
));
?>
<?php
$this->renderPartial('//site/removedialog');
?>
<?php

$js = "
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genDeleteData(Yii::app()->createUrl('work/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

