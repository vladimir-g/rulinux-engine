<?php
final class search extends object
{
	static $baseC = null;
	function __construct()
	{
		self::$baseC = new base;
	}
	function find($str, $include, $date, $section, $username)
	{
		$query = 'SELECT * FROM comments WHERE ';
		if($include == 'topics')
			$query = $query.'subject LIKE \'%::0::%\'';
		else if($include == 'comments')
			$query = $query.'comment LIKE \'%::0::%\'';
		else
			$query = $query.'(subject LIKE \'%::0::%\' OR comment LIKE \'%::0::%\')';
		if($date=='3month')
		{
			$month = gmdate("m")-3;
			$timestamp = gmdate("Y").'-'.$month.'-'.gmdate("d H:i:s");
			$query = $query.' AND timest > \''.$timestamp.'\'';
		}
		else if($date=='year')
		{
			$month = gmdate("Y")-1;
			$timestamp = $year.'-'.gmdate("m-d H:i:s");
			$query = $query.' AND timest > \''.$timestamp.'\'';
		}
		$section = (int)$section;
		if($section !=0)
			$query = $query.' AND tid IN (SELECT id FROM threads WHERE section = \'::1::\')';
		if(!empty($username))
			$query = $query.' AND uid IN(SELECT id FROM users WHERE nick = \'::2::\')';
		$query = $query.' ORDER BY timest DESC';
		$param_arr = array($str, $section, $username);
		$sel = self::$baseC->query($query, 'assoc_array', $param_arr);
		return $sel;
	}
	
	function find_by_filters($str, $include, $date, $section, $username, $method, $filters_arr)
	{
		$query = 'SELECT * FROM comments WHERE ';
		if($include == 'topics')
			$query = $query.'subject LIKE \'%::0::%\'';
		else if($include == 'comments')
			$query = $query.'comment LIKE \'%::0::%\'';
		else
			$query = $query.'(subject LIKE \'%::0::%\' OR comment LIKE \'%::0::%\')';
		if($date=='3month')
		{
			$month = gmdate("m")-3;
			$timestamp = gmdate("Y").'-'.$month.'-'.gmdate("d H:i:s");
			$query = $query.' AND timest > \''.$timestamp.'\'';
		}
		else if($date=='year')
		{
			$month = gmdate("Y")-1;
			$timestamp = $year.'-'.gmdate("m-d H:i:s");
			$query = $query.' AND timest > \''.$timestamp.'\'';
		}
		$section = (int)$section;
		if($section !=0)
			$query = $query.' AND tid IN (SELECT id FROM threads WHERE section = \'::1::\')';
		if(!empty($username))
			$query = $query.' AND uid IN(SELECT id FROM users WHERE nick = \'::2::\')';
		if(!empty($filters_arr))
		{
			$query = $query.' AND (';
			foreach($filters_arr as $key => $value)
			{
				$query = $query.' filters LIKE \'%'.$value.'%\'';
				if($method == 'or')
					$query = $query.' OR';
				else
					$query = $query.' AND';
			}
			if($method == 'or')
				$query = substr($query, 0, strlen($query) - 3);
			else
				$query = substr($query, 0, strlen($query) - 4);
			$query = $query.' )';
		}
		$query = $query.' ORDER BY timest DESC';
		$param_arr = array($str, $section, $username);
		$sel = self::$baseC->query($query, 'assoc_array', $param_arr);
		return $sel;
	}
}
?>