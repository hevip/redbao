<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 17-9-4
 * Time: 下午5:09
 */
namespace greatsir\Notice\Channel;

use greatsir\Notice\SocketNotice;
class WebSocket extends SocketNotice
{

    private $message;
    protected $persons;
    protected $channel;
    /**
     * 设置通知消息
     * @param $content　内容
     */
    public function setMessage($content)
    {
        // TODO: Implement setMessage() method.
        $this->message = $content;
    }
    /*
     * 设置通知接受人
     */
    public function setPersons()
    {
        // TODO: Implement setPersons() method.
    }

    /**
     * 设置通知发送渠道
     */
    public function setChannel($host,$port)
    {
        // TODO: Implement setChannel() method.
        $this->channel['host'] = $host;
        $this->channel['port'] = $port;
    }
    public function getMessage()
    {
        return $this->message;
    }
}