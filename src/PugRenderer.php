<?php

namespace Md\Pug;

use Psr\Http\Message\ResponseInterface;
use Pug\Pug;

/**
 * Class PugRenderer
 * @package Md\Pug
 *
 * Render Pug view scripts into a PSR-7 Response object
 */
class PugRenderer
{
    private $templatePath;

    public function __construct($templatePath = "", $attributes = [])
    {
        $this->adaptee = new Pug($this->validateDefaultAttributes($attributes));
        $this->templatePath = rtrim($templatePath, '/\\') . '/';
        $this->attributes = $attributes;
    }

    /**
     * Get the attributes for the renderer
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set the attributes for the renderer
     *
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Add an attribute
     *
     * @param $key
     * @param $value
     */
    public function addAttribute($key, $value) {
        $this->attributes[$key] = $value;
    }

    /**
     * Retrieve an attribute
     *
     * @param $key
     * @return mixed
     */
    public function getAttribute($key) {
        if (!isset($this->attributes[$key])) {
            return false;
        }

        return $this->attributes[$key];
    }

    /**
     * Get the template path
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * Set the template path
     *
     * @param string $templatePath
     */
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = rtrim($templatePath, '/\\') . '/';
    }
    
    /**
     * Renders a template
     *
     * Fetches the template and wraps it in a response object
     *
     * @param ResponseInterface $response
     * @param string             $template
     * @param array              $data
     *
     * @return ResponseInterface
     *
     * @throws \InvalidArgumentException if it contains template as a key
     * @throws \RuntimeException if `$templatePath . $template` does not exist
     */
    public function render(ResponseInterface $response, $template, array $data = [])
    {
        $output = $this->fetch($template, $data);
        $response->getBody()->write($output);
        return $response;
    }

    /**
     * Fetches a template and returns the result as a string
     *
     * @param $template
     * @param array $data
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException if it contains template as a key
     * @throws \RuntimeException if `$templatePath . $template` does not exist
     */
    public function fetch($template, array $data = [])
    {
        if (isset($data['template'])) {
            throw new \InvalidArgumentException("Duplicate template key found");
        }

        if (!is_file($this->templatePath . $template)) {
            throw new \RuntimeException("View cannot render `$template` because the template does not exist");
        }

        $data = array_merge($this->attributes, $data);

        return $this->adaptee->render($this->templatePath . $template, $data);
    }

    private function validateDefaultAttributes(array $attributes)
    {
        $settings = [];

        if (isset($attributes["cache"])) {
            $settings["cache"] = $attributes["cache"];
        }
        
        if (isset($attributes["upToDateCheck"])) {
            $settings["upToDateCheck"] = $attributes["upToDateCheck"];
        }
        
        $settings["extension"] = isset($attributes["extension"]) ? $attributes["extension"] : ".pug";
        $settings["basedir"] = $templatePath;
        $settings["expressionLanguage"] = isset($attributes["expressionLanguage"]) ? $attributes["expressionLanguage"] : "php";

        return $settings;
    }
}