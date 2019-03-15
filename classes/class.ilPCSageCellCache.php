<?php
/**
 * Copyright (c) 2018 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv3, see docs/LICENSE
 */

/**
 * Caching of SageCell data
 * This cache us used to transmit a cell definition between page comonent an cell iframe
 *
 */
class ilPCSageCellCache extends ilCache
{
	public function __construct()
	{
		parent::__construct("PCSageCell", "properties", true);
		$this->setExpiresAfter(10);		// seconds to make a hit
	}


	/**
	 * Never disable this cache
	 * @return bool
	 */
	public function isDisabled()
	{
		return false;
	}
}