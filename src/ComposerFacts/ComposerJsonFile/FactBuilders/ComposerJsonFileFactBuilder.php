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

namespace GanbaroDigital\FactFinder\ComposerFacts\ComposerJsonFile\FactBuilders;

use GanbaroDigital\FactFinder\All\DataTypes\FilesystemData;
use GanbaroDigital\FactFinder\ComposerFacts\Facts\ComposerJsonFileFact;

class ComposerJsonFileFactBuilder
{
	static public function fromFilesystemData(FilesystemData $fsData)
	{
		$retval = new ComposerJsonFileFact;

		// load the composer file
		$composerJsonFilename = $fsData->getFileOrFolderPath();
		$contents = json_decode(file_get_contents($composerJsonFilename));

		// convert the contents to facts
		$retval->setPathToFile($composerJsonFilename);
		$retval->setRawJson($contents);

		// everything below here is obsolete and needs removing
		if (isset($contents->require)) {
			$retval->setRequire($contents->require);
		}
		if (isset($contents->{'require-dev'})) {
			$retval->setRequireDev($contents->{'require-dev'});
		}
		if (isset($contents->autoload, $contents->autoload->files)) {
			$retval->setAutoloadFiles($contents->autoload->files);
		}

		// all done
		return $retval;
	}
}