<?php
/**
 * Created by PhpStorm.
 * User: programmer
 * Date: 15.02.2018
 * Time: 11:26
 */

namespace exceptions;


class CommonException extends \Exception
{
    protected $logfileName = 'log.txt';
    protected $meta = '';

    public function log()
    {
    }

    public function setMeta(string $meta) {
        $this->meta = $meta;
    }


    public function toArray()
    {
        return ['error' => $this->getMessage()];
    }

    /**
     * @return string
     */
    protected function getFileName()
    {
        $file = get_last_exploded($this->getFile(), DIRECTORY_SEPARATOR);
        return explode('.',$file)[0];
    }

    public function handleError($msg='')
    {
        if($msg) $this->message = $msg;
        # echo $this->toJSON();
        die();
    }
}