<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/9/18
 * Time: 10:35
 */
use yii\web\UploadedFile;
class PapersstaffController extends Controller
{
    public $function_id='PQ01';

    public function filters()
    {
        return array(
            'enforceRegisteredStation',
            'enforceSessionExpiration',
            'enforceNoConcurrentLogin',
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }
    public function accessRules()
    {
        return array(
            /* request */
            array('allow',
                'actions'=>array('new','edit','delete','save','list','item','UpdateData','Upload'),
                'expression'=>array('PapersstaffController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('PapersstaffController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    // 列表
    public function actionIndex($pageNum=0)
    {

        print_r($_POST['PapersstaffList']);
        $model = new PapersstaffList();
        if (isset($_POST['PapersstaffList'])) {
            $model->attributes = $_POST['PapersstaffList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['employeesignature_ss01']) && !empty($session['employeesignature_ss01'])) {
                $materiallist = $session['employeesignature_ss01'];
                $model->setCriteria($materiallist);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }
    public function actionView($index)
    {
        $model = new EmployeesignatureList('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model));
        }
    }
    public function actionNew()
    {
        $model = new PapersstaffForm('new');
        //当前城市员工列表
        $se_suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $sql = "select s.StaffID,s.StaffName,b.name city_name from staff  as s left join officecity as o on o.City = s.City left join enums as e on e.EnumID = o.Office left join security".$se_suffix.".sec_city as b on e.Text=b.code where  e.EnumType=8 and s.Status in(1,2,5) and e.Text in(".$city_allow.")";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        $employee_lists = [];

        // 模拟数据
//        $rows = array(
//            array('StaffID'=>400668,'StaffName'=>'许建荣','city_name'=>'成都'),
//            array('StaffID'=>400746,'StaffName'=>'伊帆','city_name'=>'成都'),
//            array('StaffID'=>400801,'StaffName'=>'谢松霖','city_name'=>'成都'),
//        );

        foreach ($rows as $row) {
            $StaffID = $row['StaffID'];
            $sql = "select * from papersstaff where code= '$StaffID'";
//            echo $sql;exit;
            $item = Yii::app()->db->createCommand($sql)->queryRow();
            if(!$item){
                $employee_lists[$row['StaffID']] = $row['StaffName']."(".$row['city_name'].")";
            }
//
        }

        $this->render('form',array('model'=>$model,'employee_lists'=>$employee_lists));
    }
    // 新增提交
    public function actionSave()
    {
//        print_r($_POST);
//        exit;
        if (isset($_POST['PapersstaffForm'])) {
            $model = new PapersstaffForm($_POST['PapersstaffForm']['scenario']);
            $model->attributes = $_POST['PapersstaffForm'];
//            $file = CUploadedFile::getInstance($model,'signaturefile');
//            if ($file) {
//                $model->signature_file_type = $file->type;
//                $content = file_get_contents($file->tempName);
//                print_r($content);exit;
//                $model->signature = "data:image/jpg;base64,".base64_encode($content);
//            }
            if ($model->validate()) {
                //新增
                $model->saveData();
                $model->scenario = 'edit';
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('papersstaff/edit',array('index'=>$model->id)));
            } else {
                echo "0";
//                $message = CHtml::errorSummary($model);
//                Dialog::message(Yii::t('dialog','Validation Message'), $message);
//                $this->redirect(Yii::app()->createUrl('papersstaffForm/edit',array('index'=>$model->id)));
            }
        }
    }
    public function actionDelete()
    {
        $model = new EmployeesignatureFrom('delete');
        if (isset($_POST['EmployeesignatureFrom'])) {
            $model->attributes = $_POST['EmployeesignatureFrom'];
            $model->saveData();
            Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
        }
//		$this->actionIndex();
        $this->redirect(Yii::app()->createUrl('employeesignature/index'));
    }
    public function actionEdit($index)
    {
        $model = new PapersstaffForm('edit');


        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
//            print_r($model);exit;
            //当前城市员工列表
            $se_suffix = Yii::app()->params['envSuffix'];
            $city_allow = Yii::app()->user->city_allow();
//            $sql = "select s.StaffID,s.StaffName,b.name city_name from staff  as s left join officecity as o on o.City = s.City left join enums as e on e.EnumID = o.Office left join security".$se_suffix.".sec_city as b on e.Text=b.code where  e.EnumType=8 and s.Status in(1,2,5) and e.Text in(".$city_allow.")";
            $sql = "select * from papersstaff where id=".$index;

            $item = Yii::app()->db->createCommand($sql)->queryRow();
//            print_r($rows);exit;
            $employee_lists = [];
//            foreach ($rows as $row) {
//                $employee_lists[$row['StaffID']] = $row['StaffName']."(".$row['city_name'].")";
//            }

            $this->render('form',array('model'=>$model,'employee_lists'=>$employee_lists,'item'=>$item));
        }
    }
    public function actionList(){
        $id = $_GET['id'];
        $sql = "select * from papersstaff_info where papersstaff_id = ".$id;

        $records = Yii::app()->db->createCommand($sql)->queryAll();

        $data['data'] = $records;
        echo json_encode($data);
//        print_r($records);
    }
    // 获取证件详情
    public function actionItem(){
        $id = $_GET['id'];

        $sql = "select * from papersstaff_info where id = ".$id;

        $records = Yii::app()->db->createCommand($sql)->queryRow();

        $data['data'] = $records;
        echo json_encode($data);
    }
    public function actionUpdateData(){
//        print_r($_POST);exit;

        // add
        if(trim($_POST['id']) ==''){
            $name = $_POST['name'];
            $code = $_POST['code'];
            $papersstaff_id = $_POST['papersstaff_id'];
            $startDate = $_POST['startDate'];
            $endDate = $_POST['endDate'];
            $imgUrl = $_POST['imgUrl'];

            $sql = "INSERT INTO `papersstaff_info` (`id`, `papersstaff_id`, `StaffCode`, `PapersName`, `StartDate`, `EndDate`, `imgUrl`) 
VALUES (NULL, '$papersstaff_id', '$code', '$name', '$startDate', '$endDate', '$imgUrl')";
            Yii::app()->db->createCommand($sql)->query();

            $id = Yii::app()->db->getLastInsertID();
            $data['id'] = $id;
            echo json_encode($data);
        }
        if(trim($_POST['id']) !=''){
            $name = $_POST['name'];
            $code = $_POST['code'];
            $papersstaff_id = $_POST['papersstaff_id'];
            $startDate = $_POST['startDate'];
            $endDate = $_POST['endDate'];
            $imgUrl = $_POST['imgUrl'];

            $id = $_POST['id'];
            $sql = $sql = "update papersstaff_info set 
					PapersName='$name',StartDate='$startDate',EndDate='$endDate',imgUrl='$imgUrl'
					where id = ".$id;

            Yii::app()->db->createCommand($sql)->query();

            $id = Yii::app()->db->getLastInsertID();
            $data['id'] = $id;
            echo json_encode($data);
        }
    }
    /* 上传图片 */
    public function actionUpload(){

        $file = $_FILES["img"];
        //图片目录
        $dir = 'upload/papers';
        if(!is_dir($dir)) {
            mkdir(iconv("UTF-8", "GBK", $dir), 0777, true);
        }
        //图片上传地址
        move_uploaded_file($file["tmp_name"],$dir."/" . $file["name"]);
        //得到当前时间,如;20190911034728
        $date = date('Ymdhis');
        //得到上传文件的名字
        $fileName = $file["name"];
        //将文件名以'.'分割得到后缀名,得到一个数组
        $name = explode('.', $fileName);
        //得到一个新的文件为'20190911034728.jpg',即新的路径
        $newPath = time() . '.' . $name[1];
        //临时文件夹,即以前的路径
        $oldPath = $file["tmp_name"];
        rename($dir ."/". $fileName, $dir ."/". $newPath);
        //这里可以写你的SQL语句,图片的地址是 "upload/".$newPath
        $img_url = $dir."/".$newPath;
//        echo $img_url;
        $data['img'] = $img_url;
        echo json_encode($data);

    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('SS01');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('SS01');
    }
}