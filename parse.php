<?php

// Lib for parsing
require_once "simple_html_dom.php";
require_once "db.class.php";
// DB Connect
$db = new DB('localhost', 'root', '', 'ananaska');
// Pages catalog
$url = 'http://ananaska.com/vse-novosti/';

if(isset($argv[1]) && $argv[1] == 'catalog'){
    parseCatalog($url);
} else {
    while($article = $db->query('select * from articles where dt_parsed is null limit 1')){

        $id = $article[0]['id'];
        $result = parseArticle($article[0]['url']);

        echo PHP_EOL.date('H:i:s')." START PAGE #: {$id}".PHP_EOL.PHP_EOL;

        $h1 = $db->escape($result['h1']);
        $content = $db->escape($result['content']);

        $sql = "
            update articles 
              set h1 = '{$h1}',
                  content = '{$content}',
                  dt_parsed = NOW()
              where id = {$id}
              limit 1              
        ";
        $db->query($sql);

        echo PHP_EOL.date('H:i:s')." FINISH PAGE #: {$id}".PHP_EOL.PHP_EOL;

    }
}


// Parse all links to articles
function parseCatalog($url){
    global $db;

    echo PHP_EOL.date('H:i:s')."START URL: {$url}".PHP_EOL.PHP_EOL;

    // Get Catalog page
    $html = file_get_html($url);
    // Get each article
    foreach($html->find('a.read-more-link') as $link){
        // Get link to article
        $article_url = $db->escape($link->href);
        // Insert
        $sql = "insert ignore into articles 
              set url = '{$article_url}'";
        $db->query($sql);
        echo $link->href.PHP_EOL;
    }

    // Go recursively to next page
    if($next_link = $html->find('a.next', 0)){
        parseCatalog($next_link->href);
    }

}

// Get one article
function parseArticle($url){
    $article = file_get_html($url);

    $h1 = $article->find('h1', 0)->innertext;
    $content = $article->find('article', 0)->innertext;

    // TODO: Compact
    $result['h1'] = $h1;
    $result['content'] = $content;

    return $result;
}