<?php
namespace view;

class DateTimeView {


	public function show() {

		$dateObject = new \DateTime();
		\date_modify($dateObject, "1 hours"); 
		$dayOfWeek = $dateObject->format('l');
		$dayOfMonth = $dateObject->format('dS');
		$month = $dateObject->format('F');
		$year = $dateObject->format('Y');
		$time = $dateObject->format('H:i:s');

		$dateString = $dayOfWeek . ", the " . $dayOfMonth . " of ". $month . " " . $year . ", The time is " . $time;

		return "<p>" . $dateString. "</p>";
	}
}