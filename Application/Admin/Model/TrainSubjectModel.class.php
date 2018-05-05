<?php
namespace Admin\Model;

use Think\Model\RelationModel;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/19
 * Time: 11:39
 */
class TrainSubjectModel extends RelationModel
{
    protected $_link = array(
        'TrainMemberOne' => array(
            'mapping_type' => self::HAS_MANY,
            'class_name' => 'TrainMember',
            'foreign_key'=>'train_id',
            'condition'=>'status=1'
        ),
        'TrainMemberZero' => array(
            'mapping_type' => self::HAS_MANY,
            'class_name' => 'TrainMember',
            'foreign_key'=>'train_id',
        ),
    );
}