<?php

namespace app\helpers;

use app\models\documents\documentType\DocumentType;
use kartik\date\DatePicker;

class ViewHelper
{
    public static function defaultDatePickerConfig()
    {
        return [
            'type' => DatePicker::TYPE_INPUT,
            'removeButton' => false,
            'pickerButton' => false,
            'addInputCss' => 'form-control form-control-sm',
            'options' => ['autocomplete' => 'off'],
            'pluginOptions' => [
                'orientation' => 'bottom left',
                'autoclose' => true,
                'format' => 'dd.mm.yyyy',
            ]
        ];
    }

    public static function defaultSelect2Config()
    {
        return [
            'options' => [
                'autocomplete' => 'off',
                'class' => 'form-control-sm',
                'placeholder' => '',
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ];
    }
    
    public static function getDocumentsCountInterval($from, $to)
    {
        if (($from == '0' || $from == '1') && $to == '2147483647') return '-';
        
        $fromText = "от $from";
        $toText = ($to != '2147483647') ? " до $to" : "";
        
        return $fromText.$toText;
    }    
    
    public static function getDocumentMessage($msg_id)
    {
        $documentType = DocumentType::find()
                                        ->where(['status' => 1])
                                        ->where(['type_id' => $msg_id])
                                        ->one();
        

        
        return $documentType->message_name.' ('.$documentType->message_code.')';
    }
}
