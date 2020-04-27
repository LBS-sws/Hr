<?php
$this->pageTitle=Yii::app()->name . ' - SalesReview';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'SalesReview-form',
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
		<strong><?php echo Yii::t('app','Sales Review Search')." - ".$model->getGroupListStr("group_name"); ?></strong>
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
				'submit'=>Yii::app()->createUrl('SalesReview/index')));
		?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'city'); ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'year',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo TbHtml::dropDownList("year",$model->year,ReviewAllotList::getYearList(),array("readonly"=>true)) ?>
                </div>
                <div class="col-sm-2">
                    <?php echo TbHtml::dropDownList("year_type",$model->year_type,ReviewAllotList::getYearTypeList(),array("readonly"=>true)) ?>
                </div>
            </div>
            <?php
            $tabs = $model->getTabList();
            $this->widget('bootstrap.widgets.TbTabs', array(
                'tabs'=>$tabs,
            ));
            ?>
		</div>
	</div>
</section>
<?php

$js = "
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

