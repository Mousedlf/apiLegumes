<?php

namespace App\Service;

use App\Entity\Platform;
use App\Repository\PlatformRepository;
use Symfony\Component\HttpFoundation\Response;

class AdminKeyService
{
    public function __construct(
        public PlatformRepository $platformRepository,
    ){
    }

    public function checkAdminKey(string $key):bool
    {
        $encodedKey = base64_encode($key);
        $platform = $this->platformRepository->findOneBy(['adminKey' => $encodedKey ]);

        if(!$platform){
            return false;
        }
        return true;
    }

    public function getPlatformWithAdminKey(string $key):Platform
    {
        $encodedKey = base64_encode($key);
        $platform = $this->platformRepository->findOneBy(['adminKey' => $encodedKey ]);

        return $platform;
    }

    public function hasPlatformAccess(string $key):array
    {
        $hasAccess = $this->checkAdminKey($key);

        if($hasAccess){
            $platform = $this->getPlatformWithAdminKey($key);

            $response = [
                'success' => true,
                'platform' => $platform,
            ];
            return $response;
        } else {
            $response = [
                'success' => false
            ];
            return $response;
        }
    }
}