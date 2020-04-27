<?php
$this->pageTitle=Yii::app()->name . ' - salesReview';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'salesReview-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','Sales Review Search'); ?></strong>
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
        'group_name',
    );
    $search_add_html="";
    $modelName = get_class($model);
    if (!Yii::app()->user->isSingleCity()){
        //城市搜索
    }
    $search_add_html .= TbHtml::dropDownList($modelName.'[year]',$model->year,ReviewAllotList::getYearList(),
        array("class"=>"form-control"));
    $search_add_html.="<span>&nbsp;&nbsp;&nbsp;&nbsp;</span>";
    $search_add_html .= TbHtml::dropDownList($modelName.'[year_type]',$model->year_type,ReviewAllotList::getYearTypeList(),
        array("class"=>"form-control"));

    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('contract','Sales Group List'),
        'model'=>$model,
        'viewhdr'=>'//salesReview/_listhdr',
        'viewdtl'=>'//salesReview/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search'=>$search,
        'search_add_html'=>$search_add_html,
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
$js = Script::genTableRowClick();
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

