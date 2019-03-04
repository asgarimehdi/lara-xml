<?php

namespace App\Http\Controllers;


use App\Xml;
use Illuminate\Http\Request;
use Orchestra\Parser\Xml\Facade as XmlParser;
use Carbon\Carbon;
use Ixudra\Curl\Facades\Curl;

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

    public function remote(){
        $url='https://behroo165.com/Product/B165-13129/%d8%b4%d8%a7%d9%85%d9%be%d9%88-%d8%a8%d8%af%d9%86-%da%a9%d9%88%d8%af%da%a9-%d8%ac%d8%a7%d9%86%d8%b3%d9%88%d9%86/';
        $response = Curl::to($url)->
        withOption('REFERER', 'http://www.baidu.com')->
        withOption('SSL_VERIFYPEER', 'false')->
        withOption('RETURNTRANSFER', '1')->
        withOption('FOLLOWLOCATION', '0')->
        withOption('USERAGENT', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0')->
        get();
        return $response;
    }
}
