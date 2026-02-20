<?php

namespace app\models;

use yii\base\Model;

class PhysicalForm extends Model
{
    const POWDER = 1;
    const SOLUTION = 2;

    public $id;
    public $label;
    public $first_letter;
    public $class;

    public static $data = [
        self::POWDER => ['label' => 'Polvere', 'first_letter' => 'P', 'class' => ' bg-polvere'],
        self::SOLUTION => ['label' => 'Soluzione', 'first_letter' => 'S', 'class' => ' bg-soluzione'],
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
        return '<span class="badge'.self::$data[$id]['class'].$blockClass.'" title="'.self::$data[$id]['label'].'">' . self::$data[$id]['label'] . '</span>';
    }

    public static function getIcon($id)
    {
        return '<span class="badge'.self::$data[$id]['class'].'" title="'.self::$data[$id]['label'].'">' . self::$data[$id]['first_letter'] . '</span>';
    }
}
