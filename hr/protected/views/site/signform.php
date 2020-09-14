<legend><?php echo  Yii::t("contract","courier detail");?></legend>
<div class="form-group">
    <?php echo $form->labelEx($model,'sign_type',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-5">
        <?php echo $form->textField($model, 'sign_type',
            array('readonly'=>(true))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'send_date',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <?php echo $form->textField($model, 'send_date',
                array('class'=>'form-control pull-right','readonly'=>($readonly),"id"=>"send_date"));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'courier_str',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-5">
        <?php echo $form->textField($model, 'courier_str',
            array('readonly'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'courier_code',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-5">
        <?php echo $form->textField($model, 'courier_code',
            array('readonly'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-5">
        <?php
        echo $form->textArea($model, 'remark',
            array('rows'=>4,'cols'=>80,'maxlength'=>1000,'readonly'=>($readonly),)
        );
        ?>
    </div>
</div>
<legend><?php echo  Yii::t("contract","Staff View");?></legend>
<div class="form-group">
    <?php echo $form->labelEx($model,'code',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'code',
            array('readonly'=>(true))
        ); ?>
    </div>
    <!--分割-->
    <?php echo $form->labelEx($model,'name',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'name',
            array('readonly'=>(true))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'city_name',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'city_name',
            array('readonly'=>(true))
        ); ?>
    </div>
    <!--分割-->
    <?php echo $form->labelEx($model,'company_id',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'company_id',
            array('readonly'=>(true))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'user_card',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'user_card',
            array('readonly'=>(true))
        ); ?>
    </div>
    <!--分割-->
    <?php echo $form->labelEx($model,'phone',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'phone',
            array('readonly'=>(true))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'department',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'department',
            array('readonly'=>(true))
        ); ?>
    </div>
    <!--分割-->
    <?php echo $form->labelEx($model,'position',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'position',
            array('readonly'=>(true))
        ); ?>
    </div>
</div>

<div class="form-group">
    <?php echo $form->label($model,'fix_time',array('class'=>"col-sm-2 control-label",'required'=>true)); ?>
    <div class="col-sm-5">
        <?php echo $form->inlineRadioButtonList($model, 'fix_time',EmployList::getFixTimeList(),
            array('disabled'=>(true),'class'=>"fixTime")
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->label($model,'start_time',array('class'=>"col-sm-2 control-label",'required'=>true)); ?>
    <div class="col-sm-3">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <?php echo $form->textField($model, 'start_time',
                array('class'=>'form-control pull-right','readonly'=>(true),));
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
            }
            echo $form->textField($model, 'end_time',
                array('class'=>'form-control pull-right','readonly'=>(true),));
            ?>
        </div>
    </div>
</div>