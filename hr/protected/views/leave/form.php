<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('leave/index'));
}
$this->pageTitle=Yii::app()->name . ' - Leave Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'leave-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('fete','Ask leave Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('leave/index')));
		?>

        <?php if ($model->scenario!='view'): ?>
            <?php if ($model->scenario=='new'||$model->status == 0||$model->status == 3): ?>
                <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save'), array(
                    'submit'=>Yii::app()->createUrl('leave/save')));
                ?>
                <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','Audit'), array(
                    'submit'=>Yii::app()->createUrl('leave/audit')));
                ?>
            <?php endif ?>
            <?php if ($model->scenario=='edit'&&($model->status == 0||$model->status == 3)): ?>
                <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                        'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
                );
                ?>
            <?php endif; ?>
        <?php endif; ?>
	</div>
            <div class="btn-group pull-right" role="group">
                <?php
                $counter = ($model->no_of_attm['leave'] > 0) ? ' <span id="docleave" class="label label-info">'.$model->no_of_attm['leave'].'</span>' : ' <span id="docleave"></span>';
                echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('misc','Attachment').$counter, array(
                        'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploadleave',)
                );
                ?>
            </div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'status'); ?>

            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-2 text-danger">
                    半天请用0.5表示，未满半天按半天计算
                </div>
            </div>
            <?php
            $this->renderPartial('//site/leaveform',array('model'=>$model,
                'form'=>$form,
                'model'=>$model,
            ));
            ?>
		</div>
	</div>
</section>

<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'LEAVE',
    'header'=>Yii::t('misc','Attachment'),
    'ronly'=>($model->getInputBool()),
));
?>
<div id="fete_error" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal" type="button">×</button>
                <h4 class="modal-title">验证信息</h4></div><div class="modal-body"><p></p>
                <div class="errorSummary">
                    <p>请更正下列输入错误:</p>
                    <ul>
                    </ul>
                </div>
                <p></p>
            </div>
            <div class="modal-footer"><button data-dismiss="modal" class="btn btn-primary" name="yt4" type="button">确定</button></div>
        </div>
    </div>
</div>
<?php
$this->renderPartial('//site/removedialog');
?>
<?php
Script::genFileUpload($model,$form->id,'LEAVE');

$js = "
$('#start_time').datepicker({autoclose: true, format: 'yyyy/mm/dd',language: 'zh_cn'});

$('#start_time,#log_time').on('change',changeTime);
$('#log_time').on('keyup',changeTime);
function changeTime(){
    var startTime = $('#start_time').val();
    var logDay = $('#log_time').val();
    var thisType = $('#leave_type').val();
    if(startTime == ''||logDay == ''){
        $('#end_time').val('');
        return false;
    }
    if(logDay < 2){
        $('#end_time').val(startTime);
        return false;
    }
    $.ajax({
        type: 'post',
        url: '".Yii::app()->createUrl('leave/addDate')."',
        data: {startDate:startTime,day:logDay},
        dataType: 'json',
        success: function(data){
            if(data.status == 1){
                $('#end_time').val(data.lastDate);
            }else{
                $('#fete_error .errorSummary>ul').html('<li>'+data.message+'</li>');
                $('#fete_error').modal('show');
            }
        }
    });
}
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genDeleteData(Yii::app()->createUrl('leave/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

