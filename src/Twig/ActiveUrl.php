<?php
/**
 * Twig_ActiveUrl_Extension.php
 * Created at 24/06/15 17:42
 */

namespace App\Twig;


use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ActiveUrl extends AbstractExtension
{

    /**
     * @var string Default active class name – "active"
     */
    private $activeClass = 'active';

    /**
     * @var RequestContext
     */
    private $request;

    /**
     * @var RouterInterface
     */
    private $router;

    function __construct(UrlGeneratorInterface $router, RequestContext $request)
    {
        $this->router = $router;
        $this->request = $request;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction("active_url", array($this, "urlActiveClass"), array(
                    "is_safe" => array("html"),
                )
            ),
            new TwigFunction("active_suburl", array($this, "suburlActiveClass"), array(
                    "is_safe" => array("html"),
                )
            ),
        ];
    }

    /**
     * Perform and exact match
     *
     * @return string
     */
    public function urlActiveClass($route, $parameters = array())
    {
        $output = '';

        list($routeParams, $className, $classAttr, $checkQuery) = $this->prepareParams($parameters);

        $requestedURL = $this->request->getPathInfo();

        $routeBaseURL = $this->router->generate($route, $routeParams);
        $routeInfo = parse_url($routeBaseURL);
        $routeURL = $routeInfo['path'];


        $matched = (0 === strcmp($requestedURL, $routeURL));

        if ($checkQuery) {
            $requestedQuery = $this->request->getQueryString();
            $routeQuery = (isset($routeInfo['query']) ? $routeInfo['query'] : '');

            $matchedQuery = (0 === strcmp($requestedQuery, $routeQuery));
            $matched = $matched && $matchedQuery;
        }


        if ($matched) {
            $output = $this->makeOutput($className, $classAttr);
        }

        return $output;
    }

    private function prepareParams($parameters = array())
    {
        // URL parameters
        $routeParams = (isset($parameters['params']) && is_array($parameters['params']) ? $parameters['params'] : []);

        // you can redefine class by providing {class: "css-class"} in params
        $className = (isset($parameters['class']) && is_string($parameters['class']) ? $parameters['class'] : $this->activeClass);

        // you may ask function to output class attr by adding {attr: true} in params
        $classAttr = (isset($parameters['attr']) && is_bool($parameters['attr']) ? $parameters['attr'] : false);

        // if you want to check both path and query string – add {query: true} to params
        $checkQuery = (isset($parameters['query']) && is_bool($parameters['query']) ? $parameters['query'] : false);

        return [
            $routeParams,
            $className,
            $classAttr,
            $checkQuery,
        ];
    }

    /**
     * Prepare class names and generate an output
     *
     * @param string $activeClass
     * @param bool $classAttr
     * @return string
     */
    private function makeOutput(string $activeClass, bool $classAttr)
    {
        if ($classAttr) {
            $output = 'class="'.$activeClass.'"';
        } else {
            $output = $activeClass;
        }

        return $output;
    }

    /**
     * Perform and exact match
     *
     * @return string
     */
    public function suburlActiveClass($route, $parameters = array())
    {
        $output = '';

        list($routeParams, $className, $classAttr, $checkQuery) = $this->prepareParams($parameters);

        $requestedURL = $this->request->getPathInfo();

        $routeBaseURL = $this->router->generate($route, $routeParams);
        $routeInfo = parse_url($routeBaseURL);
        $routeURL = $routeInfo['path'];

        $matched = (0 === mb_strpos($requestedURL, $routeURL));

        if ($matched) {
            $output = $this->makeOutput($className, $classAttr);
        }

        return $output;
    }

}