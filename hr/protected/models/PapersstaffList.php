<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/9/18
 * Time: 10:40
 */


class PapersstaffList extends CListPageModel
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

    // 列表对应字段
    public function attributeLabels()
    {
        return array(
//            'id'=>Yii::t('employeesignature','id'),
//            'city_name'=>Yii::t('employeesignature','City_name'),
//            'city'=>Yii::t('employeesignature','City'),
//            'staffid'=>Yii::t('employeesignature','Staff_Id'),
//            'staffname'=>Yii::t('employeesignature','Staff_name'),
//            'signature'=>Yii::t('employeesignature','Signature'),
//            'creat_time'=>Yii::t('employeesignature','Creat_time'),

            'id'=>Yii::t('papersstaff','id'),
            'city_name'=>Yii::t('papersstaff','City_name'),
            'city'=>Yii::t('papersstaff','City'),
            'staffid'=>Yii::t('papersstaff','Staff_Id'),
            'staffname'=>Yii::t('papersstaff','Staff_name'),
            'create_time'=>Yii::t('papersstaff','Create_time'),
            'update_time'=>Yii::t('papersstaff','Update_time'),

        );
    }
    // 数据列表
    public function retrieveDataByPage($pageNum=1)
    {

        $tab_suffix = Yii::app()->params['table_envSuffix'];
        $se_suffix = Yii::app()->params['envSuffix'];

        $city_allow = Yii::app()->user->city_allow();
//        $sql1 = "select m.*,b.name city_name,s.StaffName as staffname from ".$tab_suffix."employee_signature as m left join staff as s on m.staffid=s.StaffID left join security".$se_suffix.".sec_city as b on m.city=b.code
//				";
//        $sql2 = "select count(m.id)
//				 from ".$tab_suffix."employee_signature as m left join staff as s on m.staffid=s.StaffID left join security".$se_suffix.".sec_city as b on m.city=b.code
//				";

        $sql1 = "select a.* from papersstaff a
                where a.city IN ($city_allow) AND a.staff_status = 0
			";

        $sql2 = "select count(a.id)
				from papersstaff a
				where a.city IN ($city_allow) AND a.staff_status = 0
			";


        $clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'name':
                    $clause .= General::getSqlConditionClause('a.name',$svalue);
                    break;
                case 'code':
                case 'city':
                    $clause .= General::getSqlConditionClause('a.city',$svalue);
                    break;
//                case 'staffid':
//                    $clause .= General::getSqlConditionClause('m.staffid',$svalue);
//                    break;
//                case 'staffname':
//                    $clause .= General::getSqlConditionClause('s.StaffName',$svalue);
//                    break;
            }
        }

        $order = "";
        if (!empty($this->orderField)) {
            $order .= " order by ".$this->orderField." ";
            if ($this->orderType=='D') $order .= "desc ";
        }else{
            $order .= " order by a.id asc ";
        }

        $sql = $sql2.$clause;
        $this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();

        $sql = $sql1.$clause.$order;
        $sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
        $records = Yii::app()->db->createCommand($sql)->queryAll();

        foreach ($records as $key=>$val){
//            echo $val['code'];
            $city = $val['city'];
            $sqlItem = "select * from securitydev.sec_city where code='$city'";
//            echo $sqlItem;
            $item = Yii::app()->db->createCommand($sqlItem)->queryRow();
//            print_r($item);
            $records[$key]['city_name'] = $item['name'];
        }
//        print_r($records);
        $list = array();
        $this->attr = array();
        if (count($records) > 0) {
            foreach ($records as $k=>$record) {
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'city'=>$record['city_name'],
                    'code'=>$record['code'],
                    'staffname'=>$record['name'],
                    'create_time'=>$record['create_time'],
                    'update_time'=>$record['update_time'],
                    'city_name'=>$records['city_name']
                );
            }
        }
        $session = Yii::app()->session;
        $session['papersstaff_ss01'] = $this->getCriteria();
        return true;
    }


}