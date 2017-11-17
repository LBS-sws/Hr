<?php
if (empty($model->employee_id)){
    $this->redirect(Yii::app()->createUrl('history/index'));
}
$this->pageTitle=Yii::app()->name . ' - Detail Form';
?>
<style>
    input[readonly="readonly"]{pointer-events: none;}
</style>
<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'detail-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo $model->setFormTitle()." - ".$model->name; ?></strong>
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
            <div class="btn-group pull-right" role="group">
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
            <?php echo $form->hiddenField($model, 'city'); ?>
            <?php echo $form->hiddenField($model, 'staff_status'); ?>
            <?php echo $form->hiddenField($model, 'employee_id'); ?>
            <?php echo $form->hiddenField($model, 'id'); ?>

            <?php if ($model->opr_type=="transfer"): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'change_city_old',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->dropDownList($model, 'city',WordForm::getCityListAll(),
                            array('disabled'=>(true))
                        ); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'change_city',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->dropDownList($model, 'change_city',WordForm::getCityListAll(),
                            array('disabled'=>(true))
                        ); ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'update_remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php echo $form->textArea($model, 'update_remark',
                        array('rows'=>3,'readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
            </div>
            <legend></legend>

            <legend><?php echo Yii::t("contract","personal data");?></legend>
            <div class="form-group">
                <?php echo $form->labelEx($model,'name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'name',
                        array('size'=>10,'maxlength'=>10,'readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'sex',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'sex',EmployList::getSexList(),
                        array('disabled'=>($model->scenario=='view'))
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
                            array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'&&$model->staff_status!=3),));
                        ?>
                    </div>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'age',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'age',
                        array('readonly'=>true)
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'address',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textField($model, 'address',
                        array('readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
                <label class="pull-left control-label"><?php echo Yii::t("contract","postcode");?></label>
                <div class="col-sm-2">
                    <?php echo $form->textField($model, 'address_code',
                        array('readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'contact_address',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textField($model, 'contact_address',
                        array('readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
                <label class="pull-left control-label"><?php echo Yii::t("contract","postcode");?></label>
                <div class="col-sm-2">
                    <?php echo $form->textField($model, 'contact_address_code',
                        array('readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'phone',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'phone',
                        array('size'=>18,'maxlength'=>18,'readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'phone2',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'phone2',
                        array('size'=>18,'maxlength'=>18,'readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'emergency_user',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'emergency_user',
                        array('size'=>18,'maxlength'=>18,'readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'emergency_phone',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'emergency_phone',
                        array('size'=>18,'maxlength'=>18,'readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'user_card',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'user_card',
                        array('readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
                <?php echo $form->labelEx($model,'user_card_date',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'user_card_date',
                            array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'&&$model->staff_status!=3),));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'social_code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'social_code',
                        array('disabled'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'empoyment_code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'empoyment_code',
                        array('disabled'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'nation',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'nation',
                        array('disabled'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'household',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'household',EmployList::getNationList(),
                        array('disabled'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'email',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'email',
                        array('readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'health',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'health',EmployList::getHealthList(),
                        array('readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
            </div>

            <legend><?php echo Yii::t("contract","position data");?></legend>
            <div class="form-group">
                <?php echo $form->labelEx($model,'code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'code',
                        array('size'=>20,'maxlength'=>20,'readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
                <?php echo $form->labelEx($model,'entry_time',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'entry_time',
                            array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'&&$model->staff_status!=3),));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'staff_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'staff_id',$model->getCompanyToCity(),
                        array('disabled'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'department',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'department',
                        array('readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'position',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'position',
                        array('readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'staff_type',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'staff_type',EmployList::getStaffTypeList(),
                        array('disabled'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'staff_leader',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'staff_leader',EmployList::getStaffLeaderList(),
                        array('disabled'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'price1',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'price1',WagesForm::getWagesList(),
                        array('disabled'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'code_old',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'code_old',
                        array('readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
            </div>

            <legend><?php echo Yii::t("contract","contract data");?></legend>
            <div class="form-group">
                <?php echo $form->labelEx($model,'fix_time',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->inlineRadioButtonList($model, 'fix_time',EmployList::getFixTimeList(),
                        array('disabled'=>($model->scenario=='view'&&$model->staff_status!=3),'class'=>"fixTime")
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
                        <?php
                        if($model->fix_time == "nofixed"){
                            $model->end_time = "";
                            echo $form->textField($model, 'end_time',
                                array('class'=>'form-control pull-right','readonly'=>(true),));
                        }else{
                            echo $form->textField($model, 'end_time',
                                array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),));
                        }
                        ?>
                    </div>
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
                        array('readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'company_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'company_id',$model->getCompanyToCity(),
                        array('disabled'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'contract_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'contract_id',$model->getContractToCity(),
                        array('disabled'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'test_type',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'test_type',array(
                        "1"=>Yii::t("contract","Have probation period"),
                        "0"=>Yii::t("contract","No probation period")
                    ),array('disabled'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
            </div>
            <div class="test-div">
                <div class="form-group">
                    <?php echo $form->labelEx($model,'test_length',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->dropDownList($model, 'test_length',EmployList::getMonthList(),
                            array('class'=>'test_add_time','disabled'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'test_time',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <?php echo $form->textField($model, 'test_start_time',
                                array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'&&$model->staff_status!=3),));
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
                                array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'&&$model->staff_status!=3),));
                            ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'test_wage',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->numberField($model, 'test_wage',
                            array('min'=>0,'readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                        ); ?>
                    </div>
                </div>
            </div>

            <legend><?php echo Yii::t("contract","additional information");?></legend>
            <div class="form-group">
                <?php echo $form->labelEx($model,'education',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'education',EmployList::getEducationList(),
                        array('disabled'=>($model->scenario=='view'))
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'experience',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'experience',
                        array('readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'english',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'english',
                        array('rows'=>3,'readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'technology',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'technology',
                        array('rows'=>3,'readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'other',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'other',
                        array('rows'=>3,'readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
            </div>

            <legend><?php echo Yii::t("contract","archives");?></legend>
            <div class="form-group">
                <?php echo $form->labelEx($model,'image_user',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php
                    if(!empty($model->image_user)){
                        echo $form->fileField($model, 'image_user',
                            array('readonly'=>($model->scenario=='view'&&$model->staff_status!=3),"class"=>"file-update form-control hide")
                        );
                        echo "<div class='media fileImgShow'><div class='media-left'><img height='80px' src='".$model->image_user."'></div>
                        <div class='media-body media-bottom'></div></div>";
                    }else{
                        echo $form->fileField($model, 'image_user',
                            array('readonly'=>($model->scenario=='view'&&$model->staff_status!=3),"class"=>"file-update form-control")
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
                            array('readonly'=>($model->scenario=='view'&&$model->staff_status!=3),"class"=>"file-update form-control hide")
                        );
                        echo "<div class='media fileImgShow'><div class='media-left'><img height='80px' src='".$model->image_code."'></div>
                        <div class='media-body media-bottom'></div></div>";
                    }else{
                        echo $form->fileField($model, 'image_code',
                            array('readonly'=>($model->scenario=='view'&&$model->staff_status!=3),"class"=>"file-update form-control")
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
                            array('readonly'=>($model->scenario=='view'&&$model->staff_status!=3),"class"=>"file-update form-control hide")
                        );
                        echo "<div class='media fileImgShow'><div class='media-left'><img height='80px' src='".$model->image_work."'></div>
                        <div class='media-body media-bottom'></div></div>";
                    }else{
                        echo $form->fileField($model, 'image_work',
                            array('readonly'=>($model->scenario=='view'&&$model->staff_status!=3),"class"=>"file-update form-control")
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
                            array('readonly'=>($model->scenario=='view'&&$model->staff_status!=3),"class"=>"file-update form-control hide")
                        );
                        echo "<div class='media fileImgShow'><div class='media-left'><img height='80px' src='".$model->image_other."'></div>
                        <div class='media-body media-bottom'></div></div>";
                    }else{
                        echo $form->fileField($model, 'image_other',
                            array('readonly'=>($model->scenario=='view'&&$model->staff_status!=3),"class"=>"file-update form-control")
                        );
                    }
                    ?>
                </div>
            </div>

            <legend></legend>
            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'remark',
                        array('rows'=>3,'readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'ld_card',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textField($model, 'ld_card',
                        array('readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'sb_card',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textField($model, 'sb_card',
                        array('readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'jj_card',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textField($model, 'jj_card',
                        array('readonly'=>($model->scenario=='view'&&$model->staff_status!=3))
                    ); ?>
                </div>
            </div>
        </div>
    </div>
</section>


<?php
/*if ($model->scenario!='new')
    $this->renderPartial('//site/flowword',array('model'=>$model));*/

$js = "
var staffStatus = '".$model->staff_status."';
$('#HistoryForm_test_type').on('change',function(){
    if($(this).val() == 1){
        $(this).parents('.form-group').next('div.test-div').slideDown(100);
    }else{
        $(this).parents('.form-group').next('div.test-div').slideUp(100);
    }
}).trigger('change');

    //工資單變化
    DEFINE_WAGES =".json_encode($model->price3).";
    var objWagesPar = {'url':'".Yii::app()->createUrl('wages/ajaxGetWageType')."','str':'".Yii::t("contract","Wages Type")."','form':'HistoryForm'};
    $('#HistoryForm_price1').on('change',objWagesPar,ajaxWagesChange).trigger('change');
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/wages.js", CClientScript::POS_END);

?>

<?php $this->endWidget(); ?>

<?php $this->renderPartial('//site/attachmentload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'PAYREQ',
    'type'=>1,
    'header'=>Yii::t('dialog','File Attachment'),
    'ronly'=>($model->scenario=='view'&&$model->staff_status!=3),
));
?>
</div><!-- form -->

