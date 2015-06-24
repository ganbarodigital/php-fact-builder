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
 * @package   FactBuilder/Core
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2015-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-factbuilder
 */

namespace GanbaroDigital\FactBuilder\Core\FactBuilding;

use GanbaroDigital\FactBuilder\Core\Fact;
use GanbaroDigital\FactBuilder\Core\FactBuilderQueue;
use GanbaroDigital\FactBuilder\Core\Data;

class InMemoryFactBuilderQueue implements FactBuilderQueue
{
	protected $exploreQueue = [];

	public function addItemToExplore($item)
	{
		if ($item instanceof Fact) {
			$this->addFactToExplore($item);
		}
		else if ($item instanceof Data) {
			$this->addDataToExplore($item);
		}
		else if (is_object($item)) {
			throw new \Exception("Unsupported item type " . get_class($item));
		}
		else {
			throw new \Exception("Unsupported item type " . json_encode($item));
		}
	}

	public function addFactToExplore(Fact $fact)
	{
		$this->exploreQueue[] = $fact;
	}

	public function addDataToExplore(Data $data)
	{
		$this->exploreQueue[] = clone $data;
	}

	public function iterateFromQueue()
	{
		// return the next thing in our queue
		$retval = array_shift($this->exploreQueue);

		// all done
		return $retval;
	}
}