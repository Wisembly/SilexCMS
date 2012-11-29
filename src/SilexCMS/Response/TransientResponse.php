<?php

namespace SilexCMS\Response;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class TransientResponse extends Response
{
    private $twig;
    private $template;
    private $variables;

    public function __construct($twig, $template, $variables = null)
    {
        try {
            $this->template = $twig->loadTemplate($template);
        } catch (\Exception $e) {
            $content = @file_get_contents($template);
            if ($content !== false) {
                $template = $content;
            }

            $loader = $twig->getLoader();
            $twig->setLoader(new \Twig_Loader_String());
            $this->template = $twig->loadTemplate($template);
            $twig->setLoader($loader);
        }

        $this->variables = $variables;

        parent::__construct();
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function getVariables()
    {
        return $this->variables;
    }

    public function prepare(Request $request)
    {
        try {
            $this->setContent($this->template->render((array) $this->variables));
        } catch (\Exception $e) {}

        return parent::prepare($request);
    }
}
