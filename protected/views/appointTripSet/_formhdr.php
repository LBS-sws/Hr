<tr>
    <th width="50%">
        <?php echo TbHtml::label($this->getLabelName('audit_user'), false); ?>
    </th>
	<th width="50%">
		<?php echo TbHtml::label($this->getLabelName('z_index'), false); ?>
	</th>
	<th>
		<?php echo Yii::app()->user->validRWFunction('ZC19') ?
				TbHtml::Button('+',array('id'=>'btnAddRow','title'=>Yii::t('misc','Add'),'size'=>TbHtml::BUTTON_SIZE_SMALL))
				: '&nbsp;';
		?>
	</th>
</tr>
