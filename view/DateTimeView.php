<?php
namespace view;

class DateTimeView {

	private $dateObject;

	public function __construct() {
	$this->dateObject = new \DateTime('now', new \DateTimeZone('Europe/Stockholm'));
	}
	public function show() {

		$dayOfWeek = $this->dateObject->format('l');
		$dayOfMonth = $this->dateObject->format('jS');
		$month = $this->dateObject->format('F');
		$year = $this->dateObject->format('Y');
		$time = $this->dateObject->format('H:i:s');

		$dateString = $dayOfWeek . ", the " . $dayOfMonth . " of ". $month . " " . $year . ", The time is " . $time;

		return "<p>" . $dateString. "</p>";
	}
}