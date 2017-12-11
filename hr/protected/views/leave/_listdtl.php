<tr class='clickable-row<?php echo $this->record['style']; ?>' data-href='<?php echo $this->getLink('ZA06', 'leave/edit', 'leave/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->needHrefButton('ZA06', 'leave/edit', 'edit', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['leave_code']; ?></td>
    <td><?php echo $this->record['employee_id']; ?></td>
    <td><?php echo $this->record['vacation_id']; ?></td>
    <td><?php echo $this->record['start_time']; ?></td>
    <td><?php echo $this->record['end_time']; ?></td>
    <td><?php echo $this->record['log_time']; ?></td>
    <td><?php echo $this->record['status']; ?></td>
</tr>
