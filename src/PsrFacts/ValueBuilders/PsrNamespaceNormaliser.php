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

namespace GanbaroDigital\FactBuilder\PsrFacts\ValueBuilders;

class PsrNamespaceNormaliser
{
	/**
	 * main entry point
	 *
	 * @param  string $namespace
	 *         the namespace that we want to tidy up
	 *
	 * @return string
	 *         the tidied up namespace
	 */
	static public function normaliseNamespace($namespace)
	{
		$namespace = self::dealWithEmptyNamespace($namespace);
		$namespace = self::stripNamespaceSeparatorSuffix($namespace);

		return $namespace;
	}

	/**
	 * remove any '\' from the end of the namespace
	 *
	 * @param  string $namespace
	 *         the namespace to change
	 * @return string
	 *         the corrected namespace
	 */
	static public function stripNamespaceSeparatorSuffix($namespace)
	{
		// special case
		//
		// Composer (currently) requires namespaces to end with '\'
		// no idea why
		//
		// if it's there, strip it
		if (substr($namespace, -1, 1) == '\\') {
			$namespace = substr($namespace, 0, -1);
		}

		return $namespace;
	}

	/**
	 * convert Composer's idea of an empty namespace into PHP's idea of one
	 *
	 * @param  string $namespace
	 *         the namespace to change
	 * @return string
	 *         the corrected namespace
	 */
	static public function dealWithEmptyNamespace($namespace)
	{
		// special case
		//
		// Composer currently sets an empty namespace to the string _empty_
		//
		// we need to fix that
		if ($namespace === '_empty_') {
			$namespace = '';
		}

		return $namespace;
	}
}