<?php
$this->pageTitle=Yii::app()->name . ' - ReviewHandle Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'reviewHandle-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>
<style>
    tbody>tr{position: relative;}
    select[readonly="readonly"]{pointer-events: none;}
    td.remark{position: absolute;min-width: 300px;}
</style>
<tr ></tr>
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
				'submit'=>Yii::app()->createUrl('reviewHandle/index')));
		?>

        <?php if (!$model->getReadonly()): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('app','review'), array(
                    'id'=>'btnConfirm','data-toggle'=>'modal','data-target'=>'#confirmDialog',)
            );
            ?>
            <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('contract','Draft'), array(
                'submit'=>Yii::app()->createUrl('reviewHandle/draft')));
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
            <?php if (!empty($model->employee_remark)): ?>
                <div class="form-group has-error">
                    <?php echo $form->labelEx($model,'employee_remark',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-4">
                        <?php echo $form->textArea($model, 'employee_remark',
                            array('readonly'=>(true),'rows'=>3)
                        ); ?>
                    </div>
                </div>
            <?php endif ?>
            <legend><?php echo Yii::t("contract","reviewAllot project");?></legend><!--考核项目-->
            <div class="form-group">
                <?php echo $form->labelEx($model,'handle_name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->textField($model, 'handle_name',
                        array('readonly'=>(true))
                    ); ?>
                </div>
                <?php echo $form->labelEx($model,'handle_per',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->textField($model, 'handle_per',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <?php
                echo $model->reviewHandleDiv();
            ?><legend><?php echo Yii::t("contract","reviewAllot comments");?></legend><!--考核評語-->
            <div class="form-group">
                <?php echo $form->labelEx($model,'review_remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textArea($model, 'review_remark',
                        array('readonly'=>($model->getReadonly()),'rows'=>3)
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'strengths',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textArea($model, 'strengths',
                        array('readonly'=>($model->getReadonly()),'rows'=>3)
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'target',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textArea($model, 'target',
                        array('readonly'=>($model->getReadonly()),'rows'=>3)
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'improve',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textArea($model, 'improve',
                        array('readonly'=>($model->getReadonly()),'rows'=>3)
                    ); ?>
                </div>
            </div>
		</div>

	</div>
</section>
<xmp id="xmpText">
    <td class="remark">
        <textarea rows="1" name=":name" class="form-control" placeholder="<?php echo Yii::t("contract","Scoring remark")?>"></textarea>
    </td>
</xmp>
<?php
$content = "<p>".Yii::t('contract','assessment score is confirmed and submitted, it cannot be modified after submission?')."</p>";
$this->widget('bootstrap.widgets.TbModal', array(
    'id'=>'confirmDialog',
    'header'=>Yii::t('contract','Confirm review'),
    'content'=>$content,
    'footer'=>array(
        TbHtml::button(Yii::t('dialog','OK'), array('id'=>'btnConfirmData','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
        TbHtml::button(Yii::t('dialog','Cancel'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
    ),
    'show'=>false,
));
?>
<?php

$link = Yii::app()->createUrl('reviewHandle/save');
$js = "
$('#btnConfirmData').on('click',function() {
	$('#removedialog').modal('hide');
	deletedata();
});

function deletedata() {
	var elm=$('#btnConfirm');
	jQuery.yii.submitForm(elm,'$link',{});
}

var xmpText = $('#xmpText').html();
$('#xmpText').remove();
    $('.changeSelect').change(function(){
        var num = $(this).val();
        var tr = $(this).parents('tr').eq(0);
        var name = tr.data('name')+'[remark]';
        var html = '';
        if(num!=6&&num!=7){
            html = xmpText.replace(/:name/g,name);
            tr.append(html);
        }else{
            tr.find('td.remark').remove();
        }
        console.log($(this).val());
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

