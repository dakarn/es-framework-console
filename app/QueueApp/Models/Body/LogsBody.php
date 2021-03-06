<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 04.05.2019
 * Time: 0:09
 */

namespace ES\App\QueueApp\Models\Body;

use ES\Kernel\Kafka\Message\AbstractQueueBody;
use ES\Kernel\ObjectMapper\ClassToMappingInterface;

class LogsBody extends AbstractQueueBody implements ClassToMappingInterface
{
	private $level;
	private $time;
	private $message;

	public function getProperties(): array
	{
		return [
			'level',
			'time',
			'message',
		];
	}

	/**
	 * @return mixed
	 */
	public function getLevel()
	{
		return ucfirst($this->level);
	}

	/**
	 * @param mixed $level
	 */
	public function setLevel($level): void
	{
		$this->level = $level;
	}

	/**
	 * @return mixed
	 */
	public function getTime()
	{
		return $this->time;
	}

	/**
	 * @param mixed $time
	 */
	public function setTime($time): void
	{
		$this->time = $time;
	}

	/**
	 * @return mixed
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * @param mixed $message
	 */
	public function setMessage($message): void
	{
		$this->message = $message;
	}
}