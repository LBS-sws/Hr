<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('name'),'#',$this->createOrderLink('company-list','name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('head').$this->drawOrderArrow('head'),'#',$this->createOrderLink('company-list','head'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('agent').$this->drawOrderArrow('agent'),'#',$this->createOrderLink('company-list','agent'))
			;
		?>
	</th>
</tr>
