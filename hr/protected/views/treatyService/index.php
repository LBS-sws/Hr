<?php
$this->pageTitle=Yii::app()->name . ' - TreatyService';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'treatyService-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('treaty','Treaty Hint'); ?></strong>
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
		<?php 
			if (Yii::app()->user->validRWFunction('TH01'))
				echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('treaty','add treaty'), array(
					'submit'=>Yii::app()->createUrl('treatyService/new'),
				)); 
		?>
	</div>
	</div></div>
    <?php
    $searchArr=array(
        'treaty_code',
        'treaty_name'
    );
    if(!Yii::app()->user->isSingleCity()){
        $searchArr[]='city';
    }
    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('app','Treaty Service'),
        'model'=>$model,
        'viewhdr'=>'//treatyService/_listhdr',
        'viewdtl'=>'//treatyService/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search'=>$searchArr,
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
