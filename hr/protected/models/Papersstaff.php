<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/9/18
 * Time: 10:40
 */

class Employeesignature extends CListPageModel
{  /**
 * Declares customized attribute labels.
 * If not declared here, an attribute would have a label that is
 * the same as its name with the first letter in upper case.
 */
    public $id = 0;
    public $city;
    public $city_name;
    public $staffid;
    public $signature;
    public $signature_file_type;
    public $creat_time;

    public function attributeLabels()
    {
        return array(
            'id'=>Yii::t('employeesignature','id'),
            'city_name'=>Yii::t('employeesignature','City_name'),
            'city'=>Yii::t('employeesignature','City'),
            'staffid'=>Yii::t('employeesignature','Staff_Id'),
            'staffname'=>Yii::t('employeesignature','Staff_name'),
            'signature'=>Yii::t('employeesignature','Signature'),
            'creat_time'=>Yii::t('employeesignature','Creat_time'),

            'create_time'=>Yii::t('paperstaff','Create_time'),
            'update_time'=>Yii::t('paperstaff','Update_time'),
        );
    }
    public function rules()
    {
        return array(
            array('city, staffid, signature','safe'),
        );
    }
    public function retrieveDataByPage($pageNum=1)
    {
        $tab_suffix = Yii::app()->params['table_envSuffix'];
        $se_suffix = Yii::app()->params['envSuffix'];

        $city_allow = Yii::app()->user->city_allow();
        $sql1 = "select m.*,b.name city_name,s.StaffName as staffname from ".$tab_suffix."employee_signature as m left join staff as s on m.staffid=s.StaffID left join security".$se_suffix.".sec_city as b on m.city=b.code
				";
        $sql2 = "select count(m.id)
				 from ".$tab_suffix."employee_signature as m left join staff as s on m.staffid=s.StaffID left join security".$se_suffix.".sec_city as b on m.city=b.code
				";
        $clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'city':
                    $clause .= General::getSqlConditionClause('b.name',$svalue);
                    break;
                case 'staffid':
                    $clause .= General::getSqlConditionClause('m.staffid',$svalue);
                    break;
                case 'staffname':
                    $clause .= General::getSqlConditionClause('s.StaffName',$svalue);
                    break;
            }
        }

        $order = "";
        if (!empty($this->orderField)) {
            switch ($this->orderField) {
                case 'city':
                    $order .= " order by b.name ";
                    break;
                case 'staffid':
                    $order .= " order by m.staffid ";
                    break;
                case 'staffname':
                    $order .= " order by s.StaffName ";
                    break;
            }
            if ($this->orderType=='D') $order .= "desc ";
        }

        $sql = $sql2.$clause;
        $this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();

        $ct_where = " where m.city in(".$city_allow.")";
        $sql = $sql1.$ct_where.$clause.$order;
        $sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
        $records = Yii::app()->db->createCommand($sql)->queryAll();

        $list = array();
        $this->attr = array();
        if (count($records) > 0) {
            foreach ($records as $k=>$record) {
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'city'=>$record['city'],
                    'city_name'=>$record['city_name'],
                    'creat_time'=>$record['creat_time'],
                    'staffname'=>$record['staffname'],
                    'staffid'=>$record['staffid'],
                    'signature'=>$record['signature'],
                    'creat_time'=>$record['creat_time'],
                );
            }
        }
        $session = Yii::app()->session;
        $session['employeesignature_ss01'] = $this->getCriteria();
        return true;
    }
    function retrieveData($index) {
        $tab_suffix = Yii::app()->params['table_envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select("*")
            ->from($tab_suffix."employee_signature")->where("id=:id",array(":id"=>$index))->queryAll();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->city = $row['city'];
                $this->staffid = $row['staffid'];
                $this->signature = $row['signature'];
                $this->creat_time = $row['creat_time'];
                break;
            }
        }
        return true;
    }
    public function getSignatureString() {
        $rtn = '';
        if (!empty($this->signature)) {
            $type = ($this->signature_file_type=='jpg') ? 'jpeg' : $this->signature_file_type;
            $rtn = "data:image/$type;base64,".$this->signature;
        }
        return $rtn;
    }

}