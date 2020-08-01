<?php

require 'phpQuery.php';
$url='https://www.amazon.com/ZESPROKA-Garlic-Mincing-Crushing-Ergonomic/product-reviews/B085CD89ML/ref=cm_cr_getr_d_paging_btm_next_3?ie=UTF8&reviewerType=all_reviews&pageNumber=3';


//$proxy = '195.154.53.196:5836'; // ip:port public proxy
function parser($url){

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);

   // curl_setopt($ch, CURLOPT_PROXY, $proxy); //if used proxy
    curl_setopt($ch, CURLOPT_HEADER, 0); // return headers 0 no 1 yes
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return page 1:yes
    curl_setopt($ch, CURLOPT_TIMEOUT, 200); // http request timeout 20 seconds
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects, need this if the url changes
    curl_setopt($ch, CURLOPT_MAXREDIRS, 2); //if http server gives redirection responce
    curl_setopt($ch, CURLOPT_USERAGENT,
        "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.7) Gecko/20070914 Firefox/2.0.0.7");
    curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie.txt'); // сохранять куки в файл
    curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie.txt');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // false for https
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");

    $page = curl_exec($ch);
    curl_close($ch);

    print_r($page);

    $review_CSV[0] = array('User','User_link' ,'Star', 'Date', 'Text', 'Link_rev');
    $fp = fopen('sample.csv', 'wa');
    $document = phpQuery::newDocument($page); //Загружаем полученную страницу в phpQuery

    foreach ($document->find('.review ') as $article) {

        $article = pq($article);
        $star=$article->find('.a-icon-alt')->text();
        $user = $article->find('.a-profile-name')->html();
        $linkUser=substr_replace($article->find('.a-row.a-spacing-mini a')->attr('href'),'https://www.amazon.com',0,0);
        $linkRev=substr_replace($article->find('.a-link-normal')->attr('href'),'https://www.amazon.com',0,0);
        $date=str_replace('Reviewed in the United States on','',$article->find('.review-date')->text());
        $text = preg_replace('/ {3,}/','',str_replace(array("\r\n", "\r", "\n"), '',trim(strip_tags($article->find('.review-text')->text()), "\n")));
        $review_CSV[] = array("$user","$linkUser","$star","$date","$text","$linkRev");
    }

    foreach ($review_CSV as $line) {
        fputcsv($fp, $line, ';');
    }
}
parser($url);




