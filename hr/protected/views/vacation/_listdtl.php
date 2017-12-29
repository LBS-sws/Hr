<tr class='clickable-row' data-href='<?php echo $this->getLink('ZC04', 'vacation/edit', 'vacation/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->needHrefButton('ZC04', 'vacation/edit', 'edit', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['only']; ?></td>
</tr>
