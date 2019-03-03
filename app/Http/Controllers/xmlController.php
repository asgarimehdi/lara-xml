<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Orchestra\Parser\Xml\Facade as XmlParser;

class xmlController extends Controller
{
    //
    public function index(){
        $xml = XmlParser::load('https://behroo165.com/product-sitemap1.xml');

        $url = $xml->parse([
            'urls' => ['uses' => 'url[loc,lastmod]'],
        ]);
        return view('xml-table',compact('url'));
    }
}
