<?php

namespace app\common\models;

use yii\db\Expression;

/**
 * This is the model class for table "{{%subject}}".
 *
 * @property integer $id
 * @property integer $car_id
 * @property string $name
 * @property string $desc
 * @property integer $created_at
 */
class Subject extends Model
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%subject}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'car_id'], 'required'],
            [['car_id', 'status', 'sort'], 'integer'],
            [['name'], 'string', 'max' => 255, 'min' => 2],
            [['desc'], 'string', 'min' => 2, 'max' => 1000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '科目分类信息',
            'car_id' => '车型ID',
            'desc' => '说明',
            'status' => '状态',
            'created_at' => '创建时间',
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) $this->created_at = new Expression('UNIX_TIMESTAMP()');
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    // 获取车型信息
    public function getCar()
    {
        return $this->hasOne(CarType::className(), ['id' => 'car_id']);
    }

    // 获取章节信息
    public function getChapters()
    {
        return $this->hasMany(Chapter::className(), ['subject_id' => 'id']);
    }

    public static function getSubject($where = [])
    {
        $arrReturn = [];
        $where = array_merge(['status' => 1], $where);
        // 查询所有车型
        $cars = CarType::find()->indexBy('id')->all();
        // 查询所有的科目
        $subject = self::find()->where($where)->asArray()->all();
        if ($subject) {
            foreach ($subject as $value) {
                $arrReturn[$value['id']] = isset($cars[$value['car_id']]) ? $cars[$value['car_id']]->name.'--'.$value['name'] : $value['name'];
            }
        }

        return $arrReturn;
    }
}
