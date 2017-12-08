<?php
class datecon
{

	public function dateTimeTh($dDate)
	{
		$date = new DateTime($dDate);
		$sMonth = Array(
						'01' => 'มกราคม',
						'02' => 'กุมภาพันธ์',
						'03' => 'มีนาคม',
						'04' => 'เมษายน',
						'05' => 'พฤษภาคม',
						'06' => 'มิถุนายน',
						'07' => 'กรกฏาคม',
						'08' => 'สิงหาคม',
						'09' => 'กันยายน',
						'10' => 'ตุลาคม',
						'11' => 'พฤศจิกายน',
						'12' => 'ธันวาคม');
		$y = $date->format('Y') + 543;
		return $date->format('j').' '.$sMonth[$date->format('m')].' '.$y.' เวลา '.$date->format('H:i:s').' น.';
	}

	public function dateThF($dDate)
	{
		$date = new DateTime($dDate);
		$sMonth = Array(
						'01' => 'มกราคม',
						'02' => 'กุมภาพันธ์',
						'03' => 'มีนาคม',
						'04' => 'เมษายน',
						'05' => 'พฤษภาคม',
						'06' => 'มิถุนายน',
						'07' => 'กรกฏาคม',
						'08' => 'สิงหาคม',
						'09' => 'กันยายน',
						'10' => 'ตุลาคม',
						'11' => 'พฤศจิกายน',
						'12' => 'ธันวาคม');
		$y = $date->format('Y') + 543;
		return $date->format('j').' '.$sMonth[$date->format('m')].' '.$y;
	}

	public function dateThS($dDate)
	{
		$date = new DateTime($dDate);
		$sMonth = Array(
						'01' => 'ม.ค.',
						'02' => 'ก.พ.',
						'03' => 'ม.ค.',
						'04' => 'เม.ย.',
						'05' => 'พ.ค.',
						'06' => 'มิ.ย.',
						'07' => 'ก.ค.',
						'08' => 'ส.ค.',
						'09' => 'ก.ย.',
						'10' => 'ต.ค.',
						'11' => 'พ.ย.',
						'12' => 'ธ.ค.');
		$y = $date->format('Y') + 543;
		return $date->format('j').' '.$sMonth[$date->format('m')].' '.$y;
	}

}
