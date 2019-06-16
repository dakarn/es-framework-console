<?php

include_once  '../../bootstrap-console.php';

use ES\TranslateModule\Repositories\TranslateRepository;
use ES\Kernel\Helper\RepositoryHelper\StorageRepository;
use ES\TranslateModule\Models\{Word, WordList};

class LoaderTranslateFile
{
	private const SAVE_PATH  = 'C:/apache/htdocs/myblog-yii2/backend/web/mp3/%s.mp3';
	private const URL        = 'https://www.translate.ru/services/soap.asmx/CallForvo';
	private const USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36';

	/**
	 * @throws Exception
	 */
	public function execute()
	{
		$this->loadTranslateFile($this->getWordList());
	}

	/**
	 * @return WordList
	 * @throws Exception
	 */
	private function getWordList(): WordList
	{
		/** @var TranslateRepository $repository */
		$repository = StorageRepository::getRepository(TranslateRepository::class);

		return $repository->getWordList('sort');
	}

	/**
	 * @param WordList $wordList
	 */
	private function loadTranslateFile(WordList $wordList)
	{
		/** @var Word $word */
		foreach ($wordList->getAll() as $word) {

			if (\file_exists(\sprintf(self::SAVE_PATH, $word->getFirstWordOfText()))) {
				echo 'This file ' . $word->getText() . '.mp3 already exist.' . PHP_EOL;
				continue;
			}

			$response = $this->sendRequest($word);
			$mp3URL   = \json_decode($response, true)['d']['mp3URL'] ?? '';

			if (empty($mp3URL)) {
				echo 'MP3 file for ' . $word->getText() . ' not found.' . PHP_EOL;
				continue;
			}

			if (!\copy($mp3URL, \sprintf(self::SAVE_PATH, $word->getText()))) {
				echo 'Unable copy file ' . $word->getText() . '.' . PHP_EOL;
				continue;
			}

			echo 'File for ' . $word->getText() . ' copied!' . PHP_EOL;

			\sleep(1);
		}
	}

	/**
	 * @param Word $word
	 * @return mixed
	 */
	private function sendRequest(Word $word)
	{
		$ch = \curl_init(self::URL);
		\curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		\curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		\curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		\curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		\curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT);
		\curl_setopt($ch, CURLOPT_POST, true);
		\curl_setopt($ch, CURLOPT_POSTFIELDS, "{ dirCode:'en-ru', dKey:'" . $word->getFirstWordOfText() . "'}");
		\curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);

		$response = \curl_exec($ch);
		\curl_close($ch);

		return $response;
	}
}

(new LoaderTranslateFile())->execute();
