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
 * @package   FactBuilder/Cli
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2015-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-factbuilder
 */

use GanbaroDigital\FactBuilder\Core\Data;
use GanbaroDigital\FactBuilder\Core\Fact;
use GanbaroDigital\FactBuilder\Core\FactRepositories\InMemoryFactRepository;
use GanbaroDigital\FactBuilder\Core\FactBuilderQueues\InMemoryFactBuilderQueue;
use GanbaroDigital\FactBuilder\Core\FactBuilding\InMemoryInterestsList;
use GanbaroDigital\FactBuilder\Core\FactBuilderFromData;
use GanbaroDigital\FactBuilder\Core\DataTypes\FilesystemPathData;

// a list of the fact builders that we want to use
// @TODO: find a way to make this discoverable in code
use GanbaroDigital\FactBuilder\ComposerFacts;
use GanbaroDigital\FactBuilder\PhpFacts;
use GanbaroDigital\FactBuilder\PsrFacts;

// temporary
require_once (__DIR__ . '/../vendor/autoload.php');

// first argument is the 'root' fact builder to start from
$rootFactBuilderName = $argv[1];

// second argument is the starting point to seed the 'root' fact with
$rootFactSeed = $argv[2];

// do we have a fact builder that we can use here?
if (!class_exists($rootFactBuilderName)) {
	die("Cannot find fact builder class '{$rootFactBuilderName}" . PHP_EOL);
}

// we need something to store the facts in
$factRepository = new InMemoryFactRepository();

// we need something to keep track of where we should look next
$FactBuilderQueue = new InMemoryFactBuilderQueue();

// we're going to seed the whole thing now
$FactBuilderSeed = new FilesystemPathData($rootFactSeed);
$seedFacts = $rootFactBuilderName::fromFilesystemPathData($FactBuilderSeed);
foreach ($seedFacts as $fact) {
	$factRepository->addFact($fact);
	$FactBuilderQueue->addFactToExplore($fact);
}

// at this point, we (hopefully) have at least one fact in the queue to be
// examined

// let's build up the list of builders that are interested in facts
$interestsList = new InMemoryInterestsList();
$knownBuilderClasses = [
	ComposerFacts\FactBuilders\ComposerProjectFactBuilder::class,
	ComposerFacts\FactBuilders\ComposerJsonFileFactBuilder::class,
	ComposerFacts\FactBuilders\AutoloadPsr0FactBuilder::class,
	ComposerFacts\FactBuilders\AutoloadPsr4FactBuilder::class,

	PsrFacts\FactBuilders\Psr0FolderFactBuilder::class,
	PsrFacts\FactBuilders\Psr4FolderFactBuilder::class,

	PhpFacts\FactBuilders\PhpSourceCodeFileFactBuilder::class,
	PhpFacts\FactBuilders\PhpNamespaceFactBuilder::class,
	PhpFacts\FactBuilders\PhpClassFactBuilder::class,
];
foreach($knownBuilderClasses as $knownBuilderClass) {
	$interestsList->addInterestedBuilderClass($knownBuilderClass);
}

// this is the fact-finding loop
while (($item = $FactBuilderQueue->iterateFromQueue()) !== null) {
	// what are we looking at?
	echo "Exploring " . get_class($item) . PHP_EOL . "  " . json_encode($item) . PHP_EOL;

	// who wants to look at it?
	$factBuilderClasses = $interestsList->getBuildersInterestedIn(get_class($item));
	if (count($factBuilderClasses) === 0) {
		echo "  no interest :(" . PHP_EOL;
		continue;
	}

	// at least one fact builder is interested
	foreach ($factBuilderClasses as $factBuilderClass) {
		// this class seems interested
		echo "  sending to " . $factBuilderClass . PHP_EOL;
		$parts = explode('\\', get_class($item));
		$method = 'from' . end($parts);

		if (!method_exists($factBuilderClass, $method)) {
			die("class " . $factBuilderClass . " does not accept " . get_class($item) . " items" . PHP_EOL);
		}

		// let's get some results
		$newItems = $factBuilderClass::$method($item);

		// what did we discover?
		echo "  discovered " . count($newItems) . " item(s) to explore" . PHP_EOL;
		foreach ($newItems as $newItem) {
			if ($newItem instanceof Fact) {
				// remember our new facts for future discovery
				$factRepository->addFact($newItem);
			}

			// these new items will need exploring
			$FactBuilderQueue->addItemToExplore($newItem);
		}
	}
}

// to make it easier to inspect, dump the facts as JSON
echo PHP_EOL;
echo "Final set of facts are:" . PHP_EOL . PHP_EOL;
echo json_encode($factRepository->getFacts(), JSON_PRETTY_PRINT) . PHP_EOL;