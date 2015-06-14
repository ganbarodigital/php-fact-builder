#!/usr/bin/env php
<?php

/**
 * Copyright (c) 2015-present Ganbaro Digital Ltd
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Libraries
 * @package   FactFinder/Cli
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2015-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-factfinder
 */

use GanbaroDigital\FactFinder\RootFactFinder;
use GanbaroDigital\FactFinder\SeedDataTypes\FilesystemData;
use GanbaroDigital\FactFinder\FactRepositories\InMemoryFactRepository;
use GanbaroDigital\FactFinder\FactFinderQueues\InMemoryFactFinderQueue;

// temporary
require_once (__DIR__ . '/../vendor/autoload.php');

// first argument is the 'root' fact to start from
$rootFactName = $argv[1];

// second argument is the starting point to seed the 'root' fact with
$rootFactSeed = $argv[2];

// do we have a fact finder that we can use here?
$rootFactFinderName = $rootFactName . '\DefinitionFactFinder';
if (!class_exists($rootFactFinderName)) {
	die("Cannot find root fact finder class '{$rootFactFinderName}" . PHP_EOL);
}
$rootFactFinder = new $rootFactFinderName();
if (!$rootFactFinder instanceof RootFactFinder) {
	die("class '{$rootFactFinderName}' does not support being a 'root' for fact finding" . PHP_EOL);
}

// we need something to store the facts in
$factRepository = new InMemoryFactRepository();

// we need something to keep track of where we should look next
$factFinderQueue = new InMemoryFactFinderQueue();

// alright, let's see where this takes us
$factFinderSeed = new FilesystemData($rootFactSeed);
$rootFactFinder->findFactsFromRoot($factFinderSeed, $factRepository, $factFinderQueue);

// hopefully, our root fact has given us at least one more fact finder to use
foreach ($factFinderQueue->iterateFactFinders() as list ($fact, $factFinder)) {
	echo "Finding facts: " . get_class($factFinder) . PHP_EOL;
	$factFinder->findFactsFromFact($fact, $factRepository, $factFinderQueue);
}

// to make it easier to inspect, dump the facts as JSON
echo json_encode($factRepository->getFacts(), JSON_PRETTY_PRINT) . PHP_EOL;