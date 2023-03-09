<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of inRecode2Bitrate128
 *
 * @author drweb_000
 */
class inRecode2Bitrate48 extends inRecodeAbstract {
    const TARGET_BITRATE="48k";
    const TARGET_CODEC="aac";
    
    public function recode($in_file, $out_file) {
        $ffmpeg_cmd = sprintf("ffmpeg -loglevel quiet -y -i \"%s\" -c:a %s -b:a %s \"%s\"", $in_file, self::TARGET_CODEC, self::TARGET_BITRATE, $out_file);
        
        $exit_code = 0;
        system($ffmpeg_cmd, $exit_code);
        
        if ($exit_code !== 0) {
            throw new Exception("Error calling ffmpeg command. Exit code: $exit_code", -10004);
        }
        
        return $out_file;
    }

}
