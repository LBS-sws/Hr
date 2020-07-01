<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('kpi_name').$this->drawOrderArrow('kpi_name'),'#',$this->createOrderLink('bossKPI-list','kpi_name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('sum_bool').$this->drawOrderArrow('sum_bool'),'#',$this->createOrderLink('bossKPI-list','sum_bool'))
			;
		?>
	</th>
</tr>
