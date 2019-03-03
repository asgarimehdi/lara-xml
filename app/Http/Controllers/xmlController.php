<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Orchestra\Parser\Xml\Facade as XmlParser;

class xmlController extends Controller
{
    //
    public function index(){
        //
    }

    public function show(){
        $xml = XmlParser::load('http://127.0.0.1/test.xml');

        $url = $xml->parse([
            'urls' => ['uses' => 'url[loc,lastmod]'],
        ]);
        $pars1='B165-';
        $pars2='/';
        $i=0;
        foreach ($url['urls'] as $urls){
            $final_urls[$i]['url']= $urls['loc'];
            $final_urls[$i]['modification']= $urls['lastmod'];
            $final_urls[$i]['page_id']= @$this->slice($urls['loc'],$pars1,$pars2);
            $i++;
        }
        return view('xml-table',compact('final_urls'));


    }
    private function slice($content,$pars1,$pars2)
    {
        $part1=explode($pars1,$content);
        $new_content=$part1['1'];
        $part2=explode($pars2,$new_content);
        $result=$part2['0'];
        return $result;

    }
}
