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
 * @package   FactBuilder/PsrFacts
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2015-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-factbuilder
 */

namespace GanbaroDigital\FactBuilder\PsrFacts\FactBuilders;

use GanbaroDigital\FactBuilder\Core\FactBuilderFromData;
use GanbaroDigital\FactBuilder\Core\DataTypes\PhpFileData;
use GanbaroDigital\FactBuilder\Core\DataTypes\FilesystemPathData;

use GanbaroDigital\FactBuilder\PhpFacts;
use GanbaroDigital\FactBuilder\PsrFacts;
use GanbaroDigital\Filesystem;

class Psr0AutoloaderFolderFactBuilder implements FactBuilderFromData
{
	static public function getInterestsList()
	{
		return [
			PsrFacts\DataTypes\Psr0AutoloaderFolderData::class
		];
	}

	static public function fromPsr0AutoloaderFolderData(PsrFacts\DataTypes\Psr0AutoloaderFolderData $data)
	{
		// our return value
		$retval = [];

		// what are we starting from?
		$path      = $data->getPathToFolder();
		$namespace = $data->getNamespace();

		// let's build up a list of facts from here
		$fsPath = new FilesystemPathData($path);
		$folders = Filesystem\ValueBuilders\MatchingFolders::fromFilesystemPathData($fsPath);

		// PSR-0 complicates things a little, because (like PEAR before it)
		// an _ in a class name can be a folder separator
		//
		// that means that any sub-folder can be a namespace, or it can be
		// part of the namespace of the parent folder
		//
		// it's a very handy feature of PSR-0 for developers
		//
		// for static analysis, the best we can do is capture the top-level
		// path and namespace, so that any checker has the data necessary
		// to work out whether code in the folder is PSR-0-compliant or not

		// convert the list of folders into a list of possible namespaces
		foreach ($folders as $folder) {
			$fact = new PsrFacts\Facts\Psr0AutoloaderFolderFact($folder, $path, $namespace);
			$retval[] = $fact;
		}

		// all done
		return $retval;
	}
}