<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class DeptForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $name;
	public $city;
	public $z_index;
	public $dept_id=1;
	public $type;
	public $dept_class;
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>Yii::t('staff','Record ID'),
			'name'=>Yii::t('contract',' Name'),
			'z_index'=>Yii::t('contract','Level'),
            'dept_id'=>Yii::t('contract','in department'),
            'dept_class'=>Yii::t('contract','Job category'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			//array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id, name, z_index, dept_id, type, dept_class','safe'),
			array('name','required'),
			array('name','validateName'),
			array('dept_id','validateDeptId'),
		);
	}

	public function validateName($attribute, $params){
        $city = Yii::app()->user->city();
        $rows = Yii::app()->db->createCommand()->select()->from("hr_dept")
            ->where('id!=:id and name=:name and city=:city and type=:type and dept_id=:dept_id ',
                array(':id'=>$this->id,':name'=>$this->name,':city'=>$city,':type'=>$this->type,':dept_id'=>$this->dept_id))->queryAll();
        if (count($rows) > 0){
            $message = Yii::t('contract',' Name'). Yii::t('contract',' can not repeat');
            $this->addError($attribute,$message);
        }
    }

	public function validateDeptId($attribute, $params){
	    if($this->type == 1){
	        if(!is_numeric($this->dept_id)){
                $message = Yii::t('contract','in department'). Yii::t('contract',' can not be empty');
                $this->addError($attribute,$message);
            }else{
                $city = Yii::app()->user->city();
                $rows = Yii::app()->db->createCommand()->select()->from("hr_dept")
                    ->where('id!=:id and city=:city and type=0 ', array(':id'=>$this->dept_id,':city'=>$city))->queryRow();
                if (!$rows){
                    $message = Yii::t('contract','in department'). Yii::t('contract',' can not be empty');
                    $this->addError($attribute,$message);
                }
            }
        }
    }

    public function getTypeName(){
        if ($this->type == 1){
            return Yii::t("contract","Leader");
        }else{
            return Yii::t("contract","Dept");
        }
    }
    public function getTypeAcc(){
        if ($this->type == 1){
            return "ZC02";
        }else{
            return "ZC01";
        }
    }
    //獲取職位列表
	public function getDeptAllList($type=0){
        $city = Yii::app()->user->city();
	    $arr=array(""=>"");
        $rows = Yii::app()->db->createCommand()->select()->from("hr_dept")
            ->where('type=:type and city=:city', array(':type'=>$type,':city'=>$city))->order("z_index desc")->queryAll();
        if ($rows){
            foreach ($rows as $row){
                $arr[$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }
    //獲取職位列表(僅職位)
    public function getDeptOneAllList(){
        $city = Yii::app()->user->city();
        $arr=array(""=>array("name"=>"","type"=>"","dept_class"=>""));
        $rows = Yii::app()->db->createCommand()->select()->from("hr_dept")
            ->where('type=:type and city=:city', array(':type'=>1,':city'=>$city))->order("z_index desc")->queryAll();
        if ($rows){
            foreach ($rows as $row){
                $arr[$row["id"]] = array("name"=>$row["name"],"type"=>$row["dept_id"],"dept_class"=>$row["dept_class"]);
            }
        }
        return $arr;
    }
    //獲取職位名字
	public function getDeptToId($dept_id){
        $rows = Yii::app()->db->createCommand()->select()->from("hr_dept")
            ->where('id=:id', array(':id'=>$dept_id))->queryRow();
        if ($rows){
            return $rows["name"];
        }
        return $dept_id;
    }

    //職位刪除時必須沒有員工
	public function validateDelete(){
	    if($this->type == 1){
            $rows = Yii::app()->db->createCommand()->select()->from("hr_employee")
                ->where('position=:position', array(':position'=>$this->id))->queryAll();
        }else{
            $rows = Yii::app()->db->createCommand()->select()->from("hr_employee")
                ->where('department=:department', array(':department'=>$this->id))->queryAll();
        }
        if ($rows){
            return false;
        }
        return true;
    }

	public function retrieveData($index)
	{
        $city = Yii::app()->user->city();
        $rows = Yii::app()->db->createCommand()->select()->from("hr_dept")
            ->where('id=:id and city=:city ', array(':id'=>$index,':city'=>$city))->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->name = $row['name'];
				$this->z_index = $row['z_index'];
                $this->type = $row['type'];
                $this->dept_id = $row['dept_id'];
                $this->dept_class = $row['dept_class'];
				break;
			}
		}
		return true;
	}
	
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveStaff($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveStaff(&$connection)
	{
		$sql = '';
        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;
		switch ($this->scenario) {
			case 'delete':
                $sql = "delete from hr_dept where id = :id and city = :city";
				break;
			case 'new':
				$sql = "insert into hr_dept(
							name, type, z_index, dept_id, city, dept_class, lcu
						) values (
							:name, :type, :z_index, :dept_id, :city, :dept_class, :lcu
						)";
				break;
			case 'edit':
				$sql = "update hr_dept set
							name = :name, 
							type = :type, 
							z_index = :z_index,
							dept_id = :dept_id,
							dept_class = :dept_class,
							luu = :luu 
						where id = :id
						";
				break;
		}

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':name')!==false)
			$command->bindParam(':name',$this->name,PDO::PARAM_STR);
		if (strpos($sql,':dept_id')!==false)
			$command->bindParam(':dept_id',$this->dept_id,PDO::PARAM_STR);
		if (strpos($sql,':z_index')!==false)
			$command->bindParam(':z_index',$this->z_index,PDO::PARAM_INT);
		if (strpos($sql,':type')!==false)
			$command->bindParam(':type',$this->type,PDO::PARAM_INT);
		if (strpos($sql,':dept_class')!==false)
			$command->bindParam(':dept_class',$this->dept_class,PDO::PARAM_STR);

        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$city,PDO::PARAM_STR);
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
