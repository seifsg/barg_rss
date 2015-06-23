<?php


Class Rssfeed{
    public $url;
    public $sitename;
    public $page_content;
    public $word_array;
    
    public function __construct($url,$sitename){
        $this->url = $url;
        $this->sitename = $sitename;
        $this->get_page_content();
        $this->clean_page_content();
        $this->calcul_occurances();
    }
    
    private function get_page_content(){
        $curl_handle = curl_init();

        curl_setopt($curl_handle, CURLOPT_URL, $this->url);
        curl_setopt($curl_handle, CURLOPT_HEADER, 0);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1); 

        $this->page_content = curl_exec($curl_handle);

        curl_close($curl_handle);
    }
    
    private function clean_page_content(){
        $this->page_content = htmlspecialchars_decode(trim($this->page_content));
        
        // Get only paragraphs
        preg_match_all("/<p class=\"first-text\">(.*)<\/p>/U",$this->page_content,$new_array);
        $new_array = $new_array[1];
        
        // Remove html tags from paragraphs
        for($i=0;$i<count($new_array);$i++){
            $new_array[$i] = strip_tags($new_array[$i]);
        }
        
        $this->page_content = $new_array;
    }
    
    private function calcul_occurances(){
        $all_summery_texts = implode($this->page_content);
        $this->word_array = array_count_values( str_word_count($all_summery_texts, 1) );
        //arsort($this->word_array);
        
        //applying constrant
        //Keeping words with 5 or more chars
        $temporary_array = array();
        foreach($this->word_array as $key=>$value){
            if(strlen($key)>=5){
                $temporary_array[$key] = $value;
            }
        }
        $this->word_array = $temporary_array; unset($temporary_array);
        arsort($this->word_array);
        
        //Keeping only 10 words in the array
        $this->word_array = array_slice($this->word_array,0,10,true);
        
    }
    
    public function render_bar_chart(){
        
        ?>
        <script>
          // Load the Visualization API and the piechart package.
          google.load('visualization', '1.0', {'packages':['corechart']});

          // Set a callback to run when the Google Visualization API is loaded.
          google.setOnLoadCallback(drawChart);

          // Callback that creates and populates a data table,
          // instantiates the pie chart, passes in the data and
          // draws it.
          function drawChart() {

            // Create the data table.
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Words');
            data.addColumn('number', 'Words Count');
            data.addRows([
      <?php foreach($this->word_array as $key=>$value){
                echo "['$key',$value],";
            }
      ?>
            ]);

            // Set chart options
            var options = {'title':'<?php echo $this->sitename;?>',
                           'width':600,
                           'height':300};

            // Instantiate and draw our chart, passing in some options.
            var chart = new google.visualization.BarChart(document.getElementById('chart_<?php echo $this->sitename;?>'));
            chart.draw(data, options);
          }
        </script>
        <div id="chart_<?php echo $this->sitename;?>"></div>
        <div><?php echo $this->sitename." <a href=\"".$this->url."\" target='_blank'>".$this->url."</a>"; ?></div>
<?php
        
    }
    
}

?>