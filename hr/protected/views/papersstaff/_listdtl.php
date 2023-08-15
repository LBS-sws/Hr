<!-- 列表数据 -->

<tr class='clickable-row' data-href='<?php echo $this->getLink('PQ01', 'papersstaff/edit', 'papersstaff/edit', array('index'=>$this->record['id']));?>'>
<!--    --><?php //if (!Yii::app()->user->isSingleCity()) : ?>
<!--        <td class="name">--><?php //echo $this->record['city']; ?><!--</td>-->
<!--    --><?php //endif ?>

    <td class="city"><?php echo $this->record['city_name']; ?><?php echo $this->record['city']; ?></td>
    <td class="code"><?php echo $this->record['code']; ?></td>
    <td class="staffname"><?php echo $this->record['staffname']; ?></td>
    <td class="create_time"><?php echo $this->record['create_time']; ?></td>
    <td class="update_time"><?php echo $this->record['update_time']; ?></td>
</tr>
