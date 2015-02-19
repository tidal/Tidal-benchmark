<?php
namespace Tidal\Benchmark;

/*
 * Copyright (C) 2008 Timo Michna
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 */ 

/**
 * A simple Printer/Writer for the data of the Benchmark class.
 * Can be unsed in CLI or Web
 *
 * @author Timo Michna
 */
class TidalBenchmarkPrinter {
    
    /*
     * @var Tidal\Benchmark\Benchmark a Benchmark instance with data to fetch from
     */
    protected $benchmark;
    
    /**
     * Constructor
     * @param \Tidal\Benchmark\Benchmark $benchmark
     */
    public function __construct(Benchmark $benchmark){
        $this->benchmark = $benchmark;
    }
    
    /**
     * Prints out the benchmark data for given key
     * 
     * @param string $key the key of the benchmark
     * @param boolean $wrapPre wether to wrap output in '<pre>' tag for html
     */
    public function printData($key, $wrapPre = false){
        if($wrapPre) echo '<pre>';
        echo $this->formatData($key);
        if($wrapPre) echo '</pre>';
        
    }
    
    /**
     * Formats out the benchmark data for given key
     * 
     * @param string $key the key of the benchmark
     * @return string. formatted data 
     */
    public function formatData($key){
        $output = '';
        $data = $this->benchmark->getData($key);
        $devider = "-----------------------------------------------";
        
        $output .= "\n$devider\n$key ->\n$devider";
        $output .= "\nCALLS: ..................... ".$data['calls'];
        $output .= "\nTIME (s): .................. ".$data['time_all'];
        $output .= "\nTIME EACH (ms): ............ ".($data['time_each']*1000);
        $output .= "\nCALLS PER SECOND: .......... ".$data['calls_second'];
        $output .= "\nMEMORY RAISED(MB): ......... ".$this->formatMemory($data['mem_raised']);
        $output .= "\nMAX MEMORY RAISED(MB): ..... ".$this->formatMemory($data['mem_peak_raised']);
        $output .= "\nSETUP MEMORY RAISED(MB): ... ".$this->formatMemory($data['mem_setup_call']);
        $output .= "\nSETUP MAX MEMORY RAISED(MB): ".$this->formatMemory($data['mem_setup_call_peak']);
        $output .= "\n$devider\n"; 
            
        return $output;
    }
    
    /**
     * Formats memory to MB floar format
     * 
     * @param string $key the key of the benchmark
     * @return float. memory in MB 
     */
    protected function formatMemory($memory){
        $mbBytes = 1048576;
        $kb = $memory%$mbBytes;
        $mb = ($memory-$kb)/$mbBytes;
        
        return "$mb.$kb";
    }
    

}


