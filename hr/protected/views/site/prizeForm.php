
<div class="form-group">
    <?php echo $form->labelEx($model,'city',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownList($model, 'city',PrizeForm::getSingleCityToList(),
            array('disabled'=>($model->getInputBool()),'id'=>"city")
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'employee_id',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownList($model, 'employee_id',PrizeForm::getEmployeeList($model->city),
            array('disabled'=>($model->getInputBool()),'id'=>"staff")
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'prize_date',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <?php echo $form->textField($model, 'prize_date',
                array('readonly'=>($model->getInputBool()),"id"=>"prize_date")
            );
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'prize_num',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'prize_num',
            array('readonly'=>($model->getInputBool()))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'prize_pro',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownList($model, 'prize_pro',PrizeList::getPrizeList(),
            array('disabled'=>($model->getInputBool()))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'customer_name',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-5">
        <?php echo $form->textField($model, 'customer_name',
            array('readonly'=>($model->getInputBool()))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'contact',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'contact',
            array('readonly'=>($model->getInputBool()))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'phone',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'phone',
            array('readonly'=>($model->getInputBool()))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'posi',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'posi',
            array('readonly'=>($model->getInputBool()))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'photo1',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php
        if($model->getInputBool()){
            if(empty($model->photo1)){
                echo "<div class='form-control-static'>無</div>";
            }else{
                echo "<div class='form-control-static'><img class='openBigImg' height='80px' src='".$model->photo1."'></div>";
            }
        }else{
            if(!empty($model->photo1)){
                echo $form->fileField($model, 'photo1',
                    array('readonly'=>($model->getInputBool()),"class"=>"file-update form-control hide")
                );
                echo "<div class='media fileImgShow'><div class='media-left'><img height='80px' src='".$model->photo1."'></div>
                        <div class='media-body media-bottom'><a>".Yii::t("contract","update")."</a></div></div>";
            }else{
                echo $form->fileField($model, 'photo1',
                    array('readonly'=>($model->getInputBool()),"class"=>"file-update form-control")
                );
            }
        }
        ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'photo2',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php
        if($model->getInputBool()){
            if(empty($model->photo2)){
                echo "<div class='form-control-static'>無</div>";
            }else{
                echo "<div class='form-control-static'><img class='openBigImg' height='80px' src='".$model->photo2."'></div>";
            }
        }else{
            if(!empty($model->photo2)){
                echo $form->fileField($model, 'photo2',
                    array('readonly'=>($model->getInputBool()),"class"=>"file-update form-control hide")
                );
                echo "<div class='media fileImgShow'><div class='media-left'><img height='80px' src='".$model->photo2."'></div>
                        <div class='media-body media-bottom'><a>".Yii::t("contract","update")."</a></div></div>";
            }else{
                echo $form->fileField($model, 'photo2',
                    array('readonly'=>($model->getInputBool()),"class"=>"file-update form-control")
                );
            }
        }
        ?>
    </div>
</div>

<div class="form-group">
    <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-5">
        <?php echo $form->textArea($model, 'remark',
            array('rows'=>3,'readonly'=>($model->getInputBool()))
        ); ?>
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