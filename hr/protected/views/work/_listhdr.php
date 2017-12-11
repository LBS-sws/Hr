<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('work_code').$this->drawOrderArrow('work_code'),'#',$this->createOrderLink('work-list','work_code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee_id').$this->drawOrderArrow('employee_id'),'#',$this->createOrderLink('work-list','employee_id'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('work_type').$this->drawOrderArrow('work_type'),'#',$this->createOrderLink('work-list','work_type'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('start_time').$this->drawOrderArrow('start_time'),'#',$this->createOrderLink('work-list','start_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('end_time').$this->drawOrderArrow('end_time'),'#',$this->createOrderLink('work-list','end_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('log_time').$this->drawOrderArrow('log_time'),'#',$this->createOrderLink('work-list','log_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('status'),'#',$this->createOrderLink('work-list','status'))
			;
		?>
	</th>
</tr>
