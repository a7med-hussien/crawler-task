<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $pagesNumber = 1;

    public function getHomeGateLinks()
    {
        $links = [];

        for ($page = 1; $page <= $this->pagesNumber ; $page++) {
            $ch = curl_init();
            $webLink = "https://www.homegate.ch/mieten/immobilien/kanton-zuerich/trefferliste?ep=" . $this->pagesNumber;
            curl_setopt($ch, CURLOPT_URL, $webLink);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $headers = array();
            $headers[] = "Content-Type: application/json";
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            curl_close ($ch);
    
            // get number of pages
            if ($this->pagesNumber === 1) {
                preg_match('!von <span>([0-9]*?)</span>!', $result, $matches);
                $this->pagesNumber = (int)$matches[1];
            }
    
            // match all ids
            preg_match_all('!href="/mieten/([0-9]*?)"!', $result, $matches);
            $links = array_merge($links, $matches[1]);
        }

        return $links;
    }

    public function getNewhomeLinks()
    {
        $links = [];

        for ($page = 1; $page <= $this->pagesNumber ; $page++) {
            $ch = curl_init(); 
            $webLink = "https://www.newhome.ch/de/kaufen/suchen/haus_wohnung/kanton_zuerich/liste.aspx?p=" . $page;
            curl_setopt($ch, CURLOPT_URL, $webLink);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_ENCODING , "gzip");
            $headers = array();
            $headers[] = "Host: www.newhome.ch";
            $headers[] = "Connection: keep-alive";
            $headers[] = "Cache-Control: max-age=0";
            $headers[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8";
            $headers[] = "DNT: 1";
            $headers[] = "Upgrade-Insecure-Requests: 1";
            $headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36";
            $headers[] = "Referer: https://github.com/internsvalley/php-test";
            $headers[] = "Accept-Language: en-US,en;q=0.9,ar;q=0.8,de;q=0.7";
            $headers[] = "Cookie: ASP.NET_SessionId=fb4wncrcgkjdhwepyryhtr45; newhome_ch_language=de";
            $headers[] = "Content-Type: text/html; charset=utf-8";
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            curl_close ($ch);
    
            // get number of pages
            if ($this->pagesNumber === 1) {
                preg_match('!Seite(.*?)von(.*?)</span>!', $result, $matches);
                $this->pagesNumber = (int)$matches[2];
            }
    
            // match all ids
            preg_match_all('#detail.aspx\?pc\=new\&amp\;id\=(.*?)\&amp\;liste\=1#', $result, $matches);
            // preg_match_all('#href="https://www.newhome.ch/de/kaufen/immobilien/wohnung/wohnung/(.*?)>#s', $result, $matches);
            // <a rel="nofollow" data-action="nh-click-internal" href="https://www.newhome.ch/de/kaufen/immobilien/wohnung/wohnung/ort_nassenwil/3.5_zimmer/detail.aspx?pc=new&amp;id=2916766&amp;liste=1">
            // <a rel="nofollow" data-action="nh-click-internal" href="https://www.newhome.ch/de/kaufen/immobilien/wohnung/wohnung/ort_winkel/5.5_zimmer/detail.aspx?pc=new&amp;id=2916770&amp;liste=1">
            
            $links = array_merge($links, $matches[1]); // 1143
        }

        return $links;
    }
}
