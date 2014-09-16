<?php

class Security
{
		private static $db = null;
		private static $date_fmt = 'Y-m-d H:i:s';

		public function __construct($config_file)
		{
				self::$db = new base;
				$this->config = parse_ini_file($config_file, true);
				$this->block_interval = DateInterval::createFromDateString(
						$this->config['block']['interval']
				);
		}

		public function log_action($type)
		{
				$ip = $_SERVER['REMOTE_ADDR'];
				$date = new DateTime();
				$res = self::$db->query(
						"INSERT INTO failed_actions (type, ip, date) ".
						"VALUES ('::0::', '::1::', '::2::')",
						'assoc_array', 
						array($type, $ip, $date->format(self::$date_fmt))
				);
				if ($this->need_block($type))
						$this->block();
		}

		public function is_allowed()
		{
				$ip = $_SERVER['REMOTE_ADDR'];
				$result = self::$db->query("SELECT count(id) FROM blocked_ip WHERE ip='::0::'",
										   'assoc_array', array($ip));
				if (!empty($result) && $result[0]['count'])
				{
						if ($this->need_unblock($ip))
						{
								$this->unblock($ip);
								return true;
						}
						else
								return false;
				}
				else
						return true;
				
		}
		
		public function need_block($type)
		{
				$ip = $_SERVER['REMOTE_ADDR'];
				$current_date = new DateTime();
				$interval = DateInterval::createFromDateString(
						$this->config[$type]['interval']
				);
				$start = $current_date->sub($interval);
				$count = self::$db->query(
						"SELECT count(id) FROM failed_actions ".
						"WHERE type='::0::' AND ip='::1::' AND date > '::2::'",
						'assoc_array',
						array($type, $ip, $start->format(self::$date_fmt))
				);
				return (int)$count[0]['count'] > (int)$this->config[$type]['attempts'];
		}

		public function need_unblock()
		{
				$ip = $_SERVER['REMOTE_ADDR'];
				$res = self::$db->query("SELECT date FROM blocked_ip WHERE ip='::0::' ORDER BY date DESC",
										'assoc_array', array($ip));
				if (empty($res))
						return false;
				$date = new DateTime($res[0]['date']);
				$current_date = new DateTime();
				return $current_date->sub($this->block_interval) > $date;
		}

		public function block()
		{
				$ip = $_SERVER['REMOTE_ADDR'];
				$date = new DateTime();
				self::$db->query("INSERT INTO blocked_ip (ip, date) VALUES ('::0::', '::1::')",
								 'assoc_array', array($ip, $date->format(self::$date_fmt)));
		}

		public function unblock()
		{
				$ip = $_SERVER['REMOTE_ADDR'];
				self::$db->query("DELETE FROM blocked_ip WHERE ip = '::0::'",
								 'assoc_array', array($ip));
		}
}