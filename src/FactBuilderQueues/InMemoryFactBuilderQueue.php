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
 * @package   FactFinder/FactFinderQueues
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2015-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-factfinder
 */

namespace GanbaroDigital\FactFinder\FactBuilderQueues;

use GanbaroDigital\FactFinder\Fact;
use GanbaroDigital\FactFinder\FactBuilderQueue;
use GanbaroDigital\FactFinder\All\Data;

class InMemoryFactBuilderQueue implements FactBuilderQueue
{
	protected $factFinders = [];

	public function addFactFinder(Fact $fact, $factFinderClasses)
	{
		$this->factFinders[] = [ $fact, $factFinderClasses ];
	}

	public function addSeedDataToExplore(Data $data, $factFinderClass)
	{
		$this->factFinders[] = [ $data, [$factFinderClass] ];
	}

	public function iterateFactFinders()
	{
		// we keep going until we run out of fact finders
		//
		// we cannot use a foreach() loop here, as foreach() does not notice
		// when we add new things to the end of the factFinders list
		while (true) {
			$nextGroup = each($this->factFinders);
			if (!is_array($nextGroup)) {
				// we're done here
				return;
			}

			$nextFactToFindFrom = $nextGroup[1][0];
			$finderClasses      = $nextGroup[1][1];

			foreach ($finderClasses as $nextFactFinderClass) {
				if (!class_exists($nextFactFinderClass)) {
					throw new E4xx_FactFinderNotFound($nextFactFinderClass);
				}

				$nextFactFinder = new $nextFactFinderClass;
				yield([$nextFactToFindFrom, $nextFactFinder]);
			}
		}
	}
}