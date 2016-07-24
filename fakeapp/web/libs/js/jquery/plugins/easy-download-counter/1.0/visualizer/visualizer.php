<?php




//------------------------------------------------------------------------------/
// VISUALIZER CONFIG
//------------------------------------------------------------------------------/
require_once __DIR__ . '/../function/functions.php';

$conf = getGrappinConf(dirname($_SERVER['SCRIPT_FILENAME']) . "/..");


$statsDir = $conf['statsDirVisualizer'];


//------------------------------------------------------------------------------/
// SCRIPT
//------------------------------------------------------------------------------/
function scanFiles($dir, $fn, $filter = 'all')
{
    $files = scandir($dir);
    foreach ($files as $file) {
        if ('.' !== $file && '..' !== $file) {
            $parse = true;
            $afile = $dir . '/' . $file;
            if ('dir' === $filter && false === is_dir($afile)) {
                $parse = false;
            }
            if ('file' === $filter && false === is_file($afile)) {
                $parse = false;
            }
            if (true === $parse) {
                $fn($afile, $file);
            }
        }
    }
}

function getMonthSum($aMois)
{
    // skip .DS_Store...
    if ('.txt' === substr($aMois, -4)) {
        $monthSum = 0;
        $lines = file($aMois);
        foreach ($lines as $line) {
            // assume the file is not corrupted
            $n = substr($line, 11);
            $monthSum += (int)$n;
        }
        return $monthSum;
    }
    return false;
}


if (isset($_POST['id'])) {
    $data = [];
    $identifier = $_POST['id'];
    $absIdentifier = $statsDir . '/' . $identifier;
    if (is_dir($absIdentifier)) {
        // scanning years, here we just want to find the oldest and newest year
        scanFiles($absIdentifier, function ($aYear, $year) use ($identifier, &$data) {
            $data[$year] = [];
            scanFiles($aYear, function ($aMois, $mois) use (&$data, $year, $identifier) {
                if (false !== $n = getMonthSum($aMois)) {
                    $data[$year][substr($mois, 0, 2)] = $n;
                }
            }, 'file');
        }, 'dir');
    }
    echo json_encode($data);
    exit;
}
else {

    require_once __DIR__ . '/inc/header.php';


    // creating main table with all identifiers
    if (is_dir($statsDir)) {


        // first collecting data the way we want
        $oldestYear = 2600;
        $newestYear = 0;
        $identifiers2Global = [];
        scanFiles($statsDir, function ($absIdentifier, $identifier) use (&$oldestYear, &$newestYear, &$identifiers2Global) {


            // scanning years, here we just want to find the oldest and newest year
            scanFiles($absIdentifier, function ($aYear, $year) use (&$oldestYear, &$newestYear, &$identifiers2Global, $identifier) {
                if ($year < $oldestYear) {
                    $oldestYear = $year;
                }
                if ($year > $newestYear) {
                    $newestYear = $year;
                }

                if (!array_key_exists($identifier, $identifiers2Global)) {
                    $identifiers2Global[$identifier] = [];
                }
                $yearSum = 0;
                scanFiles($aYear, function ($aMois, $mois) use (&$yearSum, $year, $identifier) {
                    if (false !== $n = getMonthSum($aMois)) {
                        $yearSum += $n;
                    }
                }, 'file');

                $identifiers2Global[$identifier][$year] = $yearSum;

            }, 'dir');

        }, 'dir');


        // now displaying the table
        $s = '<table class="maintable">';
        $s .= '<tr><th>identifier</th>';
        for ($i = $oldestYear; $i <= $newestYear; $i++) {
            $s .= '<th>' . $i . '</th>';
        }
        $s .= '</tr>';

        foreach ($identifiers2Global as $id => $years) {
            $s .= '<tr>';
            $s .= '<td><a class="edctrigger" data-edc-id="' . $id . '" href="#">' . $id . '</a></td>';


            for ($i = $oldestYear; $i <= $newestYear; $i++) {
                if (array_key_exists($i, $years)) {
                    $s .= '<td>' . $years[$i] . '</td>';
                }
                else {
                    $s .= '<td>0</td>';
                }
            }
            $s .= '</tr>';
        }

        $s .= '</table>';
        echo $s;


        // prepare js to query the graph for a specific identifier
        echo '<script>';
        require_once __DIR__ . '/inc/visualizer.js';
        echo '</script>';


        // preparing charts
        echo '<div id="chartscontainer"></div>';


    }
    else {
        echo "Stats dir not created yet";
    }
    require_once __DIR__ . '/inc/footer.php';
}



