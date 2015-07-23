<?php

namespace Bolt\Extension\Bobdenotter\Chart;

use Bolt\Application;
use Bolt\BaseExtension;

class Extension extends BaseExtension
{


    public function initialize() {

        $this->addTwigFunction('chart', 'chart');
        $this->addTwigFilter('chartsimplode', 'chartsimplode');

    }

    public function getName()
    {
        return "chart";
    }

    public function chart($id = null)
    {
        // $this->addCss('assets/extension.css');
        $this->addJavascript('assets/Chart.min.js', true);

        $this->app['twig.loader.filesystem']->addPath(__DIR__."/twig");

        $chartrecord = $this->app['storage']->getContent($this->config['contenttype'] . '/' . $id);

        if (empty($chartrecord)) {
            return "[ No such chart ]";
        }

        $twigvars = [
            'config' => $this->config,
            'chart' => $chartrecord->values,
            'id' => substr(md5($chartrecord->values['slug']),0,6)
        ];

        // dump($chartrecord);

        switch($chartrecord->values['type']) {

            case "line":
            default:
                $twigvars['opacity'] = "0.2"; // Note: string to prevent number_formatting by twig.
                $template = 'line.twig';
                break;

            case "bar":
                $twigvars['opacity'] = "0.8";
                $template = 'line.twig';
                break;

        }


        $chart = $this->app['render']->render('line.twig', $twigvars);

        return new \Twig_Markup($chart, 'utf-8');

    }

    public function chartsimplode($arr)
    {

        $arr = $this->arrayTrim($arr);

        if(!array_filter($arr)) {
            return;
        }

        $res = "[ '" . implode($arr, "', '") . "' ]";


        return new \Twig_Markup($res, 'utf-8');

    }


    private function arrayTrim($arr)
    {
        if (empty($arr)) {
            return [];
        }

        if (empty($arr[0])) {
            return $this->arrayTrim(array_slice($arr, 1));
        }
        if (empty($arr[count($arr)-1])) {
            return $this->arrayTrim(array_slice($arr, 0, -1));
        }

        return $arr;
    }

}






