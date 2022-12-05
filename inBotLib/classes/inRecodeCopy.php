<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of inRecodeCopy
 *
 * @author drweb_000
 */
class inRecodeCopy extends inRecodeAbstract {
    
    public function recode($in_file, $out_file) {
        file_put_contents($out_file, file_get_contents($in_file));
        return $out_file;
    }

}
