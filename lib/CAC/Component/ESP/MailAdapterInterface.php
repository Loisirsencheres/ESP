<?php

namespace CAC\Component\ESP;

interface MailAdapterInterface
{
    /**
     * Send a mail
     *
     * @param array  $users
     * @param string $content
     *
     * @return boolean
     */
    public function send(array $users, $subject, $body);

    /**
     * Send an Email based on a template at the ESP
     *
     * @param integer|string $templateId
     * @param array $users
     * @param null $subject
     * @param array $params
     *
     * @param string $group
     * @param null $fromName
     *
     * @return bool
     */
    public function sendByTemplate($templateId, array $users, $subject = null, $params = array(), $group = 'default', $fromName = null);
}
