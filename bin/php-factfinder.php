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

use GanbaroDigital\FactFinder\Core\Data;
use GanbaroDigital\FactFinder\Core\DataTypes\FilesystemData;
use GanbaroDigital\FactFinder\Core\Fact;
use GanbaroDigital\FactFinder\Core\FactRepositories\InMemoryFactRepository;
use GanbaroDigital\FactFinder\Core\FactBuilderQueues\InMemoryFactBuilderQueue;
use GanbaroDigital\FactFinder\Core\FactBuilding\InMemoryInterestsList;
use GanbaroDigital\FactFinder\Core\FactBuilderFromData;

// a list of the fact builders that we want to use
// @TODO: find a way to make this discoverable in code
use GanbaroDigital\FactFinder\ComposerFacts;
use GanbaroDigital\FactFinder\PhpFacts;
use GanbaroDigital\FactFinder\PsrFacts;

// temporary
require_once (__DIR__ . '/../vendor/autoload.php');

// first argument is the 'root' fact to start from
$rootFactName = $argv[1];

// second argument is the starting point to seed the 'root' fact with
$rootFactSeed = $argv[2];

// do we have a fact finder that we can use here?
$rootFactFinderName = $rootFactName . '\FactBuilder';
if (!class_exists($rootFactFinderName)) {
	die("Cannot find root fact finder class '{$rootFactFinderName}" . PHP_EOL);
}
$rootFactFinder = new $rootFactFinderName();
if (!$rootFactFinder instanceof FactBuilderFromData) {
	die("class '{$rootFactFinderName}' does not support being a 'root' for fact finding" . PHP_EOL);
}

// we need something to store the facts in
$factRepository = new InMemoryFactRepository();

// we need something to keep track of where we should look next
$factFinderQueue = new InMemoryFactBuilderQueue();

// we're going to seed the whole thing now
$factFinderSeed = new FilesystemData($rootFactSeed);
$seedFacts = $rootFactFinder->buildFactsFromData($factFinderSeed);
foreach ($seedFacts as $fact) {
	$factRepository->addFact($fact);
	$factFinderQueue->addFactToExplore($fact);
}

// at this point, we (hopefully) have at least one fact in the queue to be
// examined

// let's build up the list of builders that are interested in facts
$interestsList = new InMemoryInterestsList();
$knownBuilderClasses = [
	ComposerFacts\ComposerProject\FactBuilder::class,
	ComposerFacts\ComposerJsonFile\FactBuilder::class
];
foreach($knownBuilderClasses as $knownBuilderClass) {
	$interestsList->addInterestedBuilderClass($knownBuilderClass);
}

// this is the fact-finding loop
foreach ($factFinderQueue->iterateFromQueue() as $item) {
	echo "Exploring " . get_class($item) . ": " . json_encode($item) . PHP_EOL;
	foreach ($interestsList->getBuildersInterestedIn(get_class($item)) as $factFinderClass) {
		echo "  sending to " . $factFinderClass . PHP_EOL;
		$factFinder = new $factFinderClass();
		$facts = [];
		if ($item instanceof Data) {
			$facts = $factFinder->buildFactsFromData($item);
		}
		else if ($item instanceof Fact) {
			$facts = $factFinder->buildFactsFromFact($item);
		}
		else {
			die("class '" . get_class($fact) . "' is unsupported in the fact finding loop" . PHP_EOL);
		}

		foreach ($facts as $fact) {
			// remember our new facts for future discovery
			$factRepository->addFact($fact);

			// these new facts will need exploring
			$factFinderQueue->addFactToExplore($fact);
		}
	}
}

// to make it easier to inspect, dump the facts as JSON
echo PHP_EOL;
echo "Final set of facts are:" . PHP_EOL . PHP_EOL;
echo json_encode($factRepository->getFacts(), JSON_PRETTY_PRINT) . PHP_EOL;