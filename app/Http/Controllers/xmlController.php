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


    public function save(){
        $final_urls= $this->xmlToArr();
        foreach($final_urls as $final_url){
            $mytime =  Carbon::now();
            $link= new Xml;
            if($final_url['url_decoded']){
                try{
                    $link->url=$final_url['url'];
                    $link->url_decoded=$final_url['url_decoded'];
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

        $i=0;
        foreach ($url['urls'] as $urls){
            $final_urls[$i]['url']= $urls['loc'];
            $final_urls[$i]['url_decoded']= urldecode($urls['loc']);
            $final_urls[$i]['modification']= $urls['lastmod'];
            $i++;
        }
        return $final_urls;
    }

    public function remote(){
        $url='https://behroo165.com/Product/B165-12833/دستمال-مرطوب-کودک-مولفیکس-پوست-حساس/';
        $response = Curl::to($url)->
        withOption('REFERER', 'http://www.google.com')->
        withOption('SSL_VERIFYPEER', 'false')->
        withOption('RETURNTRANSFER', '1')->
        withOption('FOLLOWLOCATION', '0')->
        withOption('USERAGENT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.119 Safari/537.36')->
        withResponseHeaders() ->returnResponseObject()->
        get();
        return  $test= $response->status;
//        return strlen($test);
    }

    public function userAgent(Request $request){
        $ua = $request->server('HTTP_USER_AGENT');
//        $ub = $request->header('User-Agent');
        return $ua;
    }
}
