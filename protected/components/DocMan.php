<?php
class DocMan {

    public $docType;

    public $docId;

    public $formId = "";

    public $masterId;

    public $files; //CUploadFile Objects

    // For View Variable
    public $inputName = 'attachment';
    public $tableName = 'tblFile';
    public $listName = 'T7-log';
    public $removeFunctionName = 'removeFile';
    public $downloadFunctionName = 'downloadFile';
    public $uploadButtonName = 'btnUploadFile';
    public $closeButtonName = 'btnUploadClose';
    public $widgetName = 'fileupload';

    protected $baseDir;

    private $_allowedFiles = array(
        'bmp'  => 'image/bmp',
        'gif'  => 'image/gif',
        'jpeg' => 'image/jpeg',
        'jpg'  => 'image/jpeg',
        'png'  => 'image/png',
        'tif'  => 'image/tiff',
        'tiff' => 'image/tiff',

        'pdf' => 'application/pdf',		//'application/x-pdf',
        'txt' => 'text/plain',
        'rtf' => 'application/rtf',		//'text/rtf',

        'odt' => 'application/vnd.oasis.opendocument.text',
        'ott' => 'application/vnd.oasis.opendocument.text-template',
        'odp' => 'application/vnd.oasis.opendocument.presentation',
        'otp' => 'application/vnd.oasis.opendocument.presentation-template',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'ots' => 'application/vnd.oasis.opendocument.spreadsheet-template',
        'odc' => 'application/vnd.oasis.opendocument.chart',
        'odf' => 'application/vnd.oasis.opendocument.formula',

        'doc'  => 'application/x-msword',	//'application/msword',
        'xls'  => 'application/vnd.ms-excel',	//'application/excel',
        'xlsm' => 'application/vnd.ms-excel.sheet.macroenabled.12',
        'ppt'  => 'application/vnd.ms-powerpoint',

        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',

        'avi' => 'video/x-msvideo',
        'flv' => 'video/x-flv',
        'mov' => 'video/quicktime',
        'mp4' => 'video/vnd.objectvideo',
        'mpg' => 'video/mpeg',
        'wmv' => 'video/x-ms-wmv',

        '7z'  => 'application/x-7z-compressed', 	//'application/7z',
        'rar' => 'application/x-rar-compressed', 	//'application/rar',
        'zip' => 'application/x-zip-compressed', 	//'application/zip',
        'gz'  => 'application/x-gzip',				//'application/gzip',
        'tar' => 'application/x-tar', 				//'application/tar',
        'tgz' => 'application/gzip', 				//'application/tar', 'application/tar+gzip',

        'mp3' => 'audio/mpeg',
        'ogg' => 'application/ogg',
        'wma' => 'audio/x-ms-wma',
    );

    public function __construct($type, $id, $form="") {
        $this->docType = $type;
        $this->docId = $id;
        $this->formId = $form;
        $this->baseDir = Yii::app()->params['docmanPath'];

        $this->inputName .= strtolower($type);
        $this->tableName .= strtolower($type);
        $this->listName .= strtolower($type);
        $this->removeFunctionName .= strtolower($type);
        $this->downloadFunctionName .= strtolower($type);
        $this->uploadButtonName .= strtolower($type);
        $this->closeButtonName .= strtolower($type);
        $this->widgetName .= strtolower($type);
    }

    protected function getMasterId($genId = true) {
        if ($this->masterId >0) return $this->masterId;

        $suffix = Yii::app()->params['envSuffix'];
        if ($this->docId > 0) {
            $code = $this->docType;
            $id = $this->docId;
            $sql = "select id from docman$suffix.dm_master 
						where doc_type_code='$code' and doc_id=$id
					";
            $mid = Yii::app()->db->createCommand($sql)->queryScalar();
        } else {
            $mid = false;
        }

        if ($mid===false && $genId) {
            $uid = Yii::app()->user->id;
            $sql = "insert into docman$suffix.dm_master(doc_type_code, doc_id, lcu) 
						values (:doc_type_code, :doc_id, :lcu)
					";
            try {
                $command = Yii::app()->db->createCommand($sql);
                if (strpos($sql,':doc_type_code')!==false)
                    $command->bindParam(':doc_type_code',$this->docType,PDO::PARAM_STR);
                if (strpos($sql,':doc_id')!==false)
                    $command->bindParam(':doc_id',$this->docId,PDO::PARAM_INT);
                if (strpos($sql,':lcu')!==false)
                    $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
                $command->execute();
            } catch(Exception $e) {
                throw new CHttpException(404,'Cannot update.'.$e->getMessage());
            }
            $mid = Yii::app()->db->getLastInsertID();
        }
        $this->masterId = $mid;
        return $mid;
    }

