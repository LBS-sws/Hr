<tr>
	<th>
		<?php echo TbHtml::link($this->getLabelName('code').$this->drawOrderArrow('b.code'),'#',$this->createOrderLink('salesStaff-list','b.code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('salesStaff-list','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('department_name').$this->drawOrderArrow('d.name'),'#',$this->createOrderLink('salesStaff-list','d.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('position_name').$this->drawOrderArrow('c.name'),'#',$this->createOrderLink('salesStaff-list','c.name'))
			;
		?>
	</th>
    <?php if (Yii::app()->user->validRWFunction('SR01')): ?>
	<th width="1%"></th>
    <?php endif; ?>
</tr>
