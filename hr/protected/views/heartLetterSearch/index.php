<?php
$this->pageTitle=Yii::app()->name . ' - heartLetterSearch';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'heartLetterSearch-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','Search Heart letter'); ?></strong>
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
    <?php
    $search = array(
        'employee_code',
        'employee_name',
        'city'
    );
    $search_add_html="";
    $modelName = get_class($model);
    $search_add_html .= TbHtml::textField($modelName.'[searchTimeStart]',$model->searchTimeStart,
        array('size'=>15,'placeholder'=>Yii::t('misc','Start Date'),"class"=>"form-control","id"=>"start_time"));
    $search_add_html.="<span>&nbsp;&nbsp;-&nbsp;&nbsp;</span>";
    $search_add_html .= TbHtml::textField($modelName.'[searchTimeEnd]',$model->searchTimeEnd,
        array('size'=>15,'placeholder'=>Yii::t('misc','End Date'),"class"=>"form-control","id"=>"end_time"));

    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('contract','Heart letter list'),
        'model'=>$model,
        'viewhdr'=>'//heartLetterSearch/_listhdr',
        'viewdtl'=>'//heartLetterSearch/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search_add_html'=>$search_add_html,
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

<?php
$js = "
$('#start_time').datepicker({autoclose: true, format: 'yyyy/mm/dd',language: 'zh_cn'});
$('#end_time').datepicker({autoclose: true, format: 'yyyy/mm/dd',language: 'zh_cn'});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genTableRowClick();
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