    public function fileUpload() {
        if (empty($this->files['name'][0])) return;

        $mast_id = $this->getMasterId();
        $company_id = $this->getDocId($mast_id);     // 公司ID

        // 域名
        if(Yii::app()->params['envSuffix']=='dev'){
            $domain = 'http://localhost:10004';
        }elseif (Yii::app()->params['envSuffix']=='uat'){
            $domain = 'https://uatapps.lbsapps.cn/nu';
        }else{
            $domain = 'https://dms.lbsapps.cn/nu';
        }

        $recs = array();
        foreach ($this->files['name'] as $idx=>$value) {
            $dispname = $value;
            $phyname = $this->files['tmp_name'][$idx];
            if (!empty($phyname)) {
                $filename = hash_file('md5',$phyname);
                $ext = pathinfo($dispname,PATHINFO_EXTENSION);
                $filename .= '.'.$ext;
                $filetype = $this->files['type'][$idx];
                $path = $this->hashDirectory($filename);
                if (rename($phyname, $path.'/'.$filename)) {
                    $recs[] = array('path'=>$path, 'filename'=>$filename, 'dispname'=>$dispname, 'filetype'=>$filetype);
                    $url = $domain.'/Upload/index';

                    $path = $path.'/'.$filename;

                    $res = $this->sendPost($url, $path, $filename);
//                    var_dump($res);
                }
            }
        }
        $connection = Yii::app()->db;
        $transaction=$connection->beginTransaction();
        try {
            foreach ($recs as $record) {
                $data = array(
                    'mast_id'=>$mast_id,
                    'phy_path_name'=>$record['path'],
                    'phy_file_name'=>$record['filename'],
                    'display_name'=>$record['dispname'],
                    'file_type'=>$record['filetype'],
                );
                $this->saveFile($connection, 'insert', $data);
            }
            // 更新
            $sql1 = "SELECT * FROM hr".Yii::app()->params['envSuffix'].".hr_company where id='$company_id'";
            $item = Yii::app()->db->createCommand($sql1)->queryRow();

            // 查询附件
            $sql = "SELECT a.* FROM docman".Yii::app()->params['envSuffix'].".dm_file a LEFT JOIN docman".Yii::app()->params['envSuffix'].".dm_master b ON a.mast_id = b.id WHERE b.doc_type_code='COMPANY2'";
            if($company_id){
                $sql .=" and b.doc_id='".$company_id."' and a.remove = 'N' ";
            }

            $arrRows=Yii::app()->db->createCommand($sql)->query();
            $list = [];
            foreach ($arrRows as $key=>$val)
            {
                array_push($list,$val);
            }

            $item['x_file_names'] = implode(',',array_column($list,'phy_file_name'));
            $item['x_lcd'] = implode(',',array_column($list, 'lcd'));
            $item['x_lud'] = implode(',',array_column($list, 'lud'));

            $list_arr = [];
            foreach ($list as $key=>$val){
                $a = $val['phy_path_name'].'/'.$val['phy_file_name'];
                array_push($list_arr,$a);
            }
            $item['x_list'] = implode(',',$list_arr);

            $data = [];
            $data = $item;
            $url = $domain . '/Upload/company';
            $res = $this->sendPostCompany($item,$url);
            $transaction->commit();
            $this->resetModelFileNumber();
        } catch(Exception $e) {
            $transaction->rollback();
            throw new CHttpException(404,'Cannot update.'.$e->getMessage());
        }
    }
    public function sendPostCompany($data, $url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        //curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, "TEST");
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
    public function sendPost($url, $path, $filename)
    {
        $curl = curl_init();
        if (class_exists('\CURLFile')) {
            //PHP版本 >= 5.5
            curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
            $suffixIndex = strripos($path, '.');
            if($suffixIndex === false){
                $suffix = 'jpg';
            } else {
                $suffix = substr($path, $suffixIndex+1);
            }
//            $filename = time() . rand(1000, 9999);
            $data = array('phy_file_name'=>$filename,'file' => new \CURLFile(realpath($path), 'image/' . $suffix, $filename .'.'.  $suffix));
        } else {
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
            }
            $data = array('file' => '@' . realpath($path));//<=5.5
        }
        curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, "TEST");
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
    public function fileDownload($fileId) {
        $mast_id = $this->getMasterId(false);
        $file_mast_id = $this->getFileMasterId($fileId);
        if ($mast_id == $file_mast_id) {
            $data = $this->getFileName($fileId);
            $path = $data['phy_path_name'];
            $name = $data['phy_file_name'];
            $filename = $data['display_name'];
            $filetype = $data['file_type'];
            if (file_exists($path.'/'.$name)) {
                $ext = pathinfo($path.'/'.$name,PATHINFO_EXTENSION);
                $type = (!empty($filetype)) ? $filetype : (isset($this->_allowedFiles[$ext]) ? $this->_allowedFiles[$ext] : '');

                $file = file_get_contents($path.'/'.$name);
                header("Content-type:".$type);
                header('Content-Disposition: attachment; filename="'.$filename.'"');
                header('Content-Length: ' . strlen($file));
                echo $file;
                Yii::app()->end();
            } else {
                throw new CHttpException(404,'File not found.');
            }
        } else {
            throw new CHttpException(404,'ID not match.');
        }
    }

