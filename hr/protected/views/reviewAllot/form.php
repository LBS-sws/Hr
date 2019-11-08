<?php
$this->pageTitle=Yii::app()->name . ' - ReviewAllot Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'reviewAllot-form',
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
		<strong><?php echo Yii::t('contract','reviewAllot Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('reviewAllot/index')));
		?>

        <?php if (!$model->getReadonly()): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('app','review'), array(
                'submit'=>Yii::app()->createUrl('reviewAllot/save')));
            ?>
            <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('contract','Draft'), array(
                'submit'=>Yii::app()->createUrl('reviewAllot/draft')));
            ?>
        <?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'employee_id'); ?>
			<?php echo $form->hiddenField($model, 'city'); ?>
			<?php echo $form->hiddenField($model, 'year_type'); ?>

            <?php
            $this->renderPartial('//site/reviewStaff',array(
                'form'=>$form,
                'model'=>$model,
            ));
            ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'review_type',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo TbHtml::textField("review_type",DeptForm::getReviewType($model->review_type),array("readonly"=>true)) ?>
                </div>
            </div>

            <?php
                echo $model->returnChangeReviewType();
            ?>

            <legend><?php echo Yii::t("contract","reviewAllot manager");?></legend><!--考核经理-->
            <div class="form-group">
                <div class="col-sm-5 col-sm-offset-2">
                    <?php
                    $ReviewAllotForm = new ReviewAllotForm();
                    echo $ReviewAllotForm->returnManager($model);
                    ?>
                </div>
            </div>
            <legend><?php echo Yii::t("contract","reviewAllot project");?></legend><!--考核项目-->
            <?php if ($model->scenario!='view'): ?>
                <?php echo TbHtml::button(Yii::t('contract','review template'), array(
                    'class'=>"pull-right btn btn-default",'id'=>'btnTemplate'));
                ?>
            <?php endif ?>
            <?php
            echo TemplateForm::parentTemStrDiv($model);
            ?>
		</div>

	</div>
</section>
<?php
$list = TbHtml::listBox('lsttemplate', '', array(), array(
        'size'=>'15')
);

$content = "
<div class=\"row\">
	<div class=\"col-sm-11\" id=\"lookup-list\">
			$list
	</div>
</div>
";

$this->widget('bootstrap.widgets.TbModal', array(
    'id'=>'applytempdialog',
    'header'=>Yii::t('contract','review template'),
    'content'=>$content,
    'footer'=>array(
        TbHtml::button(Yii::t('dialog','OK'), array('id'=>'btnApply','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
        TbHtml::button(Yii::t('dialog','Cancel'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
    ),
    'show'=>false,
));
?>

<xmp hidden id="readyOne">
    <?php echo $model->getRowOnly($model,":num",$model->getReviewManagerList($model->city),false,array("employee_id"=>"","num"=>""));?>
</xmp>
<?php

$mesg = Yii::t('dialog','No Record Found');
$link = Yii::app()->createAbsoluteUrl("lookup");

$js = "
$('#btnTemplate').on('click',function(){
	var link = '$link/template';
	$('#applytempdialog').modal('show');
	$.ajax({
		type: 'GET',
		url: link,
		data: [],
		dataType: 'json',
		success: function(data) {
			$('#lsttemplate').empty();
			$.each(data, function(index, element) {
				$('#lsttemplate').append('<option value=\"'+element.id+'\">'+element.name+'</option>');
			});
			
			var count = $('#lsttemplate').children().length;
			if (count<=0) $('#lsttemplate').append('<option value=\"-1\">$mesg</option>');
		},
		error: function(data) { // if error occured
			alert('Error occured.please try again');
		}
	});
});

$('#btnApply').on('click',function(){
	var tid = $('#lsttemplate').val();
	var data = 'id='+tid;
	var link = '$link/applytemplate';
	$.ajax({
		type: 'GET',
		url: link,
		data: data,
		dataType: 'json',
		success: function(data) {
		    $(\"select[id^='ReviewAllotForm_tem']\").val('');
			$.each(data, function(index, element) {
				var fldid = 'ReviewAllotForm_tem_'+element;
				$('#'+fldid).val('on');
			});
		},
		error: function(data) { // if error occured
			alert('Error occured.please try again');
		}
	});
});
	";
Yii::app()->clientScript->registerScript('lookupTemplate',$js,CClientScript::POS_READY);

$js = "
    var rowHtml = $('#readyOne').html();
    $('#readyOne').remove();
    $('#addManager').on('click',function(){
        var num = $('#managerTable>tbody').data('num');
        num = parseInt(num,10);
        num++;
        $('#managerTable>tbody').data('num',num)
        var newHtml = rowHtml.replace(/:num/g,num);
        $('#managerTable>tbody').append(newHtml);
    });
    
    $('#managerTable').delegate('.delManager','click',function(){
        $(this).parents('tr').remove();
    });

    $('#managerTable').delegate('.changeNum','keyup',function(){
        var value = $(this).val();
        $(this).addClass('noneChange');
        var num = $('.changeNum').not('.noneChange').length;
        var sum = ".$model->count_num.";
        if(value<sum&&num!=0){
            $('.changeNum.noneChange').each(function(){
                sum-=$(this).val();
            });
            var newNum = Math.floor(sum/num);
            var proNum = sum%num;
            $('.changeNum').not('.noneChange').val(newNum);
            if(proNum!=0){
                $('.changeNum').not('.noneChange').last().val(newNum+proNum);
            }
        }
    });
    
    $('#changeTwo').keyup(function(){
        var value = $(this).val();
        var change = $(this).data('change');
        if(value!=''){
            if(change == 'three'){
                value *=10;
                value = parseFloat(value).toFixed(2);
            }else{
                value =15-(value*0.5);
                value = value<0?0:value;
            }
            $('#change_value').val(value);
        }
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

