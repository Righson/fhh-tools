<?php
/**
 * Created by PhpStorm.
 * User: programmer
 * Date: 07.12.2017
 * Time: 17:17
 */

namespace exceptions;

class DataBaseException extends CommonException
{
    protected $logfileName = 'db_log.txt';

    public function toJSON($clientData)
    {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }

        $this->log();

        return json_encode(['error' => "DataBaseError", "client"=>$clientData]); // File: $file Line:". $this->getLine(). ""]);
    }


}