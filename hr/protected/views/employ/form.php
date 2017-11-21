<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('employ/index'));
}
$this->pageTitle=Yii::app()->name . ' - Employ Form';
?>

<style>
    input[readonly]{pointer-events: none;}
    select[readonly]{pointer-events: none;}
</style>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'employ-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('contract','Employ Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('employ/index')));
		?>
        <?php
            if($model->scenario!='view'){
                if($model->staff_status == 1 || $model->staff_status == 3){
                    echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','Audit'), array(
                        'submit'=>Yii::app()->createUrl('employ/audit')));
                }
                if(($model->city == Yii::app()->user->city() || $model->scenario=='new')&&$model->staff_status == 1){
                    echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save'), array(
                        'submit'=>Yii::app()->createUrl('employ/save')));
                }
                if(($model->city == Yii::app()->user->city() || $model->scenario=='edit')&&$model->staff_status == 1){
                    echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                            'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
                    );
                }
                if($model->staff_status == 4){
                    echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('contract','Finish'), array(
                        'submit'=>Yii::app()->createUrl('employ/finish')));
                }
            }
        ?>
	</div>

	<div class="btn-group pull-right" role="group">
        <?php if ($model->scenario!='new'): ?>
    <?php if ($model->staff_status == 4): ?>
            <?php echo TbHtml::button('<span class="fa fa-file-word-o"></span> '.Yii::t('contract','Staff Contract'),array(
                'submit'=>Yii::app()->createUrl('employee/Downfile?index='.$model->id)));
            ?>
    <?php endif; ?>
    <?php endif; ?>
    <?php
        echo $form->hiddenField($model, 'attachment',array("class"=>"changeAttachment"));
    $counter = $model->setAttachment();
    $counter = (count($counter) > 0) ? ' <span id="docpayreq" class="label label-info">'.count($counter).'</span>' : ' <span id="docpayreq"></span>';
    echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('misc','Attachment').$counter, array(
            'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploadpayreq',)
    );
    ?>
	</div>
	</div></div>

	<div class="box box-info">
        <div class="box-body" style="position: relative">
            <?php if (!empty($model->image_user)): ?>
                <img src="<?php echo $model->image_user;?>" width="150px" style="position: absolute;right: 5px;top: 5px;z-index: 2;">
            <?php endif; ?>

            <?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'city'); ?>
			<?php echo $form->hiddenField($model, 'staff_status'); ?>

            <?php if ($model->staff_status == 3): ?>
                <div class="form-group has-error">
                    <?php echo $form->labelEx($model,'ject_remark',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-6">
                        <?php echo $form->textArea($model, 'ject_remark',
                            array('readonly'=>true)
                        ); ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($model->staff_status == 4): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'ld_card',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-5">
                        <?php echo $form->textField($model, 'ld_card',
                            array('readonly'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'sb_card',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-5">
                        <?php echo $form->textField($model, 'sb_card',
                            array('readonly'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'jj_card',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-5">
                        <?php echo $form->textField($model, 'jj_card',
                            array('readonly'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php
            $this->renderPartial('//site/employform',array('model'=>$model,
                'form'=>$form,
                'model'=>$model,
                'readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)),
            ));
            ?>

            <legend></legend>
            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'remark',
                        array('rows'=>3,'readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
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
/*if ($model->scenario!='new')
    $this->renderPartial('//site/flowword',array('model'=>$model));*/

$js = "
var staffStatus = '".$model->staff_status."';
$('#EmployForm_test_type').on('change',function(){
    if($(this).val() == 1){
        $(this).parents('.form-group').next('div.test-div').slideDown(100);
    }else{
        $(this).parents('.form-group').next('div.test-div').slideUp(100);
    }
}).trigger('change');
    $('.file-update').upload({uploadUrl:'".Yii::app()->createUrl('employ/uploadImg')."'});
    
    $('body').delegate('.fileImgShow a','click',function(){
        $(this).parents('.fileImgShow').parents('.form-group:first').find('input[type=\"file\"]').show();
        $(this).parents('.fileImgShow').remove();
    });
    $('.fileImgShow').each(function(){
        var url = $(this).find('img:first').attr('src');
        $(this).parent('div').children('input[type=\"hidden\"]').val(url);
        $(this).parent('div').children('input[type=\"file\"]').removeClass('hide').hide();
    });
    
    //時間計算
    $('.test_add_time').on('change',function(){
        $.ajax({
            type: 'post',
            url: '".Yii::app()->createUrl('employ/addDate')."',
            data: {dateTime:$('.test_add_time').eq(1).val(),month:$('.test_add_time').eq(0).val()},
            dataType: 'json',
            success: function(data){
                $('.test_sum_time').val(data);
            }
        });
    }).trigger('change');
    //部門變化
    if($('.depart').length == 2){
        DEPARTLIST = new Array();
        $('.depart:last>option').each(function(){
            var key = $(this).data('type');
            var dept_class = $(this).data('dept');
            var text = $(this).text();
            var value = $(this).attr('value');
            if(typeof DEPARTLIST[key] == 'undefined'){
                DEPARTLIST[key] = new Array();
            }
            DEPARTLIST[key].push({'text':text,'value':value,'dept_class':dept_class});
        });
        $('.depart:first').on('change',function(){
            var key = $(this).val();
            var oldValue = $('.depart:last').val();
            $('.depart:last').html('');
            for (var x in DEPARTLIST){
                if(x == key){
                    for(var i= 0;i<DEPARTLIST[key].length;i++){
                        var html = '';
                        if(oldValue == DEPARTLIST[x][i]['value']){
                            html = '<option value=\"'+DEPARTLIST[x][i]['value']+'\" data-dept=\"'+DEPARTLIST[x][i]['dept_class']+'\" selected>'+DEPARTLIST[x][i]['text']+'</option>';
                        }else{
                            html = '<option value=\"'+DEPARTLIST[x][i]['value']+'\" data-dept=\"'+DEPARTLIST[x][i]['dept_class']+'\">'+DEPARTLIST[x][i]['text']+'</option>';
                        }
                        $('.depart:last').append(html);
                    }
                }
            }
            $('.depart:last').trigger('change');
        }).trigger('change');
    }
    
    //附件上傳
    $('#importUp').on('click',function(){
        $('#UploadFileForm').ajaxSubmit({
            'type':'POST',
            'dataType':'JSON',
            'success':function(data){
                if(data.status == 1){
                    var file = data.data;
                    var html = '<tr>';
                    html+='<td>';
                    html+=file['down_url'];
                    html+='&nbsp;&nbsp;';
                    html+=file['delete_url'];
                    html+='</td>';
                    html+='<td>'+file['file_name']+'</td>';
                    html+='<td>'+file['lcd']+'</td>';
                    html+='</tr>';
                    $('#attachmentList>tbody').append(html);
                    $('#file_fasd').val('');
                    if($('.changeAttachment:first').val() == ''){
                        $('.changeAttachment:first').val(file['id']);
                    }else{
                        var value = $('.changeAttachment:first').val()+','+file['id'];
                        $('.changeAttachment:first').val(value);
                    }
                }else{
                    location.reload();
                }
            },
        });
    });
    //附件刪除
    $('#attachmentList').delegate('.attachmentDelete','click',function(){
        var value = $('.changeAttachment:first').val();
        var deleteId=$(this).data('id');
        if(value != ''){
            var list = value.split(',');
            for(var i= 0;i<list.length;i++){
                if(list[i] == deleteId){
                    list.splice(i,1);
                }
            }
            $('.changeAttachment:first').val(list.join(','))
        }
        $(this).parents('tr:first').remove();
    });
    //年齡計算
    $('#EmployForm_birth_time').on('change',function(){
        var birth_time = $(this).val();
        if(birth_time != ''){
            var age = jsGetAge(birth_time);
            $('#EmployForm_age').val(age);
        }
    });
    
    $('#EmployForm_staff_id').on('change',function(){
        if($('#EmployForm_company_id').val() == ''){
            $('#EmployForm_company_id').val($(this).val());
        }
    });
    $('.changeButton').on('change',function(){
        $('#EmployForm_staff_type').val($(this).find('option:selected').data('dept'));
    });
    //合同期限變化
    $('.fixTime').on('change',function(){
        var netDom = $(this).parents('.form-group:first').next('.form-group');
        if($(this).val() == 'nofixed'){
            netDom.find('input').eq(1).val('').prop('readonly',true).addClass('readonly');
        }else{
            netDom.find('input').eq(1).prop('readonly',false).removeClass('readonly');
        }
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
if ($model->scenario!='view') {
    $js = Script::genDatePicker(array(
        'EmployForm_birth_time',
        'EmployForm_entry_time',
        'EmployForm_start_time',
        'EmployForm_end_time',
        'EmployForm_test_start_time',
        'EmployForm_user_card_date',
    ));
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}

$js = Script::genDeleteData(Yii::app()->createUrl('employ/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/jquery-form.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/ajaxFile.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/wages.js?2", CClientScript::POS_END);

?>

<?php $this->endWidget(); ?>
<?php $this->renderPartial('//site/attachmentload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'PAYREQ',
    'type'=>1,
    'header'=>Yii::t('dialog','File Attachment'),
    'ronly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)),
));
?>
</div><!-- form -->

