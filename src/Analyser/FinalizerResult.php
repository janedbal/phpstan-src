<?php declare(strict_types = 1);

namespace PHPStan\Analyser;

use function array_merge;

class FinalizerResult
{

	/**
	 * @param list<Error> $collectorErrors
	 * @param list<Error> $locallyIgnoredCollectorErrors
	 */
	public function __construct(
		private AnalyserResult $analyserResult,
		private array $collectorErrors,
		private array $locallyIgnoredCollectorErrors,
	)
	{
	}

	/**
	 * @return list<Error>
	 */
	public function getErrors(): array
	{
		return array_merge(
			$this->analyserResult->getErrors(),
			$this->analyserResult->getFilteredPhpErrors(),
		);
	}

	public function getAnalyserResult(): AnalyserResult
	{
		return $this->analyserResult;
	}

	/**
	 * @return list<Error>
	 */
	public function getCollectorErrors(): array
	{
		return $this->collectorErrors;
	}

	/**
	 * @return list<Error>
	 */
	public function getLocallyIgnoredCollectorErrors(): array
	{
		return $this->locallyIgnoredCollectorErrors;
	}

}
