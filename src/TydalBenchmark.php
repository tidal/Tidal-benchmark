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

/*
 * Simple Raw Benchmarking Class measuring time spend on running code and it's memory consumption
 */
class Benchmark {
    
    protected $benchs = array();
    

    /** 
     * Execute the code to measure
     * 
     * @param  string   $key. key to identify your measures
     * @param  integer  $nrCalls. how often to execute given code
     * @param  callable $function. a callback containg the code to measure
     * @param  callable $setupFunction Called once before every code run. 
     * @return void      
     */
    public function execute($key, $nrCalls = false, $function = false, $setupFunction = false){
        if($nrCalls && $function){
            $this->register($key, $nrCalls, $function, $setupFunction);
        }elseif(!isset($this->benchs[$key])){
            throw new \RuntimeException('Key is not registered');
        }
        $this->benchs[$key]['mem_start'] = memory_get_usage(true);
        $this->benchs[$key]['mem_peak_start'] = memory_get_peak_usage(true);
        $this->benchs[$key]['time_start'] = microtime(true);
        $this->benchs[$key]['mem_setup_call'] = 0;
        $this->benchs[$key]['mem_setup_call_peak'] = 0;
        
        $timeTaken = 0;
        
        for($x = 0; $x < $this->benchs[$key]['calls']; $x++){
            $setupResult = null;
            if($this->benchs[$key]['setup_function']){
                $mem = memory_get_usage(true);
                $memPeak = memory_get_peak_usage(true);
                $setupResult = $this->benchs[$key]['setup_function']($x); 
                $this->benchs[$key]['mem_setup_call'] += memory_get_usage(true) - $memPeak;
                $this->benchs[$key]['mem_setup_call_peak'] += memory_get_peak_usage(true) - $memPeak;
            }
            $timeStartCall = microtime(true);
            $this->benchs[$key]['function']($x, $setupResult);
            $timeStopCall = microtime(true);
            $timeTaken += ($timeStopCall-$timeStartCall);
        }
        $memStop = memory_get_usage(true) - $this->benchs[$key]['mem_setup_call'];
        $peakMemStop = memory_get_peak_usage(true) - $this->benchs[$key]['mem_setup_call_peak'];
        $this->benchs[$key]['time_stop'] = microtime(true);  
        $this->benchs[$key]['mem_stop'] = $memStop;
        $this->benchs[$key]['mem_peak_stop'] = $peakMemStop;
        $this->benchs[$key]['time_all'] = $timeTaken;
        $this->benchs[$key]['time_each'] = $timeTaken/$this->benchs[$key]['calls'];
        $this->benchs[$key]['calls_second'] = 1/($timeTaken/$this->benchs[$key]['calls']);
        $this->benchs[$key]['mem_raised'] = $memStop-$this->benchs[$key]['mem_start'];      
        $this->benchs[$key]['mem_peak_raised'] = $peakMemStop-$this->benchs[$key]['mem_peak_start'];
              
    }

    /** 
     * Register code to measure. Code can be run by method 'execute' by only passing the key
     * 
     * @param  string   $key. key to identify your measures
     * @param  integer  $nrCalls. how often to execute given code
     * @param  callable $function. a callback containg the code to measure
     * @param  callable $setupFunction Called once before every code run. 
     * @return void      
     */
    public function register($key, $nrCalls, $function, $setupFunction = false){
        $this->benchs[$key] = array(
            'calls' => $nrCalls,
            'function' => $function,
            'setup_function' => $setupFunction
        );
    }
    
    /** 
     * Returns preprocessed measured data
     * 
     * @param  string $key. key to identify your measures
     * @return array  key/value pairs of measured data     
     */
    public function getData($key){
        if(!isset($this->benchs[$key])){
            throw new \RuntimeException('Key is not registered');
        }
        
        $data = $this->benchs[$key];
        unset($data['function']);
        unset($data['setup_function']);
        return $data;
    }
    
    /** 
     * Returns list of registered benchmark keys
     * 
     * @return array  list of keys     
     */
    public function getBenchmarkKeys(){
        return array_keys($this->benchs);
    }
    
}

