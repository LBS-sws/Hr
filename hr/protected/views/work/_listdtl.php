<tr class='clickable-row<?php echo $this->record['style']; ?>' data-href='<?php echo $this->getLink('ZA05', 'work/edit', 'work/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->needHrefButton('ZA05', 'work/edit','work/view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['work_code']; ?></td>
    <td><?php echo $this->record['employee_code']; ?></td>
    <td><?php echo $this->record['employee_name']; ?></td>
    <?php if (Yii::app()->user->validFunction('ZR03')): ?>
        <td><?php echo $this->record['city']; ?></td>
    <?php endif; ?>
    <td><?php echo $this->record['work_type']; ?></td>
    <td><?php echo $this->record['start_time']; ?></td>
    <td><?php echo $this->record['end_time']; ?></td>
    <td><?php echo $this->record['log_time']; ?></td>
    <td><?php echo $this->record['status']; ?></td>
</tr>
