<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of inUniversalRecoder
 *
 * @author drweb_000
 */
class inUniversalRecoder extends lhSelfTestingClass {
    protected $processing_class;    

    public function recode($in_file, $out_file, $algorithm=null) {
        if ($algorithm !== null) {
            $this->processing_class = "inRecode$algorithm";
        }
        
        if (!file_exists(__DIR__. "/". $this->processing_class. ".php")) {
            throw new Exception("Can't find processing class for algorithm: $algorithm", -10003);
        }
        
        $recoder = new $this->processing_class();
        return $recoder->recode($in_file, $out_file);
    }
       
    protected function _test_data() {
        return [
            'recode' => [
                ["test_files/01_test.txt", "test_files/01_result.txt", "incorrect", new Exception("", -10003)],
                ["test_files/01_test.txt", "test_files/01_result.txt", new Exception("", -10003)],
                ["test_files/01_test.txt", "test_files/01_result.txt", "Copy", new lhTest("inUniversalRecoder::_test_compare_files", "test_files/01_result.txt")],
                ["test_files/01_test.txt", "test_files/01_result.txt", new lhTest("inUniversalRecoder::_test_compare_files", "test_files/01_result.txt")],
                ["test_files/02_test.m4a", "test_files/02_result.m4a", "2Bitrate128", new lhTest("inUniversalRecoder::_test_compare_files", "test_files/02_result.m4a")],
                ["test_files/04_test.m4a", "test_files/04_result.mp3", "2Mp3", new lhTest("inUniversalRecoder::_test_compare_files", "test_files/04_result.m4a")],
                ["test_files/04_result.mp3", "test_files/05_result.txt", "Speech2Text", "test_files/05_result.txt"],
            ],
        ];
    }
    
    static function _test_compare_files($result) {
        if (!file_exists($result)) {
            throw new Exception("The target file does not exist: $result");
        }
        
        if (!file_exists($result. ".correct")) {
            throw new Exception("The correct file does not exist: $result.correct");
        }
        
        $data1 = file_get_contents($result);
        $data2 = file_get_contents($result. ".correct");
        if ($data1 !== $data2) {
            throw new Exception("The files are not equal", -10002);
        }
    }
}
