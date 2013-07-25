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
        } catch (\Twig_Error_Loader $e) {
            $content = @file_get_contents($template);
            if ($content !== false) {
                $template = $content;
            } else {
                return $this->handleException(new \Exception("{$template} is not a valid template file"));
            }

            $app['twig']->setLoader(new \Twig_Loader_String());
            $this->template = $app['twig']->loadTemplate($template);
        } catch (\Exception $e) {
            $this->handleException($e);
        }

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
        } catch (\Exception $e) {
            $this->handleException($e);
        }

        return parent::prepare($request);
    }

    private function handleException($exception)
    {
        $message = $exception->getMessage();

        if ($this->app['debug']) {
            die($message);
        }

        error_log($message);
        throw new \Exception($message);
    }
}
