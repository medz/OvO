<?php

declare(strict_types=1);

namespace App\Sms\Messages;

use Exception;
use App\Sms\Channel;
use App\Sms\ScenesInterface;
use Overtrue\EasySms\Message;
use Overtrue\EasySms\Contracts\GatewayInterface;
use Overtrue\EasySms\Contracts\MessageInterface;

class TextVerificationCode extends Message
{
    protected $code;

    public function __construct(int $code)
    {
        $this->code = $code;
    }

    static public function make(int $code): MessageInterface
    {
        return new static($code);
    }

    /**
     * Return the message type.
     *
     * @return string
     */
    public function getMessageType()
    {
        return MessageInterface::TEXT_MESSAGE;
    }

    public function getContent(GatewayInterface $gateway = null)
    {
        return $this->getVendorChannel($gateway)->content($this->code);
    }

    public function getTemplate(GatewayInterface $gateway = null)
    {
        return $this->getVendorChannel($gateway)->template();
    }

    public function getData(GatewayInterface $gateway = null)
    {
        return $this->getVendorChannel($gateway)->data($this->code);
    }

    protected function getVendorChannel(?GatewayInterface $gateway = null): ScenesInterface
    {
        if (is_null($gateway)) {
            throw new Exception('SMS gateway does not support');
        }

        return Channel::make($gateway, $this);
    }
}