    public function fileRemove($fileId) {
        $mast_id = $this->getMasterId(false);

        $company_id = $this->getDocId($mast_id);     // 公司ID

        // 域名
        if(Yii::app()->params['envSuffix']=='dev'){
            $domain = 'http://localhost:10004';
        }elseif (Yii::app()->params['envSuffix']=='uat'){
            $domain = 'https://uatapps.lbsapps.cn/nu';
        }else{
            $domain = 'https://dms.lbsapps.cn/nu';
        }

        $file_mast_id = $this->getFileMasterId($fileId);
        $doc_id = $this->getDocId($file_mast_id);
        if ($mast_id == $file_mast_id) {
            $connection = Yii::app()->db;
            $transaction=$connection->beginTransaction();
            try {
                $data = array(
                    'id'=>$fileId,
                    'mast_id'=>$mast_id,
                );
                $this->saveFile($connection, 'delete', $data);

                // 更新
                $sql1 = "SELECT * FROM hr".Yii::app()->params['envSuffix'].".hr_company where id='$company_id'";
                $item = Yii::app()->db->createCommand($sql1)->queryRow();

                // 查询附件
                $sql = "SELECT a.* FROM docman".Yii::app()->params['envSuffix'].".dm_file a LEFT JOIN docman".Yii::app()->params['envSuffix'].".dm_master b ON a.mast_id = b.id WHERE b.doc_type_code='COMPANY2'";
                if($company_id){
                    $sql .=" and b.doc_id='".$company_id."' and a.remove = 'N' ";
                }

                $arrRows=Yii::app()->db->createCommand($sql)->query();
                $list = [];
                foreach ($arrRows as $key=>$val)
                {
                    array_push($list,$val);
                }

                $item['x_file_names'] = implode(',',array_column($list,'phy_file_name'));
                $item['x_lcd'] = implode(',',array_column($list, 'lcd'));
                $item['x_lud'] = implode(',',array_column($list, 'lud'));

                $list_arr = [];
                foreach ($list as $key=>$val){
                    $a = $val['phy_path_name'].'/'.$val['phy_file_name'];
                    array_push($list_arr,$a);
                }
                $item['x_list'] = implode(',',$list_arr);

                $data = [];
                $data = $item;
                $url = $domain . '/Upload/company';
                $res = $this->sendPostCompany($item,$url);

                $transaction->commit();
                $this->resetModelFileNumber();
            } catch(Exception $e) {
                $transaction->rollback();
                throw new CHttpException(404,'Cannot update.');
            }
        } else {
            throw new CHttpException(404,'ID not match.');
        }
    }

