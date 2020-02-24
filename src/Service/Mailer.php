<?php
/**
 * Created by PhpStorm.
 * User: maggotik
 * Date: 2/24/20
 * Time: 8:42 PM
 */

namespace App\Service;


use App\Entity\User;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;

class Mailer
{
    const FROM_ADDRESS = 'maksmaggot@yandex.ru';
    /**
     * @var Swift_Mailer
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $twig;

    /**
     * Mailer constructor.
     * @param Swift_Mailer $mailer
     * @param Environment $twig
     */
    public function __construct(Swift_Mailer $mailer, Environment $twig)
    {

        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @param User $user
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function sendConfirmationMessage(User $user)
    {
        $messageBody = $this->twig->render('security/confirmation.html.twig',
            [
                'user' => $user
            ]);

        $message = new Swift_Message();

        $message->setSubject('Registration Success!')
            ->setFrom(self::FROM_ADDRESS)
            ->setTo($user->getEmail())
            ->setBody($messageBody, 'text/html');

        $this->mailer->send($message);
    }

}