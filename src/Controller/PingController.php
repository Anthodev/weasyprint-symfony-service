<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class PingController
{
    #[Route(path: '/', methods: [Request::METHOD_GET])]
    public function index(): JsonResponse
    {
        return new JsonResponse();
    }

    #[Route(path: '/ping', methods: [Request::METHOD_GET])]
    public function ping(): JsonResponse
    {
        return new JsonResponse('pong');
    }
}
