<?php
declare(strict_types=1);

namespace Microsoft\Kiota\Abstraction\Promise;

/**
 * A rejected promise.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 *
 * @template-covariant T
 *
 * @implements Promise<T>
 */
class RejectedPromise implements Promise
{
	/**
	 * @var \Exception
	 */
	private $exception;

	public function __construct(\Exception $exception)
	{
		$this->exception = $exception;
	}

	public function then(?callable $onFulfilled = null, ?callable $onRejected = null)
	{
		if (null === $onRejected) {
			return $this;
		}

		try {
			return new FulfilledPromise($onRejected($this->exception));
		} catch (\Exception $e) {
			return new self($e);
		}
	}

	public function getState()
	{
		return Promise::REJECTED;
	}

	public function wait($unwrap = true)
	{
		if ($unwrap) {
			throw $this->exception;
		}

		return NULL;
	}
}