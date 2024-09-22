<?php   

if(!function_exists('convert_price')){
    function convert_price(string $price = ''){
        return str_replace('.','', $price);
    }
}

if(!function_exists('renderSystemLink')){
    function renderSystemLink(array $item = []){
        return (isset($item['link'])) ? '<a 
            href="'.$item['link']['href'].'" 
            style="font-style: italic;"
            target="'.$item['link']['target'].'">'.$item['link']['text'].'</a>': '';
    }
}

if(!function_exists('renderSystemSelect')){
    function renderSystemSelect(array $item = [], string $name = ''){
        $html ='<select class="form-control">';
            foreach ($item['options'] as $key => $val) {
                $html .= '<option value="'.$key.'">'.$val.'</option>';
            }
        $html .= '</select>';

        return $html;
    }
}