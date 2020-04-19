<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

class FrontController extends AbstractController
{
    /**
     * Detect user locale from request and redirect
     *
     * @Route("/", name="front")
     * @param Request $request
     * @param TranslatorInterface $trans
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function frontAction(Request $request, TranslatorInterface $trans)
    {
        // default locale
        $redirectLocale = $request->getLocale();

        $supportedLocales = $this->getParameter('supported_locales');
        $userLanguages = $request->getLanguages();

        foreach ($userLanguages as $locale) {
            if (in_array($locale, $supportedLocales)) {
                $redirectLocale = $locale;

                /** @var Translator $trans */
                $trans->setLocale($locale);
                $this->addFlash('info', $trans->trans('We detected your browser language, you can change in in top right corner'));
                break;
            }
        }

        return $redirectResponse = $this->redirectToRoute('index', ['_locale' => $redirectLocale]);
    }

}
