<tr class='clickable-row' data-href='<?php echo $this->getLink('A01', 'staff/edit', 'staff/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->needHrefButton('A01', 'staff/edit', 'edit', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['city_name']; ?></td>
	<td><?php echo $this->record['code']; ?></td>
	<td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['position']; ?></td>
	<td><?php echo $this->record['email']; ?></td>
    <td><?php echo $this->needHrefButton('A01', 'staffdetail/index', 'view', array('index'=>$this->record['id'])); ?></td>
</tr>
