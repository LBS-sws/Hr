<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('city_name'),'#',$this->createOrderLink('staff-list','city_name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('code').$this->drawOrderArrow('code'),'#',$this->createOrderLink('staff-list','code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('name'),'#',$this->createOrderLink('staff-list','name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('position').$this->drawOrderArrow('position'),'#',$this->createOrderLink('staff-list','position'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('email').$this->drawOrderArrow('email'),'#',$this->createOrderLink('staff-list','email'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link(Yii::t('staff','status').$this->drawOrderArrow('change_type'),'#',$this->createOrderLink('staff-list','change_type'))
        ;
        ?>
    </th>
</tr>
