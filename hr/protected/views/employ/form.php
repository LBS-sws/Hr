<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('employ/index'));
}
$this->pageTitle=Yii::app()->name . ' - Employ Form';
?>

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

<?php if ($model->scenario!='new'): ?>
	<div class="btn-group pull-right" role="group">
    <?php if ($model->staff_status == 4): ?>
            <?php echo TbHtml::button('<span class="fa fa-file-word-o"></span> '.Yii::t('contract','Down'),array(
                'submit'=>Yii::app()->createUrl('employee/Downfile?index='.$model->id)));
            ?>
    <?php endif; ?>
	</div>
<?php endif; ?>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
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
			<div class="form-group">
                <?php if ($model->scenario!='new'): ?>
                    <?php echo $form->labelEx($model,'code',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->textField($model, 'code',
                            array('size'=>20,'maxlength'=>20,'readonly'=>true)
                        ); ?>
                    </div>
                <?php endif; ?>

				<?php echo $form->labelEx($model,'entry_time',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'entry_time',
                            array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)),));
                        ?>
                    </div>
				</div>
			</div>

			<div class="form-group">
                <?php echo $form->labelEx($model,'name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'name',
                        array('size'=>10,'maxlength'=>10,'readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'sex',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'sex',
                        array('size'=>10,'maxlength'=>10,'readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
			</div>
			<div class="form-group">
                <?php echo $form->labelEx($model,'birth_time',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'birth_time',
                            array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)),));
                        ?>
                    </div>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'age',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->numberField($model, 'age',
                        array('size'=>10,'maxlength'=>10,'readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'company_id',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php echo $form->dropDownList($model, 'company_id',$model->getCompanyToCity(),
						array('disabled'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
					); ?>
				</div>
                <!--分割-->
                <?php echo $form->labelEx($model,'contract_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'contract_id',$model->getContractToCity(),
                        array('disabled'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
			</div>


            <div class="form-group">
                <?php echo $form->labelEx($model,'address',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textField($model, 'address',
                        array('readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
                <label class="pull-left control-label"><?php echo Yii::t("contract","postcode");?></label>
                <div class="col-sm-2">
                    <?php echo $form->textField($model, 'address_code',
                        array('readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'contact_address',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textField($model, 'contact_address',
                        array('readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
                <label class="pull-left control-label"><?php echo Yii::t("contract","postcode");?></label>
                <div class="col-sm-2">
                    <?php echo $form->textField($model, 'contact_address_code',
                        array('readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'phone',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'phone',
                        array('size'=>18,'maxlength'=>18,'readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'phone2',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'phone2',
                        array('size'=>18,'maxlength'=>18,'readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'user_card',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'user_card',
                        array('readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'health',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'health',
                        array('readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'department',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'department',
                        array('readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'position',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'position',
                        array('readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'wage',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->numberField($model, 'wage',
                        array('min'=>0,'readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'year_day',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'year_day',
                        array('readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'time',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'start_time',
                            array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)),));
                        ?>
                    </div>
                </div>
                <div class="pull-left control-label">至</div>
                <div class="col-sm-3">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'end_time',
                            array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)),));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'test_type',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'test_type',array(
                            "1"=>Yii::t("contract","Have probation period"),
                            "0"=>Yii::t("contract","No probation period")
                        ),array('disabled'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
            </div>
            <div class="test-div">
                <div class="form-group">
                    <?php echo $form->labelEx($model,'test_time',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <?php echo $form->textField($model, 'test_start_time',
                                array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)),));
                            ?>
                        </div>
                    </div>
                    <div class="pull-left control-label">至</div>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <?php echo $form->textField($model, 'test_end_time',
                                array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)),));
                            ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'test_wage',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->numberField($model, 'test_wage',
                            array('min'=>0,'readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                        ); ?>
                    </div>
                </div>
            </div>


            <div class="form-group">
                <?php echo $form->labelEx($model,'education',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'education',
                        array('readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'experience',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'experience',
                        array('readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'email',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'email',
                        array('readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'image_user',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php
                    if(!empty($model->image_user)){
                        echo $form->fileField($model, 'image_user',
                            array('readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)),"class"=>"file-update form-control hide")
                        );
                        echo "<div class='media fileImgShow'><div class='media-left'><img height='80px' src='".$model->image_user."'></div>
                        <div class='media-body media-bottom'><a>".Yii::t("contract","update")."</a></div></div>";
                    }else{
                        echo $form->fileField($model, 'image_user',
                            array('readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)),"class"=>"file-update form-control")
                        );
                    }
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'image_code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php
                    if(!empty($model->image_code)){
                        echo $form->fileField($model, 'image_code',
                            array('readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)),"class"=>"file-update form-control hide")
                        );
                        echo "<div class='media fileImgShow'><div class='media-left'><img height='80px' src='".$model->image_code."'></div>
                        <div class='media-body media-bottom'><a>".Yii::t("contract","update")."</a></div></div>";
                    }else{
                        echo $form->fileField($model, 'image_code',
                            array('readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)),"class"=>"file-update form-control")
                        );
                    }
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'image_work',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php
                    if(!empty($model->image_work)){
                        echo $form->fileField($model, 'image_work',
                            array('readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)),"class"=>"file-update form-control hide")
                        );
                        echo "<div class='media fileImgShow'><div class='media-left'><img height='80px' src='".$model->image_work."'></div>
                        <div class='media-body media-bottom'><a>".Yii::t("contract","update")."</a></div></div>";
                    }else{
                        echo $form->fileField($model, 'image_work',
                            array('readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)),"class"=>"file-update form-control")
                        );
                    }
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'image_other',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php
                    if(!empty($model->image_other)){
                        echo $form->fileField($model, 'image_other',
                            array('readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)),"class"=>"file-update form-control hide")
                        );
                        echo "<div class='media fileImgShow'><div class='media-left'><img height='80px' src='".$model->image_other."'></div>
                        <div class='media-body media-bottom'><a>".Yii::t("contract","update")."</a></div></div>";
                    }else{
                        echo $form->fileField($model, 'image_other',
                            array('readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)),"class"=>"file-update form-control")
                        );
                    }
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'english',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'english',
                        array('rows'=>3,'readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'technology',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'technology',
                        array('rows'=>3,'readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'other',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'other',
                        array('rows'=>3,'readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'price1',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'price1',
                        array('readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'price2',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'price2',
                        array('readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'price3',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'price3',
                        array('readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
            </div>

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
        $(this).parents('.fileImgShow').prev('input[type=\"file\"]').show();
        $(this).parents('.fileImgShow').remove();
    });
    $('.fileImgShow').each(function(){
        var url = $(this).find('img:first').attr('src');
        $(this).parent('div').children('input[type=\"hidden\"]').val(url);
        $(this).parent('div').children('input[type=\"file\"]').removeClass('hide').hide();
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
        'EmployForm_test_end_time',
    ));
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}

$js = Script::genDeleteData(Yii::app()->createUrl('employ/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/jquery-form.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/ajaxFile.js", CClientScript::POS_END);

?>

<?php $this->endWidget(); ?>
</div><!-- form -->

