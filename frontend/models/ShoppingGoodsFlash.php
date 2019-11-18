<?php
namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

class ShoppingGoodsFlash extends ActiveRecord {

    public static function tableName() {
        return '{{%shopping_goods_flash}}';
    }

    /**
     * 获取当次闪购商品
     */
    public function getCurrentFlashGoods(){
        $ctime = date('His');
        $query = (new Query())
                ->select('sg.*, sg.timestart starttime')
                ->from("{{%shopping_goods}} sg")
                ->where(['sg.isflash'=>1, 'sg.status'=>1]);
        if ($ctime>=0 && $ctime<=120000) {
            $query = $query->andWhere(['between', 'sg.timestart', 0, strtotime(date('Y-m-d 12:00:00'))]);
        } else {
            $query = $query->andWhere(['between', 'sg.timestart', strtotime(date('Y-m-d 12:00:00')), strtotime(date('Y-m-d 23:59:59'))]);
        }
        // ->andWhere(['<=', 'gf.starttime', time()])
        return $query->orderBy('sg.timestart ASC')->all();
    }

    private function querySql(){
        $query = (new Query())
                ->select('id, title, marketprice, total, thumb, timestart, timeend')
                ->from(self::tableName())
                ->where(['status'=>1, 'isflash'=>1])
                ->all();

        foreach($query as $goods){
            if(date('Ymd') == date('Ymd',$goods['timestart']) && time() > $goods['timestart'] && time() < $goods['timeend']){
                $fruits[] = $goods;
            }else{
                $fruits[] = 0;
            }
        }

        if(!empty($fruits)){
            return $this->bubbleSort($fruits);
        }else{
            return false;
        }
    }

    /**
     * 当日闪购商品
     * @return array
     */
    public function getFlashGoodsCurrent(){
        $cache = Yii::$app->cache;
        $key = 'FlashData';
        $FlashData = $cache->get($key);

        if(date('H') > 10){
            if($FlashData === false){
                $cache->set($key, $this->querySql(), 60*10);        //缓存10分钟
            }
            $FlashData = $cache->get($key);         //缓存成功存储之后，重新获取值
            return $this->bubbleSort($FlashData);
        }else{
            return $this->querySql();
        }
    }

    /**
     * 明日闪购商品
     * @return array
     */
    public function getFlashGoodsTomorrow(){
        $begin = strtotime(date('Y-m-d',strtotime('+1 day')).' 00:00:00');   //第二天时间
        $end   = strtotime(date('Y-m-d',strtotime('+1 day')).' 23:59:59');   //第三天时间 ['>=', 'id', 10]
        return (new Query())
            ->select('id,title,marketprice,total,thumb,timestart, timeend')
            ->from(self::tableName())
            ->where(['status'=>1])
            ->andwhere(['>=', 'timestart', $begin])
            ->andwhere(['<=', 'timeend', $end])
            ->all();
    }

    /**
     * 下周闪购商品
     * @return array
     */
    public function getFlashGoodsNextWeek(){
        $NextWeekBegin = strtotime(date('Y-m-d', strtotime('+7 day')).' 00:00:00');       //下周开始时间
        $NextWeekend   = strtotime(date('Y-m-d', strtotime('+13 day')).' 23:59:59');      //下周结束时间
        return (new Query())
            ->select('id,title,marketprice,total,thumb,timestart, timeend')
            ->from(self::tableName())
            ->where(['status'=>1])
            ->andwhere(['>=', 'timestart', $NextWeekBegin])
            ->andwhere(['<=', 'timeend',  $NextWeekend])
            ->all();
    }

    /**
     * [bubbleSort description]
     * @param  [type] $data [description]
     * @param  [type] $key  [description]
     * @return [type]       [description]
     */
    private function bubbleSort($data, $key=null) {
        $count = count($data);
        if($count < 0) {
            return false;
        }

        for($i = 0; $i < $count; $i++) {
            for($j = $count - 1; $j > $i; $j--) {
                if($key && isset($data[$key])){//二维数组健存在
                    if($data[$j][$key] < $data[$j - 1][$key]) {
                        $tmp = $data[$j];
                        $data[$j] = $data[$j - 1];
                        $data[$j - 1] = $tmp;
                    }
                }else{ //一维数组
                    if(date('H') >= 12){
                        if($data[$j]['timestart'] > $data[$j - 1]['timestart']) { //如果是下午的时间段，则 降序
                            $tmp = $data[$j];
                            $data[$j] = $data[$j - 1];
                            $data[$j - 1] = $tmp;
                        }
                    } else {//如果是上午的时间段，则 升序
                        if($data[$j]['timestart'] < $data[$j - 1]['timestart']) {
                            $tmp = $data[$j];
                            $data[$j] = $data[$j - 1];
                            $data[$j - 1] = $tmp;
                        }
                    }
                }
            }
        }
        return $data;
    }

}