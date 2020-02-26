<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\RegisterUserEvent;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\CodeGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class RegisterController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * RegisterController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/register", name="register")
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Request $request
     * @param CodeGenerator $codeGenerator
     * @param EventDispatcherInterface $eventDispatcher
     * @return Response
     * @throws \Exception
     */
    public function register(
        UserPasswordEncoderInterface $passwordEncoder,
        Request $request,
        CodeGenerator $codeGenerator,
        EventDispatcherInterface $eventDispatcher)
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

            $this->em->persist($user);
            $this->em->flush();

            $eventDispatcher->dispatch(new RegisterUserEvent($user));

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

        $this->em->persist($user);
        $this->em->flush();

        return $this->render('security/account_confirm.html.twig',
            ['user' => $user]
        );
    }
}
