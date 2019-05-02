<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.04.2018
 * Time: 14:53
 */

namespace App;

use System\Logger\Logger;
use System\Logger\LoggerAware;
use System\Kernel\TypesApp\AbstractApplication;

final class ConsoleApp extends AbstractApplication implements ConsoleAppInterface
{
	/**
	 * @throws \Exception\FileException
	 * @throws \Throwable
	 */
	public function run()
	{
	    $this->runInternal();
	}

	/**
	 * @return void
	 */
	public function outputResponse(): void
	{
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