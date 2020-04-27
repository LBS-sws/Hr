<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('group_name').$this->drawOrderArrow('a.group_name'),'#',$this->createOrderLink('salesReview-list','a.group_name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('staff_num').$this->drawOrderArrow('staff_num'),'#',$this->createOrderLink('salesReview-list','staff_num'))
			;
		?>
	</th>
</tr>
