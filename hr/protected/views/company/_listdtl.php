<tr class='clickable-row' data-href='<?php echo $this->getLink('ZA02', 'company/edit', 'company/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->needHrefButton('ZA02', 'company/edit', 'edit', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['head']; ?></td>
	<td><?php echo $this->record['agent']; ?></td>
</tr>
