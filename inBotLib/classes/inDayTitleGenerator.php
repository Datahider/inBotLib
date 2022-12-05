<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of inDayTitleGenerator
 *
 * @author drweb_000
 */
class inDayTitleGenerator extends lhSelfTestingClass {
    const KIRYGMA = 1;
    const LECTURE = 2;
    const DISCUSSION = 3;
    const READINGS = 4;
    
    private $evening_starts = "16:00";
    
    ///////////////////////////////////////////////
    // Параметр $config - ассоциативный массив параметров, 
    // от которых зависит генерация названия дня. 
    // Содержит элементы:
    //   evening_starts - время начала "вечера", всё что до этого времени - утро
    
    public function __construct($config=[]) {
        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                if (method_exists($this, $key)) {
                    $this->$key($value); 
                } else {
                    $this->$key = $value;
                }
            }
        };
    }

    function getTitle($datetime, $recording_type) {
        $datetime_object = new DateTime($datetime);
        
        switch ($recording_type) {
            case self::KIRYGMA:
                return $this->getTitleKirygma($datetime_object);
            case self::LECTURE:
                return $this->getTitleLecture($datetime_object);
            case self::DISCUSSION:
                return $this->getTitleDiscussion($datetime_object);
            case self::READINGS:
                return $this->getTitleReadings($datetime_object);
            default:
                throw new Exception("Incorrect recording type given: $recording_type", -10003);
        }
    }
    
    private function getTitleKirygma($datetime) {
        return "!!!---НАЗВАНИЕ ДНЯ ДЛЯ ПРОПОВЕДИ---!!!";
    }
    private function getTitleLecture($datetime) {
        return "!!!---НАЗВАНИЕ ДНЯ ДЛЯ ЛЕКЦИИ---!!!";
    }
    private function getTitleDiscussion($datetime) {
        return "!!!---НАЗВАНИЕ ДНЯ ДЛЯ БЕСЕДЫ---!!!";
    }
    private function getTitleReadings($datetime) {
        return "!!!---НАЗВАНИЕ ДНЯ ДЛЯ ЕВАНГЕЛЬСКИХ ЧТЕНИЙ---!!!";
    }
    
    protected function isEveningTime($datetime) {
        if ($datetime->format("H:i") >= $this->evening_starts) {
            return true;
        }
        return false;
    }
    
    public function evening_starts(...$arg) {
        if (count($arg) == 0) {
            return $this->evening_starts;
        } elseif (count($arg) == 1) {
            $evening_starts = $arg[0];
            if (is_int($evening_starts)) {
                $this->evening_starts = sprintf("%02d:00", $evening_starts);
            } elseif (is_string($evening_starts) && preg_match("/^0?(\d{2}:\d{2})$/", "0$evening_starts", $matches)) {
                $this->evening_starts = $matches[1];
            } else {
                throw new Exception("Incorrect argument for ". __CLASS__. "->". __FUNCTION__. ": $arg[0]", -10003);
            }
            return $this->evening_starts;
        } else {
            throw new Exception("Too many arguments for ". __CLASS__. "->". __FUNCTION__. ". 0 or 1 expected. ". count($arg). " given.", -10003);
        }
    }
    
    public function _test_data() {
        return [
            'evening_starts' => [
                ["16:00"],
                ["9:30", "09:30"],
                ["08:30", 15, new Exception("", -10003)],
                ["16:00:00", new Exception("", -10003)],
                [15, "15:00"],
                ["15:00"]
            ],
            'isEveningTime' => [
                [new DateTime("2022-11-18 15:00:00"), true],
                [new DateTime("2022-11-18 14:59:59"), false]
            ],
            'getTitle' => [
                ["2022-11-18 16:00:00", 5, new Exception("", -10003)],
                ["2022-11-18 16:00:00", self::KIRYGMA, "!!!---НАЗВАНИЕ ДНЯ ДЛЯ ПРОПОВЕДИ---!!!"],
                ["2022-11-18 16:00:00", self::LECTURE, "!!!---НАЗВАНИЕ ДНЯ ДЛЯ ЛЕКЦИИ---!!!"],
                ["2022-11-18 16:00:00", self::DISCUSSION, "!!!---НАЗВАНИЕ ДНЯ ДЛЯ БЕСЕДЫ---!!!"],
                ["2022-11-18 16:00:00", self::READINGS, "!!!---НАЗВАНИЕ ДНЯ ДЛЯ ЕВАНГЕЛЬСКИХ ЧТЕНИЙ---!!!"]
            ]
        ];
    }
}
