<?php


namespace App\EventSubscriber;


use App\Event\RegisterUserEvent;
use App\Service\Mailer;
use \Twig;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * UserSubscriber constructor.
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }


    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            RegisterUserEvent::NAME => 'onUserRegister'
        ];
    }

    /**
     * @param RegisterUserEvent $registerUserEvent
     * @throws Twig\Error\LoaderError
     * @throws Twig\Error\RuntimeError
     * @throws Twig\Error\SyntaxError
     */
    public function onUserRegister(RegisterUserEvent $registerUserEvent){
        $this->mailer->sendConfirmationMessage($registerUserEvent->getRegisteredUser());
    }
}