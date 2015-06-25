<?php

function show_charts(){
    
    $hndl = fopen("rssfeeds.txt","r");
    while($line = trim(stream_get_line($hndl,10000,"\n"))){
        $line = explode(" ",trim($line));
        $$line[0] = new Rssfeed($line[1],$line[0]);
        $$line[0]->render_bar_chart();
    }
    fclose($hndl);
    
}

?>