<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.05.2019
 * Time: 2:01
 */

namespace ES\App\QueueApp\Models\Body;

use ES\Kernel\Helper\AbstractList;

class LogsBodyList extends AbstractList
{
	public function getMappingClass(): string
	{
		return LogsBody::class;
	}
}