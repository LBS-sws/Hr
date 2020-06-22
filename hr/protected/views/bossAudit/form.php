<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('bossAudit/index'));
}
$this->pageTitle=Yii::app()->name . ' - Boss Apply Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'bossAudit-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<style>
    .table-responsive th{white-space: nowrap;}
    .table-responsive>table{table-layout:fixed}
</style>
<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('contract','Boss Apply Form'); ?></strong>
	</h1>
</section>

<section class="content">
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('bossAudit/index')));
		?>

        <?php if ($model->scenario!='view'): ?>
            <?php if ($model->status_type == 1): ?>
                <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','Audit'), array(
                    'submit'=>Yii::app()->createUrl('bossAudit/audit')));
                ?>
                <?php
                echo TbHtml::button('<span class="fa fa-mail-reply-all"></span> '.Yii::t('contract','Rejected'), array(
                    'name'=>'btnJect','id'=>'btnJect','data-toggle'=>'modal','data-target'=>'#jectdialog'));
                ?>
            <?php endif ?>
        <?php endif; ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'status_type'); ?>

           <?php if ($model->status_type == 3): ?>
                <div class="form-group has-error">
                    <?php echo $form->labelEx($model,'reject_remark',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-6 error">
                        <?php echo $form->textArea($model, 'reject_remark',
                            array('readonly'=>(true),"rows"=>4)
                        ); ?>
                    </div>
                </div>
               <legend>&nbsp;</legend>
           <?php endif; ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->textField($model, 'code',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->textField($model, 'name',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'audit_year',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <div class="input-group">
                        <?php echo $form->textField($model, 'audit_year',
                            array('readonly'=>(true))
                        ); ?>
                        <span class="input-group-addon"><?php echo Yii::t("contract"," year");?></span>
                    </div>
                </div>
            </div>
            <legend><?php echo Yii::t("contract","review number");?>：
                <span id="sum_label">
                    <?php echo $model->results_a."*50% + ".$model->results_b."*35% + ".$model->results_c."% = ".$model->results_sum."%";?>
                </span>
            </legend>
            <?php
            $bossApplyModel = new BossApplyForm();
            $tabs = $bossApplyModel->getTabList($model);
            $this->widget('bootstrap.widgets.TbTabs', array(
                'tabs'=>$tabs,
            ));
            ?>
		</div>
	</div>
</section>
<script>
    function resetTableSum() {
        var sum = 0;
        var sum_a = 0;
        var sum_b = 0;
        var sum_c = (isNaN($("#three_sum").val())||$("#three_sum").val()=='')?0:parseFloat($("#three_sum").val());
        $('#table_id_BossReviewA').find('input[name$="[one_12]"]').each(function () {
            sum_a+=$(this).val()==''?0:parseFloat($(this).val());
        });
        $('#table_id_BossReviewB').find('input[name$="[two_9]"]').each(function () {
            sum_b+=$(this).val()==''?0:parseFloat($(this).val());
        });
        sum = sum_a*0.5+sum_b*0.35+sum_c;
        sum_a = sum_a.toFixed(2);
        sum_b = sum_b.toFixed(2);
        sum = sum.toFixed(2);
        $("#sum_label").text(sum_a+"*50% + "+sum_b+"*35% + "+sum_c+"% = "+sum+"%");
    }
</script>
<?php
$this->renderPartial('//site/ject',array('model'=>$model,'form'=>$form,'rejectName'=>"reject_remark",'submit'=>Yii::app()->createUrl('bossAudit/reject')));
?>
<?php
$js = "
resetTableSum();
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

