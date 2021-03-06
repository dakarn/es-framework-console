<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.04.2018
 * Time: 14:53
 */

namespace ES\App;

use ES\Kernel\Logger\Logger;
use ES\Kernel\Logger\LoggerAware;
use ES\Kernel\Kernel\TypesApp\AbstractApplication;

final class ConsoleApp extends AbstractApplication implements ConsoleAppInterface
{
	/**
	 */
	public function run()
	{
	    $this->runInternal();
	}

	/**
	 * @return void
	 */
	public function setupClass()
	{
	}

	/**
	 *
	 */
	public function terminate()
	{
		LoggerAware::setLogger(Logger::class)->getLoggerStorage()->releaseLogs();
	}

	/**
	 * @param \Throwable $e
	 * @throws \Throwable
	 */
	public function customOutputError(\Throwable $e)
	{
		throw $e;
	}
}