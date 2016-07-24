<?php



//------------------------------------------------------------------------------/
// MACGUI - MENU
//------------------------------------------------------------------------------/
/**
 * LingTalfi -- 2014-11-20
 */



?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>


    <title>MacGuiMenu: tuto topmenu</title>
    <style>


        #imac {
            width: 613px;
            height: 507px;
            background: url(../../../img/imac.png) no-repeat center center;
            position: relative;
        }

        .mousezone {
            height: 335px;
            left: 15px;
            position: absolute;
            top: 15px;
            width: 583px;
            background: orange;
        }

        .mousezone2 {
            height: 40px;
            left: 470px;
            position: absolute;
            top: 150px;
            width: 60px;
            background: red;
            cursor: pointer;
        }

        .userzonetitle {
            margin-top: 20px;
            margin-bottom: 0px;
        }

        .userzone {
            background: #6c8061;
            padding: 20px;
        }

        .actiondemo {
            font-family: Verdana, sans-serif;
            float: left;
            width: 30%;
            padding-right: 10px;
            padding-left: 10px;
            padding-bottom: 20px;
            border-right: 1px solid #89a279;
            height: 200px;
        }

        .actiondemo h3 {
            font-size: 14px;
            font-weight: normal;
            text-decoration: underline;
        }

        .actiondemo ul {
            list-style-type: none;
        }

        .clear {
            clear: both;
        }

        .info {
            position: absolute;
            top: 30px;
            right: 5px;
            background: white;
            opacity: 0.6;
            border: 2px solid #ddd;
            border-radius: 10px;
            width: 300px;
            padding: 20px;

        }

        #logcontainer {
            left: 340px;
            position: absolute;
            top: 368px;
        }

        .red {
            color: red;
        }

        .blue {
            color: blue;
        }

        .green {
            color: green;
        }

        .hover .red,
        .hover .blue,
        .hover .green {
            color: white;
        }

    </style>
</head>

<body>

<div id="imac">
    <div id="mousezoneone" class="mousezone">
        <div id="mousezonetwo" class="mousezone2"></div>
    </div>
    <p id="logcontainer">
        <span>Executing: </span><span id="log"></span>
    </p>
</div>



</body>
</html>