    public function getFileMasterId($fileId) {
        $suffix = Yii::app()->params['envSuffix'];
        $sql = "select mast_id from docman$suffix.dm_file where id=$fileId and remove<>'Y'";
        return Yii::app()->db->createCommand($sql)->queryScalar();
    }

    public function getDocId($masterId) {
        $suffix = Yii::app()->params['envSuffix'];
        $sql = "select doc_id from docman$suffix.dm_master where id=$masterId";
        return Yii::app()->db->createCommand($sql)->queryScalar();
    }

    public function save($type) {
        $mast_id = $this->getMasterId();
        $connection = Yii::app()->db;
        $transaction=$connection->beginTransaction();
        try {
            $this->saveFile($connection, $type);

            $transaction->commit();
        }
        catch(Exception $e) {
            $transaction->rollback();
            throw new CHttpException(404,'Cannot update.');
        }
    }

    protected function getFileName($id) {
        $rtn = array();
        $suffix = Yii::app()->params['envSuffix'];
        $sql = "select phy_file_name, phy_path_name, display_name, file_type 
				from docman$suffix.dm_file
				where id = $id
			";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        foreach ($rows as $row) {
            $rtn['phy_path_name'] = $row['phy_path_name'];
            $rtn['phy_file_name'] = $row['phy_file_name'];
            $rtn['display_name'] = $row['display_name'];
            $rtn['file_type'] = $row['file_type'];
            break;
        }
        return $rtn;
    }

    protected function saveFile(&$connection, $type, $data) {
        $suffix = Yii::app()->params['envSuffix'];

        $sql = '';
        switch ($type) {
            case 'delete':
                $sql = "update docman$suffix.dm_file set remove = 'Y', luu = :luu where id = :id";
                break;
            default:
                $sql = "insert into docman$suffix.dm_file
							(mast_id, phy_file_name, phy_path_name, file_type,
							display_name, archive, remove, lcu, luu)
						values 
							(:mast_id, :phy_file_name, :phy_path_name, :file_type,
							:display_name, 'N', 'N', :lcu, :luu)
					";
                break;
        }

        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$data['id'],PDO::PARAM_INT);
        if (strpos($sql,':mast_id')!==false)
            $command->bindParam(':mast_id',$data['mast_id'],PDO::PARAM_INT);
        if (strpos($sql,':phy_file_name')!==false)
            $command->bindParam(':phy_file_name',$data['phy_file_name'],PDO::PARAM_STR);
        if (strpos($sql,':phy_path_name')!==false)
            $command->bindParam(':phy_path_name',$data['phy_path_name'],PDO::PARAM_STR);
        if (strpos($sql,':display_name')!==false)
            $command->bindParam(':display_name',$data['display_name'],PDO::PARAM_STR);
        if (strpos($sql,':display_name')!==false)
            $command->bindParam(':file_type',$data['file_type'],PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        $command->execute();
    }

    public function retrieve() {
        $rtn = array();

        $suffix = Yii::app()->params['envSuffix'];
        $type = $this->docType;
        $id = empty($this->docId) ? 0 : $this->docId;
        $mastId = empty($this->masterId) ? 0 : $this->masterId;
        if ($id==0 && $mastId==0) return $rtn;
        /*
                $sql = "select
                            a.id, a.doc_type_code, a.doc_id,
                            b.id as file_id, b.display_name, b.archive, b.lcd,
                            c.id as field_id, c.field_code, c.field_value
                        from
                            docman.dm_master a inner join docman.dm_file b on a.id=b.mast_id
                            left outer join docman.dm_detail c on a.id=c.mast_id
                        where
                            a.doc_type_code='$type' and a.doc_id=$id and b.remove='N'
                    ";
        */
        $sql = ($mastId > 0)
            ? "select
						a.id, a.doc_type_code, a.doc_id, 
						b.id as file_id, b.display_name, b.archive, b.lcd, b.file_type, b.phy_file_name, b.phy_path_name   
					from 
						docman$suffix.dm_master a inner join docman$suffix.dm_file b on a.id=b.mast_id 
					where 
						a.id=$mastId and b.remove='N'
					order by b.display_name, b.lcd desc
				"
            : "select
						a.id, a.doc_type_code, a.doc_id, 
						b.id as file_id, b.display_name, b.archive, b.lcd, b.file_type, b.phy_file_name, b.phy_path_name   
					from 
						docman$suffix.dm_master a inner join docman$suffix.dm_file b on a.id=b.mast_id 
					where 
						a.doc_type_code='$type' and a.doc_id=$id and b.remove='N'
					order by b.display_name, b.lcd desc
				";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        if (count($rows) > 0) {
            /*
                        $oid = 0;
                        $temp = array();
                        $orow = array();
                        foreach ($rows as $row) {
                            if ($oid == 0) $oid = $row['file_id'];
                            if ($row['file_id'] != $oid) {
                                $rtn[] = array(
                                            'id'=>$orow'id',
                                            'doc_type_code'=>$orow'doc_type_code',
                                            'doc_id'=>$orow'doc_id',
                                            'file_id'=>$orow'file_id',
                                            'display_name'=>$orow'display_name',
                                            'archive'=>$orow'archive',
                                            'detail'=>$temp,
                                        );
                                $oid = $row['file_id'];
                                $temp = array();
                            } else {
                                $orow = $row;
                                $temp[$row['field_code']] = array('id'=>$row['field_id'[,'value'=>$row['field_value']);
                            }
                        }
            */
            foreach ($rows as $row) {
                $rtn[] = array(
                    'id'=>$row['id'],
                    'doc_type_code'=>$row['doc_type_code'],
                    'doc_id'=>$row['doc_id'],
                    'file_id'=>$row['file_id'],
                    'file_type'=>$row['file_type'],
                    'display_name'=>$row['display_name'],
                    'phy_file_name'=>$row['phy_file_name'],
                    'phy_path_name'=>$row['phy_path_name'],
                    'archive'=>$row['archive'],
                    'lcd'=>$row['lcd'],
                );
            }
        }
        return $rtn;
    }

    private function getImgHtml($row){
        static $no_calls = 0;//判断调用了多少次
        $no_calls++;
        $html = "";
        if (strpos($row["file_type"],'image/')!==false){
            $path = $row["phy_path_name"]."/".$row["phy_file_name"];
            if(is_file($path)){
                $size = filesize($path);
                $imgSrc = Yii::app()->baseUrl."/images/user-icon.png";
                $maxSize = 4*1024*1024;//4M
                if($no_calls>20||$size>$maxSize){
                    $html = "<span class='fa fa-search-plus'><img src='{$imgSrc}' data-src=\"{$size}\" data-path='{$path}' class='hide'></span>";
                }else{
                    $imgData=file_get_contents($path);
                    $imgData = base64_encode($imgData);
                    $imgData="data:{$row["file_type"]};base64,{$imgData}";
                    $html = "<span class='fa fa-search-plus'><img src='{$imgSrc}' data-src=\"{$imgData}\" class='hide'></span>";
                }
            }
        }
        return $html;
    }

    public function genTableFileList($readonly,$delBtn=true) {
        $rtn = "";
        $reccnt = 0;
        $filelist = $this->retrieve();
        if (empty($filelist)) {
            $msg = Yii::t('dialog','No File Record');
            $rtn = "<tr><td>&nbsp;</td><td colspan=2>$msg</td></tr>";
        } else {
            $title1 = Yii::t('dialog','Download');
            $title2 = Yii::t('dialog','Remove');
            $doctype = strtolower($this->docType);
            foreach ($filelist as $filerec) {
                $mid = $filerec['id'];
                $did = $this->docId;
                $id = $filerec['file_id'];
                $x = $this->masterId;
                $y = $this->formId;
                $imgHtml = $this->getImgHtml($filerec);//图片资源
                $clickImg = empty($imgHtml)?"":" search_box_img";
                $vbutton = ($this->docId==0) ? "" : "<a href=\"#\" onclick=\"downloadFile$doctype($mid, $did, $id);return false;\" title=\"$title1\"><span class=\"fa fa-download\"></span></a>";
                $dbutton = $readonly ? "" : "<a href=\"#\" onclick=\"removeFile$doctype($id);return false;\"><span class=\"fa fa-remove\" title=\"$title2\"></span></a>";
                if(!$delBtn){
                    $dbutton  = "";
                }
                $fname = $filerec['display_name'];
                $ldate = $filerec['lcd'];
                $rtn .= "<tr><td>$vbutton&nbsp;&nbsp;$dbutton</td><td class='{$clickImg}'>$fname {$imgHtml}</td><td>$ldate</td></tr>";
                $reccnt++;
            }
        }
        $rtn .= "<tr><td colspan=3>";
        $rtn .= CHtml::hiddenField($this->formId.'[docMasterId]['.strtolower($this->docType).']',$this->masterId, array('id'=>$this->formId.'_docMasterId_'.strtolower($this->docType),));
        $rtn .= CHtml::hiddenField($this->formId.'[no_of_attm]['.strtolower($this->docType).']',$reccnt, array('id'=>$this->formId.'_no_of_attm_'.strtolower($this->docType),));
        $rtn .= "</td></tr>";

        return $rtn;
    }

    public function genFileListView() {
        $rtn = "";
        $filelist = $this->retrieve();
        $reccnt = 0;
        if (empty($filelist)) {
            $msg = Yii::t('dialog','No File Record');
            $rtn = "<tr><td>&nbsp;</td><td colspan=2>$msg</td></tr>";
        } else {
            $title1 = Yii::t('dialog','Download');
            $doctype = strtolower($this->docType);
            foreach ($filelist as $filerec) {
                $mid = $filerec['id'];
                $did = $this->docId;
                $id = $filerec['file_id'];
                $imgHtml = $this->getImgHtml($filerec);//图片资源
                $clickImg = empty($imgHtml)?"":" search_box_img";
                $vbutton = "<a href=\"#\" onclick=\"downloadFile$doctype($mid, $did, $id);return false;\" title=\"$title1\"><span class=\"fa fa-download\"></span></a>";
                $fname = $filerec['display_name'];
                $ldate = $filerec['lcd'];
                $rtn .= "<tr><td>$vbutton</td><td class='{$clickImg}'>$fname {$imgHtml}</td><td>$ldate</td></tr>";
                $reccnt++;
            }
        }

        return $rtn;
    }

    public function updateDocId(&$connection, $masterId) {
        $suffix = Yii::app()->params['envSuffix'];
        $docId = $this->docId;
        $sql = "update docman$suffix.dm_master set doc_id=$docId where id=$masterId";
        $connection->createCommand($sql)->execute();
        $this->resetModelFileNumber();
    }

    private function hashDirectory($filename) {
        $hashcode = hash('md5',$filename);
        $firstDir = $hashcode & 255;
        $tmp = sprintf("%x",$firstDir);
        $path = $this->baseDir.'/'.$tmp;
        if (!file_exists($path)) mkdir($path);
        $secondDir = ($hashcode >> 8) & 255;
        $tmp = sprintf("%x",$secondDir);
        $path .= '/'.$tmp;
        if (!file_exists($path)) mkdir($path);
        return $path;
    }

    //由於列表需要顯示附件數量，導致列表打開太慢，所以保存附件數量
    public function resetModelFileNumber(){
        $model = new $this->formId();
        $funList = get_class_methods($model);
        if(in_array("resetFileSum",$funList)){
            $model->resetFileSum($this->docId);
        }
    }
}
?>