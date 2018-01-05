
<legend><?php echo Yii::t("contract","personal data");?></legend>
<div class="form-group">
    <?php echo $form->labelEx($model,'name',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'name',
            array('size'=>10,'maxlength'=>10,'readonly'=>($readonly))
        ); ?>
    </div>
    <!--分割-->
    <?php echo $form->labelEx($model,'sex',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownList($model, 'sex',EmployList::getSexList(),
            array('disabled'=>($readonly))
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
                array('class'=>'form-control pull-right','readonly'=>($readonly),));
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
            array('readonly'=>($readonly))
        ); ?>
    </div>
    <label class="pull-left control-label"><?php echo Yii::t("contract","postcode");?></label>
    <div class="col-sm-2">
        <?php echo $form->textField($model, 'address_code',
            array('readonly'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'contact_address',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-5">
        <?php echo $form->textField($model, 'contact_address',
            array('readonly'=>($readonly))
        ); ?>
    </div>
    <label class="pull-left control-label"><?php echo Yii::t("contract","postcode");?></label>
    <div class="col-sm-2">
        <?php echo $form->textField($model, 'contact_address_code',
            array('readonly'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'phone',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'phone',
            array('size'=>18,'maxlength'=>18,'readonly'=>($readonly))
        ); ?>
    </div>
    <!--分割-->
    <?php echo $form->labelEx($model,'phone2',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'phone2',
            array('size'=>18,'maxlength'=>18,'readonly'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'emergency_user',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'emergency_user',
            array('size'=>18,'maxlength'=>18,'readonly'=>($readonly))
        ); ?>
    </div>
    <!--分割-->
    <?php echo $form->labelEx($model,'emergency_phone',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'emergency_phone',
            array('size'=>18,'maxlength'=>18,'readonly'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'user_card',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'user_card',
            array('readonly'=>($readonly))
        ); ?>
    </div>
    <?php echo $form->labelEx($model,'user_card_date',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <?php echo $form->textField($model, 'user_card_date',
                array('class'=>'form-control pull-right','readonly'=>($readonly),));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'social_code',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'social_code',
            array('readonly'=>($readonly))
        ); ?>
    </div>
    <!--分割-->
    <?php echo $form->labelEx($model,'empoyment_code',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'empoyment_code',
            array('readonly'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'nation',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'nation',
            array('readonly'=>($readonly))
        ); ?>
    </div>
    <!--分割-->
    <?php echo $form->labelEx($model,'household',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownList($model, 'household',EmployList::getNationList(),
            array('disabled'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'email',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'email',
            array('readonly'=>($readonly))
        ); ?>
    </div>
    <!--分割-->
    <?php echo $form->labelEx($model,'health',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownList($model, 'health',EmployList::getHealthList(),
            array('disabled'=>($readonly))
        ); ?>
    </div>
</div>

<legend><?php echo Yii::t("contract","position data");?></legend>
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
                array('class'=>'form-control pull-right','readonly'=>($readonly),));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'staff_id',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownList($model, 'staff_id',$model->getCompanyToCity(),
            array('disabled'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'department',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownList($model, 'department',DeptForm::getDeptAllList(0),
            array('disabled'=>($readonly),"class"=>"depart")
        ); ?>
    </div>
    <!--分割-->
    <?php echo $form->labelEx($model,'position',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php
        $model_class = get_class($model);
        $departmentList = DeptForm::getDeptOneAllList();
        if($readonly){
            echo "<select class='depart form-control changeButton' name='".$model_class."[position]' disabled>";
        }else{
            echo "<select class='depart form-control changeButton' name='".$model_class."[position]'>";
        }
        foreach ($departmentList as $key =>$value){
            if($model->position == $key){
                echo "<option value='$key' data-type='".$value["type"]."' data-dept='".$value["dept_class"]."' selected>".$value["name"]."</option>";
            }else{
                echo "<option value='$key' data-type='".$value["type"]."' data-dept='".$value["dept_class"]."'>".$value["name"]."</option>";
            }
        }
        echo "</select>";
        ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'staff_type',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownList($model, 'staff_type',EmployList::getStaffTypeList(),
            array('readonly'=>(true))
        ); ?>
    </div>
    <!--分割-->
    <?php echo $form->labelEx($model,'staff_leader',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownList($model, 'staff_leader',EmployList::getStaffLeaderList(),
            array('disabled'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <!--刪除工資單變化
    <?php //echo $form->labelEx($model,'price1',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php
/*        echo $form->dropDownList($model, 'price1',WagesForm::getWagesList(),
            array('disabled'=>($readonly)));*/
        ?>
    </div>
    -->
    <!--分割-->
    <?php echo $form->labelEx($model,'code_old',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'code_old',
            array('readonly'=>($readonly))
        ); ?>
    </div>
</div>

<legend><?php echo Yii::t("contract","contract data");?></legend>
<?php
if(empty($model->employee_id)){
    $contractNum = EmployList::getContractNumber($model->id);
}else{
    $contractNum = EmployList::getContractNumber($model->employee_id);
}
if (!empty($contractNum)){
    echo '<div class="form-group">';
    echo TbHtml::label(Yii::t("contract","Contract Number"),'',array('class'=>"col-sm-2 control-label"));
    echo '<div class="col-sm-3">';
    echo TbHtml::textField('contractNum', $contractNum,array('class'=>'form-control pull-right','readonly'=>(true),));
    echo '</div>';
    echo '</div>';
}
?>
<div class="form-group">
    <?php echo $form->labelEx($model,'fix_time',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-5">
        <?php echo $form->inlineRadioButtonList($model, 'fix_time',EmployList::getFixTimeList(),
            array('disabled'=>($readonly),'class'=>"fixTime")
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
                array('class'=>'form-control pull-right','readonly'=>($readonly),));
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
                    array('class'=>'form-control pull-right','readonly'=>($readonly),));
            }
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <?php if (EmployForm::validateWageInput()): ?>
        <?php echo $form->labelEx($model,'wage',array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-3">
            <?php echo $form->numberField($model, 'wage',
                array('min'=>0,'readonly'=>($readonly))
            ); ?>
        </div>
    <?php else: ?>
        <?php echo $form->hiddenField($model, 'wage'); ?>
    <?php endif; ?>
    <!--分割-->
    <?php echo $form->labelEx($model,'year_day',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'year_day',
            array('readonly'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'company_id',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownList($model, 'company_id',$model->getCompanyToCity(),
            array('disabled'=>($readonly))
        ); ?>
    </div>
    <!--分割-->
    <?php echo $form->labelEx($model,'contract_id',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownList($model, 'contract_id',$model->getContractToCity(),
            array('disabled'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'test_type',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownList($model, 'test_type',array(
            "1"=>Yii::t("contract","Have probation period"),
            "0"=>Yii::t("contract","No probation period")
        ),array('disabled'=>($readonly))
        ); ?>
    </div>
</div>
<div class="test-div">
    <div class="form-group">
        <?php echo $form->labelEx($model,'test_length',array('class'=>"col-sm-2 control-label required","label"=>$model->getAttributeLabel("test_length").'&nbsp;<span class="required">*</span>')); ?>
        <div class="col-sm-3">
            <?php echo $form->dropDownList($model, 'test_length',EmployList::getMonthList(),
                array('class'=>'test_add_time','disabled'=>($readonly))
            ); ?>
        </div>
    </div>
    <div class="form-group">
        <?php echo $form->labelEx($model,'test_time',array('class'=>"col-sm-2 control-label required","label"=>$model->getAttributeLabel("test_time").'&nbsp;<span class="required">*</span>')); ?>
        <div class="col-sm-3">
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </div>
                <?php echo $form->textField($model, 'test_start_time',
                    array('class'=>'test_add_time pull-right','readonly'=>($readonly),));
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
                    array('class'=>'test_sum_time pull-right','readonly'=>true));
                ?>
            </div>
        </div>
    </div>
    <div class="form-group">
        <!--<span class="required">*</span>-->
        <?php echo $form->labelEx($model,'test_wage',array('class'=>"col-sm-2 control-label required","label"=>$model->getAttributeLabel("test_wage").'&nbsp;<span class="required">*</span>')); ?>
        <div class="col-sm-3">
            <?php echo $form->numberField($model, 'test_wage',
                array('min'=>0,'readonly'=>($readonly))
            ); ?>
        </div>
    </div>
</div>

<legend><?php echo Yii::t("contract","additional information");?></legend>
<div class="form-group">
    <?php echo $form->labelEx($model,'education',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownList($model, 'education',EmployList::getEducationList(),
            array('disabled'=>($readonly))
        ); ?>
    </div>
    <!--分割-->
    <?php echo $form->labelEx($model,'experience',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'experience',
            array('readonly'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'english',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-5">
        <?php echo $form->textArea($model, 'english',
            array('rows'=>3,'readonly'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'technology',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-5">
        <?php echo $form->textArea($model, 'technology',
            array('rows'=>3,'readonly'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'other',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-5">
        <?php echo $form->textArea($model, 'other',
            array('rows'=>3,'readonly'=>($readonly))
        ); ?>
    </div>
</div>

<legend><?php echo Yii::t("contract","archives");?></legend>
<div class="form-group">
    <?php echo $form->labelEx($model,'image_user',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php
        if($readonly){
            if(empty($model->image_user)){
                echo "<div class='form-control-static'>無</div>";
            }else{
                echo "<div class='form-control-static'><img class='openBigImg' height='80px' src='".$model->image_user."'></div>";
            }
        }else{
            if(!empty($model->image_user)){
                echo $form->fileField($model, 'image_user',
                    array('readonly'=>($readonly),"class"=>"file-update form-control hide")
                );
                echo "<div class='media fileImgShow'><div class='media-left'><img height='80px' src='".$model->image_user."'></div>
                        <div class='media-body media-bottom'><a>".Yii::t("contract","update")."</a></div></div>";
            }else{
                echo $form->fileField($model, 'image_user',
                    array('readonly'=>($readonly),"class"=>"file-update form-control")
                );
            }
        }
        ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'image_code',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php
        if($readonly){
            if(empty($model->image_code)){
                echo "<div class='form-control-static'>無</div>";
            }else{
                echo "<div class='form-control-static'><img class='openBigImg' height='80px' src='".$model->image_code."'></div>";
            }
        }else{
            if(!empty($model->image_code)){
                echo $form->fileField($model, 'image_code',
                    array('readonly'=>($readonly),"class"=>"file-update form-control hide")
                );
                echo "<div class='media fileImgShow'><div class='media-left'><img height='80px' src='".$model->image_code."'></div>
                        <div class='media-body media-bottom'><a>".Yii::t("contract","update")."</a></div></div>";
            }else{
                echo $form->fileField($model, 'image_code',
                    array('readonly'=>($readonly),"class"=>"file-update form-control")
                );
            }
        }
        ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'image_work',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php
        if($readonly){
            if(empty($model->image_work)){
                echo "<div class='form-control-static'>無</div>";
            }else{
                echo "<div class='form-control-static'><img class='openBigImg' height='80px' src='".$model->image_work."'></div>";
            }
        }else{
            if(!empty($model->image_work)){
                echo $form->fileField($model, 'image_work',
                    array('readonly'=>($readonly),"class"=>"file-update form-control hide")
                );
                echo "<div class='media fileImgShow'><div class='media-left'><img height='80px' src='".$model->image_work."'></div>
                        <div class='media-body media-bottom'><a>".Yii::t("contract","update")."</a></div></div>";
            }else{
                echo $form->fileField($model, 'image_work',
                    array('readonly'=>($readonly),"class"=>"file-update form-control")
                );
            }
        }
        ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'image_other',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php
        if($readonly){
            if(empty($model->image_other)){
                echo "<div class='form-control-static'>無</div>";
            }else{
                echo "<div class='form-control-static'><img height='80px' class='openBigImg' src='".$model->image_other."'></div>";
            }
        }else{
            if(!empty($model->image_other)){
                echo $form->fileField($model, 'image_other',
                    array('readonly'=>($readonly),"class"=>"file-update form-control hide")
                );
                echo "<div class='media fileImgShow'><div class='media-left'><img height='80px' src='".$model->image_other."'></div>
                        <div class='media-body media-bottom'><a>".Yii::t("contract","update")."</a></div></div>";
            }else{
                echo $form->fileField($model, 'image_other',
                    array('readonly'=>($readonly),"class"=>"file-update form-control")
                );
            }
        }
        ?>
    </div>
</div>

<script>
    $(function ($) {
        $("body").append('<div class="modal fade text-center" style="padding-top: 30px;" id="bigImgDiv"></div>');
        $("body").delegate(".openBigImg,.fileImgShow img","click",function () {
            var imgSrc = $(this).attr("src");
            var width = $(this).width();
            var height = $(this).height();
            var max_width= $(window).width()-100;
            var max_height= $(window).height()-100;
            var new_width = width/height*max_height;
            var new_height = height/width*new_width;
            if(new_width>max_width){
                new_width = max_width;
                new_height = height/width*new_width;
            }
            if(new_height>max_height){
                new_height = max_height;
                new_width = width/height*new_height;
            }
            $('#bigImgDiv').html("<img src='"+imgSrc+"' height='"+new_height+"px' width='"+new_width+"px'>");
            $('#bigImgDiv').modal('show');
        });
    })
</script>