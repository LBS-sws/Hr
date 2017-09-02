<tr class='clickable-row <?php echo $this->record['style'];?>' data-href='<?php echo $this->getLink('ZE04', 'history/form', 'history/form', array('index'=>$this->record['id'],'type'=>'view'));?>'>


    <td><?php echo $this->needHrefButton('ZE04', 'history/form', 'edit', array('index'=>$this->record['id'],'type'=>'view')); ?></td>



    <td><?php echo $this->record['code']; ?></td>
    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['phone']; ?></td>
    <td><?php echo $this->record['position']; ?></td>
    <td><?php echo $this->record['company_id']; ?></td>
    <td><?php echo Yii::t("contract",$this->record['operation']); ?></td>
    <td><?php echo $this->record['staff_status']; ?></td>
</tr>
