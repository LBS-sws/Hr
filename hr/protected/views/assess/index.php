<?php
$this->pageTitle=Yii::app()->name . ' - Assess';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'assess-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','Staff appraisal'); ?></strong>
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
                <?php
                //var_dump(Yii::app()->session['rw_func']);
                if (Yii::app()->user->validRWFunction('ZE07'))
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                        'submit'=>Yii::app()->createUrl('assess/new'),
                    ));
                ?>
            </div>
            <div class="btn-group pull-right" role="group">
                <?php
                //var_dump(Yii::app()->session['rw_func']);
                if (Yii::app()->user->validRWFunction('ZE07'))
                    echo TbHtml::button('<span class="fa fa-envelope-o"></span> '.Yii::t('fete','sent email'), array(
                        'id'=>'btnSent','data-toggle'=>'modal','data-target'=>'#assess_email'
                    ));
                ?>
            </div>
        </div></div>
    <?php
    $search = array(
        'employee_code',
        'employee_name',
    );
    $search_add_html="";
    $modelName = get_class($model);
    $search[] = 'city_name';
    $search_add_html .= TbHtml::textField($modelName.'[searchTimeStart]',$model->searchTimeStart,
        array('size'=>15,'placeholder'=>Yii::t('misc','Start Date'),"class"=>"form-control","id"=>"start_time"));
    $search_add_html.="<span>&nbsp;&nbsp;-&nbsp;&nbsp;</span>";
    $search_add_html .= TbHtml::textField($modelName.'[searchTimeEnd]',$model->searchTimeEnd,
        array('size'=>15,'placeholder'=>Yii::t('misc','End Date'),"class"=>"form-control","id"=>"end_time"));

    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('fete','Evaluation list'),
        'model'=>$model,
        'viewhdr'=>'//assess/_listhdr',
        'viewdtl'=>'//assess/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search_add_html'=>$search_add_html,
        'search'=>$search,
    ));
    ?>
</section>
<?php
echo $form->hiddenField($model,'pageNum');
echo $form->hiddenField($model,'totalRow');
echo $form->hiddenField($model,'orderField');
echo $form->hiddenField($model,'orderType');
?>

<div id="assess_email" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal" type="button">×</button>
                <h4 class="modal-title"><?php echo Yii::t('fete','sent email');?></h4></div><div class="modal-body"><p></p>
                <div class="media">
                    <div class="media-left">
                        <?php echo $form->labelEx($model,'email_list',array('class'=>"control-label","style"=>"width:60px")); ?>
                    </div>
                    <div class="media-body">
                        <?php echo $form->textArea($model, 'email_list',
                            array('rows'=>4,'readonly'=>($model->scenario=='view'),"style"=>"width:100%","id"=>"email_list")
                        ); ?>
                    </div>
                    <div class="media-right media-bottom">
                        <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#email_check">选择</button>
                    </div>
                </div>
                <P>&nbsp;</P>
                <p class="text-danger">多个邮件请用;号分割。例如：aaa@lbsgroup.com.cn;bbb@lbsgroup.com.cn</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="send_email" type="button">发送</button>
                <button data-dismiss="modal" class="btn btn-default" type="button">取消</button>
            </div>
        </div>
    </div>
</div>

<div id="email_check" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal" type="button">×</button>
                <h4 class="modal-title"><?php echo Yii::t('contract','Email');?></h4></div><div class="modal-body"><p></p>
                <div>
                    <?php echo $form->checkBoxList($model, 'test',$model->getEmailList(),array("class"=>"check_dev")); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" type="button" id="dev_ok">插入</button>
                <button data-dismiss="modal" class="btn btn-default" type="button">取消</button>
            </div>
        </div>
    </div>
</div>
<?php $this->endWidget(); ?>

<?php
$js = "
$('#start_time').datepicker({autoclose: true, format: 'yyyy/mm/dd',language: 'zh_cn'});
$('#end_time').datepicker({autoclose: true, format: 'yyyy/mm/dd',language: 'zh_cn'});
$('.checkBoxSent').on('click',function(e){
    e.stopPropagation();
});

$('#dev_ok').on('click',function(){
    var list = $('.check_dev').is(':checked');
    var value = '';
    $('.check_dev:checked').each(function(){
        value+=$(this).val()+';';
    });
    $('#email_list').val(value);
    $('#email_check').modal('hide');
});
$('#send_email').on('click',function() {
	var elm=$('#send_email');
	jQuery.yii.submitForm(elm,'".Yii::app()->createUrl('assess/sent')."',{});
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genTableRowClick();
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

