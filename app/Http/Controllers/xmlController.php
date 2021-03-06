<?php

namespace App\Http\Controllers;


use App\Xml;
use App\Useragent;
use App\Worktable;
use Illuminate\Http\Request;
use Orchestra\Parser\Xml\Facade as XmlParser;
use Carbon\Carbon;
use Ixudra\Curl\Facades\Curl;

class xmlController extends Controller
{
    //
    public function index(){
        //
        if($this->isXml($_ENV['XML_TARGET'])){

            $childXmls=$this->fetchChildXmlFromSitemap($_ENV['XML_TARGET']);
            foreach ($childXmls as $childXml){

                if($this->isXml($childXml['url'])){
                    $this->save($childXml['url']);
                }

            }

        }
        else
            return "url is invalid xml";
    }

    public function show(){

        $final_urls= $this->xmlToArr();
        return view('xml-table',compact('final_urls'));


    }


    private function save($input_url){
        $final_urls= $this->xmlToArr($input_url);
        foreach($final_urls as $final_url){

            $link= new Xml;
            if($final_url['url_decoded']){
                try{
                    $link->url=$final_url['url'];
                    $link->url_decoded=$final_url['url_decoded'];
                    $link->modification=$final_url['modification'];

                    $link->save();
                }
                catch (\Illuminate\Database\QueryException $e){
                    echo $e->getMessage()."<hr>";
                    //
                }
            }
        }
    }


    private function xmlToArr($input_url){
        $xml = XmlParser::load($input_url);

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


    public function agent(Request $request){
        $ua = $request->server('HTTP_USER_AGENT');
        $agent = new Useragent;
        $agent->useragent=$ua;
        $agent->save();
//        $ub = $request->header('User-Agent');
        return $ua;
    }
    private function fetchChildXmlFromSitemap($input_url){
        $xml = XmlParser::load($input_url);
        $url = $xml->parse([
            'urls' => ['uses' => 'sitemap[loc,lastmod]'],
        ]);
        $i=0;
        foreach ($url['urls'] as $urls){
            $final_urls[$i]['url']= $urls['loc'];
            $final_urls[$i]['modification']= $urls['lastmod'];
            $i++;
        }
        return $final_urls;
    }

    private function isXml($input_url){
        $input_url=urldecode($input_url);
        $response = Curl::to($input_url)->
        withOption('REFERER', 'http://www.google.com')->
        withOption('SSL_VERIFYPEER', 'false')->
        withOption('RETURNTRANSFER', '1')->
        withOption('FOLLOWLOCATION', '0')->
        withOption('USERAGENT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.119 Safari/537.36')->
        withResponseHeaders() ->returnResponseObject()->
        get();
        $headers = $response->headers;
        if($headers['Content-Type']=='text/xml; charset=UTF-8')
            return true;
        else
            return false;
    }
//    public  function isUrlValid(){
//        $url='https://behroo165.com/pa_نوع-شوینده-sitemap.xml';
////        $url='https://behroo165.com/pa_%D8%AD%D8%AC%D9%85_%D9%88%D8%B2%D9%86-sitemap.xml';
//        return $this->isXml($url);
//    }

//    public function handelUserAgent(Request $request){
//        $agent = new Useragent();
//        $count=$agent->where('useragent','=',$request->server('HTTP_USER_AGENT'))->count();
//        if($count==0) {
//            $agent->useragent = $request->server('HTTP_USER_AGENT');
//            $agent->save();
//        }
//        echo $agent->all();
//    }

    public function properWorkTables(){

        $agent = new Useragent;
        $link = new Xml;

        $all_agent = $agent->orderBy('updated_at', 'ASC')->get('id');
        $all_link = $link->get('id');
        $mytime = Carbon::now();

        foreach ($all_agent as $agentss) {
            foreach ($all_link as $linkss) {
                try {
                    $work = new Worktable;
                    $work->useragents_id = $agentss['id'];
                    $work->xmls_id = $linkss['id'];
                    $work->last_job = $mytime->toDateTimeString();
                    $work->save();
                }
                catch (\Illuminate\Database\QueryException $e){
                    echo $e->getMessage()."<hr>";
                    //
                }
            }
            $agent2 = new Useragent;
            $agent_to_update=$agent2->findOrFail($agentss['id']);
            $agent_to_update->created_at=$mytime->toDateTimeString();
            $agent_to_update->save();
        }

    }

    private function remote($url,$useragent){
//        $url='https://behroo165.com/Product/B165-12833/دستمال-مرطوب-کودک-مولفیکس-پوست-حساس/';
        $response = Curl::to($url)->
        withOption('REFERER', 'http://www.google.com')->
        withOption('SSL_VERIFYPEER', 'false')->
        withOption('RETURNTRANSFER', '1')->
        withOption('FOLLOWLOCATION', '0')->
        withOption('USERAGENT', $useragent)->
        withResponseHeaders() ->returnResponseObject()->
        get();
        return  $test= $response->status;
//        return strlen($test);
    }

    public function fetchPage($num){

        for($i=0;$i<$num;$i++) {
            $work = new Worktable;
            $agent = new Useragent;
            $link = new Xml;
            $mytime = Carbon::now();
            $workTable = $work->orderBy('last_job', 'ASC')->take(1)->get();
            $agentTable = $agent->where('id', $workTable[0]->useragents_id)->get();;
            $xmlTable = $link->where('id', $workTable[0]->xmls_id)->get();
            $status = $this->remote($xmlTable[0]->url, $agentTable[0]->useragent);
            if ($status == '200') {
                $work->where('id', $workTable[0]->id)->update(['last_job' => $mytime->toDateTimeString()]);
            }
        }
    }
}
