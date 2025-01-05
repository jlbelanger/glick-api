<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
	protected function replaceTokens(array $tokens, array $rows) : array
	{
		foreach ($tokens as $token => $replaceWith) {
			$rows = $this->replaceToken($token, $replaceWith, $rows);
		}
		return $rows;
	}

	protected function replaceToken(string $token, $replaceWith, array $rows) : array
	{
		foreach ($rows as $key => $row) {
			if (is_array($row)) {
				$rows[$key] = $this->replaceToken($token, $replaceWith, $row);
			} elseif ($row && strpos($row, $token) !== false) {
				if ($rows[$key] === $token) {
					$rows[$key] = $replaceWith;
				} else {
					$rows[$key] = str_replace($token, $replaceWith, $row);
				}
			}
		}
		return $rows;
	}
}
