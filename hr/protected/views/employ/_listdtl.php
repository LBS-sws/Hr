<tr class='clickable-row <?php echo $this->record['style'];?>' data-href='<?php echo $this->getLink('ZE01', 'employ/edit', 'employ/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->needHrefButton('ZE01', 'employ/edit', 'edit', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['code']; ?></td>
    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['phone']; ?></td>
	<td><?php echo $this->record['position']; ?></td>
	<td><?php echo $this->record['company_id']; ?></td>
	<td><?php echo $this->record['staff_status']; ?></td>
</tr>
