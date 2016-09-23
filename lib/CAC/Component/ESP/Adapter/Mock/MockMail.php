<?php

namespace CAC\Component\ESP\Adapter\Mock;

use CAC\Component\ESP\MailAdapterInterface;

class MockMail implements MailAdapterInterface
{
    /**
     * (non-PHPdoc)
     * @see \CAC\Component\ESP\MailAdapterInterface::send()
     *
     * @param array $users
     * @param $subject
     * @param $body
     *
     * @return bool
     */
    public function send(array $users, $subject, $body)
    {
        return true;
    }

    /**
     * (non-PHPdoc)
     * @see \CAC\Component\ESP\MailAdapterInterface::sendByTemplate()
     *
     * @param int|string $templateId
     * @param array $users
     * @param null $subject
     * @param array $params
     * @param string $group
     * @param null $fromName
     *
     * @return bool
     */
    public function sendByTemplate($templateId, array $users, $subject = null, $params = [], $group = 'default', $fromName = null)
    {
        return true;
    }
}
