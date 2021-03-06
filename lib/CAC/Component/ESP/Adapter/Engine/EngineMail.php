<?php

namespace CAC\Component\ESP\Adapter\Engine;


use CAC\Component\ESP\Api\Engine\EngineApi;
use CAC\Component\ESP\ESPException;
use CAC\Component\ESP\MailAdapterInterface;
use \ForceUTF8\Encoding;

class EngineMail implements MailAdapterInterface
{
    /**
     * @var EngineApi
     */
    private $api;

    /**
     * @var array
     */
    private $options;

    public function __construct(EngineApi $api, array $options = array())
    {
        if (isset($options['templates']) && !isset($options['templates']['default'])) {
            // BC-Compatible: add the templates to the default group
            $options['templates']['default'] = $options['templates'];
        }

        $this->api = $api;
        $this->options = array_replace_recursive(
            array(
                'fromName' => 'Crazy Awesome ESP',
                'fromEmail' => 'changeme@crazyawesomecompany.com',
                'replyTo' => null,
                'templates' => array(),
                'globals' => array()
            ),
            $options
        );
    }

    /**
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
        // First create a mailing ID
        $mailingId = $this->api->createMailingFromContent(
            $body,
            $body,
            Encoding::toLatin1($subject),
            Encoding::toLatin1($this->options['fromName']),
            $this->options['fromEmail'],
            $this->options['replyTo']
        );

        return (bool) $this->api->sendMailing($mailingId, $users);
    }

    /**
     * @see \CAC\Component\ESP\MailAdapterInterface::sendByTemplate()
     *
     * @param int|string $templateId
     * @param array $users
     * @param null $subject
     * @param array $params
     * @param string $group
     *
     * @param null $fromName
     *
     * @return bool
     */
    public function sendByTemplate($templateId, array $users, $subject = null, $params = array(), $group = 'default', $fromName = null)
    {
        $fromName =$fromName == null ? $this->options['fromName'] : $fromName;
        $fromEmail = $this->options['fromEmail'];
        $replyTo = $this->options['replyTo'];
        if (!is_numeric($templateId)) {
            $template = $this->findTemplateByName($templateId, $group);
            $templateId = $template['id'];
            $subject = $subject == null ? $template['subject'] : $subject;
            $fromName = utf8_encode(isset($template['fromName'])?$template['fromName']:$fromName);
            $fromEmail = isset($template['fromEmail'])?$template['fromEmail']:$fromEmail;
            $replyTo = isset($template['replyTo'])?$template['replyTo']:$replyTo;

            if (isset($template['mailinglist'])) {
                $this->api->selectMailinglist($template['mailinglist']);
            }
        }

        for ($i = 0; $i < count($users); $i++) {
            $users[$i] = array_merge($this->options['globals'], $users[$i]);
        }

        $mailingId = $this->api->createMailingFromTemplate(
            $templateId,
            Encoding::toLatin1($subject),
            Encoding::toLatin1(isset($params['fromName'])? $params['fromName'] : $fromName),
            isset($params['fromEmail'])? $params['fromEmail'] : $fromEmail,
            isset($params['replyTo'])? $params['replyTo'] : $replyTo
        );

        return (bool) $this->api->sendMailing($mailingId, $users, null, (isset($template['mailinglist']) ? $template['mailinglist'] : null));
    }

    /**
     * @param string $name
     * @param string $group
     *
     * @return array
     *
     * @throws ESPException
     */
    private function findTemplateByName($name, $group = 'default')
    {
        if (!array_key_exists($name, $this->options['templates'][$group])) {
            throw new ESPException("Template configuration could not be found");
        }

        return $this->options['templates'][$group][$name];
    }

    /**
     * Add a new template
     *
     * @param string $name
     * @param integer $id
     * @param string $subject
     * @param string $group
     */
    public function addTemplate($name, $id, $subject, $group = 'default')
    {
        if (!array_key_exists($group, $this->options['templates'])) {
            $this->options['templates'][$group] = array();
        }

        $this->options['templates'][$group][$name] = array('id' => $id, 'subject' => $subject);
    }
}
