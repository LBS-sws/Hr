<?php

class AuditWorkList extends CListPageModel
{
    public $only = 1;//1：地區審核  2：總部審核
    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'work_code'=>Yii::t('fete','Work Code'),
            'work_type'=>Yii::t('fete','Work Type'),
            'employee_id'=>Yii::t('contract','Employee Name'),
            'start_time'=>Yii::t('contract','Start Time'),
            'end_time'=>Yii::t('contract','End Time'),
            'log_time'=>Yii::t('fete','Log Date'),
            'status'=>Yii::t('contract','Status'),
            'city'=>Yii::t('contract','City'),
        );
    }

    public function getAcc(){
        if($this->only == 1){
            return "ZE05";
        }else{
            return "ZG04";
        }
    }

    public function retrieveDataByPage($pageNum=1)
    {
        $city_allow = Yii::app()->user->city_allow();
        $sql1 = "select * from hr_employee_work 
                where city in ($city_allow) AND status !=0
			";
        $sql2 = "select count(id)
				from hr_employee_work 
				where city in ($city_allow) AND status !=0  AND z_index =0 
			";
        if($this->only ==1 ){
            $sql1.=" AND z_index =0 ";
            $sql2.=" AND z_index =0 ";
        }else{
            $sql1.=" AND z_index =1 ";
            $sql2.=" AND z_index =1 ";
        }
        $clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'work_code':
                    $clause .= General::getSqlConditionClause('work_code',$svalue);
                    break;
                case 'employee_id':
                    $clause .= General::getSqlConditionClause('employee_id',$svalue);
                    break;
                case 'start_time':
                    $clause .= General::getSqlConditionClause('start_time',$svalue);
                    break;
                case 'end_time':
                    $clause .= General::getSqlConditionClause('end_time',$svalue);
                    break;
                case 'log_time':
                    $clause .= General::getSqlConditionClause('log_time',$svalue);
                    break;
                case 'status':
                    $clause .= General::getSqlConditionClause('status',$svalue);
                    break;
                case 'city':
                    $clause .= General::getSqlConditionClause('city',$svalue);
                    break;
            }
        }

        $order = "";
        if (!empty($this->orderField)) {
            $order .= " order by ".$this->orderField." ";
            if ($this->orderType=='D') $order .= "desc ";
        }

        $sql = $sql2.$clause;
        $this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();

        $sql = $sql1.$clause.$order;
        $sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
        $records = Yii::app()->db->createCommand($sql)->queryAll();

        $costNumList = WorkList::getWorkTypeList();
        $this->attr = array();
        if (count($records) > 0) {
            foreach ($records as $k=>$record) {
                $colorList = $this->statusToColor($record['status']);
                $employeeList = EmployeeForm::getEmployeeOneToId($record['employee_id']);
                if($record['work_type'] == 2){
                    $record['start_time'] = date("Y/m/d",strtotime($record['start_time']));
                    $record['end_time'] = date("Y/m/d",strtotime($record['end_time']));
                    $dayStr ="天";
                }else{
                    $record['start_time'] = date("Y/m/d H:i:s",strtotime($record['start_time']));
                    $record['end_time'] = date("Y/m/d H:i:s",strtotime($record['end_time']));
                    $dayStr ="小時";
                }
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'work_code'=>$record['work_code'],
                    'employee_id'=>$employeeList["name"],
                    'start_time'=>$record['start_time'],
                    'end_time'=>$record['end_time'],
                    'log_time'=>$record['log_time'].$dayStr,
                    'work_type'=>$costNumList[$record['work_type']],
                    'status'=>$colorList["status"],
                    'city'=>CGeneral::getCityName($record["city"]),
                    'style'=>$colorList["style"],
                );
            }
        }
        $session = Yii::app()->session;
        $session['auditwork_01'] = $this->getCriteria();
        return true;
    }

    //根據狀態獲取顏色
    public function statusToColor($status){
        switch ($status){
            case 1:
                return array(
                    "status"=>Yii::t("contract","pending approval"),//等待審核
                    "style"=>" text-primary"
                );
            case 2:
                return array(
                    "status"=>Yii::t("contract","Finish approval"),//審核通過
                    "style"=>" text-yellow"
                );
            case 3:
                return array(
                    "status"=>Yii::t("contract","Rejected"),//拒絕
                    "style"=>" text-danger"
                );
            case 4:
                return array(
                    "status"=>Yii::t("contract","Finish"),//完成
                    "style"=>" text-green"
                );
        }
        return array(
            "status"=>"",
            "style"=>""
        );
    }
}
