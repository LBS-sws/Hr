<?php

class TemplateEmployeeForm extends CFormModel
{
	public $id=0;
	public $tem_id;
	public $city;
	public $employee_id;
	public $employee_name;
    public $id_list;
    public $id_s_list;
    public $name_list;

	public function attributeLabels()
	{
		return array(
            'employee_id'=>Yii::t('contract','Employee Name'),
            'tem_name'=>Yii::t('contract','template name'),
            'tem_id'=>Yii::t('contract','template name'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, tem_id,city,employee_id','safe'),
            array('tem_id','required'),
            array('employee_id','required'),
            array('employee_id','validateName'),
            array('id_list','validateIdList'),
		);
	}

	public function validateName($attribute, $params){
	    if(!empty($this->tem_id)){
            $city_allow = Yii::app()->user->city_allow();
            $row = Yii::app()->db->createCommand()->select("city")->from("hr_employee")
                ->where("id=:id and city IN ($city_allow)",array(':id'=>$this->employee_id))->queryRow();
            if($row){
                $row = Yii::app()->db->createCommand()->select("city")->from("hr_template")
                    ->where("id=:id and city=:city",array(':id'=>$this->tem_id,':city'=>$row['city']))->queryRow();
                if($row){
                    $row = Yii::app()->db->createCommand()->select("id")->from("hr_template_employee")
                        ->where("employee_id=:employee_id",array(':employee_id'=>$this->employee_id))->queryRow();
                    if($row){
                        $this->id = $row['id'];
                        $this->setScenario("edit");
                    }else{
                        $this->setScenario('new');
                    }
                }else{
                    $message = "模板不存在";
                    $this->addError($attribute,$message);
                }
            }else{
                $message = "員工不存在";
                $this->addError($attribute,$message);
            }
        }
	}
    public function validateIdList($attribute, $params){
        if(!empty($this->id_list)){ //考核經理驗證
            $sum = 0;
            $this->name_list = array();
            $idList =array();
            foreach ($this->id_list as &$list){
                if(in_array($list["employee_id"],$idList)){
                    $message = Yii::t('contract','reviewAllot manager'). Yii::t('contract',' can not repeat');
                    $this->addError($attribute,$message);
                    return false;
                }else{
                    $idList[] = $list["employee_id"];
                }
                $rows = Yii::app()->db->createCommand()->select("name")->from("hr_employee")
                    ->where("id=:id",array(":id"=>$list["employee_id"]))->queryRow();
                if(!$rows){
                    $message = Yii::t('contract','reviewAllot manager'). Yii::t('contract',' not exist');
                    $this->addError($attribute,$message);
                    return false;
                }else{
                    $list["employee_name"] = $rows["name"];
                }
                if(empty($list['num'])||!is_numeric($list['num'])){
                    $message = Yii::t('contract','manager percent').Yii::t('contract',' can not be empty');
                    $this->addError($attribute,$message);
                    return false;
                }
                $this->name_list[] = $rows["name"]."（".$list["num"]."%）";
                $sum+=intval($list["num"]);
            }
            if($sum!=100){
                $message = '經理考核佔比必須為100';
                $this->addError($attribute,$message);
                return false;
            }
            $this->id_s_list = implode(",",$idList);
            $this->name_list = implode(",",$this->name_list);
            //var_dump($this->id_s_list);die();
        }
    }

	public function retrieveData($index) {
		$row = Yii::app()->db->createCommand()->select("city,name")
            ->from("hr_employee")->where("id=:id",array(":id"=>$index))->queryRow();
		if($row){
		    $this->city = $row["city"];
		    $this->employee_id = $index;
		    $this->employee_name = $row["name"];
            $row = Yii::app()->db->createCommand()->select("*")
                ->from("hr_template_employee")->where("employee_id=:id",array(":id"=>$index))->queryRow();
            if($row){
                $this->tem_id = $row["tem_id"];
                $this->id_s_list = $row['id_s_list'];
                $this->id_list = json_decode($row['id_list'],true);
            }else{
                $this->tem_id = "";
                $this->id_s_list = '';
                $this->id_list = array();
            }
        }
		return true;
	}

    public function getReadonly(){
        return $this->getScenario() =='view';
    }

    //根據id獲取請假類型
    public function getTemplateListToCity($city){
        $arr = array(''=>'');
        $rows = Yii::app()->db->createCommand()->select("id,tem_name")
            ->from("hr_template")->where("city=:city",array(":city"=>$city))->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row['id']] = $row['tem_name'];
            }
        }
        return $arr;
    }

    //刪除驗證
    public function deleteValidate(){
        return false;
    }

	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveGoods($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

	protected function saveGoods(&$connection) {
		$sql = '';
        $uid = Yii::app()->user->id;
        switch ($this->scenario) {
            case 'new':
                $connection->createCommand()->insert("hr_template_employee", array(
                    'tem_id'=>$this->tem_id,
                    'employee_id'=>$this->employee_id,
                    'id_list'=>json_encode($this->id_list),
                    'id_s_list'=>$this->id_s_list,
                    'name_list'=>$this->name_list,
                    'lcu'=>$uid,
                ));
                break;
            case 'edit':
                $connection->createCommand()->update('hr_template_employee', array(
                    'tem_id'=>$this->tem_id,
                    'id_list'=>json_encode($this->id_list),
                    'id_s_list'=>$this->id_s_list,
                    'name_list'=>$this->name_list,
                    'luu'=>$uid,
                ), 'id=:id', array(':id'=>$this->id));
                break;
        }
		return true;
	}
}
