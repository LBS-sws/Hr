<tr>

    <td><?php echo $this->record['code']; ?></td>
    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['department_name']; ?></td>
    <td><?php echo $this->record['position_name']; ?></td>


    <?php if (Yii::app()->user->validRWFunction('SR01')): ?>
        <td>
            <?php
            echo TbHtml::link("<span class='fa fa-trash-o'></span>","javascript:void(0);",
                array("style"=>"padding:10px;","class"=>"delStaff","data-id"=>$this->record['id']
            )); ?>
        </td>
    <?php endif; ?>
</tr>

<script>
    $(function () {
        $(".delStaff").click(function () {
            $("#removedialog").data("id",$(this).data("id")).modal("show");
        });

        $("#btnDeleteData").click(function () {
            window.location.href="<?php echo Yii::app()->createUrl('salesGroup/delStaff');?>"+"?index="+$("#removedialog").data("id");
        })
    })
</script>
