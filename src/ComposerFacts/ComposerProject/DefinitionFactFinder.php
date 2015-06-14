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
 * @package   FactFinder/ComposerFacts
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2015-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-factfinder
 */

namespace GanbaroDigital\FactFinder\ComposerFacts\ComposerProject;

use GanbaroDigital\FactFinder\FactRepository;
use GanbaroDigital\FactFinder\RootFactFinder;
use GanbaroDigital\FactFinder\SeedData;
use GanbaroDigital\FactFinder\SeedDataTypes\FilesystemData;
use GanbaroDigital\FactFinder\Specifications\IsValidJsonFile;
use GanbaroDigital\FactFinder\ComposerFacts\ComposerProject\Builders\ComposerJsonFilePathBuilder;
use GanbaroDigital\FactFinder\ComposerFacts\ComposerProject\FactFinding\HasAComposerJsonFile;

class DefinitionFactFinder implements RootFactFinder
{
	public function getDependencies()
	{
		return [];
	}

	// ==================================================================
	//
	// support for being a 'root' for fact finding
	//
	// ------------------------------------------------------------------

	public function findFactsFromRoot(SeedData $rootData, FactRepository $factRepo)
	{
		// is this a composer project?
		$this->requireIsComposerProject($rootData);

		// our composer file
		$composerJsonFilename = ComposerJsonFilePathBuilder::fromFilesystemData($rootData);

		// do we have a valid JSON file?
		$this->requireComposerFileIsValidJson($composerJsonFilename);

		// at this point, we have something that is valid JSON, but that's
		// all we know about it
		$composerProjectFact = new ComposerProjectFact();
		$composerProjectFact->setPathToFolder(dirname($composerJsonFilename));

		// remember the fact
		$factRepo->addFact($composerProjectFact);

		// all done
	}

	public function findFactsFromFacts(FactsRepository $factsRepository)
	{
		//
	}

	protected function requireIsComposerProject(FilesystemData $rootData)
	{
		$spec = new HasAComposerJsonFile($rootData);
		if (!$spec->isSatisfiedBy($rootData)) {
			throw new E4xx_NotAComposerProject($rootData);
		}
	}

	protected function requireComposerFileIsValidJson($composerJsonFilename)
	{
		$spec = new IsValidJsonFile($composerJsonFilename);
		if (!$spec->isSatisfiedBy($composerJsonFilename)) {
			throw new E4xx_ComposerJsonIsNotValid($composerJsonFilename);
		}
	}
}