<?php
$this->pageTitle=Yii::app()->name . ' - ReviewSearch Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'ReviewSearch-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>
<style>
    tbody>tr{position: relative;}
    select[readonly="readonly"]{pointer-events: none;}
    td.remark{;min-width: 300px;}
</style>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('contract','reviewHandle Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('ReviewSearch/index')));
		?>

        <?php if ($model->status_type != 3 && $model->login_id == $model->employee_id): ?>
            <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('ReviewSearch/save')));
            ?>
        <?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'city'); ?>
			<?php echo $form->hiddenField($model, 'year_type'); ?>

            <?php
            $this->renderPartial('//site/reviewStaff',array(
                'form'=>$form,
                'model'=>$model,
            ));
            ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'name_list',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'name_list',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <?php if ($model->status_type != 3 && $model->login_id == $model->employee_id): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'employee_remark',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-4">
                        <?php echo $form->textArea($model, 'employee_remark',
                            array('readonly'=>(false),'rows'=>3)
                        ); ?>
                    </div>
                </div>
            <?php endif ?>
            <legend><?php echo Yii::t("contract","reviewAllot project");?></legend><!--考核项目-->
            <?php
            $tabs = $model->getTabList();
            $this->widget('bootstrap.widgets.TbTabs', array(
                'tabs'=>$tabs,
            ));
            ?>
            <legend><?php echo Yii::t("contract","reviewAllot comments");?></legend><!--考核評語-->
            <?php if (!($model->status_type != 3 && $model->login_id == $model->employee_id)): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'employee_remark',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-4">
                        <?php echo $form->textArea($model, 'employee_remark',
                            array('readonly'=>(true),'rows'=>3)
                        ); ?>
                    </div>
                </div>
            <?php endif ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'review_remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textArea($model, 'review_remark',
                        array('readonly'=>(true),'rows'=>3)
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'strengths',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textArea($model, 'strengths',
                        array('readonly'=>(true),'rows'=>3)
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'target',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textArea($model, 'target',
                        array('readonly'=>(true),'rows'=>3)
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'improve',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textArea($model, 'improve',
                        array('readonly'=>(true),'rows'=>3)
                    ); ?>
                </div>
            </div>
		</div>

	</div>
</section>
<?php


$js = "
$(function(){
    $('td.remark').each(function(){
        if(!$(this).parents('.tab-pane.fade').hasClass('active')){
            $(this).parents('.tab-pane.fade').addClass('active in');
        }
        var height = $(this).outerHeight();
        $(this).parent('tr').height(height);
        $(this).css({
            'position':'absolute',
            'height':height+'px'
        });
        $(this).parents('.tab-pane.fade').removeClass('active in');
    });
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

