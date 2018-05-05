<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/26
 * Time: 8:36
 */

namespace Admin\Model;


use Think\Model\RelationModel;

class UsersModel extends RelationModel
{

    protected $_link = array(
        'UsersBan' => array(
            'class_name' => 'AuthorizedSize',
            'mapping_type' => self::BELONGS_TO,
            'foreign_key' => 'ban',
            'mapping_fields'=>'bz_name',
            'as_fields'=>'bz_name:banTitle'
        ),
        'UsersPai' => array(
            'class_name' => 'AuthorizedSize',
            'mapping_type' => self::BELONGS_TO,
            'foreign_key' => 'pai',
            'mapping_fields'=>'bz_name',
            'as_fields'=>'bz_name:paiTitle'
        ),
        'UsersLian' => array(
            'class_name' => 'AuthorizedSize',
            'mapping_type' => self::BELONGS_TO,
            'foreign_key' => 'lian',
            'mapping_fields'=>'bz_name',
            'as_fields'=>'bz_name:lianTitle'
        ),
        'UsersYing' => array(
            'class_name' => 'AuthorizedSize',
            'mapping_type' => self::BELONGS_TO,
            'foreign_key' => 'ying',
            'mapping_fields'=>'bz_name',
            'as_fields'=>'bz_name:yingTitle'
        ),
        'UsersTuan' => array(
            'class_name' => 'AuthorizedSize',
            'mapping_type' => self::BELONGS_TO,
            'foreign_key' => 'tuan',
            'mapping_fields'=>'bz_name',
            'as_fields'=>'bz_name:tuanTitle'
        ),
        'UsersShi' => array(
            'class_name' => 'AuthorizedSize',
            'mapping_type' => self::BELONGS_TO,
            'foreign_key' => 'shi',
            'mapping_fields'=>'bz_name',
            'as_fields'=>'bz_name:shiTitle'
        ),
        'UsersZhiwu' => array(
            'class_name' => 'Duties',
            'mapping_type' => self::BELONGS_TO,
            'foreign_key' => 'duties',
            'mapping_fields'=>'duties_name',
            'as_fields'=>'duties_name:dutiesTitle'
        ),
        'UsersZhuanye' => array(
            'class_name' => 'Major',
            'mapping_type' => self::BELONGS_TO,
            'foreign_key' => 'major',
            'mapping_fields'=>'major_name',
            'as_fields'=>'major_name:majorTitle'
        ),
        'UsersJunxian' => array(
            'class_name' => 'Rank',
            'mapping_type' => self::BELONGS_TO,
            'foreign_key' => 'rank',
            'mapping_fields'=>'rank_name',
            'as_fields'=>'rank_name:rankTitle'
        ),

    );
}