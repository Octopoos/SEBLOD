<?php
/**
 * @author Ovunc Tukenmez <ovunct@live.com>
 * version 1.0.1 - 10/26/2017
 *
 * This class is used to generate combinations with or without repetition allowed
 * as well as permutations with or without repetition allowed
 */

defined( '_JEXEC' ) or die;

class Combinations
{
	private $_elements = array();
	
	public function __construct($elements)
	{
		$this->setElements($elements);
	}
	
	public function setElements($elements){
		$this->_elements = array_values($elements);
	}

	public function getCombinations($length, $with_repetition = false){
		$combinations = array();
		
		foreach ($this->x_calculateCombinations($length, $with_repetition) as $value){
			$combinations[] = $value;
		}
		
		return $combinations;
	}
	
	public function getPermutations($length, $with_repetition = false){
		$permutations = array();
		
		foreach ($this->x_calculatePermutations($length, $with_repetition) as $value){
			$permutations[] = $value;
		}
		
		return $permutations;
	}
	
	private function x_calculateCombinations($length, $with_repetition = false, $position = 0, $elements = array()){

		$items_count = count($this->_elements);
		
		for ($i = $position; $i < $items_count; $i++){
			
			$elements[] = $this->_elements[$i];
			
			if (count($elements) == $length){
				yield $elements;
			}
			else{
				foreach ($this->x_calculateCombinations($length, $with_repetition, ($with_repetition == true ? $i : $i + 1), $elements) as $value2){
					yield $value2;
				}
			}
			
			array_pop($elements);
		}
	}
	
	private function x_calculatePermutations($length, $with_repetition = false, $elements = array(), $keys = array()){

		foreach($this->_elements as $key => $value){

			if ($with_repetition == false){
				if (in_array($key, $keys)){
					continue;
				}
			}

			$keys[] = $key;
			$elements[] = $value;
			
			if (count($elements) == $length){
				yield $elements;
			}
			else{
				foreach ($this->x_calculatePermutations($length, $with_repetition, $elements, $keys) as $value2){
					yield $value2;
				}
			}
			
			array_pop($keys);
			array_pop($elements);
		}
	}

}
