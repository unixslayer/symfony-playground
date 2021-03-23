<?php

declare(strict_types=1);

namespace App\Controller\Mercure;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\WebLink\Link;

class MercureController extends AbstractController
{
    /**
     * @Route("/mercure/publish", methods={"POST"})
     */
    public function publish(Request $request, PublisherInterface $publisher): Response
    {
        $update = new Update('test', \json_encode($request->request->all(), JSON_THROW_ON_ERROR));
        $publisher($update);

        return new Response(null, Response::HTTP_ACCEPTED);
    }

    /**
     * @Route("/mercure", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $hubUrl = $this->getParameter('mercure.default_hub');
        $this->addLink($request, new Link('mercure', $hubUrl));

        $key = InMemory::plainText($this->getParameter('mercure.key'));
        $configuration = Configuration::forSymmetricSigner(new Sha256(), $key);

        $token = $configuration->builder()
            ->withClaim('mercure', ['subscribe' => ["test"]])
            ->getToken($configuration->signer(), $configuration->signingKey())
            ->toString()
        ;

        $cookie = Cookie::create('mercureAuthorization')
            ->withValue($token)
            ->withPath($hubUrl)
            ->withSecure($request->isSecure())
            ->withHttpOnly(true)
            ->withSameSite('strict')
        ;

        $response = $this->render('@mercure/index.twig', ['token' => $token]);
        $response->headers->setCookie($cookie);

        return $response;
    }
}
