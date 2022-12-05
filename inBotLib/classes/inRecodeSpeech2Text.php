<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of inRecodeSpeech2Text
 *
 * @author drweb_000
 */
class inRecodeSpeech2Text extends inRecodeAbstract {
    const URL_AUTH = 'https://ngw.devices.sberbank.ru:9443/api/v2/oauth';
    const URL_UPLOAD = 'https://smartspeech.sber.ru/rest/v1/data:upload'; 
    const URL_RECOGNIZE = 'https://smartspeech.sber.ru/rest/v1/speech:async_recognize';
    const URL_STATUS = 'https://smartspeech.sber.ru/rest/v1/task:get';
    const URL_DOWNLOAD = 'https://smartspeech.sber.ru/rest/v1/data:download';
    
    const SLEEP_TIME = 10;
    
    private $access_token;
    private $token_expires;
    private $request_file_id;
    private $task_id;
    private $response_file_id;
    private $error;


    public function auth($client_id, $client_secret, $scope) {
        $authorization = 'Basic '. base64_encode("$client_id:$client_secret");
        $rquid = vsprintf('%s%s-%s-4000-8%.3s-%s%s%s0',str_split(dechex( (int)(microtime(true) * 1000) ) . bin2hex( random_bytes(8) ),4));
        
        $result = $this->post(self::URL_AUTH, 
                [
                    'scope' => $scope
                ],
                [
                    CURLOPT_HTTPHEADER => ["Authorization: $authorization", "RqUID: $rquid"],
                ]);
        
        $data = json_decode($result);
        $this->access_token = $data->access_token;
        $this->token_expires = $data->expires_at;
        
        return true;
    }
    
    public function recode($in_file, $out_file) {
        
        if (!$this->access_token || $this->token_expires < time()) {
            $this->auth(SBER_CLIENT_ID, SBER_SECRET, SBER_SCOPE);
        }
        
        $this->sendFile($in_file);
        $this->startProcessing();
        while ( true ) {
            $done = $this->processingDone();
            if ($done === null) {
                throw new Exception($error);
            } elseif ($done === true) {
                break;
            }
            sleep(self::SLEEP_TIME);
        }
        
        $this->receiveFile($out_file);
        return $out_file;
    }
    
    protected function sendFile($file) {
        $result = $this->post(self::URL_UPLOAD, [], [
            CURLOPT_HTTPHEADER => ["Authorization: Bearer $this->access_token"],
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_POSTFIELDS => file_get_contents($file)
        ]);
        
        $data = json_decode($result);
        $this->request_file_id = $data->result->request_file_id;
        return true;
    }

    protected function startProcessing() {
        $result = $this->post(self::URL_RECOGNIZE, [], [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $this->access_token",
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode([
                'request_file_id' => $this->request_file_id,
                'options' => [
                    'audio_encoding' => 'MP3'
                ]
            ])
        ]);
        $data = json_decode($result);
        $this->task_id = $data->result->id;
        return true;
    }

    protected function processingDone() {
        $result = $this->get(self::URL_STATUS, [ 'id' => $this->task_id ], [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $this->access_token",
            ]
        ]);
        $data = json_decode($result);
        $status = $data->result->status;
        if ($status == 'DONE') {
            $this->response_file_id = $data->result->response_file_id;
            return true;
        } elseif ($status == 'ERROR') {
            $this->error = $data->result->error;
            return null;
        }
        return false;
    }
    
    protected function receiveFile($file) {
        $result = $this->get(self::URL_DOWNLOAD, ['response_file_id' => $this->response_file_id], [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $this->access_token",
            ]
        ]);
        
        $data = json_decode($result);
        $text = [];
        
        foreach ($data as $unit) {
            $sentences = [];
            foreach ($unit->results as $sentence) {
                $sentences[] = $sentence->normalized_text;
            }
            $text[] = implode(' ', $sentences);
        }
        
        file_put_contents($file, implode("\n\n", $text));
        return true;
    }
    
    private function post($url, array $post = [], array $options = array()) {
        $defaults = array(
            CURLOPT_POST => true,
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query($post),
            CURLOPT_SSL_VERIFYPEER => false,
        );

        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));
        if( ! $result = curl_exec($ch))
        {
            throw new Exception(curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }

    private function get($url, array $params = [], array $options = array()) {
        $defaults = array(
            CURLOPT_URL => $url. '?'. http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
        );

        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));
        if( ! $result = curl_exec($ch))
        {
            throw new Exception(curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }

    protected function _test_data() {
        return [
            'post' => '_test_skip_',
            'get' => '_test_skip_',
            'processingDone' => '_test_skip_',
            'auth' => [
                ['7332cfbe-30a1-4983-a941-7be4867302c0', 'c76ecd12-5051-4a4b-83b7-3fc6b550a09c', 'SALUTE_SPEECH_CORP', true]
            ],
            'sendFile' => [
                ['test_files/05_test.mp3', true]
            ],
            'startProcessing' => [
                [true]
            ],
        ];
    }
}
