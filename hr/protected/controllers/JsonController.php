<?php

class JsonController extends Controller
{
    public function filters()
    {
        return array(
            'enforceRegisteredStation - error', //apply station checking, except error page
            'enforceSessionExpiration - error,login,logout',
            'enforceNoConcurrentLogin - error,login,logout',
            'accessControl - error,login,index,home', // perform access control for CRUD operations
        );
    }

    public function accessRules()
    {
        return array(
            array('allow',
                'users' => array('@'),
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Declares class-based actions.
     */
    public function actions()
    {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    /**
     * type 0部门1职位
     * city 城市
     * dept_id 部门id
     * dept_class 职位类别
     */
    public function actionIndex()
    {
        if($_GET['user']!='admin'){
			exit;
		}
        // 部门
        $items = Yii::app()->db->createCommand('SELECT id,name FROM hr_dept WHERE type = 0 ORDER BY id ASC ')->queryAll();

        // 职位
        $list = Yii::app()->db->createCommand('SELECT id,name,dept_id FROM hr_dept WHERE type = 1 ORDER BY id ASC ')->queryAll();

        $data['items'] = $items;
        $data['list'] = $list;

        echo json_encode($data);

    }
}
