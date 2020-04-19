<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class StaticController extends AbstractController
{
    /**
     * @Route("/page/disclaimer", name="static_disclaimer")
     * @Template()
     */
    public function disclaimerAction()
    {
    }

    /**
     * @Route("/page/about", name="static_about")
     * @Template()
     */
    public function aboutAction()
    {
    }

    /**
     * @Route("/page/help", name="static_help")
     * @Template()
     */
    public function helpAction()
    {
    }
}
