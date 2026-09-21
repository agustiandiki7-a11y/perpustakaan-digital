<?php
$pageTitle=$pageTitle??'Dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle,ENT_QUOTES,'UTF-8') ?> - Perpustakaan Digital</title>

<link rel="icon" href="/perpustakaan/backend/assets/img/kaiadmin/favicon.ico" type="image/x-icon">

<script src="/perpustakaan/backend/assets/js/plugin/webfont/webfont.min.js"></script>

<script>
WebFont.load({
    google:{
        families:["Public Sans:300,400,500,600,700"]
    },
    custom:{
        families:[
            "Font Awesome 5 Solid",
            "Font Awesome 5 Regular",
            "Font Awesome 5 Brands",
            "simple-line-icons"
        ],
        urls:[
            "/perpustakaan/backend/assets/css/fonts.min.css"
        ]
    },
    active:function(){
        sessionStorage.fonts=true;
    }
});
</script>

<link rel="stylesheet" href="/perpustakaan/backend/assets/css/bootstrap.min.css">
<link rel="stylesheet" href="/perpustakaan/backend/assets/css/plugins.min.css">
<link rel="stylesheet" href="/perpustakaan/backend/assets/css/kaiadmin.min.css">

<style>
html,body{
    margin:0;
    padding:0;
    min-height:100%;
}

body{
    font-family:"Public Sans",sans-serif;
    background:#f5f7fb;
}

.wrapper{
    min-height:100vh;
}

.main-panel{
    min-height:100vh;
}

.main-panel .container{
    width:100%;
}

img{
    max-width:100%;
}
</style>
</head>

<body>