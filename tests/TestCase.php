<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
	use CreatesApplication;

	protected function replaceTokens(array $tokens, array $rows) : array
	{
		foreach ($tokens as $token => $replaceWith) {
			$rows = $this->replaceToken($token, $replaceWith, $rows);
		}
		return $rows;
	}

	protected function replaceToken(string $token, string $replaceWith, array $rows) : array
	{
		foreach ($rows as $key => $row) {
			if (is_array($row)) {
				$rows[$key] = $this->replaceToken($token, $replaceWith, $row);
			} elseif (strpos($row, $token) !== false) {
				$rows[$key] = str_replace($token, $replaceWith, $row);
			}
		}
		return $rows;
	}
}
