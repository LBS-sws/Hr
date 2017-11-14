<?php

class HistoryList extends CListPageModel
{
    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'id'=>Yii::t('contract','ID'),
            'name'=>Yii::t('contract','Employee Name'),
            'code'=>Yii::t('contract','Employee Code'),
            'phone'=>Yii::t('contract','Employee Phone'),
            'position'=>Yii::t('contract','Position'),
            'company_id'=>Yii::t('contract','Company Name'),
            'contract_id'=>Yii::t('contract','Contract Name'),
            'staff_status'=>Yii::t('contract','Status'),
            'operation'=>Yii::t('contract','Operation Status'),
        );
    }

    public function retrieveDataByPage($pageNum=1)
    {
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city();
        $sql1 = "select * from hr_employee_operate
                where city='$city' AND finish != 1
			";
        $sql2 = "select count(id)
				from hr_employee_operate 
				where city='$city' AND finish != 1
			";
        $clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'name':
                    $clause .= General::getSqlConditionClause('name',$svalue);
                    break;
                case 'code':
                    $clause .= General::getSqlConditionClause('code',$svalue);
                    break;
                case 'phone':
                    $clause .= General::getSqlConditionClause('phone',$svalue);
                    break;
                case 'position':
                    $clause .= General::getSqlConditionClause('phone',$svalue);
                    break;
                case 'company_id':
                    //$clause .= General::getSqlConditionClause('company_id',$svalue);
                    break;
            }
        }

        $order = "";
        if (!empty($this->orderField)) {
            $order .= " order by ".$this->orderField." ";
            if ($this->orderType=='D') $order .= "desc ";
        }else{
            $order .= " order by id desc ";
        }

        $sql = $sql2.$clause;
        $this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();

        $sql = $sql1.$clause.$order;
        $sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
        $records = Yii::app()->db->createCommand($sql)->queryAll();

        $list = array();
        $this->attr = array();
        if (count($records) > 0) {
            foreach ($records as $k=>$record) {
                $arr = $this->translateEmploy($record['staff_status']);
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'name'=>$record['name'],
                    'code'=>$record['code'],
                    'operation'=>$record['operation'],
                    'position'=>DeptForm::getDeptToid($record['position']),
                    'company_id'=>CompanyForm::getCompanyToId($record['company_id'])["name"],
                    //'contract_id'=>ContractForm::getContractNameToId($record['contract_id']),
                    'phone'=>$record['phone'],
                    'staff_status'=>$arr["status"],
                    'style'=>$arr["style"],
                );
            }
        }
        $session = Yii::app()->session;
        $session['criteria_a07'] = $this->getCriteria();
        return true;
    }


    public function translateEmploy($status,$remark=0){
        switch ($status){
            // text-danger
            case 1:
                return array(
                    "status"=>Yii::t("contract","Draft"),
                    "style"=>""
                );
            case 2:
                return array(
                    "status"=>Yii::t("contract","Sent, pending approval"),//已發送，等待審核
                    "style"=>" text-primary"
                );
            case 3:
                return array(
                    "status"=>Yii::t("contract","Rejected"),//拒絕
                    "style"=>" text-danger"
                );
            case 4:
                return array(
                    "status"=>Yii::t("contract","Reviewed and awaiting confirmation"),//等待社保
                    "style"=>" text-yellow"
                );
            default:
                return array(
                    "status"=>"",
                    "style"=>""
                );
        }
    }

    public function historyOperation($status){
        return Yii::t("contract",$status);
    }
}
