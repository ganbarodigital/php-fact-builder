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
 * @package   FactBuilder/ComposerFacts
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2015-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-factbuilder
 */

namespace GanbaroDigital\FactBuilder\ComposerFacts\FactBuilders;

use GanbaroDigital\FactBuilder\AllFacts;
use GanbaroDigital\FactBuilder\Core\Fact;
use GanbaroDigital\FactBuilder\Core\FactBuilderQueue;
use GanbaroDigital\FactBuilder\Core\FactRepository;
use GanbaroDigital\FactBuilder\Core\FactBuilderFromData;
use GanbaroDigital\FactBuilder\Core\DataTypes\FilesystemPathData;
use GanbaroDigital\FactBuilder\ComposerFacts;

use GanbaroDigital\Filesystem\Checks\IsValidJsonFile;

class ComposerProjectFactBuilder implements FactBuilderFromData
{
	/**
	 * return a list of the facts that we are interested in exploring
	 *
	 * @return array<string>
	 */
	static public function getInterestsList()
	{
		return [];
	}

	static public function fromFilesystemPathData(FilesystemPathData $fsData)
	{
		// our return value
		$retval = [];

		if (!ComposerFacts\Checks\HasAComposerJsonFile::checkFilesystemPathData($fsData)) {
			return $retval;
		}

		// our composer file
		$composerJsonFilename = ComposerFacts\ValueBuilders\ComposerJsonFilePathBuilder::fromFilesystemPathData($fsData);

		if (!IsValidJsonFile::checkFilesystemPathData($composerJsonFilename)) {
			return $retval;
		}

		// at this point, we have something that looks like a composer project
		$composerProjectFact = new ComposerFacts\Facts\ComposerProjectFact();
		$composerProjectFact->setPathToFolder(dirname($composerJsonFilename));
		$composerProjectFact->setHasComposerJson(true);
		$composerProjectFact->setComposerJsonFilename($composerJsonFilename);
		$retval[] = $composerProjectFact;

		// all done
		return $retval;
	}
}