<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14/02/17
 * Time: 22:56
 */
session_start();
//session_destroy();
 ?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ADMIM PJI</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <script   src="https://code.jquery.com/jquery-3.1.1.js"   integrity="sha256-16cdPddA6VdVInumRGo6IbivbERE8p7CQR3HzTBuELA="   crossorigin="anonymous"></script>
    <script src="js/bootstrap.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <style>
        body{
            background:#2196F3;
            overflow: hidden;
            font-family: 'Lato', sans-serif;
        }

        .status-disconected{
            border-radius: 0; font-weight: bold; color:white; background-color: #F44336; border: none;
        }
        .status-conected{
            border-radius: 0; font-weight: bold; color:white; background-color: #8BC34A; border: none;
        }
        .well{
            margin: 100px 10px;
        }

    </style>
</head>
<body>
<div class="status alert alert-danger text-center" style="display: none;"></div>

<div class="error-connected text-center" style="color:white; display: none;">
    <img src="logo_site16.png" alt="" style="text-align: center;">
</div>


<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-4 col-sm-4 porcents">
                <div class="well">
                    <div></div>
                    Sim: <h1 style="color:#1c1c1c; font-size: 4em; text-align:center; ">0%</h1>
                    Não: <h1 style="color:#1c1c1c; font-size: 4em; text-align:center; ">0%</h1>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 porcents">
                <div class="well">
                    <div></div>
                    Sim: <h1 style="color:#1c1c1c; font-size: 4em; text-align:center; ">0%</h1>
                    Não: <h1 style="color:#1c1c1c; font-size: 4em; text-align:center; ">0%</h1>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 porcents">
                <div class="well">
                    <div></div>
                    Sim: <h1 style="color:#1c1c1c; font-size: 4em; text-align:center; ">0%</h1>
                    Não: <h1 style="color:#1c1c1c; font-size: 4em; text-align:center; ">0%</h1>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    var conn = new WebSocket('ws://192.168.0.29:8080');
    var quests = [];

    conn.onopen = function(e) {
        $(".status").addClass("status-conected").html("Conectado!!").show();


        setTimeout(function(){
            $(".status").fadeOut();
        }, 500);
    };

    conn.onerror = function(e) {
        $(".status").removeClass("status-conected");
        $(".status").addClass("status-disconected").html("Erro ao se conectar!!").show();

        $(".container").hide();
    };

    conn.onmessage = function(e) {

        var itens = e.data.split(";");
        var y, n, controller=[];

        /**
         *
         * caso o array de perguntas esteja vazio ele cria o primeiro registro
         *
         * **/
        if(quests.length == 0){
            quests.push({
                "ask": itens[1].split(":")[1],
                "y" : itens[0].split(":")[1] == "y" ? 1 : 0,
                "n" : itens[0].split(":")[1] == "n" ? 1 : 0

            });

            /**
             *
             * atualizando o array de controle de perguntas já
             * registradas em memoria para que não haja duplicação
             *
             * **/
            quests.forEach(function(it){
                if(controller.indexOf(it["ask"]) == -1)
                    controller.push(it["ask"]);
            });
        }else{

            /**
             *
             * atualizando o array de controle de perguntas já
             * registradas em memoria para que não haja duplicação
             *
             * **/
            quests.forEach(function(it){
                if(controller.indexOf(it["ask"]) == -1)
                    controller.push(it["ask"]);
            });

            var ask = (itens[1].split(":")[1]);

            /**
             *
             * verificando se já existe a pergunta no array de controlle
             * se houver ele vai atualizar a resposta y or n
             * caso contrario ele ira criar um novo registro.
             *
             * **/
            if(controller.indexOf(ask) != -1){
                //caso já exista o array
                quests.forEach(function(it){
                    //incrementando deacordo com as opções escolhidas
                    if( it['ask'] == itens[1].split(":")[1]){
                        if(itens[0].split(":")[1] == "y")
                            it['y']++;
                        else if(itens[0].split(":")[1] == "n")
                            it['n']++;
                    }
                });
            }else{
                /**
                 *
                 * criando um novo registro caso não exista a pergunta gadastrada
                 *
                 * **/
                quests.push({
                    "ask": itens[1].split(":")[1],
                    "y" : itens[0].split(":")[1] == "y" ? 1 : 0,
                    "n" : itens[0].split(":")[1] == "n" ? 1 : 0

                });
            }

        }


        $(".porcents").each(function(a,b){
            var calcY = quests[a]['y'] == 0 ? 0 : (quests[a]['y']/(quests[a]['y']+quests[a]['n']))*100;
            var calcN = quests[a]['n'] == 0 ? 0 : (quests[a]['n']/(quests[a]['y']+quests[a]['n']))*100;
            //console.log();


            $($($(b).children()[0]).children()[0]).html("<p>Pergunta:"+quests[a]['ask']+"</p><hr>");

            $($($(b).children()[0]).children()[1]).html(calcY.toFixed(1) + "%");
            $($($(b).children()[0]).children()[2]).html(calcN.toFixed(1) + "%");
        });


    };



</script>
</body>
</html>
