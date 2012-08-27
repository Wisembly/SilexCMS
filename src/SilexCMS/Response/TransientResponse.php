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
        $content = @stream_get_contents($template);

        if ($content !== false) {
            $loader = $twig->getLoader();
            $twig->setLoader(new \Twig_Loader_String());
            $this->template = $twig->loadTemplate($content);
            $twig->setLoader($loader);
        } else {
            $this->template = $twig->loadTemplate($template);
        }

        $this->variables = (object) $variables;

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
        $this->setContent($this->template->render((array) $this->variables));

        return parent::prepare($request);
    }
}
