<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2018/3/20 0020
 * Time: 9:43
 */
class imgdata{

    public $imgsrc;
    public $imgdata;
    public $imgform;
    public function getdir($source){
        $this->imgsrc  = $source;
    }
    public function img2data(){
        $this->_imgfrom($this->imgsrc);
        return $this->imgdata=fread(fopen($this->imgsrc,'rb'),filesize($this->imgsrc));
    }
    public function data2img(){
        header("content-type:$this->imgform");
        echo $this->imgdata;
        //echo $this->imgform;
        //imagecreatefromstring($this->imgdata);
    }
    public function _imgfrom($imgsrc){
        $info=getimagesize($imgsrc);
        //var_dump($info);
        return $this->imgform = $info['mime'];
    }
}