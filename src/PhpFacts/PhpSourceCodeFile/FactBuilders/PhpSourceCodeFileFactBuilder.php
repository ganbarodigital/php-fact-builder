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
 * @package   FactFinder/PhpFacts
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2015-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-factfinder
 */

namespace GanbaroDigital\FactFinder\PhpFacts\PhpSourceCodeFile\FactBuilders;

use GanbaroDigital\FactFinder\DataFactBuilder;
use GanbaroDigital\FactFinder\FactBuilderQueue;
use GanbaroDigital\FactFinder\FactRepository;
use GanbaroDigital\FactFinder\All\Data;
use GanbaroDigital\FactFinder\All\DataTypes\FilesystemData;
use GanbaroDigital\FactFinder\All\DataTypes\PhpFileData;

use GanbaroDigital\FactFinder\PhpFacts\PhpSourceCodeFile\FactBuilders;
use GanbaroDigital\FactFinder\PhpFacts;

class PhpSourceCodeFileFactBuilder
{
	static public function fromFilesystemData(FilesystemData $data)
	{
		$retval = new PhpFacts\Facts\PhpSourceCodeFileFact();
		$retval->setPathToFile($data->getFileOrFolderPath());

		return $retval;
	}

	static public function fromPhpFileData(PhpFileData $data)
	{
		$retval = new PhpFacts\Facts\PhpSourceCodeFileFact();
		$retval->setPathToFile($data->getPathToFile());
		$retval->setExpectedNamespace($data->getNamespace());

		return $retval;
	}
}