<?php

namespace SilexCMS\Response;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class TransientResponse extends Response
{
    private $app;
    private $template;
    private $variables;

    public function __construct($app, $template, $variables = null)
    {
        $this->app = $app;
        $this->variables = $variables;

        try {
            $this->template = $app['twig']->loadTemplate($template);
        } catch (\Twig_Error_Loader $exception) {
            $content = @file_get_contents($template);
            if ($content !== false) {
                $template = $content;
            } else {
                throw new \Exception($exception->getMessage());
            }

            $app['twig']->setLoader(new \Twig_Loader_String());
            $this->template = $app['twig']->loadTemplate($template);
        }

        parent::__construct();
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function prepare(Request $request)
    {
        try {
            $this->setContent($this->template->render((array) $this->variables));
        } catch (\Twig_Error_Runtime $e) {
            if (!$this->app['debug']) {
                throw new \Exception($e->getMessage());
            }

            $this->setContent($e->getMessage());
        }

        return parent::prepare($request);
    }
}
