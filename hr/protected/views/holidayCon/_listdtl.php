
<tr class='clickable-row' data-href='<?php echo $this->getLink($this->record['acc'], 'holidayCon/edit', 'holidayCon/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->needHrefButton($this->record['acc'], 'holidayCon/edit', 'edit', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['z_index']; ?></td>
</tr>
