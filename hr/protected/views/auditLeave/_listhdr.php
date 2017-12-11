<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('leave_code').$this->drawOrderArrow('leave_code'),'#',$this->createOrderLink('auditLeave-list','leave_code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee_id').$this->drawOrderArrow('employee_id'),'#',$this->createOrderLink('auditLeave-list','employee_id'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('city'),'#',$this->createOrderLink('auditLeave-list','city'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('vacation_id').$this->drawOrderArrow('vacation_id'),'#',$this->createOrderLink('auditLeave-list','vacation_id'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('start_time').$this->drawOrderArrow('start_time'),'#',$this->createOrderLink('auditLeave-list','start_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('end_time').$this->drawOrderArrow('end_time'),'#',$this->createOrderLink('auditLeave-list','end_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('log_time').$this->drawOrderArrow('log_time'),'#',$this->createOrderLink('auditLeave-list','log_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('status'),'#',$this->createOrderLink('auditLeave-list','status'))
			;
		?>
	</th>
</tr>
