<?php
require_once '../src/TidalBenchmark.php';
require_once '../src/TidalBenchmarkPrinter.php';

use 
    Tidal\Benchmark\TidalBenchmark,
    Tidal\Benchmark\TidalBenchmarkPrinter;

////////////////////////////////////////////////////////////////////
// test native 'array_intersect' function against a custom variant
////////////////////////////////////////////////////////////////////

// nr of calls
$nrCalls = 1000;

// alterrnative 'array_intersect' function
// taken from : http://php.net/manual/en/function.array-intersect.php#111563
function simple_array_intersect($a,$b) {
    $a_assoc = $a != array_values($a);
    $b_assoc = $b != array_values($b);
    $ak = $a_assoc ? array_keys($a) : $a;
    $bk = $b_assoc ? array_keys($b) : $b;
    $out = array();
    for ($i=0;$i<sizeof($ak);$i++) { 
        if (in_array($ak[$i],$bk)) {
            if ($a_assoc) {
                $out[$ak[$i]] = $a[$ak[$i]];
            } else {
                $out[] = $ak[$i];
            }
        }
    }
    return $out;
}

// create array
$first = array();
// create a smaller array (associative)
$second = array();

// setup function populating some test data
// something like this should propably not be done here. Merely fpr demo purposes
$setup = function($index)use($first, $second){ 
    $first = array();
    for ($i=500;$i<50000;$i++) {
        $first[] = $i;
    }
    $second = array();
    for ($i=499990;$i<500000;$i++) {
        $second[$i] = rand();
    }
};


// create Benchmark and Printer instances
$benchmark 	= new \Tidal\Benchmark\TidalBenchmark();
$printer 	= new \Tidal\Benchmark\TidalBenchmarkPrinter($benchmark);

/////////////////////////////////////
// test code for native function
/////////////////////////////////////
$callArrayIntersect = function($index)use($first, $second){ 
    array_intersect($first, $second);
};
// running benchmark and printing results
$benchmarkKey = 'array_intersect nativ';
$benchmark->execute($benchmarkKey, $nrCalls, $callArrayIntersect, $setup);
$printer->printData($benchmarkKey, false);


/////////////////////////////////////
// test code for custom function
/////////////////////////////////////
$callSimpleArrayIntersect = function($index)use($first, $second){ 
    simple_array_intersect($first, $second);
};
$benchmarkKey = 'array_intersect custom';
// this time registering and running in two steps
$benchmark->register($benchmarkKey, $nrCalls, $callArrayIntersect, $setup);
$benchmark->execute($benchmarkKey);
$printer->printData($benchmarkKey, false);
