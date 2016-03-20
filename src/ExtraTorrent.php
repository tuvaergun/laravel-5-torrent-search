<?php namespace iWedmak\TorrentSearch;

use iWedmak\ExtraCurl\Parser;

class ExtraTorrent implements TorrentSearchInterface 
{

    public static function page($url, $cache=5, $client=false)
    {
        if(!$client)
        {
            $client=new Parser;
        }
        if($resp=$client->get($url, $cache))
        {
            $html=new \Htmldom;
            $html->str_get_html($resp);
            pre($url);
            //pre($resp);
            $torrent=Search::makeRes
                (
                    'ExtraTorrent', 
                    $url, 
                    $html->find('td.tabledata0', 0)->last_child()->plaintext, 
                    $html->find('a[href*=magnet]', 0)->attr['href'], 
                    $html->find('td.tabledata0', 9)->plaintext, 
                    @$html->find('span.seed', 0)->plaintext, 
                    @$html->find('span.leech', 0)->plaintext
                );
            pre($torrent);
            return $torrent;
        }
        else
        {
            return ['error_code'=>$client->c->error_code];
        }
        
    }
    
    public static function search($url, $cache=5, $client=false)
    {
        if(!$client)
        {
            $client=new Parser;
        }
        if($resp=$client->get($url, $cache))
        {
            $html=new \Htmldom;
            $html->str_get_html($resp);
            $trs=$html->find('table.tl tbody tr');
            $result=[];
            foreach($trs as $tr)
            {
                if(trim($tr->find('td a', 1)->plaintext)!='')
                {
                    $torrent=Search::makeRes
                        (
                            'ExtraTorrent', 
                            'http://extratorrent.cc'.$tr->find('td a', 1)->attr['href'], 
                            $tr->find('td a', 1)->plaintext, 
                            'http://extratorrent.cc'.$tr->find('td a[href*=torrent]', 0)->attr['href'], 
                            $tr->find('td', 2)->plaintext, 
                            $tr->find('td', 3)->plaintext, 
                            $tr->find('td', 4)->plaintext
                        );
                    $result[]=$torrent;
                }
            }
            return $result;
        }
        else
        {
            return ['error_code'=>$client->c->error_code];
        }
    }
    
}
?>