<?php
    $linkArr =array(
        "index"=>$this->record['id'],
        "year"=>$this->model->year,
        "year_type"=>$this->model->year_type,
    );
?>

<tr class='clickable-row' data-href='<?php echo $this->getLink('SR01', 'salesReview/view', 'salesReview/view', $linkArr);?>'>

    <td>
        <?php
        echo $this->needHrefButton('SR01', 'salesReview/view', 'view', $linkArr);
        ?>
    </td>

    <td><?php echo $this->record['group_name']; ?></td>
    <td><?php echo $this->record['staff_num']; ?></td>
</tr>
