<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('set_name').$this->drawOrderArrow('set_name'),'#',$this->createOrderLink('configSystem-list','set_name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('set_value').$this->drawOrderArrow('set_value'),'#',$this->createOrderLink('configSystem-list','set_value'))
			;
		?>
	</th>
</tr>
