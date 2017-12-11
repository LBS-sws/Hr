<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('leave_code').$this->drawOrderArrow('leave_code'),'#',$this->createOrderLink('leave-list','leave_code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee_id').$this->drawOrderArrow('employee_id'),'#',$this->createOrderLink('leave-list','employee_id'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('vacation_id').$this->drawOrderArrow('vacation_id'),'#',$this->createOrderLink('leave-list','vacation_id'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('start_time').$this->drawOrderArrow('start_time'),'#',$this->createOrderLink('leave-list','start_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('end_time').$this->drawOrderArrow('end_time'),'#',$this->createOrderLink('leave-list','end_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('log_time').$this->drawOrderArrow('log_time'),'#',$this->createOrderLink('leave-list','log_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('status'),'#',$this->createOrderLink('leave-list','status'))
			;
		?>
	</th>
</tr>
