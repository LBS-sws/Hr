
<?php if (!empty($model->reject_cause)): ?>
    <div class="form-group has-error">
        <?php echo $form->labelEx($model,'reject_cause',array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-6">
            <?php echo $form->textArea($model, 'reject_cause',
                array('readonly'=>(true),"rows"=>4)
            ); ?>
        </div>
    </div>
<?php endif; ?>
<?php if ($model->scenario!='new'): ?>
    <div class="form-group">
        <?php echo $form->labelEx($model,'leave_code',array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-4">
            <?php echo $form->textField($model, 'leave_code',
                array('readonly'=>(true))
            ); ?>
        </div>
    </div>
<?php endif; ?>
<div class="form-group">
    <?php echo $form->labelEx($model,'employee_id',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-4">
        <?php echo $form->textField($model, 'employee_id',
            array('readonly'=>(true))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'vacation_id',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownList($model, 'vacation_id',LeaveForm::getLeaveTypeList($model->city),
            array('disabled'=>($model->getInputBool()),"id"=>"leave_type")
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'start_time',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <?php echo $form->textField($model, 'start_time',
                array('readonly'=>($model->getInputBool()),"id"=>"start_time")
            ); ?>
        </div>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'log_time',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <div class="input-group">
            <?php echo $form->numberField($model, 'log_time',
                array('disabled'=>($model->getInputBool()),"id"=>"log_time")
            ); ?>
            <span class="input-group-addon">天</span>
        </div>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'end_time',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <?php echo $form->textField($model, 'end_time',
                array('readonly'=>(true),"id"=>"end_time")
            ); ?>
        </div>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'leave_cause',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-6">
        <?php echo $form->textArea($model, 'leave_cause',
            array('readonly'=>($model->getInputBool()),"rows"=>4)
        ); ?>
    </div>
</div>

<?php if (!empty($model->area_lcu)): ?>
    <legend>&nbsp;</legend>
    <div class="form-group">
        <?php echo $form->labelEx($model,'area_lcu',array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-3">
            <?php echo $form->textField($model, 'area_lcu',
                array('readonly'=>(true))
            ); ?>
        </div>
        <?php echo $form->labelEx($model,'area_lcd',array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-3">
            <?php echo $form->textField($model, 'area_lcd',
                array('readonly'=>(true))
            ); ?>
        </div>
    </div>
<?php endif; ?>
<?php if (!empty($model->head_lcu)): ?>
    <div class="form-group">
        <?php echo $form->labelEx($model,'head_lcu',array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-3">
            <?php echo $form->textField($model, 'head_lcu',
                array('readonly'=>(true))
            ); ?>
        </div>
        <?php echo $form->labelEx($model,'head_lcd',array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-3">
            <?php echo $form->textField($model, 'head_lcd',
                array('readonly'=>(true))
            ); ?>
        </div>
    </div>
<?php endif; ?>