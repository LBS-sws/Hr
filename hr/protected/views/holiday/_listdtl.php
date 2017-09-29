
<tr class='clickable-row <?php echo $this->record['status']["style"]?>' data-href='<?php echo $this->getLink($this->record['acc'], 'holiday/edit', 'holiday/view',
    array('index'=>$this->record['id'],'type'=>$this->model->type,'only'=>$this->model->only));?>'>


	<td><?php echo $this->needHrefButton($this->record['acc'], 'holiday/edit', 'edit', array('index'=>$this->record['id'],'type'=>$this->model->type,'only'=>$this->model->only)); ?></td>



    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['holiday_name']; ?></td>
    <td><?php echo $this->record['start_time']; ?></td>
    <td><?php echo $this->record['end_time']; ?></td>
    <td><?php echo $this->record['status']["status"]; ?></td>
</tr>
