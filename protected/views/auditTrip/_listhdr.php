<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('trip_code').$this->drawOrderArrow('a.trip_code'),'#',$this->createOrderLink('auditTrip-list','a.trip_code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('lcd').$this->drawOrderArrow('a.lcd'),'#',$this->createOrderLink('auditTrip-list','a.lcd'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('auditTrip-list','b.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('f.name'),'#',$this->createOrderLink('auditTrip-list','f.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('start_time').$this->drawOrderArrow('a.start_time'),'#',$this->createOrderLink('auditTrip-list','a.start_time'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('end_time').$this->drawOrderArrow('a.end_time'),'#',$this->createOrderLink('auditTrip-list','a.end_time'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('trip_address').$this->drawOrderArrow('a.trip_address'),'#',$this->createOrderLink('auditTrip-list','a.trip_address'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('trip_cost').$this->drawOrderArrow('a.trip_cost'),'#',$this->createOrderLink('auditTrip-list','a.trip_cost'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('a.status'),'#',$this->createOrderLink('auditTrip-list','a.status'))
        ;
        ?>
    </th>
</tr>
