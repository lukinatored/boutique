<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CookieController extends AbstractController
{
    #[Route('/cookie/consent', name: 'app_cookie_consent', methods: ['POST'])]
    public function consent(Request $request): JsonResponse
    {
        $consent = $request->get('consent');
        $response = $this->json(['status' => 'ok']);
        
        $cookie = Cookie::create('cookie_consent')
            ->withValue($consent)
            ->withExpires(strtotime('+1 year'))
            ->withSecure(false) // true en production avec HTTPS
            ->withHttpOnly(false)
            ->withSameSite('lax');
        
        $response->headers->setCookie($cookie);
        
        return $response;
    }

    #[Route('/cookie/check', name: 'app_cookie_check')]
    public function check(Request $request): JsonResponse
    {
        $consent = $request->cookies->get('cookie_consent');
        return $this->json(['consent' => $consent]);
    }
}
