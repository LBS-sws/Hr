<?php
if (empty($model->id)){
    $this->redirect(Yii::app()->createUrl('auditHistory/index'));
}
$this->pageTitle=Yii::app()->name . ' - AuditHistory Form';
?>
<style>
    input[readonly="readonly"]{pointer-events: none;}
</style>
<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'auditHistory-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo $model->setFormTitle(); ?></strong>
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
                echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
                    'submit'=>Yii::app()->createUrl('auditHistory/index')));
                ?>
            </div>
            <?php
            if($model->scenario=='view'){
                if($model->staff_status == 2){
                    echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','Audit'), array(
                        'submit'=>Yii::app()->createUrl('auditHistory/audit')));
                }
                if(($model->city == Yii::app()->user->city() || $model->scenario=='new')&&$model->staff_status == 2){
                    echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('contract','Rejected'), array(
                        'submit'=>Yii::app()->createUrl('auditHistory/reject')));
                }
            }
            ?>
            <div class="btn-group pull-right" role="group">
                <?php if ($model->scenario!='new'){
                    //流程
                    echo TbHtml::button('<span class="fa fa-file-text-o"></span> '.Yii::t('app','History'), array(
                        'name'=>'btnFlow','id'=>'btnFlow','data-toggle'=>'modal','data-target'=>'#flowinfodialog'));
                } ?>
            </div>
        </div></div>

    <div class="box box-info">
        <div class="box-body">
            <?php echo $form->hiddenField($model, 'scenario'); ?>
            <?php echo $form->hiddenField($model, 'city'); ?>
            <?php echo $form->hiddenField($model, 'staff_status'); ?>
            <?php echo $form->hiddenField($model, 'operation'); ?>
            <?php echo $form->hiddenField($model, 'id'); ?>
            <?php echo $form->hiddenField($model, 'employee_id'); ?>

            <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo Yii::t("contract","Operation Status");?></label>
                <div class="col-sm-5">
                    <input class="input-10 form-control readonly" readonly type="text" value="<?php echo Yii::t("contract",$model->operation);?>">
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'update_remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php echo $form->textArea($model, 'update_remark',
                        array('rows'=>3,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <legend></legend>
            <div class="form-group">
                <?php echo $form->labelEx($model,'code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'code',
                        array('size'=>20,'maxlength'=>20,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
                <?php echo $form->labelEx($model,'entry_time',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'entry_time',
                            array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'name',
                        array('size'=>10,'maxlength'=>10,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'sex',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'sex',
                        array('size'=>10,'maxlength'=>10,'readonly'=>($model->scenario=='view'))
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
                            array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),));
                        ?>
                    </div>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'age',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->numberField($model, 'age',
                        array('size'=>10,'maxlength'=>10,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'company_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'company_id',$model->getCompanyToCity(),
                        array('disabled'=>($model->scenario=='view'))
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'contract_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'contract_id',$model->getContractToCity(),
                        array('disabled'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>


            <div class="form-group">
                <?php echo $form->labelEx($model,'address',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textField($model, 'address',
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
                <label class="pull-left control-label"><?php echo Yii::t("contract","postcode");?></label>
                <div class="col-sm-2">
                    <?php echo $form->textField($model, 'address_code',
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'contact_address',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textField($model, 'contact_address',
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
                <label class="pull-left control-label"><?php echo Yii::t("contract","postcode");?></label>
                <div class="col-sm-2">
                    <?php echo $form->textField($model, 'contact_address_code',
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'phone',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'phone',
                        array('size'=>18,'maxlength'=>18,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'phone2',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'phone2',
                        array('size'=>18,'maxlength'=>18,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'user_card',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'user_card',
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'health',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'health',
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'department',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'department',
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'position',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'position',
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'wage',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->numberField($model, 'wage',
                        array('min'=>0,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'year_day',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'year_day',
                        array('readonly'=>($model->scenario=='view'))
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
                            array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),));
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
                            array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),));
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
                    ),array('disabled'=>($model->scenario=='view'))
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
                                array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),));
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
                                array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),));
                            ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'test_wage',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->numberField($model, 'test_wage',
                            array('min'=>0,'readonly'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                </div>
            </div>


            <div class="form-group">
                <?php echo $form->labelEx($model,'education',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'education',
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'experience',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'experience',
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'email',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'email',
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'image_user',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php
                    if(!empty($model->image_user)){
                        echo $form->fileField($model, 'image_user',
                            array('readonly'=>($model->scenario=='view'),"class"=>"file-update form-control hide")
                        );
                        echo "<div class='media fileImgShow'><div class='media-left'><img height='80px' src='".$model->image_user."'></div>
                        <div class='media-body media-bottom'></div></div>";
                    }else{
                        echo $form->fileField($model, 'image_user',
                            array('readonly'=>($model->scenario=='view'),"class"=>"file-update form-control")
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
                            array('readonly'=>($model->scenario=='view'),"class"=>"file-update form-control hide")
                        );
                        echo "<div class='media fileImgShow'><div class='media-left'><img height='80px' src='".$model->image_code."'></div>
                        <div class='media-body media-bottom'></div></div>";
                    }else{
                        echo $form->fileField($model, 'image_code',
                            array('readonly'=>($model->scenario=='view'),"class"=>"file-update form-control")
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
                            array('readonly'=>($model->scenario=='view'),"class"=>"file-update form-control hide")
                        );
                        echo "<div class='media fileImgShow'><div class='media-left'><img height='80px' src='".$model->image_work."'></div>
                        <div class='media-body media-bottom'></div></div>";
                    }else{
                        echo $form->fileField($model, 'image_work',
                            array('readonly'=>($model->scenario=='view'),"class"=>"file-update form-control")
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
                            array('readonly'=>($model->scenario=='view'),"class"=>"file-update form-control hide")
                        );
                        echo "<div class='media fileImgShow'><div class='media-left'><img height='80px' src='".$model->image_other."'></div>
                        <div class='media-body media-bottom'></div></div>";
                    }else{
                        echo $form->fileField($model, 'image_other',
                            array('readonly'=>($model->scenario=='view'),"class"=>"file-update form-control")
                        );
                    }
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'english',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'english',
                        array('rows'=>3,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'technology',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'technology',
                        array('rows'=>3,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'other',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'other',
                        array('rows'=>3,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'price1',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'price1',
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'price2',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'price2',
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'price3',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'price3',
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'remark',
                        array('rows'=>3,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>

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

            <legend></legend>
            <div class="form-group">
                <?php echo $form->labelEx($model,'ject_remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php echo $form->textArea($model, 'ject_remark',
                        array('rows'=>3,'readonly'=>($model->scenario==1))
                    ); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$this->renderPartial('//site/historylist',array('model'=>$model));
?>
<?php
/*if ($model->scenario!='new')
    $this->renderPartial('//site/flowword',array('model'=>$model));*/

$js = "
var staffStatus = '".$model->staff_status."';
$('#AuditHistoryForm_test_type').on('change',function(){
    if($(this).val() == 1){
        $(this).parents('.form-group').next('div.test-div').slideDown(100);
    }else{
        $(this).parents('.form-group').next('div.test-div').slideUp(100);
    }
}).trigger('change');
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);

/*Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/jquery-form.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/ajaxFile.js", CClientScript::POS_END);*/

?>

<?php $this->endWidget(); ?>
</div><!-- form -->

