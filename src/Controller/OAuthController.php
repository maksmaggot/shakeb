<?php


namespace App\Controller;


use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse as RedirectResponseAlias;
use Symfony\Component\Routing\Annotation\Route;

class OAuthController extends AbstractController
{
    /**
     * @Route("/connect/google", name="connect_google_start")
     *
     * @param ClientRegistry $clientRegistry
     * @return RedirectResponseAlias
     */
    public function redirectToGoogleConnect(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect(
                ['email', 'profile'],
                []
            );
    }

    /**
     * @Route("/google/auth", name="google_auth")
     *
     * @return JsonResponse|RedirectResponseAlias
     */
    public function connectGoogleCheck()
    {
        if (!$this->getUser()) {
            return new JsonResponse(['status' => false, 'message' => 'User not found!']);
        }
        return $this->redirectToRoute('blog_posts');
    }
}