<?php

class DateTimeView {


	public function show() {

		$date = (new DateTime())->format('Y-m-d H:i:s');
		return "<p>" . $date. "</p>";
	}
}