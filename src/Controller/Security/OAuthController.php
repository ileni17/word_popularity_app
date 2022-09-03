<?php

namespace App\Controller\Security;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\FacebookUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OAuthController extends AbstractController
{
    // Key used in config/packages/knpu_oauth2_client.yaml
    const DEFAULT_CLIENT = 'facebook_main';

    #[Route('/oauth/connect', name: 'oauth_connect')]
    public function connectAction(ClientRegistry $clientRegistry)
    {
        // will redirect to Facebook!
        return $clientRegistry
            ->getClient(self::DEFAULT_CLIENT)
            ->redirect([
                'public_profile', 'email'
            ]);
    }

    /**
     * After going to specified client, you're redirected back here
     * because this is the "redirect_route" that is configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @Route("/oauth/redirect", name="auth_client_redirect")
     */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        /** @var FacebookClient $client */
        $client = $clientRegistry->getClient('facebook_main');

        try {
            // the exact class depends on which provider you're using
            /** @var FacebookUser $user */
            $user = $client->fetchUser();

            dump($user);die;

            // do something with all this new power!
            // e.g. $name = $user->getFirstName();
            // ...
        } catch (IdentityProviderException $e) {
            // something went wrong!
            // probably you should return the reason to the user
            dump($e->getMessage());die;
        }
    }
}
