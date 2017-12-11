<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('work_code').$this->drawOrderArrow('work_code'),'#',$this->createOrderLink('auditWork-list','work_code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee_id').$this->drawOrderArrow('employee_id'),'#',$this->createOrderLink('auditWork-list','employee_id'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('city'),'#',$this->createOrderLink('auditWork-list','city'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('work_type').$this->drawOrderArrow('work_type'),'#',$this->createOrderLink('auditWork-list','work_type'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('start_time').$this->drawOrderArrow('start_time'),'#',$this->createOrderLink('auditWork-list','start_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('end_time').$this->drawOrderArrow('end_time'),'#',$this->createOrderLink('auditWork-list','end_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('log_time').$this->drawOrderArrow('log_time'),'#',$this->createOrderLink('auditWork-list','log_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('status'),'#',$this->createOrderLink('auditWork-list','status'))
			;
		?>
	</th>
</tr>
