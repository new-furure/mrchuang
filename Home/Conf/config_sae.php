<?php
//sae专用配置文件
$st = new SaeStorage();
return array(
    "SAE" => true,
    'TMPL_PARSE_STRING'=>array(
         './upload' => $st->getUrl('Uploads','upload'),
    ),
);