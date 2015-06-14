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
 * @package   FactFinder/FactTypes
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2015-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-factfinder
 */

namespace GanbaroDigital\FactFinder\FactTypes;

class InMemoryFact
{
	/**
	 * the information we have about this fact
	 * @var array
	 */
	public $info = [];

	/**
	 * retrieve a single piece of information about this fact
	 *
	 * @param  string $key
	 *         the information to retrieve
	 * @return mixed
	 *         the information found
	 */
	public function getInfo($key)
	{
		if (array_key_exists($key, $this->info)) {
			return $this->info[$key];
		}

		// if we get here, we do not know anything
		return null;
	}

	/**
	 * store some information about this fact
	 *
	 * @param string $key
	 *        the name of this information
	 * @param mixed $value
	 *        the information to store
	 */
	public function setInfo($key, $value)
	{
		$this->info[$key] = $value;
	}

	public function __call($methodName, $args)
	{
		list($verb, $infoName) = $this->convertMethodName($methodName);

		switch ($verb) {
			case 'get':
				return $this->getInfo($infoName);
			case 'set':
				return $this->setInfo($infoName, $args[0]);
			default:
				die("unsupported method '{$methodName}'");
		}
	}

	protected function convertMethodName($methodName)
	{
        // turn the method name into an array of words
        $words = explode(' ', strtolower(preg_replace('/([^A-Z])([A-Z])/', "$1 $2", $methodName)));

        // lose the first word
        $verb = array_shift($words);

        // concat into underscore_format
        $retval = implode("_", $words);

        // all done
        return [$verb, $retval];
	}
}