<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\CodeGenerator;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Request $request
     * @param CodeGenerator $codeGenerator
     * @param Mailer $mailer
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function register(
        UserPasswordEncoderInterface $passwordEncoder,
        Request $request,
        CodeGenerator $codeGenerator,
        Mailer $mailer)
    {
        $user = new User;
        $form = $this->createForm(
            UserType::class,
            $user
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());

            $user->setPassword($password);
            $user->setConfirmationCode($codeGenerator->getConfirmationCode());

            $em = $this->getDoctrine()->getManager();

            $em->persist($user);
            $em->flush();
            $mailer->sendConfirmationMessage($user);
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/confirm/{code}", name="email_confirmation")
     * @param UserRepository $userRepository
     * @param string $code
     * @return Response
     */
    public function confirmEmail(UserRepository $userRepository, string $code)
    {
        /** @var User $user */
        $user = $userRepository->findOneBy(['confirmationCode' => $code]);

        if (is_null($user)) {
            return new Response('Not found', 404);
        }

        $user->setEnabled(true);
        $user->setConfirmationCode('');
        $em = $this->getDoctrine()->getManager();

        $em->persist($user);
        $em->flush();

        return $this->render('security/account_confirm.html.twig',
            ['user' => $user]
        );
    }
}
