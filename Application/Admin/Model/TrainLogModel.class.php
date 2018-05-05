<?php
namespace Admin\Model;

use Think\Model\RelationModel;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/19
 * Time: 11:39
 */
class TrainLogModel extends RelationModel
{
    protected $_link = array(
        'TrainSubject' => array(
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'TrainSubject',
            'foreign_key' => 'subject_id',
        )
    );
}