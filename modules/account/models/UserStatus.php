<?php

namespace app\modules\account\models;

use yii\base\Model;

class UserStatus extends Model
{
    
    public static $data = [
        0 => ['label' => 'Disattivato', 'badge' => 'Disattivato', 'class' => 'badge bg-danger'],
        1 => ['label' => 'Attivo', 'badge' => 'Attivo', 'class' => 'badge bg-success'],
    ];


    public static function getList()
    {
        $result = [];
        foreach (self::$data as $key => $value) {
            $result[$key] = $value['label'];
        }
        return $result;
    }

    public static function getLabel($id)
    {
        return self::$data[$id]['label'];
    }
    public static function getBadge($id, $dblock=true)
    {
        $blockClass = '';
        if($dblock)
        {
            $blockClass = ' d-block';
        }
        return '<span class="'.self::$data[$id]['class'].$blockClass.'" title="'.self::$data[$id]['label'].'">' . self::$data[$id]['badge'] . '</span>';
    }
}
