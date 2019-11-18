<?php
namespace frontend\controllers;

use Yii;
use yii\web\UploadedFile;
use yii\helpers\BaseArrayHelper;
use frontend\controllers\BaseController;
use frontend\models\ShoppingCollect;

class CollectController extends BaseController {
    /**
     * 添加到我的收藏
     * @param is_sign
     */
    public function actionLike() {
        $goodsid = $this->request->get('id');
        $uid       = $this->userinfo['uid'];
        if ($uid === 0) {
            return json_encode([
                'success' => false,
                'msg' => '请先登录，再收藏商品！'
            ]);
        }

        $add = true;
        $ShoppingCollectModel = new ShoppingCollect();

        //查询该产品是否被收藏
        $CollectData = $ShoppingCollectModel->getCollectDataOne($uid, $goodsid);
        if ($CollectData === null) {
            $ShoppingCollectModel->uid = $uid;
            $ShoppingCollectModel->goodsid = $goodsid;
            $ShoppingCollectModel->createdt = time();
            $ShoppingCollectModel->status = 1;
            $ShoppingCollectModel->save();
        } else {
            if ($CollectData->status==1) {
               $add = false;
            }
            $CollectData->status = $CollectData->status ? 0 : 1;
            $CollectData->save();
        }

        return json_encode([
            'success' => true,
            'add' => $add
        ]);
    }

    public function actionIsIn() {
        $goodsid = $this->request->get('id');
        $uid     = $this->userinfo['uid'];

        $isIn = false; //默认值

        $ShoppingCollectModel = new ShoppingCollect();
        $CollectData          = $ShoppingCollectModel->getCollectDataOne($uid, $goodsid);
        if (isset($CollectData->status) && $CollectData->status) {
            $isIn = true;
        }

        echo json_encode([
            'sucess' => true,
            'is_in'  => $isIn,
        ]);
    }
}