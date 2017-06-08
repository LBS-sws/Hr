<tr class='clickable-row' data-href='<?php echo $this->getLink('A01', 'staffdetail/edit', 'staffdetail/edit', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->needHrefButton('A01', 'staffdetail/edit', 'edit', array('index'=>$this->record['id'])); ?></td>

    <td><?php echo $this->record['city_name']; ?></td>
	<td><?php echo $this->record['code']; ?></td>
	<td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['position']; ?></td>
	<td><?php echo $this->record['email']; ?></td>
	<td <?php if ($this->record['status'] == "running"){ echo " class='text-primary'";}else{ echo " class='text-warning'";} ?>><?php echo Yii::t("staff",$this->record['status']); ?></td>
</tr>