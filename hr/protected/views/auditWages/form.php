<?php
$this->pageTitle=Yii::app()->name . ' - AuditWages Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'autitWages-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
    <h1>
        <strong>
            <?php
            echo Yii::t('app','Wages Audit');
            ?>
        </strong>
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
                    'submit'=>Yii::app()->createUrl('auditWages/index')));
                ?>

                <?php if ($model->scenario!='view' && $model->wages_status == 2): ?>
                    <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('contract','Audit'), array(
                        'submit'=>Yii::app()->createUrl('auditWages/audit')));
                    ?>
                <?php endif ?>
            </div>
            <div class="btn-group pull-right" role="group">
                <?php
                    //流程
                    echo TbHtml::button('<span class="fa fa-file-text-o"></span> '.Yii::t('contract','Wages History'), array(
                        'name'=>'btnFlow','id'=>'btnFlow','data-toggle'=>'modal','data-target'=>'#flowinfodialog'));
                 ?>
                <?php if ($model->scenario!='view' && $model->wages_status == 2): ?>
                    <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('contract','Rejected'), array(
                        'submit'=>Yii::app()->createUrl('auditWages/reject')));
                    ?>
                <?php endif ?>
            </div>
        </div></div>

    <div class="box box-info">
        <div class="box-body">
            <?php echo $form->hiddenField($model, 'id'); ?>
            <?php echo $form->hiddenField($model, 'employee_id'); ?>
            <?php echo $form->hiddenField($model, 'wages_status'); ?>
            <?php echo $form->hiddenField($model, 'wages_head'); ?>
            <?php
                if(!empty($model->wages_head)){
                    $model->wages_head = explode(",",$model->wages_head);
                }
            ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'time',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textField($model, 'time',
                        array('readonly'=>true)
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textField($model, 'name',
                        array('readonly'=>true)
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textField($model, 'code',
                        array('readonly'=>true)
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'phone',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textField($model, 'phone',
                        array('readonly'=>true)
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'position',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textField($model, 'position',
                        array('readonly'=>true)
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'wages_body',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-8">

                    <?php if (!empty($model->wages_head)): ?>
                        <table class="table table-bordered table-striped" id="WagesTable">
                            <thead>
                            <tr>
                                <?php
                                $tableBody = "";
                                foreach ($model->wages_head as $key =>  $con_name){
                                    $text = "";
                                    if (!empty($model->wages_body[$key])){
                                        $text = $model->wages_body[$key];
                                    }
                                    echo "<th>".$con_name."</th>";
                                    $bool = ($model->wages_status == 2 ||$model->wages_status == 3||$model->wages_status == 4||$model->scenario=='view');
                                    $tableBody.="<td>".TbHtml::textField("AuditWagesForm[wages_body][]",$text,array('readonly'=>$bool))."</td>";
                                }
                                ?>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <?php echo $tableBody;?>
                            </tr>
                            </tbody>
                        </table>
                        <?php else:?>
                        <div class="form-control-static text-danger">該員工沒有工資單</div>
                    <?php endif; ?>

                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'just_remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'just_remark',
                        array('rows'=>3,'readonly'=>($model->wages_status != 2 || $model->scenario == "view"))
                    ); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$this->renderPartial('//site/wageslist',array('model'=>$model));
?>
<?php


$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

