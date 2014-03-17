<?php
//sae专用配置文件
$st = new SaeStorage();
return array(
    "IS_SAE" => true,
    'FILE_UPLOAD_TYPE' => 'Sae',
    // 'TMPL_PARSE_STRING'=>array(
    //      './uploads' => $st->getUrl('Uploads','uploads'),
    // ),
);