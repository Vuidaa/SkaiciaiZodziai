<?php
namespace SkaiciaiZodziai;

class Converter

{
	private $special = array(
		'Nulis',
		'Vienas',
		'Du',
		'Trys',
		'Keturi',
		'Penki',
		'Šeši',
		'Septyni',
		'Aštuoni',
		'Devyni',
		'Dešimt',
		'Vienuolika',
		'Dvylika',
		'Trylika',
		'Keturiolika',
		'Penkiolika',
		'Šešiolika',
		'Septyniolika',
		'Aštuoniolika',
		'Devyniolika'
	);
	private $tens = array(
		'20' => 'Dvidešimt',
		'30' => 'Trisdešimt',
		'40' => 'Keturiasdešimt',
		'50' => 'Penkiasdešimt',
		'60' => 'Šešiasdešimt',
		'70' => 'Septyniasdešimt',
		'80' => 'Aštuoniasdešimt',
		'90' => 'Devyniasdešimt'
	);
	private $largeNumbers = array(
		'100' => 'Šimtas',
		'1000' => 'Tūkstantis',
		'1000000' => 'Milijonas',
		'1000000000' => 'Milijardas',
		'1000000000000' => 'Trilijonas'
	);
	private $largeNumbersMultiple = array(
		'100' => 'Šimtai',
		'1000' => 'Tūkstančiai',
		'1000000' => 'Milijonai',
		'1000000000' => 'Milijardai',
		'1000000000000' => 'Trilijonai'
	);
	private $largeNumbersSpecial = array(
		'100' => 'Šimtų',
		'1000' => 'Tūkstančių',
		'1000000' => 'Milijonų',
		'1000000000' => 'Milijardų',
		'1000000000000' => 'Trilijonų'
	);
	private $words;
	private $number;
	private
	function validateAndFormatNumber($num)
	{
		$num = @number_format($num, 0, '', '');
		if ($num != '0' && $num != '') {
			if (strlen($num) < 16) {
				return $this->number = ltrim($num, '0');
			}
			else {
				die("Skačius - '$num' per didelis");
			}
		}

		$this->number = '0';
	}

	private
	function convertNumbersToWords($num)
	{
		switch (true) {
		case $num < 20:
			$this->words = $this->words . ' ' . $this->special[$num];
			return $this->words;
		case $num <= 99:
			$this->words = $this->words . ' ' . $this->tens[10 * floor($num / 10) ];
			$ones = $num % 10;
			if ($ones == 0) {
				return $this->words;
			}
			else {
				$this->words = $this->words . ' ' . $this->special[$ones];
				return $this->words;
			}

		case $num <= 999:
			$this->words.= $this->special[floor($num / 100) ] . ' ' . (floor($num / 100) == 1 ? $this->largeNumbers[100] : $this->largeNumbersMultiple[100]);
			$tens = $num % 100;
			if ($tens == 0) {
				return $this->words;
			}
			else {
				$this->words . ' ' . $this->convertNumbersToWords($tens);
				return $this->words;
			}

		case $num > 999:
			$cleanNum = $this->determineNumber($num) ['num'];
			$this->words = $this->convertNumbersToWords(floor($num / $cleanNum)) . ' ' . $this->determineNumber($num) ['word'] . ' ';
			$rest = bcmod($num, $cleanNum);
			if ($rest == 0) {
				return $this->words;
			}
			else {
				return $this->words . ' ' . $this->convertNumbersToWords($rest);
			}
		}
	}

	private
	function determineNumber($num)
	{
		$lenght = strlen($num);
		switch ($lenght) {
		case 4:
		case 7:
		case 10:
		case 13:
			$cleanNum = '1' . str_repeat('0', $lenght - 1);
			$flored = (string)floor($num / $cleanNum);
			return ($flored == 1) ? array(
				'word' => $this->largeNumbers[$cleanNum],
				'num' => $cleanNum
			) : array(
				'word' => $this->largeNumbersMultiple[$cleanNum],
				'num' => $cleanNum
			);
		case 5:
		case 8:
		case 11:
		case 14:
			$cleanNum = '1' . str_repeat('0', $lenght - 2);
			$flored = (string)floor($num / $cleanNum);
			if (array_key_exists($flored, $this->special) || array_key_exists($flored, $this->tens)) {
				return array(
					'word' => $this->largeNumbersSpecial[$cleanNum],
					'num' => $cleanNum
				);
			}
			else {
				if (array_key_exists($flored, $this->tens)) {
					return array(
						'word' => $this->largeNumbersMultiple[$cleanNum],
						'num' => $cleanNum
					);
				}

				return (substr($flored, -1) == 1 ? array(
					'word' => $this->largeNumbers[$cleanNum],
					'num' => $cleanNum
				) : array(
					'word' => $this->largeNumbersMultiple[$cleanNum],
					'num' => $cleanNum
				));
			}

		case 6:
		case 9:
		case 12:
		case 15:
			$cleanNum = '1' . str_repeat('0', $lenght - 3);
			$flored = (string)floor($num / $cleanNum);
			$tens = substr($flored, 1, 2);
			if (array_key_exists($tens, $this->special) || array_key_exists($tens, $this->tens)) {
				return array(
					'word' => $this->largeNumbersSpecial[$cleanNum],
					'num' => $cleanNum
				);
			}
			else {
				if ($tens == '00') {
					return array(
						'word' => $this->largeNumbersSpecial[$cleanNum],
						'num' => $cleanNum
					);
				}

				return (substr($tens, -1) == 1 ? array(
					'word' => $this->largeNumbers[$cleanNum],
					'num' => $cleanNum
				) : array(
					'word' => $this->largeNumbersMultiple[$cleanNum],
					'num' => $cleanNum
				));
			}
		}
	}

	public

	function spell($num)
	{
		$this->validateAndFormatNumber($num);
		$this->convertNumbersToWords($this->number);
		$temp = $this->words;
		$this->words = '';
		return $temp;
	}
}