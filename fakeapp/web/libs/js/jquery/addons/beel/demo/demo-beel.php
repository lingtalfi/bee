<?php

require_once 'alveolus/bee/boot/bam1.php';


use Bee\Application\Asset\AssetDependencyResolver\AssetCalls\AssetCalls;
use Bee\Application\Config\Util\FeeConfig;
use WebModule\Komin\Base\Application\Adr\Tool\AdrTool;
use WebModule\Komin\Base\Db\Mysql\QuickMysql;
use WebModule\Komin\Beel\ListRenderer\AdminTableListRenderer;
use WebModule\Komin\Beel\ListRenderer\ArrayListRenderer;
use WebModule\Komin\Beel\ListRenderer\ItemListRenderer;
use WebModule\Komin\Beel\ListRenderer\RecursiveListRenderer;
use WebModule\Komin\Beel\ListRenderer\TableListRenderer;




$items = QuickMysql::fetchAll('select * from webtv.emissions limit 0,10');
$confFile = $_beeApplicationRoot . '/app/config/parameters.yml';
$conf = FeeConfig::readFile($confFile);

$menuData = [
    [
        'name' => 'menu1',
        'children' => [
            ['name' => 'sub 1',],
            [
                'name' => 'sub 2',
                'children' => [
                    ['name' => 'sub sub 1',],
                ],
            ],
            ['name' => 'sub 3',],
        ],
    ],
    [
        'name' => 'menu2',
    ],
    [
        'name' => 'menu3',
        'children' => [
            [
                'name' => 'bus 1',
                'children' => [
                    ['name' => 'bus bus 1',],
                    ['name' => 'bus bus 2',],
                ],
            ],
            ['name' => 'bus 2',],
        ],
    ],
];


$o = new ItemListRenderer(function (array $item) {
    $s = '';
    $s .= '<div class="item">';
    $s .= 'id: ' . $item['id'] . '<br>';
    $s .= 'titre: ' . $item['titre'];
    $s .= '</div>';
    return $s;
});

$o2 = new TableListRenderer();
$o2->setRegularColumns(array_keys($items[0]));


$lev = 3;
$o3 = new ArrayListRenderer([
    'levelMax' => -1,
    'openListChar' => function ($value, $key, $level) {
        return '<ul class="level' . $level . '">';
    },
    'openItemChar' => function ($value, $key, $level) {
        return '<li class="level' . $level . '">$key: ';
    },
    'getItem' => function ($value, $key, $level) use ($lev) {
        if ($level >= $lev) {
            return '<b>$value</b>';
        }
        return '$value';
    },
]);


$o4 = new RecursiveListRenderer([
    'getItem' => function ($item) {
        return '<a href="#' . htmlspecialchars($item['name']) . '">' . $item['name'] . '</a>';
    },
]);


$o5 = new AdminTableListRenderer();
$columnNames = array_keys($items[0]);
$o5->setRegularColumns($columnNames);
$o5->setSpecialColumn('action', 'last', function ($row) {
    return "id: " . $row['id'];
});
$o5->setSpecialColumn('action2', 6, function ($row) {
    return "id2: " . $row['id'];
});
$o5->setSpecialColumn('action3', 'last', function ($row) {
    return "id3: " . $row['id'];
});
$o5->setButtonsColumn('action4', 'last', ['edit', 'delete']);
$o5->setFilter('action3', function ($v) {
    return 'a' . $v;
});



AssetCalls::getInst()->callLib([
    'jquery',
]);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <?php echo AdrTool::getAdrMeta(); ?>
    <title>Beel demo page</title>
    <style>

        h2 {
            clear: both;
        }

        .item {
            border: 1px solid #aaa;
            height: 100px;
            width: 100px;
            margin-top: 10px;
            margin-left: 10px;
            float: left;
            overflow: auto;
        }

        .beeltable,
        .beeltable tr,
        .beeltable td,
        .beeltable th {
            border: 1px solid black;
            padding: 2px;
        }

        .beeltable {
            border-collapse: collapse;
        }

        .odd {
            background: #def7fa;
        }

        .even {
            background: #faebe4;
        }

    </style>
</head>

<body>

<h1>Beel Demo</h1>

<h2>Item list</h2>
<?php echo $o->render($items); ?>
<h2>Table list</h2>
<?php echo $o2->render($items); ?>
<h2>Array list</h2>
<?php echo $o3->render($conf); ?>
<h2>Recursive list</h2>
<?php echo $o4->render($menuData); ?>
<h2>AdminTable list</h2>
<?php echo $o5->render($items); ?>
</body>
</html>


