<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/9/18
 * Time: 10:40
 */

class PapersstaffForm extends CFormModel
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
    // 编辑
    public function attributeLabels()
    {
        return array(
            'id'=>Yii::t('employeesignature','id'),
            'city_name'=>Yii::t('employeesignature','City_name'),
            'city'=>Yii::t('employeesignature','City'),
            'staffid'=>Yii::t('employeesignature','Staff_Id'),
            'staffname'=>Yii::t('papersstaff','Staff_name'),
            'signature'=>Yii::t('employeesignature','Signature'),
            'creat_time'=>Yii::t('employeesignature','Creat_time'),

            'create_time'=>Yii::t('papersstaff','Create_time'),
            'update_time'=>Yii::t('papersstaff','Update_time'),
        );
    }
    public function rules()
    {
        return array(
            array('id,city,city_name,staffid,creat_time','safe'),
//            array('staffid,signature','required'),
        );
    }
    // 编辑读取
    function retrieveData($index) {

        $tab_suffix = Yii::app()->params['table_envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()->select("*")
            ->from("papersstaff")->where("id=:id",array(":id"=>$index))->queryRow();
//        print_r($row);
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
    public function saveData()
    {
        $connection = Yii::app()->db;
        $transaction=$connection->beginTransaction();
        try {
            $this->saveEmployeesignature($connection);
            $transaction->commit();
        }
        catch(Exception $e) {
            $transaction->rollback();
            throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
        }
    }
    protected function saveEmployeesignature(&$connection)
    {

        print_r($this->staffid);
        $StaffID = $this->staffid;

        $sql_item = "select s.StaffID,s.StaffName,b.code,b.name city_name from staff as s left join officecity as o on o.City = s.City 
left join enums as e on e.EnumID = o.Office left join securitydev.sec_city as b on e.Text=b.code where e.EnumType=8 AND s.StaffID = ".$StaffID;
        $item = Yii::app()->db->createCommand($sql_item)->queryRow();
        print_r($item); //  Array ( [StaffID] => 400668 [StaffName] => 许建荣 [City] => 23600 [city_name] => 成都 )

//        exit;
        $tab_suffix = Yii::app()->params['table_envSuffix'];
        $sql = '';
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from ".$tab_suffix."employee_signature where id = :id";
                break;
            case 'new':
//                $sql = "insert into ".$tab_suffix."employee_signature(
//                        city,staffid, signature,creat_time) values (
//						:city,:staffid, :signature,:creat_time)";

                $name = $item['StaffName'];
                $code = $item['StaffID'];
                $city = $item['code'];

                $sql = "insert into papersstaff(
                        name,code,city) values (
						'$name','$code','$city')";

                break;
            case 'edit':
                $sql = "update  ".$tab_suffix."employee_signature set 
					city = :city,
					staffid = :staffid,
					signature = :signature
					where id = :id";
                break;
        }
//        echo $sql;exit;
        //查询是否存在
//        $sql1 = "select e.Text from staff  as s left join officecity as o on o.City = s.City left join enums as e on e.EnumID = o.Office where  e.EnumType=8 and s.StaffID ='".$this->staffid."'";
//        $row = Yii::app()->db->createCommand($sql1)->queryRow();
//        $city = $row['Text'];
        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
//        if (strpos($sql,':city')!==false)
//            $command->bindParam(':city',$this->city?$this->city:$city,PDO::PARAM_STR);
//        if (strpos($sql,':staffid')!==false)
//            $command->bindParam(':staffid',$this->staffid,PDO::PARAM_STR);
//        if (strpos($sql,':signature')!==false)
//            $command->bindParam(':signature',$this->signature,PDO::PARAM_STR);
//        if (strpos($sql,':creat_time')!==false)
//            $command->bindParam(':creat_time',date('Y-m-d H:i:s', time()),PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new')
            $this->id = Yii::app()->db->getLastInsertID();
        return true;
    }
}