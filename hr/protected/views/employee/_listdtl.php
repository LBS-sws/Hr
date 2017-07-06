<tr class='clickable-row' data-href='<?php echo $this->getLink('ZA03', 'employee/edit', 'employee/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->needHrefButton('ZA03', 'employee/edit', 'edit', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['code']; ?></td>
    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['phone']; ?></td>
	<td><?php echo $this->record['position']; ?></td>
	<td><?php echo $this->record['company_id']; ?></td>
</tr>
