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

	<div class="btn-group pull-right" role="group">
        <?php if ($model->scenario!='new'): ?>
    <?php if ($model->staff_status == 4): ?>
            <?php echo TbHtml::button('<span class="fa fa-file-word-o"></span> '.Yii::t('contract','Down'),array(
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
                    <?php echo $form->dropDownList($model, 'sex',EmployList::getSexList(),
                        array('disabled'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
			</div>

			<div class="form-group">
                <?php echo $form->labelEx($model,'staff_type',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'staff_type',EmployList::getStaffTypeList(),
                        array('disabled'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'staff_leader',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'staff_leader',EmployList::getStaffLeaderList(),
                        array('disabled'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
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
                    <?php echo $form->dropDownList($model, 'age',EmployList::getAgeList(),
                        array('disabled'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
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
                <?php echo $form->labelEx($model,'wage',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->numberField($model, 'wage',
                        array('min'=>0,'readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
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
                    <?php echo $form->dropDownList($model, 'health',EmployList::getHealthList(),
                        array('disabled'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'department',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'department',DeptForm::getDeptAllList(0),
                        array('disabled'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)),"class"=>"depart")
                    ); ?>
                </div>
                <!--分割-->
                <?php echo $form->labelEx($model,'position',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php
                    $model_class = get_class($model);
                    $departmentList = DeptForm::getDeptOneAllList();
                    if($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)){
                        echo "<select class='depart form-control' name='".$model_class."[position]' disabled>";
                    }else{
                        echo "<select class='depart form-control' name='".$model_class."[position]'>";
                    }
                     foreach ($departmentList as $key =>$value){
                         if($model->position == $key){
                             echo "<option value='$key' data-type='".$value["type"]."' selected>".$value["name"]."</option>";
                         }else{
                             echo "<option value='$key' data-type='".$value["type"]."'>".$value["name"]."</option>";
                         }
                     }
                     echo "</select>";
                    ?>
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
                    <?php echo $form->labelEx($model,'test_length',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->dropDownList($model, 'test_length',EmployList::getMonthList(),
                            array('class'=>'test_add_time','disabled'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
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
                                array('class'=>'test_add_time pull-right','readonly'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)),));
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
                    <?php echo $form->dropDownList($model, 'education',EmployList::getEducationList(),
                        array('disabled'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
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
                <!--分割-->
                <?php echo $form->labelEx($model,'year_day',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'year_day',
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
                    <?php echo $form->dropDownList($model, 'price1',WagesForm::getWagesList(),
                        array('disabled'=>($model->scenario=='view'||($model->staff_status != 1 && $model->staff_status != 3)))
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
    
    //工資單變化
    DEFINE_WAGES =".json_encode($model->price3).";
    var objWagesPar = {'url':'".Yii::app()->createUrl('wages/ajaxGetWageType')."','str':'".Yii::t("contract","Wages Type")."','form':'EmployForm'};
    $('#EmployForm_price1').on('change',objWagesPar,ajaxWagesChange).trigger('change');
    
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
            var text = $(this).text();
            var value = $(this).attr('value');
            if(typeof DEPARTLIST[key] == 'undefined'){
                DEPARTLIST[key] = new Array();
            }
            DEPARTLIST[key].push({'text':text,'value':value});
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
                            html = '<option value=\"'+DEPARTLIST[x][i]['value']+'\" selected>'+DEPARTLIST[x][i]['text']+'</option>';
                        }else{
                            html = '<option value=\"'+DEPARTLIST[x][i]['value']+'\">'+DEPARTLIST[x][i]['text']+'</option>';
                        }
                        $('.depart:last').append(html);
                    }
                }
            }
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
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
if ($model->scenario!='view') {
    $js = Script::genDatePicker(array(
        'EmployForm_birth_time',
        'EmployForm_entry_time',
        'EmployForm_start_time',
        'EmployForm_end_time',
        'EmployForm_test_start_time',
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

