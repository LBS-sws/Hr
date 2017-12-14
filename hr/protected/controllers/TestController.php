<?php

/**
 * Created by PhpStorm.
 * User: 臨時控制器（用替換員工的附件）
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class TestController extends Controller
{
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

    public function actionTest(){
//attachment
        $connection = Yii::app()->db;
        $sql = "SELECT id,name,attachment FROM hr_employee WHERE attachment !=''";
        $records = $connection->createCommand($sql)->queryAll();
        if($records){
            $uid = Yii::app()->user->id;
            $suffix = Yii::app()->params['envSuffix'];
            foreach ($records as $record){
                $connection->createCommand()->insert("docman$suffix.dm_master", array(
                    'doc_type_code'=>'EMPLOY',
                    'doc_id'=>$record["id"],
                    'lcu'=>$uid,
                ));
                $innerId = $connection->getLastInsertID();
                $attList = Yii::app()->db->createCommand()->select()->from("hr_attachment")->where('id in ('.$record["attachment"].')')->queryAll();
                foreach ($attList as $attachment){
                    $path = $attachment['path_url'];
                    $list = explode("/",$path);
                    //$phy_file_name = end($list);
                    $phy_file_name=array_pop($list);
                    $file_type = end(explode(".",$phy_file_name));
                    $file_type = $this->_allowedFiles[$file_type];
                    $phy_path_name = implode("/",$list);
                    $connection->createCommand()->insert("docman$suffix.dm_file", array(
                        'mast_id'=>$innerId,
                        'phy_file_name'=>$phy_file_name,
                        'phy_path_name'=>$phy_path_name,
                        'display_name'=>$attachment['file_name'],
                        'file_type'=>$file_type,
                        'lcu'=>$uid,
                    ));
                }
                $connection->createCommand()->update('hr_employee', array(
                    'attachment'=>"",
                ), 'id=:id', array(':id'=>$record["id"]));
                echo  "The attachment has been replaced. staff:".$record["name"]."<br>";
            }
        }else{
            echo  "No employee needs to replace the attachment<br>";
        }
        var_dump("Finish!");
        die();
    }
}