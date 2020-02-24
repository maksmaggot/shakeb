<?php
/**
 * Created by PhpStorm.
 * User: maggotik
 * Date: 2/24/20
 * Time: 9:03 PM
 */
declare(strict_types=1);

namespace App\Service;


class CodeGenerator
{
    /**
     * @return string
     * @throws \Exception
     */
    public function getConfirmationCode()
    {
        return strtr(base64_encode(random_bytes(64)), '+/', '-_');
    }
}