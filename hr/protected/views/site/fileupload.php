<?php
	$doc = new DocMan($doctype,$model->id,get_class($model));

	$ftrbtn = array();
	if (!$ronly) $ftrbtn[] = TbHtml::button(Yii::t('dialog','Upload'), array('id'=>$doc->uploadButtonName,));
	$ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('id'=>$doc->closeButtonName,'data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY));
	
	$this->beginWidget('bootstrap.widgets.TbModal', array(
					'id'=>$doc->widgetName,
					'header'=>$header,
					'footer'=>$ftrbtn,
					'show'=>false,
				));
?>
<div class="box" id="file-list" style="max-height: 300px; overflow-y: auto;">
	<table id="<?php echo $doc->tableName; ?>" class="table table-hover">
		<thead>
			<tr><th></th><th><?php echo Yii::t('dialog','File Name');?></th><th><?php echo Yii::t('dialog','Date');?></th></tr>
		</thead>
		<tbody>
<?php
if($model->scenario=='new'){
    if($model->docMasterId[strtolower($doc->docType)]>0){
        $doc->masterId = $model->docMasterId[strtolower($doc->docType)];
    }
}
if(get_class($model)=="HistoryForm"&&empty($model->id)){
    if($model->docMasterId[strtolower($doc->docType)]>0){
        $doc->masterId = $model->docMasterId[strtolower($doc->docType)];
    }
}
if(!isset($delBtn)){
    $delBtn = true;
}
echo $doc->genTableFileList($ronly,$delBtn);
?>
		</tbody>
	</table>
</div>
<?php
	echo CHtml::hiddenField(get_class($model).'[removeFileId]['.strtolower($doc->docType).']',$model->removeFileId[strtolower($doc->docType)], array('id'=>get_class($model).'_removeFileId_'.strtolower($doc->docType),));
?>				
<div id="inputFile">
<?php
if (!$ronly) {
	$this->widget('CMultiFileUpload', array(
		'name'=>$doc->inputName,
		'model'=>$model,
		'attribute'=>'files',
		'accept'=>'jpg|jpeg|gif|png|xlsx|xls|docx|doc|pdf|tif',
		'remove'=>Yii::t('dialog','Remove'),
		'file'=>' $file',
		'options'=>array(
			'list'=>'#'.$doc->listName,
			//'onFileSelect'=>'function(e, v, m){ console.log(e);console.log(v);console.log(m);alert("onFileSelect - "+v); }',
			'onFileSelect'=>'function(e, v, m){
			    var fileSize = e.files[0].size;
			    var fileType = e.files[0].type;
			    if(fileType.indexOf("image")!=-1){
			        if(fileSize>=4*1024*1024){
			            alert("上传的图片不能大于4M");
			            $(e).val("");
			            return false;
			        }
			    }
			 }',
//			'afterFileSelect'=>'function(e, v, m){ $("ServiceFrom_files").attr("value","00"); }',
//        'onFileAppend'=>'function(e, v, m){ alert("onFileAppend - "+v) }',
//        'afterFileAppend'=>'function(e, v, m){ alert("afterFileAppend - "+v) }',
//        'onFileRemove'=>'function(e, v, m){ alert("onFileRemove - "+v) }',
//        'afterFileRemove'=>'function(e, v, m){ alert("afterFileRemove - "+v) }',
		),
//		'htmlOptions'=>array(
//			'style'=>'height:100%; position:absolute; top:0; right:10; z-index:99; ',
//		),
	));
}
?>
</div>
<div id="<?php echo $doc->listName; ?>" style="max-height: 100px; overflow-y: auto;">
</div>

<?php
	$this->endWidget(); 
?>
