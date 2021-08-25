<?php

namespace AppBundle\Twig;

use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Symfony\Component\HttpFoundation\Session\Session;

class AppExtension extends TwigExtension {
    public function getGlobals() {
        $session = new Session();
        return array(
            'session'=>$session->all()
        );
    }
    public function getName() {
        return 'app_extension';
    }
}