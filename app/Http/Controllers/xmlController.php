<?php

namespace App\Http\Controllers;


use App\Xml;
use Illuminate\Http\Request;
use Orchestra\Parser\Xml\Facade as XmlParser;
use Carbon\Carbon;

class xmlController extends Controller
{
    //
    public function index(){
        //
    }

    public function show(){
       $final_urls= $this->xmlToArr();
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

    public function save(){
        $final_urls= $this->xmlToArr();
        foreach($final_urls as $final_url){
            $mytime =  Carbon::now();
            $link= new Xml;
            if($final_url['page_id']){
                try{
                $link->page_id=$final_url['page_id'];
                $link->url=$final_url['url'];
                $link->modification=$final_url['modification'];
                $link->last_job=$mytime->toDateTimeString();
                $link->save();
                }
                catch (\Illuminate\Database\QueryException $e){
                    echo $e->getMessage()."<hr>";
                    //
                }
            }
        }
    }


    public function xmlToArr(){
        $xml = XmlParser::load('https://behroo165.com/product-sitemap2.xml');

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
        return $final_urls;
    }
}
