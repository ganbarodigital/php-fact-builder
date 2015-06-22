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

namespace GanbaroDigital\FactFinder\Core\FactBuilding;

class InMemoryInterestsList
{
	/**
	 * a list of which builders are interested in which facts
	 * @var array<Fact,array<FactBuilderFromFact>>
	 */
	protected $interests = array();

	/**
	 * add a fact builder's interests to our lists
	 *
	 * @param string $className
	 *        the name of the class which is interested in facts
	 */
	public function addInterestedBuilderClass($className)
	{
		$interests = $className::getInterestsList();
		foreach ($interests as $factClass) {
			$this->interests[$factClass][] = $className;
			echo "{$className} is interested in {$factClass}" . PHP_EOL;
		}
	}

	/**
	 * return the list of builder classes interested in a given type of
	 * fact or data
	 *
	 * @param  string $className
	 *         the fact or data class of interest
	 * @return array<FactBuilderFromFact|FactBuilderFromData>
	 *         the list of classes that want to look at this type of fact
	 */
	public function getBuildersInterestedIn($className)
	{
		if (!isset($this->interests[$className])) {
			return [];
		}

		return $this->interests[$className];
	}
}