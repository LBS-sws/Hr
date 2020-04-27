<?php
$this->pageTitle=Yii::app()->name . ' - salesStaff';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'salesStaff-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo $model->getGroupListStr("group_name"); ?></strong>
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
    <div class="box">
        <div class="box-body">
            <div class="btn-group" role="group">
            <?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
                'submit'=>Yii::app()->createUrl('SalesGroup/index')));
            ?>
            </div>
            <div class="btn-group pull-right" role="group">
                <?php
                //var_dump(Yii::app()->session['rw_func']);
                if (Yii::app()->user->validRWFunction('SR01'))
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('contract','Add Staff Group'), array(
                           'data-toggle'=>'modal','data-target'=>'#addSalesStaff'
                    ));
                ?>
            </div>
        </div>
    </div>
    <?php
    $search = array(
        'code',
        'name',
        'department_name',
        'position_name',
    );
    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('contract','Sales Staff List')." - ".$model->getGroupListStr("group_name"),
        'model'=>$model,
        'viewhdr'=>'//salesGroup/staff_listhdr',
        'viewdtl'=>'//salesGroup/staff_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search'=>$search,
    ));
    ?>
</section>
<?php
echo $form->hiddenField($model,'pageNum');
echo $form->hiddenField($model,'totalRow');
echo $form->hiddenField($model,'orderField');
echo $form->hiddenField($model,'orderType');
?>
<?php $this->endWidget(); ?>
<form class="form-horizontal" action="" id="staffForm" method="post">
<?php
$ftrbtn = array();
$submit = Yii::app()->createUrl('salesGroup/addStaff');
$ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('id'=>'btnWFClose','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_DEFAULT,"class"=>"pull-left"));
$ftrbtn[] = TbHtml::button(Yii::t('dialog','OK'), array('id'=>'btnWFSubmit','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY,'submit' => $submit));
$this->beginWidget('bootstrap.widgets.TbModal', array(
    'id'=>'addSalesStaff',
    'header'=>Yii::t('contract','Add Staff Group'),
    'footer'=>$ftrbtn,
    'show'=>false,
));
?>

<div class="form-group">
    <?php echo $form->hiddenField($model, 'index'); ?>
    <?php echo $form->labelEx($model,"employee_id",array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-9">
        <?php echo $form->dropDownList($model, "employee_id",$model->getSalesList(),
            array('readonly'=>($model->scenario=='view'))
        );
        ?>
    </div>
</div>

<?php
$this->endWidget();
?>

</form>

<?php
$this->renderPartial('//site/removedialog');
?>
<?php
$js = Script::genTableRowClick();
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

