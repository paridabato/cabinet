<?php

namespace App\Twig;

use App\WebApi;
use Symfony\Component\HttpFoundation\Request;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ClientTwig extends AbstractExtension
{
    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('client', [$this, 'getClient']),
            new TwigFunction('messages', [$this, 'getMessages'])
        ];
    }

    public function getClient($ClientID)
    {
        return WebApi::getInstance()->getClientData($ClientID, '');
    }

    public function getMessages($ClientID)
    {
        return WebApi::getInstance()->getClientMessages($ClientID, '');
    }
}