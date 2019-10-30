<?php

class TemplateEmployeeForm extends CFormModel
{
	public $id=0;
	public $tem_id;
	public $city;
	public $employee_id;

	public function attributeLabels()
	{
		return array(
            'employee_id'=>Yii::t('contract','Employee Name'),
            'tem_name'=>Yii::t('contract','template name'),
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
		);
	}

	public function validateName($attribute, $params){
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

	public function retrieveData($index) {
		$row = Yii::app()->db->createCommand()->select("city")
            ->from("hr_employee")->where("id=:id",array(":id"=>$index))->queryRow();
		if($row){
		    $this->city = $row["city"];
		    $this->employee_id = $index;
            $row = Yii::app()->db->createCommand()->select("*")
                ->from("hr_template_employee")->where("employee_id=:id",array(":id"=>$index))->queryRow();
            if($row){
                $this->tem_id = $row["tem_id"];
            }else{
                $this->tem_id = "";
            }
        }
		return true;
	}

    //根據id獲取請假類型
    public function getEmployeeListHtml(){
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select("id,name,city")
            ->from("hr_employee")->where("city IN ($city_allow)")->order("name asc")->queryAll();
        $className = get_class($this)."[employee_id]";
        $html = "<select class='form-control' name='$className' id='changeEmployee'>";
        $html.="<option value=''></option>";
        if($rows){
            foreach ($rows as $row){
                $row['name'] = !Yii::app()->user->isSingleCity()?$row['name']." -- ".$row["city"]:$row['name'];
                $html.="<option value='".$row["id"]."' data-city='".$row["city"]."' ";
                if($this->employee_id == $row["id"]){
                    $html.=" selected='selected' ";
                }
                $html.=">".$row['name']."</option>";
            }
        }
        $html.="</select>";
        return $html;
    }

    //根據id獲取請假類型
    public function getCityListHtml(){
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select("id,tem_name,city")
            ->from("hr_template")->where("city IN ($city_allow)")->order("city")->queryAll();
        $className = get_class($this)."[tem_id]";
        $html = "<select class='form-control' name='$className' id='changeCity' data-id='".$this->tem_id."'>";
        $html.="<option value=''></option>";
        if($rows){
            foreach ($rows as $row){
                $html.="<option value='".$row["id"]."' data-city='".$row["city"]."' ";
                if($this->tem_id == $row["id"]){
                    $html.=" selected='selected' ";
                }
                if($this->city != $row["city"]){
                    $html.= " class='hide'";
                }
                $html.=">".$row['tem_name']."</option>";
            }
        }
        $html.="</select>";
        return $html;
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
        switch ($this->scenario) {
            case 'new':
                $sql = "insert into hr_template_employee(
							tem_id,employee_id, lcu
						) values (
							:tem_id,:employee_id, :lcu
						)";
                break;
            case 'edit':
                $sql = "update hr_template_employee set
							tem_id = :tem_id, 
							employee_id = :employee_id, 
							luu = :luu
						where id = :id
						";
                break;
        }
		if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        //log_bool,max_log,sub_bool,sub_multiple
        if (strpos($sql,':tem_id')!==false)
            $command->bindParam(':tem_id',$this->tem_id,PDO::PARAM_STR);
        if (strpos($sql,':employee_id')!==false)
            $command->bindParam(':employee_id',$this->employee_id,PDO::PARAM_STR);

        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->scenario = "edit";
        }
		return true;
	}
}
