<?php
if (empty($model->id)&&$model->scenario != "new"){
    $this->redirect(Yii::app()->createUrl('dept/index',array("type"=>$model->type)));
}
$this->pageTitle=Yii::app()->name . ' - Dept Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'dept-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo $model->getTypeName().Yii::t('contract',' Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('dept/index',array("type"=>$model->type))));
		?>
        <?php if ($model->scenario == "edit"): ?>
            <?php echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                'submit'=>Yii::app()->createUrl('dept/new',array("type"=>$model->type))));
            ?>
        <?php endif ?>

        <?php if ($model->scenario!='view'): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('dept/save',array("type"=>$model->type))));
            ?>
            <?php if ($model->scenario=='edit'): ?>
                <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                        'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
                );
                ?>
            <?php endif; ?>
        <?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'type'); ?>

            <?php if ($model->type==1): ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label required"><?php echo Yii::t("contract","in department");?><span class="required">*</span></label>
                    <div class="col-sm-3">
                        <?php echo $form->dropDownList($model, 'dept_id',$model->getDeptAllListNoCity(),
                            array('readonly'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                </div>
            <?php endif; ?>
			<div class="form-group">
                <label class="col-sm-2 control-label required"><?php echo $model->getTypeName().Yii::t("contract"," Name");?><span class="required">*</span></label>
				<div class="col-sm-3">
					<?php echo $form->textField($model, 'name',
						array('readonly'=>($model->scenario=='view'))
					); ?>
				</div>
			</div>


            <?php if ($model->type==1): ?>
                <?php echo $form->hiddenField($model, 'city'); ?>
            <?php else: ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'city',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->dropDownList($model, 'city',WordForm::getCityListAll(),
                            array('disabled'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'sales_type',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->dropDownList($model, 'sales_type',DeptForm::getSalesType(),
                            array('disabled'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static">人事系统里仅仅只是员工信息裡面的关联（只是显示，没有功能），其它系统不知道。</p>
                    </div>
                </div>
            <?php endif; ?>

			<div class="form-group">
				<?php echo $form->labelEx($model,'z_index',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->numberField($model, 'z_index',
                        array('mim'=>0,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
			</div>
            <?php if ($model->type==1): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'dept_class',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->dropDownList($model, 'dept_class',EmployList::getStaffTypeList(),
                            array('disabled'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static">中央技术支援：如果是中央支援组的技术员，请选择服务</p>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($model->type==1): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'manager',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->dropDownList($model, 'manager',EmployList::getManagerList(),
                            array('disabled'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static">加班、请假专用：需要注意部门经理。如果是部门经理，一定要选择部门经理</p>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($model->type==1): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'technician',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->dropDownList($model, 'technician',EmployList::getTechnicianList(),
                            array('disabled'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static">锦旗：影响锦旗表单的参与人数。  测验系统：必须进行每年的测验</p>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($model->type==1): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'review_status',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->dropDownList($model, 'review_status',array(Yii::t("contract","not Participate"),Yii::t("contract","Participate")),
                            array('disabled'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static">优化人才评核专用：差異性評分需要對比同部門（必須已經分配評分的員工）的員工才能計算得分類型</p>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'review_type',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->dropDownList($model, 'review_type',$model->getReviewType(),
                            array('disabled'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static">优化人才评核专用：选择不同类型，评分的计算公式不一样</p>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'review_leave',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->dropDownList($model, 'review_leave',$model->getReviewLeave(),
                            array('disabled'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static">分配考核专用：1、选择地区时，选择评核人只能该地区可见。2、选择所有时，所有地区都可见</p>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'manager_type',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->dropDownList($model, 'manager_type',$model->getManagerTypeLeave(),
                            array('disabled'=>($model->scenario=='view'),"id"=>"manager_type")
                        ); ?>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static">销售系统专用，具体功能问销售系统负责人</p>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'manager_leave',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->dropDownList($model, 'manager_leave',array(Yii::t("contract","not Participate"),Yii::t("contract","Participate")),
                            array('disabled'=>($model->scenario=='view'),"id"=>'manager_leave')
                        ); ?>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static">销售系统专用，具体功能问销售系统负责人</p>
                    </div>
                </div>
            <?php endif; ?>

		</div>
	</div>
</section>

<?php
$this->renderPartial('//site/removedialog');
?>
<?php
$js="
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genDeleteData(Yii::app()->createUrl('dept/delete',array("type"=>$model->type)));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

