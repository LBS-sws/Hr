<tr>
	<th></th>
	<th>
		<?php
        echo TbHtml::link($this->model->getTypeName().$this->getLabelName('name').$this->drawOrderArrow('name'),'#',$this->createOrderLink('holidayCon-list','name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('city'),'#',$this->createOrderLink('holidayCon-list','city'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('z_index').$this->drawOrderArrow('z_index'),'#',$this->createOrderLink('holidayCon-list','z_index'))
			;
		?>
	</th>
</tr>
