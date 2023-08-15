
<!-- 表格头部 -->
<tr>
    <?php if (!Yii::app()->user->isSingleCity()) : ?>
        <th>
            <?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('city_name'),'#',$this->createOrderLink('employeesignature','city_name'))
            ;
            ?>
        </th>
    <?php endif ?>

<!--    <th>-->
<!--        --><?php //echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('city_name'),'#',$this->createOrderLink('papersstaff','city_name'));
//        ?>
<!--    </th>-->
    <th>
        <?php
        echo TbHtml::link($this->getLabelName('staffid').$this->drawOrderArrow('staffid'),'#',$this->createOrderLink('employeesignature','staffid'));
        ?>
    </th>
    <th>
        <?php
        echo TbHtml::link($this->getLabelName('staffname').$this->drawOrderArrow('staffname'),'#',$this->createOrderLink('papersstaff','staffname'));
        ?>
    </th>

    <th>
        <?php
        echo TbHtml::link($this->getLabelName('create_time').$this->drawOrderArrow('create_time'),'#',$this->createOrderLink('papersstaff','create_time'));
        ?>
    </th>
    <th>
        <?php
        echo TbHtml::link($this->getLabelName('update_time').$this->drawOrderArrow('update_time'),'#',$this->createOrderLink('papersstaff','update_time'));
        ?>
    </th>
    <th width="10%"></th>
</tr>
